<?php
/**
 * @author          Remco van der Velde
 * @since           2021-03-05
 */
use Raxon\Org\App;
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;

function function_logger_alert(Parse $parse, Data $data, $message=null, $context=[], $channel=''){
    $object = $parse->object();
    if(empty($channel)){
        $channel = $object->config('project.log.app');
    }
    if($channel){
        $object->logger($channel)->alert($message, $context);
    }
}