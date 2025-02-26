<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-24
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_preg_split(Parse $parse, Data $data, $pattern=null, $subject=null, $limit=-1, $flags=0){
    if(is_string($flags)){
        $flags = constant($flags);
    }
    $result = preg_split($pattern, $subject, $limit, $flags);
    return $result;
}
