<?php
/**
 * @author          Remco van der Velde
 * @since           2023-02-03
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_data_is_empty(Parse $parse, Data $data){
    $data->is_empty();
}
