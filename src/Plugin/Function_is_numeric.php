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

function function_is_numeric(Parse $parse, Data $data, $numeric=null){
    if(strtolower($numeric) == 'nan'){
        $numeric = NAN;
    }
    return is_numeric($numeric);
}
