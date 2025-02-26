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
use Raxon\Module\Controller;
use Raxon\Module\Data;

function function_controller_name(Parse $parse, Data $data, $name=null){
    return Controller::name($name);
}
