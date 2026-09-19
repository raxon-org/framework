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

function validate_is_object(App $object, object|null $record=null, mixed $mixed=null, mixed $field='', mixed $argument='', mixed $function=false): bool
{
    return is_object($mixed);
}
