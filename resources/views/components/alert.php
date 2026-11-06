<?php
    use App\Core\View;
    $typesErrors = [
        'error' => 'bg-red-500',
        'success' => 'bg-green-500 ',
        'default' => 'bg-blue-500'
    ];
?>

<p class="<?= $typesErrors[$typeError ?? 'default'] ?> text-white font-medium p-2 rounded-md mb-3" role="alert">
    <?= View::escape($error ?? ''); ?>
</p>