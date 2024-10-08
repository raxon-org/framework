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

function modifier_replace(Parse $parse, Data $data, $value, $search='', $replace=''){
    if(is_array($value)){
        foreach($value as $key => $record){
            $value[$key] = str_replace($search, $replace, $record);
        }
        return $value;
    } else {
        return str_replace($search, $replace, $value);
    }
}
