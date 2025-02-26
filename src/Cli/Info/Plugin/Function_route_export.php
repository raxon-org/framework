<?php

use Raxon\App;
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Route;

function function_route_export(Parse $parse, Data $data){
    $object = $parse->object();
    $route = $object->data(App::ROUTE);
    $list = $route->data();
    $result = [];
    foreach($list as $nr => $record){
        $result[$nr] = Route::controller($record);
    }
    return $result;
}
