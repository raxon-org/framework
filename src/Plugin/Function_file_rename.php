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
use Raxon\Module\File;

function function_file_rename(Parse $parse, Data $data, $source='', $destination='', $overwrite=false){
    return File::rename($source, $destination, $overwrite);
}
