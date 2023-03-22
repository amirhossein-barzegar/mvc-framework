<?php

session_start();
function dumper(...$stuffs) {
    foreach($stuffs as $stuff):
    echo '<pre>';
    var_dump($stuff);
    echo '</pre>';
    endforeach;
}
require __DIR__ . '/vendor/autoload.php';

require 'Routes/web.php';
