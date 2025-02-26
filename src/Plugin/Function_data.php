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
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_data(Parse $parse, Data $data, $attribute=null, $value=null){
    if(
        $attribute === null &&
        $value === null
    ){
        return $data->data();
    } else {
        if(
            is_string($attribute) &&
            substr($attribute, 0, 1) === '$'
        ){
            $attribute = substr($attribute, 1);
        }
        return $data->data($attribute, $value);
    }
}
