<?php

use Raxon\Module\Parse;
use Raxon\Module\Data;


function function_response_file(Parse $parse, Data $data){
    $object = $parse->object();
    $object->config('response.output', 'file');
}
