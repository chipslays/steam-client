<?php 

use Steam\Client;

if (! function_exists('cli')) {
    function cli() {
        return Client::getInstance()->cli();
    }
}