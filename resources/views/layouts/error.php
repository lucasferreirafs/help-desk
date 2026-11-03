<?php
declare(strict_types=1);

use App\Core\View;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="shortcut icon" href="/assets/image/logo.svg" type="image/x-icon">
    <title><?= View::escape($title ?? "Ocorreu um erro") ?></title>
</head>
<body>
    <main>
        <?= $content ?>
    </main>
</body>
</html>