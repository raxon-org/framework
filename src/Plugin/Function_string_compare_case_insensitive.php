<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-14
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_string_compare_case_insensitive(Parse $parse, Data $data, $string1='', $string2=''){
    $result = strcasecmp($string1, $string2);
    return $result;
}
