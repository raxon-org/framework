<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-22
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_property_last(Parse $parse, Data $data, $object){
    if(is_object($object)){
        $attribute = false;
        foreach($object as $attribute => $unused){
            //intentionally
        }
        return $attribute;
    }
    return false;
}
