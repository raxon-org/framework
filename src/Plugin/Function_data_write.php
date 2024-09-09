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
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;
use Raxon\Org\Module\Core;
use Raxon\Org\Module\File;


/**
 * @throws \Raxon\Org\Exception\ObjectException
 * @throws \Raxon\Org\Exception\FileWriteException
 */
function function_data_write(Parse $parse, Data $data, $url='', $write=false, $output='json'){
    $write = Core::object($write, $output);
    $bytes = File::write($url, $write);    
    return null;
}
