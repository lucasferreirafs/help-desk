
-- PostgreSQL 18 ou superior: utiliza a funcao nativa uuidv7().
-- ATENCAO: este script remove e recria as tabelas abaixo, apagando seus dados.
-- Execute o arquivo inteiro como script, interrompendo ao primeiro erro.
BEGIN;

DROP TABLE IF EXISTS public.notificacoes;
DROP TABLE IF EXISTS public.aceites_documentos;
DROP TABLE IF EXISTS public.preferencias_usuario;
DROP TABLE IF EXISTS public.chamados;
DROP TABLE IF EXISTS public.categorias;
DROP TABLE IF EXISTS public.documentos_legais;
DROP TABLE IF EXISTS public.users;

-- A funcao e independente das tabelas e deve ser removida explicitamente,
-- depois que os triggers que a utilizam foram removidos junto das tabelas.
DROP FUNCTION IF EXISTS public.helpdesk_atualizar_updated_at();

-- 1. Contas e informacoes da pagina Meu Perfil.
CREATE TABLE public.users (
    id uuid PRIMARY KEY DEFAULT uuidv7(),
    nome varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    password varchar(255) NOT NULL,
    perfil varchar(20) NOT NULL DEFAULT 'colaborador',
    ativo boolean NOT NULL DEFAULT true,
    data_nascimento date,
    telefone varchar(30),
    created_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT users_nome_preenchido CHECK (btrim(nome) <> ''),
    CONSTRAINT users_email_preenchido CHECK (
        email = btrim(email) AND email <> ''
    ),
    CONSTRAINT users_password_preenchido CHECK (btrim(password) <> ''),
    CONSTRAINT users_perfil_valido CHECK (perfil IN ('admin', 'colaborador')),
    CONSTRAINT users_datas_validas CHECK (updated_at >= created_at)
);

-- Email unico sem distinguir maiusculas/minusculas.
-- O backend devera validar formato e consultar com lower(email) = lower(:email).
CREATE UNIQUE INDEX users_email_unico_idx ON public.users (lower(email));

COMMENT ON COLUMN public.users.password IS
    'Hash da senha, nunca a senha original. Usar password_hash/password_verify no PHP.';
COMMENT ON COLUMN public.users.ativo IS
    'Desativacao preserva referencias de chamados e aceites. O login deve conferir este campo.';

-- 2. Preferencias individuais: relacao um-para-um com users.
-- Sem linha, o backend deve usar os mesmos padroes definidos nesta tabela.
CREATE TABLE public.preferencias_usuario (
    user_id uuid PRIMARY KEY
        REFERENCES public.users(id) ON DELETE CASCADE,
    tema varchar(10) NOT NULL DEFAULT 'claro',
    tamanho_fonte smallint NOT NULL DEFAULT 3,
    idioma varchar(20) NOT NULL DEFAULT 'pt-BR',
    fuso_horario varchar(100) NOT NULL DEFAULT 'America/Sao_Paulo',
    notificacoes_chamados boolean NOT NULL DEFAULT true,
    alertas_sonoros boolean NOT NULL DEFAULT false,
    updated_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT preferencias_tema_valido CHECK (tema IN ('claro', 'escuro')),
    CONSTRAINT preferencias_fonte_valida CHECK (tamanho_fonte BETWEEN 1 AND 5),
    CONSTRAINT preferencias_idioma_preenchido CHECK (btrim(idioma) <> ''),
    CONSTRAINT preferencias_fuso_preenchido CHECK (btrim(fuso_horario) <> '')
);

COMMENT ON COLUMN public.preferencias_usuario.fuso_horario IS
    'Identificador IANA. Validar contra pg_timezone_names ou lista permitida no backend.';

