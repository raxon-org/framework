<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-18
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Core;

function function_array_bestmatch_key(Parse $parse, Data $data, $array=[], $search=''){
    return Core::array_bestmatch_key($array, $search);
}
