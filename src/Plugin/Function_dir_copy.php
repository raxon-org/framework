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
use Raxon\Org\Module\Dir;

function function_dir_change(Parse $parse, Data $data, $source='', $target=''){
    return Dir::copy($source, $target);
}
