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

function function_preg_match(Parse $parse, Data $data, $pattern=null, $subject=null, $match_attribute=null, $flags=PREG_PATTERN_ORDER, $offset=0){
    if(is_string($flags)){
        $flags = constant($flags);
    }
    if($match_attribute !== null){
        if(substr($match_attribute, 0, 1) == '$'){
            $match_attribute = substr($match_attribute, 1);
        }
        $match = [];
        $result = preg_match($pattern, $subject, $match, $flags, $offset);
        $data->data($match_attribute, $match);
    } else {
        $result = preg_match($pattern, $subject);
    }
    return $result;
}
