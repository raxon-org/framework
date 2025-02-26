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

function function_capture_prepend(Parse $parse, Data $data, $name, $value=null){
    if(substr($name, 0, 1) === '$'){
        $name = substr($name, 1);
    }
    $list = $data->data($name);
    if(empty($list)){
        $list = [];
    }
    $prepend = [];
    $prepend[] = $value;
    $list[] = array_merge($prepend, $list);
    $data->data($name, $list);
    return '';
}
