<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-14
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Core;
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_execute(Parse $parse, Data $data, $command='', $notification=''){
    $object = $parse->object();
    $command = (string) $command;
    $command = escapeshellcmd($command);
    $output = false;
    Core::execute($object, $command, $output, $notify);
    if($notification){
        if(
            is_string($notification) &&
            substr($notification, 0, 1) === '$'
        ){
            $notification = substr($notification, 1);
        }
        $data->data($notification, $notify);
    }
//    exec($command, $output);
    return $output;
}
