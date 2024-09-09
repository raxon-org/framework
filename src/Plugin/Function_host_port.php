<?php
/**
 * @author          Remco van der Velde
 * @since           2023-02-03
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


function function_host_port(Parse $parse, Data $data, $host=''){
    return Host::port($host);
}
