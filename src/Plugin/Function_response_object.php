<?php

use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;


function function_response_object(Parse $parse, Data $data){
    $object = $parse->object();
    $object->config('response.output', 'object');
}
