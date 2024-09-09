<?php

namespace Raxon\Org\Output\Filter\System;

use Raxon\Org\App;

use Raxon\Org\Module\Controller;

class Route extends Controller {
    const DIR = __DIR__ . '/';

    public static function list(App $object, $response=null): object
    {
        $result = [];
        if(
            !empty($response) &&
            is_array($response)
        ){
            foreach($response as $nr => $record){
                if(
                    is_array($record) &&
                    array_key_exists('name', $record)
                ){
                    $name = str_replace('.', '-', strtolower($record['name']));
                    $result[$name] = $record;
                }
                elseif(
                    is_object($record) &&
                    property_exists($record, 'name')
                ){
                    $name = str_replace('.', '-', strtolower($record->name));
                    $result[strtolower($name)] = $record;
                }
            }
        }
        return (object) $result;
    }
}