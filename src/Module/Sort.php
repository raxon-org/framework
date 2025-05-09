<?php
/**
 * @author          Remco van der Velde
 * @since           18-12-2020
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */
namespace Raxon\Module;

use Exception;

class Sort extends Data {
    const ASC = 'ASC';
    const DESC = 'DESC';


    /**
     * @throws Exception
     */
    public static function list($list): Sort
    {
        return new Sort($list);
    }

    /**
     * @throws Exception
     */
    public function with($sort=[], $options=[]): mixed
    {
        if(array_key_exists('output', $options)){
            $output = $options['output'];
        } else {
            $output = false;
        }
        if(array_key_exists('key', $options)){
            $key = $options['key'];
        } else {
            $key = false;
        }
        if(array_key_exists('key_reset', $options)){
            $key_reset = $options['key_reset'];
        } else {
            $key_reset = false;
        }
        if(array_key_exists('flags', $options)){
            $flags = $options['flags'];
        } else {
            $flags = SORT_NATURAL;
        }
        $list = $this->data();
        if(
            is_array($list) || 
            is_object($list)
        ){
            $result = [];  
            $no_attribute = [];
            $count = count($sort);
            if($count == 1){
                if(
                    is_object($list) &&
                    Core::object_is_empty($list)){
                    return [];
                }
                $attribute = false;
                $sortable_1 = Sort::ASC;
                foreach($list as $uuid => $node){
                    foreach($sort as $attribute => $record){
                        $value = $this->data($uuid . '.' . $attribute);
                        if(is_scalar($value)) {
                            if(is_array($node)){
                                $result[$value][] = $node;
                            }
                            elseif(is_object($node)){
                                $result[$value][] = $node;
                            }
                        }
                        elseif (is_array($value)){
                            $attr = '';
                            foreach($value as $node_attribute){
                                if(is_scalar($node_attribute)){
                                    $attr .= '.' . $node_attribute;
                                }
                            }
                            $attr = substr($attr, 1);
                            $result[$attr][] = $node;
                        }
                        elseif(is_object($value)){
                            $attr = '';
                            foreach($value as $node_attribute){
                                if(is_scalar($node_attribute)){
                                    $attr .= '.' . $node_attribute;
                                }
                            }
                            $attr = substr($attr, 1);
                            $result[$attr][] = $node;
                        } else {
                            $result[''][] = $node;
                        }
                        $sortable_1 = $record;
                        break;
                    }
                }
                unset($sort[$attribute]);                
                if(strtoupper($sortable_1) === Sort::ASC){
                    if($attribute === 'uuid'){
                        usort($result, array($this,"uuid_compare_ascending"));
                    } else {
                        ksort($result, $flags);
                    }

                } else {
                    if($attribute === 'uuid'){
                        usort($result, array($this,"uuid_compare_descending"));
                    } else {
                        krsort($result, $flags);
                    }
                }
                if($output === 'raw'){
                    return $result;
                }
                $list = [];                
                foreach($result as $attribute => $subList){
                    foreach($subList as $nr => $record){
                        if(is_array($record)){
                            if(array_key_exists('uuid', $record)){
                                $list[$record['uuid']] = $record;
                            } else {
                                while(true){
                                    $uuid = Core::uuid();
                                    if(!array_key_exists($uuid, $list)){
                                        $record['uuid'] = $uuid;
                                        break;
                                    }
                                }
                                $list[$uuid] = $record;
                            }
                        }
                        elseif(is_object($record)) {
                            if(property_exists($record, 'uuid')){
                                $list[$record->uuid] = $record;
                            } else {
                                while(true){
                                    $uuid = Core::uuid();
                                    if(
                                        !array_key_exists($uuid, $list)
                                    ){
                                        $record->uuid = $uuid;
                                        break;
                                    }
                                }
                                $list[$uuid] = $record;
                            }
                        }
                    }
                }                                
            }
            elseif($count == 2){
                if(
                    is_object($list) &&
                    Core::object_is_empty($list)){
                    return [];
                }
                $attribute = false;
                $sortable_1 = Sort::ASC;
                $sortable_2 = Sort::ASC;
                foreach($list as $uuid => $node){
                    foreach($sort as $attribute => $record){
                        $value = $this->data($uuid . '.' . $attribute);
                        if(is_scalar($value)){
                            if(is_array($node)){
                                $result[$value][] = $node;
                            }
                            elseif(is_object($node)){
                                $result[$value][] = $node;
                            }
                        }
                        elseif(is_array($value)){
                            $attr = '';
                            foreach($value as $node_attribute){
                                if(is_scalar($node_attribute)){
                                    $attr .= '.' . $node_attribute;
                                }
                            }
                            $attr = substr($attr, 1);
                            $result[$attr][] = $node;
                        }
                        elseif(is_object($value)){
                            $attr = '';
                            foreach($value as $node_attribute){
                                if(is_scalar($node_attribute)){
                                    $attr .= '.' . $node_attribute;
                                }
                            }
                            $attr = substr($attr, 1);
                            $result[$attr][] = $node;
                        } else {
                            $result[''][] = $node;
                        }
                        $sortable_1 = $record;
                        break;
                    }
                }
                unset($sort[$attribute]);
                $data = new Data($result);
                $result = [];
                if(!empty($sort)){
                    foreach($data->data() as $result_key => $list){
                        foreach($list as $list_key => $node) {
                            foreach ($sort as $attribute => $record) {
                                $value = $data->data($result_key . '.' . $list_key . '.' . $attribute);
                                if(is_scalar($value)){
                                    if (is_array($node)) {
                                        $result[$result_key][$value][] = $node;
                                    } elseif (is_object($node)) {
                                        $result[$result_key][$value][] = $node;
                                    }
                                }
                                else if (is_array($value)){
                                    $attr = '';
                                    foreach($value as $node_attribute){
                                        if(is_scalar($node_attribute)){
                                            $attr .= '.' . $node_attribute;
                                        }
                                    }
                                    $attr = substr($attr, 1);
                                    $result[$attr][] = $node;
                                } else {
                                    $result[$result_key][''][] = $node;
                                }
                                $sortable_2 = $record;
                                break;
                            }
                        }
                    }
                    unset($sort[$attribute]);
                    if(strtoupper($sortable_1) === Sort::ASC){
                        ksort($result, $flags);
                    } else {
                        krsort($result, $flags);
                    }                
                    foreach($result as $key => $list){
                        if(strtoupper($sortable_2) === Sort::ASC){
                            ksort($list, $flags);
                        } else {
                            krsort($list, $flags);
                        }
                        $result[$key] = $list;                                                
                    }
                    if($output === 'raw'){
                        return $result;
                    }
                    $list = [];          
                    $has_uuid = false;
                    foreach($result as $result_key => $subList){
                        foreach($subList as $attribute => $subSubList){
                            foreach($subSubList as $nr => $node){
                                if(is_array($node)){
                                    if(array_key_exists('uuid', $node)){
                                        $has_uuid = true;
                                        $list[$node['uuid']] = $node;
                                    } else {
                                        while(true){
                                            $uuid = Core::uuid();
                                            if(!array_key_exists($uuid, $list)){
                                                $node['uuid'] = $uuid;
                                                break;
                                            }
                                        }
                                        $list[$uuid] = $node;
                                    }
                                } else {
                                    if(property_exists($node, 'uuid')){
                                        $has_uuid = true;
                                        $list[$node->uuid] = $node;
                                    } else {
                                        while(true){
                                            $uuid = Core::uuid();
                                            if(
                                                !array_key_exists($uuid, $list) &&
                                                is_object($node)
                                            ){
                                                $node->uuid = $uuid;
                                                break;
                                            }
                                        }
                                        $list[$uuid] = $node;
                                    }
                                }
                            }
                        }
                    }                                      
                }  
            }                   
        }
        if($key_reset){
            $result = [];
            foreach($list as $record){
                $result[] = $record;
            }
            return $result;
        }
        return $list;
    }

