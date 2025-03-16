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

use Raxon\Module\Core;

function validate_is_uuid(App $object, object $record=null, mixed $string='', mixed $field='', mixed $argument='', mixed $function=false): bool
{
    $is_uuid = Core::is_uuid($string);
    if($argument === false){
        return !$is_uuid;
    } else {
        return $is_uuid;
    }
}
