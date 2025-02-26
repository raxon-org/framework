<?php

use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Core;

function function_server_token(Parse $parse, Data $data){
    if(array_key_exists('HTTP_AUTHORIZATION', $_SERVER)){
        $explode = explode('Bearer ', $_SERVER['HTTP_AUTHORIZATION'], 2);
        if(array_key_exists(1, $explode)){
            return $explode[1];
        }
    }
}
