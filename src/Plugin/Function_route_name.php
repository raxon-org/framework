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

function function_route_name(Parse $parse, Data $data, $name=null){
    $options=[];
    $result = strtolower(str_replace(
        [
            '.',
            ' '
        ],
        [
            '-',
            '-'
        ],
        $name
    ));
    return $result;
}
