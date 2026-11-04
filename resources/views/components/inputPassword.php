<div class="mb-4">
    <label 
        for="<?= $for ?>" 
        class="block text-gray-700 text-sm font-medium mb-2"
    >
        <?= $label ?>
    </label>

    <div class="relative">
        <input
            class="w-full border-2 border-gray-300 rounded-md p-2 pr-10 outline-blue-300 transition-all duration-300"
            id="<?= $id ?>" 
            name="<?= $name ?>" 
            type="password" 
            placeholder="••••••••" 
            autocomplete="current-password"
            required
        >

        <button 
            type="button"
            data-password-toggle
            aria-label="Mostrar Senha"
            class="text-gray-500 cursor-pointer absolute inset-y-0 right-0 flex items-center px-3 outline-blue-300 hover:text-[#162645]"
        >
            <i class="fa-regular fa-eye"></i>
        </button>
    </div>
</div>
