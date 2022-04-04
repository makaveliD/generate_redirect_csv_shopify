<?php

require __DIR__.'/vendor/autoload.php';

use App\CsvRedirectGenerator as CsvRedirectGenerator;

function main()
{
    $CsvRedirectGenerator = new CsvRedirectGenerator();

    $CsvRedirectGenerator->concatDataAndGenerateCsv();
}

main();
