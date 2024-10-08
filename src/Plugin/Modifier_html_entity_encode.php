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

function modifier_html_entity_encode(Parse $parse, Data $data, $string='', $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, $encoding=null, $double_encoding=true){
    if(is_string($flags)){
        $flags = constant($flags);
    }
    return htmlentities($string, $flags, $encoding, $double_encoding);
}