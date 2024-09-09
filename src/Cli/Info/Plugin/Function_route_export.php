<?php

use Raxon\Org\App;
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;
use Raxon\Org\Module\Route;

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
