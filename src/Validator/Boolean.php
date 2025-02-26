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

use Raxon\App;

function validate_boolean(App $object, $string='', $field='', $argument='', $function=false): bool
{
    $bool = $string;
    if(
        $bool == '1' ||
        $bool == 'true' ||
        $bool === true
    ){
        $bool = true;
    }
    elseif(
        $bool == '0' ||
        $bool == 'false' ||
        $bool === false
    ){
        $bool = false;
    }
    if(empty($argument)){
        if(is_bool($bool)){
            return true;
        }
        return false;
    }
    if(
        is_array($argument) &&
        in_array($bool, $argument, true)
    ){
        return true;
    }
    elseif(
        $argument == '1' ||
        $argument == 'true' ||
        $argument === true
    ){
        $argument = true;
    }
    elseif(
        $argument == '0' ||
        $argument == 'false' ||
        $argument === false
    ){
        $argument = false;
    }
    if(
        is_bool($bool) &&
        is_bool($argument)
    ){
        return $bool === $argument;
    }
    return false;
}
