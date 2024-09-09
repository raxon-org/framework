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

function function_object_clone(Parse $parse, Data $data, $object=''){
    $object= Core::deep_clone($object);
    return $object;
}
