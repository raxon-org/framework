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
use Raxon\Org\Module\Core;
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;

function function_object(Parse $parse, Data $data, $input='', $output=null, $type=null){
    $result = Core::object($input, $output, $type);
    return $result;
}
