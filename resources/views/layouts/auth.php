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
    <title><?= View::escape($title ?? 'Autenticação') ?> | Help Desk</title>
</head>
<body class="flex flex-col h-screen">
    <header class="w-screen bg-[#162645] h-15 flex items-center justify-start px-15">
        <div class="flex gap-1 items-center">
            <img class="w-10 h-10" src="/assets/image/logo.svg" alt="Ícone de uma chave inglesa dentro de um círculo.">
            <h1 class="text-white font-semibold text-2xl">Help Desk</h1>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center bg-gray-100">
        <?= $content ?>
    </main>

    <script src="/assets/js/app.js"></script>
</body>
</html>