    public function uuid_compare_ascending($a, $b): int
    {
        $object_a = null;
        $object_b = null;
        if(is_array($a)){
            $object_a = reset($a);
        }
        elseif(is_string($a)){
            $object_a = false;
        }
        if(is_array($b)){
            $object_b = reset($b);
        }
        elseif(is_string($b)){
            $object_b = false;
        }
        if(is_array($object_a)){
            $a = $object_a['uuid'];
        }
        elseif(is_object($object_a)) {
            $a = $object_a->uuid;
        }
        if(is_array($object_b)){
            $b = $object_b['uuid'];
        }
        elseif(is_object($object_b)){
            $b = $object_b->uuid;
        }
        if($a === $b){
            return 0;
        }
        if(is_array($a) || is_array($b)){
            return 0;
        }
        $explode_a = explode('-', $a);
        $explode_b = explode('-', $b);

        foreach($explode_a as $nr => $part){
            $hex = hexdec($part);
            $match = hexdec($explode_b[$nr]);
            if($hex === $match){
                continue;
            }
            if($hex > $match){
                return 1;
            }
            elseif($hex < $match){
                return -1;
            }
        }
        return 0;
    }

    public function uuid_compare_descending($a, $b): int
    {
        $object_a = null;
        $object_b = null;
        if(is_array($a)){
            $object_a = reset($a);
        }
        elseif(is_string($a)){
            $object_a = false;
        }
        if(is_array($a)){
            $object_b = reset($b);
        }
        elseif(is_string($a)){
            $object_b = false;
        }
        if(is_array($object_a)){
            $a = $object_a['uuid'];
        }
        elseif(is_object($object_a)){
            $a = $object_a->uuid;
        }
        if(is_array($object_b)){
            $b = $object_b['uuid'];
        }
        elseif(is_object($object_b)){
            $b = $object_b->uuid;
        }
        if($a === $b){
            return 0;
        }
        $explode_a = explode('-', $a);
        $explode_b = explode('-', $b);

        foreach($explode_a as $nr => $part){
            $hex = hexdec($part);
            $match = hexdec($explode_b[$nr]);
            if($hex === $match){
                continue;
            }
            if($hex < $match){
                return 1;
            }
            elseif($hex > $match){
                return -1;
            }
        }
        return 0;
    }
}
