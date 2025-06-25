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

use stdClass;

use Raxon\App;

use Raxon\Module\Data as Storage;
use Raxon\Module\Template\Main;

use Raxon\Node\Module\Node;

use Exception;

use Raxon\Exception\FileWriteException;
use Raxon\Exception\LocateException;
use Raxon\Exception\ObjectException;

class OutputFilter extends Main {

    const NAME = 'OutputFilter';
    const OBJECT = 'System.Output.Filter';
    const ROLE_HAS_PERMISSION = 'System:Output:Filter:list';

    public function __construct(App $object){
        $this->object($object);
    }

    /**
     * @throws Exception
     */
    public static function on(App $object, $data, $options=[]): void
    {
        $list = $object->get(App::OUTPUTFILTER)->get(OutputFilter::OBJECT);
        if(empty($list)){
            $list = [];
        }
        if(is_array($data)){
            foreach($data as $node){
                $list[] = $node;
            }
        } else {
            $list[] = $data;
        }
        $object->get(App::OUTPUTFILTER)->set(OutputFilter::OBJECT, $list);
    }

    public static function off(App $object, $record, $options=[]): void
    {
        //needs rewrite
        /*
        $action = $record->get('action');
        $options = $record->get('options');
        $list = $object->get(App::OUTPUTFILTER)->get(OutputFilter::NAME);
        if(empty($list)){
            return;
        }
        //remove them on the sorted list backwards so sorted on input order
        krsort($list);
        foreach($list as $key => $node){
            if(empty($options)){
                if($node['action'] === $action){
                    unset($list[$key]);
                    break;
                }
            } else {
                if($node['action'] === $action){
                    foreach($options as $options_key => $value){
                        if(
                            $value === true &&
                            is_array($node['options']) &&
                            array_key_exists($options_key, $node['options'])
                        ){
                            unset($list[$key]);
                            break;
                        }
                        if(
                            $value === true &&
                            is_object($node['options']) &&
                            property_exists($node['options'], $options_key)
                        ){
                            unset($list[$key]);
                            break;
                        }
                        elseif(
                            is_array($node['options']) &&
                            array_key_exists($options_key, $node['options']) &&
                            $node['options'][$options_key] === $value
                        ){
                            unset($list[$key]);
                            break;
                        }
                        elseif(
                            is_object($node['options']) &&
                            property_exists($node['options'], $options_key) &&
                            $node['options']->{$options_key} === $value
                        ){
                            unset($list[$key]);
                            break;
                        }
                    }
                }
            }
        }
        $object->get(App::OUTPUTFILTER)->set(OutputFilter::NAME, $list);
        */
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function trigger(App $object, Destination $destination, $options=[]): mixed
    {
        $filters = $object->get(App::OUTPUTFILTER)->data(OutputFilter::OBJECT);
        $response = null;
        if(empty($filters)){
            if(
                array_key_exists('response', $options)
            ){
                return $options['response'];
            }
            return null;
        }
        if(is_array($filters) || is_object($filters)){
            foreach($filters as $filter){
                if(is_object($filter)) {
                    if(
                        property_exists($filter, 'options') &&
                        property_exists($filter->options, 'controller') &&
                        is_array($filter->options->controller)
                    ){
                        //output filters need route match
                        if(
                            (
                                $destination->has('uuid') &&
                                property_exists($filter, 'route') &&
                                $destination->get('uuid') === $filter->route
                            ) ||
                            (
                                property_exists($filter, 'route') &&
                                $filter->route === '*'
                            )
                        ){
                            foreach($filter->options->controller as $controller){
                                $route = new stdClass();
                                $route->controller = $controller;
                                $route = Route::controller($route);
                                if(
                                    property_exists($route, 'controller') &&
                                    property_exists($route, 'function')
                                ){
                                    $filter = new Storage($filter);
                                    try {
                                        $response = $route->controller::{$route->function}($object, $destination, $filter, $options);
                                        if($filter->get('stopPropagation')){
                                            break 2;
                                        }
                                    }
                                    catch (LocateException $exception){
                                        if($object->config('project.log.error')){
                                            $object->logger($object->config('project.log.error'))->error('LocateException', [ $route, (string) $exception ]);
                                        }
                                        elseif($object->config('project.log.app')){
                                            $object->logger($object->config('project.log.app'))->error('LocateException', [ $route, (string) $exception ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if($response){
            return $response;
        }
        if(array_key_exists('response', $options)){
            return $options['response'];
        }
        return null;
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public static function configure(App $object): void
    {
        $node = new Node($object);
        $role_system = $node->role_system();
        if(!$role_system){
            return;
        }
        if(!$node->role_has_permission($role_system, OutputFilter::ROLE_HAS_PERMISSION)){
            return;
        }
        $response = $node->list(
            OutputFilter::OBJECT,
            $role_system,
            [
                'sort' => [
                    'route' => 'ASC',
                    'options.priority' => 'ASC'
                ],
                'limit' => '*',
                'ramdisk' => true
            ]
        );
        if(
            $response &&
            array_key_exists('list', $response)
        ){
            OutputFilter::on($object, $response['list']);
        }
    }
}