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

function function_dir_add_mtime(Parse $parse, Data $data, $list=[]){
    if(!is_array($list)){
        return $list;
    }
    foreach($list as $nr => $file){
        $file->mtime = File::mtime($file->url);
    }
    return $list;
}
