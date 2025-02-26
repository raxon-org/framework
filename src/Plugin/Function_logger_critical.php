<?php
/**
 * @author          Remco van der Velde
 * @since           2021-03-05
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_logger_critical(Parse $parse, Data $data, $message=null, $context=[], $channel=''){
    $object = $parse->object();
    if(empty($channel)){
        $channel = $object->config('project.log.error');
    }
    if($channel){
        $object->logger($channel)->critical($message, $context);
    }
}