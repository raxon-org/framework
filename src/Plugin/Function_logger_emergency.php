<?php
/**
 * @author          Remco van der Velde
 * @since           2021-03-05
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_logger_emergency(Parse $parse, Data $data, $message=null, $context=[], $channel=''){
    $object = $parse->object();
    if(empty($channel)){
        $channel = $object->config('project.log.app');
    }
    if($channel){
        $object->logger($channel)->emergency($message, $context);
    }
}