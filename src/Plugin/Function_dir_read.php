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
use Raxon\Module\Dir;
use Raxon\Module\File;

function function_dir_read(Parse $parse, Data $data, $url='', $recursive=false, $format='flat'){
    if(File::exist($url)){
        $dir = new Dir();
        return $dir->read($url, $recursive, $format);
    }
    return [];
}
