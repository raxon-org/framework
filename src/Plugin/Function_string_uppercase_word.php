<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-14
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_string_uppercase_word(Parse $parse, Data $data, $string='', $delimiter=null){
    if(empty($delimiter)){
        $result = ucwords($string);
    } else {
        $result = ucwords($string, $delimiter);
    }
    return $result;
}
