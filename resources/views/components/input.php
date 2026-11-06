<?php

use App\Core\View;

?>

<div class="mb-4">
    <label for="<?= $for ?? 'undefined' ?>" class="block text-gray-700 text-sm font-medium mb-2">
        <?= $label ?? '' ?>
    </label>

    <input 
        class="w-full border-2 border-gray-300 rounded-md p-2 outline-blue-300 transition-all duration-300"
        id="<?= $id ?? '' ?>" 
        name="<?= $name ?? 'undefined' ?>" 
        type="<?= $type ?? 'text' ?>"
        value="<?= View::escape($value ?? '') ?>" 
        placeholder="<?= $placeholder ?? '' ?>"
        autocomplete="<?= $autocomplete ?? 'off' ?>" 
        <?= ($required ?? false) ? 'required' : '' ?>
    >
</div>