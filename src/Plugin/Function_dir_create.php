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
use Raxon\Module\Dir;
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_dir_create(Parse $parse, Data $data, $url='', $chmod=''){
    return Dir::create($url, $chmod);
}
