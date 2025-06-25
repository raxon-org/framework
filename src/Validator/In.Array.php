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

function validate_in_array(App $object, object|null $record=null,  mixed $in='', mixed $field='', mixed $array=[], mixed $function=false): bool
{
    if(is_array($in)){
        foreach($in as $text){
            if(in_array(null, $array, true)){
                if($text === null){
                    return true;
                }
            }
            if(!in_array($text, $array, true)){
                return false;
            }
        }
        return true;
    } else {
        if(in_array(null, $array, true)){
            if($in === null){
                return true;
            }
            return in_array($in, $array, true);
        } else {
            if(empty($in)){
                return false;
            }
            return in_array($in, $array, true);
        }
    }
}
