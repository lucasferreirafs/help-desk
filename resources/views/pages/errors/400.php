<?php
use App\Core\View;
?>
<section>
    <h1>
        <?= View::escape((string) $status) ?>
        <?= View::escape($title) ?>
    </h1>

    <p><?= View::escape($message) ?></p>
</section>