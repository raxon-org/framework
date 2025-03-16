<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-18
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\App;

function validate_is_array(App $object, array $record=[], mixed $array=null, mixed $field='', mixed $argument='', mixed $function=false): bool
{
    if(is_array($argument)){
        foreach($argument as $nr => $record_argument){
            if(
                $record_argument === null &&
                $array === null
            ){
                return true;
            }
            elseif(is_bool($record_argument)){
                if($record_argument === true){
                    if($array === ''){
                        return false;
                    }
                    return is_array($array);
                } else {
                    if($array === ''){
                        return true;
                    }
                    return !is_array($array);
                }
            }
        }
        return false;
    }
    if(is_bool($argument)){
        if($argument === true){
            if($array === ''){
                return false;
            }
            return is_array($array);
        } else {
            if($array === ''){
                return true;
            }
            return !is_array($array);
        }
    } else {
        return is_array($array);
    }
}
