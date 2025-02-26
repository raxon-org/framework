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
use Raxon\Module\File;
use Raxon\Module\Core;

/**
 * @throws \Raxon\Exception\ObjectException
 * @throws \Raxon\Exception\FileWriteException
 */
function function_object_select(Parse $parse, Data $data, $url, $select=null, $compile=false, $scope='scope:object'){
    return Core::object_select($parse, $data, $url, $select, $compile, $scope);
}
