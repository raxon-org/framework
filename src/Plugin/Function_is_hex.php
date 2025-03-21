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

function function_is_hex(Parse $parse, Data $data, $hex=null){
    if(strtolower($hex) == 'nan'){
        $hex = NAN;
    }
    return ctype_xdigit($hex);
}
