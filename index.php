<?php

session_start();
ini_set('display_errors',"1");
function dumper(...$stuffs): void
{
    foreach($stuffs as $stuff):
        echo '<pre>';
        var_dump($stuff);
        echo '</pre>';
    endforeach;
}
require __DIR__ . '/vendor/autoload.php';

require 'Routes/web.php';
