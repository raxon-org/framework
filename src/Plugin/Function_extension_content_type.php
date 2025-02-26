<?php
/**
 * @author          Remco van der Velde
 * @since           2022-03-17
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

/**
 * @throws Exception
 */
function function_extension_content_type(Parse $parse, Data $data, $extension=''): ?string
{
    $object = $parse->object();
    if(substr($extension,0, 1) === '.'){
        $extension = substr($extension, 1);
    }
    return $object->config('contentType.' . strtolower($extension));
}
