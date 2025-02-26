<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-13
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_config(Parse $parse, Data $data, $attribute=null, $value=null){
    if(substr($attribute, 0, 1) === '$'){
        $attribute = substr($attribute, 1);
    }
    $object = $parse->object();
    if($value !== null){
        $object->config($attribute, $value);
    } else {
        return $object->config($attribute);
    }
}
