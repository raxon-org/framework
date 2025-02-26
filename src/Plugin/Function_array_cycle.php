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

function function_array_cycle(Parse $parse, Data $data, $name, $arguments=[]){
    $name = 'raxon.org.cycle.' . $name;
    if(substr($name, 0, 1) === '$'){
        $name = substr($name, 1);
    }
    $array = $parse->object()->data($name);
    if(
        $array &&
        is_array($array)
    ){
        $next = next($array);
        if($next === false){
            $next = reset($array);
        }
        $parse->object()->data($name, $array);
        return $next;
    } else {
        $parse->object()->data($name, $arguments);
        return reset($arguments);
    }
}
