<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-18
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Core;
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_object(Parse $parse, Data $data, $input='', $output=null, $type=null){
    $result = Core::object($input, $output, $type);
    return $result;
}
