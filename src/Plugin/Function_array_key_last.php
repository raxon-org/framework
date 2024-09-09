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

function function_array_key_last(Parse $parse, Data $data, $array=[]){
    $result = array_key_last($array);
    return $result;
}
