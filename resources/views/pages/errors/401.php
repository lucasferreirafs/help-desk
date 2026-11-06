<?php
use App\Core\View;
?>
<section class="bg-white w-md rounded-xl shadow-xl p-8">
    <div class="mb-5">
        <h1 class="text-center text-7xl font-bold text-[#162645] mb-4">
            <?= View::escape((string) $status) ?>
        </h1>
        <h2 class="text-center font-medium text-[#162645] text-3xl mb-4">Autenticação necessária</h2>
        <p class="text-center text-sm text-gray-500">Você precisa entrar na sua conta para acessar esta página. Sua sessão pode ter expirado ou ainda não ter sido inciada.</p>

        <div class="flex items-center justify-center gap-3 mt-7">

            <a 
                href="/login" 
                class="bg-[#162645] flex items-center justify-center text-sm text-white font-medium flex-1 rounded-md p-2 hover:-translate-y-0.5 duration-300 transition-all"
            >Entrar na conta</a>

            <button
                onclick="history.back()"
                type="button"
                class="text-[#162645] flex items-center justify-center text-sm font-medium shadow-sm border border-gray-300 flex-1 text-center rounded-md p-2 hover:-translate-y-0.5 duration-300 transition-all cursor-pointer"
            >Voltar</button>

        </div>
    </div>
    <span class="block border-b-2 border-gray-100 mb-4"></span>

    <p class="text-center text-xs text-gray-500 mb-2">Use seu email e sua para se identificar e continuar no Help Desk.</p>

    <a href="/reset-password" class="block text-[#162645] text-center font-medium text-sm hover:underline">Recuperar senha</a>

</section>