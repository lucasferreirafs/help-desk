<?php
    $STYLES = [
        'primary' => 'bg-[#162645] text-white outline-blue-300 hover:-translate-y-0.5',
        
        'default' => 'bg-gray-900 text-white'
    ];
    
    $extraStyles = $class ?? '';
?>

<button 
    type="<?= $type ?>"
    class="<?= $STYLES[$btnStyle ?? 'default'] . ' ' . $extraStyles ?> p-3 cursor-pointer rounded-md font-medium transition-all duration-300"
><?= $content ?></button>