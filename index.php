<?php
include './united.php';
include './tests.php';
include './classes/number.php';

function main() {
    $united = new united();
    $united->testClass('number');
    
    
    //$united->testFunction('tests');
    
    die(''.memory_get_peak_usage());
}

main();