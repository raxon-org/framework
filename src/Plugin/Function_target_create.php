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

function function_target_create(Parse $parse, Data $data, $target=null, $options=[]){    
    $result = $target;
    foreach($options as $key => $value){
        $result.='[' . $key . '=\'' . $value . '\']';
    }
    return $result;
}
