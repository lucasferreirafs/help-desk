<?php
use App\Core\View;


?>

<div class="bg-white w-md rounded-2xl shadow-xl p-8">

    <header class="mb-6">
        <h1 class="text-3xl text-gray-700 font-semibold mb-1.5">Acesse sua conta</h1>
        <p class="text-gray-600">Bem-vindo de volta! Informe seus dados para entrar no Help Desk.</p>
    </header>

    <?php if (isset($error)): ?>
         <?=
            View::component(
                'alert',
                [
                    'error' => $error,
                    'typeError' => 'error'
                ]
            )
        ?>
    <?php endif; ?>

    <form action="/login" method="post">
        <input type="hidden" name="_token" value="<?= View::escape($_SESSION['csrf_token']) ?>">

        <?=
            View::component(
                'input',
                [
                    'for' => 'email',
                    'label' => 'E-mail',
                    'id' => 'email',
                    'name' => 'email',
                    'type' => 'email',
                    'value' => $old['email'] ?? '',
                    'placeholder' => 'seu.email@empresa.com',
                    'autocomplete' => 'username',
                    'required' => true
                ]
            );
        ?>
        <?=
            View::component(
                'inputPassword',
                [
                    'for' => 'password',
                    'label' => 'Senha',
                    'id' => 'password',
                    'name' => 'password'
                ]
            );
        ?>

        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember"
                    class="outline-blue-300 w-4 h-4 accent-gray-800 border border-gray-300 rounded cursor-pointer">
                <label for="remember" class="text-gray-600 cursor-pointer">Manter acesso</label>
            </div>

            <a href="reset-password" class="text-[#243c6d] font-bold text-sm hover:underline outline-blue-300">Esqueci
                minha senha</a>
        </div>

        <?=
            View::component(
                'button',
                [
                    'type' => 'submit',
                    'btnStyle' => 'primary',
                    'class' => 'w-full mb-4',
                    'content' => 'Acessar Conta'
                ]
            );
        ?>

        <p class="text-center text-gray-600">
            Não tem uma conta? <a href="/register"
                class="text-[#162645] outline-blue-300 font-semibold hover:underline">Criar conta</a>
        </p>
    </form>
</div>