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
use Raxon\Config;

use Raxon\Module\Data;
/**
 * @throws \Raxon\Exception\ObjectException
 * @throws Exception
 */
function validate_is_unique_json(App $object, object|null $record=null, mixed $string='', mixed $field='', mixed $argument='', mixed $function=false): bool
{
    $data = new Data($record);
    $original_uuid = $data->data('uuid');
    $url = false;
    $list = null;
    if(property_exists($argument, 'url')){
        $url = $argument->url;
        $parameters =[];
        $parameters[] = $url;
        $parameters = Config::parameters($object, $parameters);
        $url = $parameters[0];
    }
    if(property_exists($argument, 'list')){
        $list = $argument->list;
    }
    $is_unique = true;
    if($url){
        $data = $object->data_read($url, sha1($url));
        if($data){
            $result = $data->data($list);
            if(is_array($result) || is_object($result)){
                foreach($result as $nr => $record_result){
                    $uuid = false;
                    if(
                        is_array($record_result) &&
                        array_key_exists('uuid', $record_result)
                    ){
                        $uuid = $record_result['uuid'];
                    }
                    elseif(
                        is_object($record_result) &&
                        property_exists($record_result, 'uuid')
                    ){
                        $uuid = $record_result->uuid;
                    }
                    if(
                        !empty($original_uuid) &&
                        $original_uuid === $uuid
                    ){
                        continue;
                    }
                    if(empty($list)){
                        $match = strtolower($data->data($nr . '.' . $field));
                    } else {
                        $match = strtolower($data->data($list . '.' . $nr . '.' . $field));
                    }
                    if(empty($match)){
                        continue;
                    }
                    if($match == strtolower($string)){
                        $is_unique = false;
                        break;
                    }
                }
            }
        }
        return $is_unique;
    }
    return false;
}