-- 3. Categorias para classificacao e filtros.
-- Hardware, Software e Rede poderao ser cadastradas depois da aprovacao.
CREATE TABLE public.categorias (
    id uuid PRIMARY KEY DEFAULT uuidv7(),
    nome varchar(100) NOT NULL,
    ativa boolean NOT NULL DEFAULT true,
    created_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT categorias_nome_valido CHECK (
        nome = btrim(nome) AND nome <> ''
    ),
    CONSTRAINT categorias_datas_validas CHECK (updated_at >= created_at)
);

CREATE UNIQUE INDEX categorias_nome_unico_idx ON public.categorias (lower(nome));

-- 4. Chamados. O nome exibido vem do JOIN com users, sem duplicacao.
-- A descricao e o texto pesquisavel; nao foi inventado um campo titulo
-- que nao aparece nas telas ou em um formulario de abertura disponibilizado.
CREATE TABLE public.chamados (
    id uuid PRIMARY KEY DEFAULT uuidv7(),
    solicitante_id uuid NOT NULL
        REFERENCES public.users(id) ON DELETE RESTRICT,
    categoria_id uuid NOT NULL
        REFERENCES public.categorias(id) ON DELETE RESTRICT,
    responsavel_id uuid
        REFERENCES public.users(id) ON DELETE RESTRICT,
    descricao text NOT NULL,
    status varchar(20) NOT NULL DEFAULT 'pendente',
    iniciado_em timestamptz,
    finalizado_em timestamptz,
    created_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chamados_descricao_preenchida CHECK (btrim(descricao) <> ''),
    CONSTRAINT chamados_status_valido CHECK (
        status IN ('pendente', 'em_andamento', 'finalizado')
    ),
    CONSTRAINT chamados_datas_validas CHECK (
        updated_at >= created_at
        AND (iniciado_em IS NULL OR iniciado_em >= created_at)
        AND (finalizado_em IS NULL OR finalizado_em >= created_at)
        AND (
            iniciado_em IS NULL OR finalizado_em IS NULL
            OR finalizado_em >= iniciado_em
        )
    ),
    CONSTRAINT chamados_estado_coerente CHECK (
        (
            status = 'pendente'
            AND iniciado_em IS NULL
            AND finalizado_em IS NULL
        )
        OR (
            status = 'em_andamento'
            AND responsavel_id IS NOT NULL
            AND iniciado_em IS NOT NULL
            AND finalizado_em IS NULL
        )
        OR (
            status = 'finalizado'
            AND responsavel_id IS NOT NULL
            AND finalizado_em IS NOT NULL
        )
    )
);

-- Indices para pagina inicial, filtros, joins e listagem do colaborador.
-- id desempata registros criados no mesmo instante para futura paginacao.
CREATE INDEX chamados_recentes_idx
    ON public.chamados (created_at DESC, id DESC);
CREATE INDEX chamados_solicitante_recentes_idx
    ON public.chamados (solicitante_id, created_at DESC, id DESC);
CREATE INDEX chamados_status_recentes_idx
    ON public.chamados (status, created_at DESC, id DESC);
CREATE INDEX chamados_categoria_recentes_idx
    ON public.chamados (categoria_id, created_at DESC, id DESC);
CREATE INDEX chamados_responsavel_idx ON public.chamados (responsavel_id);

COMMENT ON COLUMN public.chamados.responsavel_id IS
    'Usuario que atende. Backend deve validar perfil admin e registrar a atribuicao na mesma transacao da mudanca de status.';
COMMENT ON TABLE public.chamados IS
    'Contadores e listas do painel sao derivados desta tabela; nao guardar contagens duplicadas. Transicoes devem ser atomicas e autorizadas.';

-- 5. Notificacoes individuais para o indicador do cabecalho.
-- O chamado e opcional para permitir avisos sem um chamado relacionado.
CREATE TABLE public.notificacoes (
    id uuid PRIMARY KEY DEFAULT uuidv7(),
    destinatario_id uuid NOT NULL
        REFERENCES public.users(id) ON DELETE CASCADE,
    chamado_id uuid
        REFERENCES public.chamados(id) ON DELETE SET NULL,
    mensagem text NOT NULL,
    lida_em timestamptz,
    created_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT notificacoes_mensagem_preenchida CHECK (btrim(mensagem) <> ''),
    CONSTRAINT notificacoes_leitura_valida CHECK (
        lida_em IS NULL OR lida_em >= created_at
    )
);

