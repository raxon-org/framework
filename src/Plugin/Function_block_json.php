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

function function_block_json(Parse $parse, Data $data, $name='', $value=null){
    if($value === null){
        $value = $name;
        $name = null;
    }
    if(is_array($value) || is_object($value)){
        $value = Core::object($value, Core::OBJECT_JSON);
    }
    $value = trim($value, "\r\n\s\t");
    if(empty($name)){
        $content = $data->data('#content');
        $content[] = $value;
        $data->data('#content', $content);
    } else {
        $data->data($name, $value);     
    }    
    return '';
}
