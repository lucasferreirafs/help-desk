<?php
use App\Core\View;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="shortcut icon" href="/assets/image/logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <title><?= View::escape($title ?? 'Help Desk') ?></title>
</head>
<body>
    <header class="bg-gray-800 p-5 flex justify-between items-center">
        <h1 class="text-white text-3xl font-bold uppercase">logo</h1>
        <nav>
            <ul class="flex gap-3 text-white">
                <li class="font-medium cursor-pointer hover:text-gray-500">Home</li>
                <li class="font-medium cursor-pointer hover:text-gray-500">Contato</li>
                <li class="font-medium cursor-pointer hover:text-gray-500">Login</li>
            </ul>
        </nav>
    </header>
    
    <main class="p-6">
        <h1 class="text-2xl font-bold">Bem-vindo, <?= View::escape($userInfo['username']) ?></h1>
        <p>Home <?= View::escape($userInfo['role']) ?></p>
        <div>
            <?= $content ?>
        </div>

        <form action="/logout" method="post">
            <input type="hidden" name="_token" value="<?= View::escape($_SESSION['csrf_token']) ?>">
            <button type="submit" class="text-2xl font-medium text-blue-500 hover:text-blue-900 hover:underline mt-10 cursor-pointer">
                Sair da conta
            </button>
        </form>
    </main>
</body>
</html>