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
use Raxon\Module\Core;

/**
 * @throws \Raxon\Exception\ObjectException
 */
function modifier_object(Parse $parse, Data $data, $value, $output=Core::OBJECT_OBJECT){
    return Core::object($value, $output);
}
