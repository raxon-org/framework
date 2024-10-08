<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-16
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_string_token(Parse $parse, Data $data, $string='', $token=null){
    if($token === null){
        $result = strtok($string);
    } else {
        $result = strtok($string, $token);
    }
    return $result;
}
