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
use Raxon\Org\Module\Dir;

function function_dir_move(Parse $parse, Data $data, $source='', $destination='', $overwrite=false){
    try {
        return Dir::move($source, $destination, $overwrite);
    } catch (Exception $e){
        return false;
    }
}
