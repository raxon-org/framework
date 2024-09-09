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
use Raxon\Module\File;


/**
 * @throws \Raxon\Exception\ObjectException
 * @throws \Raxon\Exception\FileWriteException
 */
function function_data_write(Parse $parse, Data $data, $url='', $write=false, $output='json'){
    $write = Core::object($write, $output);
    $bytes = File::write($url, $write);    
    return null;
}
