<?php
/**
 * @author          Remco van der Velde
 * @since           2022-11-24
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_route_current(Parse $parse, Data $data, $attribute=null){
    $object = $parse->object();
    if($attribute !== null){
        $current = $object->route()->current();
        if(property_exists($current, $attribute)){
            return $current->{$attribute};
        }
    } else {
        return $object->route()->current();
    }

}
