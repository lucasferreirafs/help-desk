<?php
use App\Core\View;
?>
<section class="bg-white w-md rounded-xl shadow-xl p-8">
    <div class="mb-5">
        <h1 class="text-center text-7xl font-bold text-[#162645] mb-4">
            <?= View::escape((string) $status) ?>
        </h1>
        <h2 class="text-center font-medium text-[#162645] text-3xl mb-4">Acesso negado</h2>
        <p class="text-center text-sm text-gray-500">Sua conta está autenticada, mas não tem permissão para acessar esta página ou realizar esta ação.</p>

        <div class="flex items-center justify-center gap-3 mt-7">
            <a 
                href="/home" 
                class="bg-[#162645] flex items-center justify-center text-sm text-white font-medium flex-1 rounded-md p-2 hover:-translate-y-0.5 duration-300 transition-all"
            >Ir para a página inicial</a>

            <form action="/logout" method="post" class="flex-1">
                <input type="hidden" name="_token" value="<?= View::escape($_SESSION['csrf_token']) ?>">
                <button
                    type="submit"
                    class="text-[#162645] flex items-center justify-center text-sm font-medium shadow-sm border border-gray-300 w-full text-center rounded-md p-2 hover:-translate-y-0.5 duration-300 transition-all cursor-pointer"
                >Trocar de conta</button>
            </form>
        </div>
    </div>
    <span class="block border-b-2 border-gray-100 mb-4"></span>

    <p class="text-center text-xs text-gray-500 mb-2">Use seu email e sua para se identificar e continuar no Help Desk.</p>

    <a href="/reset-password" class="block text-[#162645] text-center font-medium text-sm hover:underline">Recuperar senha</a>

</section>