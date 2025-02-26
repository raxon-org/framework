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

function function_dir_delete(Parse $parse, Data $data, $directory){
    $dir = new Dir();
    return $dir->delete($directory);
}