CREATE INDEX notificacoes_destinatario_recentes_idx
    ON public.notificacoes (destinatario_id, created_at DESC, id DESC);
CREATE INDEX notificacoes_nao_lidas_idx
    ON public.notificacoes (destinatario_id) WHERE lida_em IS NULL;
CREATE INDEX notificacoes_chamado_idx ON public.notificacoes (chamado_id);

-- 6. Conteudo versionado dos termos e da politica de privacidade.
-- Armazenar texto/Markdown; qualquer HTML futuro precisara de sanitizacao.
-- Cada versao deve permanecer imutavel apos publicacao (regra do backend).
CREATE TABLE public.documentos_legais (
    id uuid PRIMARY KEY DEFAULT uuidv7(),
    tipo varchar(20) NOT NULL,
    versao varchar(30) NOT NULL,
    titulo varchar(255) NOT NULL,
    conteudo text NOT NULL,
    publicado_em timestamptz NOT NULL,
    created_at timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT documentos_tipo_valido CHECK (
        tipo IN ('termos_uso', 'privacidade')
    ),
    CONSTRAINT documentos_versao_valida CHECK (
        versao = btrim(versao) AND versao <> ''
    ),
    CONSTRAINT documentos_titulo_preenchido CHECK (btrim(titulo) <> ''),
    CONSTRAINT documentos_conteudo_preenchido CHECK (btrim(conteudo) <> ''),
    CONSTRAINT documentos_tipo_versao_unicos UNIQUE (tipo, versao)
);

CREATE INDEX documentos_tipo_publicacao_idx
    ON public.documentos_legais (tipo, publicado_em DESC, id DESC);

COMMENT ON TABLE public.documentos_legais IS
    'A versao vigente e a publicacao mais recente do tipo com publicado_em <= CURRENT_TIMESTAMP. Numeros como 1.2 nao devem ser ordenados como texto para escolher vigencia.';

-- 7. Vincula o aceite a uma versao exata, nao somente a um booleano.
-- Nao e salvo IP ou user-agent, pois nao foram solicitados no design.
CREATE TABLE public.aceites_documentos (
    user_id uuid NOT NULL
        REFERENCES public.users(id) ON DELETE RESTRICT,
    documento_id uuid NOT NULL
        REFERENCES public.documentos_legais(id) ON DELETE RESTRICT,
    aceito_em timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id, documento_id)
);

CREATE INDEX aceites_documento_idx
    ON public.aceites_documentos (documento_id);

COMMENT ON TABLE public.aceites_documentos IS
    'Data efetiva da aceitacao da versao. Backend deve exigir usuario autenticado e documento publicado; nao e uma certificacao de conformidade legal.';

-- Atualizacao automatica do horario de alteracao das entidades mutaveis.
CREATE FUNCTION public.helpdesk_atualizar_updated_at()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    NEW.updated_at := CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;

CREATE TRIGGER users_atualizar_updated_at
    BEFORE UPDATE ON public.users
    FOR EACH ROW EXECUTE FUNCTION public.helpdesk_atualizar_updated_at();

CREATE TRIGGER preferencias_atualizar_updated_at
    BEFORE UPDATE ON public.preferencias_usuario
    FOR EACH ROW EXECUTE FUNCTION public.helpdesk_atualizar_updated_at();

CREATE TRIGGER categorias_atualizar_updated_at
    BEFORE UPDATE ON public.categorias
    FOR EACH ROW EXECUTE FUNCTION public.helpdesk_atualizar_updated_at();

CREATE TRIGGER chamados_atualizar_updated_at
    BEFORE UPDATE ON public.chamados
    FOR EACH ROW EXECUTE FUNCTION public.helpdesk_atualizar_updated_at();

COMMIT;
