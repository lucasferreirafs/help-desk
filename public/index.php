<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\TestAutoLoad;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Aplicativo Help Desk</h1>
    <p>
        <?= 
            htmlspecialchars(
                TestAutoLoad::mensagem(),
                ENT_QUOTES,
                'UTF-8'
            ); 
        ?>
    </p>
</body>
</html>