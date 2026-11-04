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
    <header>
        <h1>logo</h1>
        <nav>
            <ul>
                <li>home</li>
                <li>contato</li>
                <li>login</li>
            </ul>
        </nav>
    </header>
    
    <main>
        <?= $content ?>
    </main>
</body>
</html>