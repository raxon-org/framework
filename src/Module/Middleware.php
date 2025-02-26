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

use Raxon\Node\Model\Node;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\ObjectException;
use Raxon\Exception\FileWriteException;

class Middleware extends Main {

    const NAME = 'Middleware';
    const OBJECT = 'System.Middleware';
    const ROLE_HAS_PERMISSION = 'System:Middleware:list';

    public function __construct(App $object){
        $this->object($object);
    }

    /**
     * @throws Exception
     */
    public static function on(App $object, $data, $options=[]): void
    {
        $list = $object->get(App::MIDDLEWARE)->get(Middleware::OBJECT);
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
        $object->get(App::MIDDLEWARE)->set(Middleware::OBJECT, $list);
    }

    public static function off(App $object, $record, $options=[]): void
    {
        //need rewrite
//        $action = $record->get('action');
//        $options = $record->get('options');
        /*
        $list = $object->get(App::MIDDLEWARE)->get(Middleware::NAME);
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
        */
//        $object->get(App::MIDDLEWARE)->set(Middleware::NAME, $list);
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function trigger(App $object, Destination $destination=null, $options=[]): Destination
    {
        $middlewares = $object->get(App::MIDDLEWARE)->data(Middleware::OBJECT);
        $target = null;
        if(empty($middlewares)){
            return $destination;
        }
        if(is_array($middlewares) || is_object($middlewares)){
            foreach($middlewares as $middleware){
                if(is_object($middleware)) {
                    if(
                        property_exists($middleware, 'options') &&
                        property_exists($middleware->options, 'controller') &&
                        is_array($middleware->options->controller)
                    ){
                        //middleware need route match
                        if(
                            (
                                !empty($destination->get('uuid')) &&
                                property_exists($middleware, 'route') &&
                                $destination->get('uuid') === $middleware->route
                            ) ||
                            (
                                property_exists($middleware, 'route') &&
                                $middleware->route === '*'
                            )
                        ){
                            foreach($middleware->options->controller as $controller){
                                $route = new stdClass();
                                $route->controller = $controller;
                                $route = Route::controller($route);
                                if(
                                    property_exists($route, 'controller') &&
                                    property_exists($route, 'function')
                                ){
                                    $middleware = new Storage($middleware);
                                    try {
                                        $target = $route->controller::{$route->function}($object, $destination, $middleware, $options);
                                        if($middleware->get('stopPropagation')){
                                            break 2;
                                        }
                                    }
                                    catch (LocateException $exception){
                                        if($object->config('project.log.error')){
                                            $object->logger($object->config('project.log.error'))->error('LocateException', [ $destination->data(), (string) $exception ]);
                                        }
                                        elseif($object->config('project.log.app')){
                                            $object->logger($object->config('project.log.app'))->error('LocateException', [ $destination->data(), (string) $exception ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if($target){
            return $target;
        }
        return $destination;
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
        if(!$node->role_has_permission($role_system, Middleware::ROLE_HAS_PERMISSION)){
            return;
        }
        $response = $node->list(
            Middleware::OBJECT,
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
            Middleware::on($object, $response['list']);
        }
    }
}