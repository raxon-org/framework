<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-20
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_math_fmod(Parse $parse, Data $data, $x=null, $y=null){
    $result = fmod($x, $y);
    return $result;
}
