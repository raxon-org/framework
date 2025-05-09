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

function function_constant(Parse $parse, Data $data, $name, $value=null){
    if($value !== null){
        define(strtoupper(str_replace('.','_', $name)), $value);
    }
    $name = strtoupper(str_replace('.','_', $name));
    if(defined($name)){
        return constant($name);
    }
    return '';
}
