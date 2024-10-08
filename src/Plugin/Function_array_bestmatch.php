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

function function_array_bestmatch(Parse $parse, Data $data, $array=[], $search='', $with_score=false){
    return Core::array_bestmatch($array, $search, $with_score);
}
