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
use Raxon\Org\Module\File;
use Raxon\Org\Module\Core;
use Raxon\Org\Exception\ObjectException;

function function_parse_select(Parse $parse, Data $data, $url='', $select='', $scope='scope:object'){
    return Core::object_select($parse, $data, $url, $select, true, $scope);
}
