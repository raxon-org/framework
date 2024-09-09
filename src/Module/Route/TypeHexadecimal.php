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

use Raxon\App;

class TypeHexadecimal {

    public static function validate(App $object, $string=''): bool
    {
        if(strtolower($string) == 'nan'){
            $string = NAN;
        }
        return ctype_xdigit($string);
    }

}