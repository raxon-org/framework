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

function function_explode(Parse $parse, Data $data, $seperator='', $string='', $limit=PHP_INT_MAX){
    return explode($seperator, $string, $limit);
}
