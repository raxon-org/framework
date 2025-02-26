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
use Raxon\Exception\ObjectException;

function function_parse_read(Parse $parse, Data $data, $url='', $cache=true){
    if(File::exist($url)){
        $object = $parse->object();
        if($cache){
            $read = $object->parse_read($url, sha1($url));
        } else {
            $read = $object->parse_read($url);
        }
        if($read){
            try {
                $data->data(Core::object_merge($data->data(), $read->data()));
            } catch (ObjectException $e) {
            }
            return $read->data();
        }
    } else {
        throw new Exception('Error: url=' . $url . ' not found');
    }
    return '';
}
