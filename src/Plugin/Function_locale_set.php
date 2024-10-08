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

function function_locale_set(Parse $parse, Data $data, $category='', $locale=[]){
    if(is_string($category)){
        $category = constant($category);
    }
    setlocale($category, $locale);
}
