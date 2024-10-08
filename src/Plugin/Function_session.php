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
use Raxon\Module\Core;

function function_session(Parse $parse, Data $data, $attribute=null, $value=null){
    $object = $parse->object();
    if($attribute === 'delete'){
        $object->session($attribute, $value);
        return;
    }
    if(!empty($parse->is_assign())){
        return $object->session($attribute, $value);
    } else {
        return $object->session($attribute, $value);
    }    
}
