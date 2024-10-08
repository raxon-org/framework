<?php
/**
 * @author          Remco van der Velde
 * @since           03-08-2022
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */

namespace Raxon\Module\Route;

use stdClass;
use Raxon\App;

class TypeObject {

    public static function validate(App $object, $string=''): bool
    {
        if(
            substr($string, 0, 1) == '{' &&
            substr($string, -1, 1) == '}'
        ){
            $object = json_decode($string);
            if(is_object($object)){
                return true;
            }
        }
        return false;
    }

    public static function cast(App $object, $string=''): stdClass
    {
        $object = json_decode($string);
        if(is_object($object)){
            return $object;
        }
        return new stdClass();
    }
}