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
use stdClass;
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Host;


function function_host_extension(Parse $parse, Data $data){
    return Host::extension();
}
