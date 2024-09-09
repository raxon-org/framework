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
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;

function function_is_empty(Parse $parse, Data $data){
    $attribute = orgc_get_args();
    array_shift($attribute);
    array_shift($attribute);
    foreach($attribute as $is_empty){
        if(!empty($is_empty)){
            return false;
        }
    }
    return true;
}
