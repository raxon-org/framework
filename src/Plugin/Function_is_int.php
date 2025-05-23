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

function function_is_int(Parse $parse, Data $data, $int=null){
    if(strtolower($int) == 'nan'){
        $int = NAN;
    }
    return is_int($int);
}
