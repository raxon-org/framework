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
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;

function function_content_type(Parse $parse, Data $data){
    return $parse->object()->data(\Raxon\Org\App::CONTENT_TYPE);
}
