<?php

namespace Raxon\Output\Filter\System;

use Raxon\App;

use Raxon\Module\Controller;

class Log extends Controller {
    const DIR = __DIR__ . '/';

    public static function output_filter(App $object, $response=null): array | object
    {
        $result = [];
        $count = 0;
        if(
            !empty($response) &&
            (
                is_object($response) ||
                is_array($response)
            )
        ) {
            foreach ($response as $nr => $record) {
                if (
                    is_array($record) &&
                    array_key_exists('name', $record)
                ) {
                    $result[$record['name']] = $record;
                    $count++;
                } elseif (
                    is_object($record) &&
                    property_exists($record, 'name')
                ) {
                    $result[$record->name] = $record;
                    $count++;
                }
            }
        }
        if($count > 0){
            return (object) $result;
        } else {
            return $response;
        }

    }
}