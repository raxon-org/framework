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
use Raxon\Org\Module\Core;
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;

function function_array_object(Parse $parse, Data $data, $array=[]){
    $result = Core::array_object($array);
    return $result;
}
