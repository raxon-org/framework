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

use Raxon\Exception\LocateException;
use Raxon\Exception\ObjectException;
use Raxon\Exception\FileWriteException;

class Event extends Main {

    const NAME = 'Event';
    const OBJECT = 'System.Event';

    const LIST = 'list';
    const RECORD = 'record';

    const ROLE_HAS_PERMISSION = 'System:Event:list';

    public function __construct(App $object){
        $this->object($object);
    }

    /**
     * @throws Exception
     */
    public static function on(App $object, $data, $options=[]): void
    {
        $list = $object->get(App::EVENT)->get(Event::OBJECT);
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
        $object->get(App::EVENT)->set(Event::OBJECT, $list);
    }

    public static function off(App $object, $action, $options=[]): void
    {
        //reinplement this
        /*
        $list = $object->get(App::EVENT)->get('event');
        if(empty($list)){
            return;
        }
        //remove them on the sorted list backwards so sorted on input order
        krsort($list);
        foreach($list as $key => $event){
            if(empty($options)){
                if($event['action'] === $action){
                    unset($list[$key]);
                    break;
                }
            } else {
                if($event['action'] === $action){
                    foreach($options as $options_key => $value){
                        if(
                            $value === true &&
                            is_array($event['options']) &&
                            array_key_exists($options_key, $event['options'])
                        ){
                            unset($list[$key]);
                            break;
                        }
                        if(
                            $value === true &&
                            is_object($event['options']) &&
                            property_exists($event['options'], $options_key)
                        ){
                            unset($list[$key]);
                            break;
                        }
                        elseif(
                            is_array($event['options']) &&
                            array_key_exists($options_key, $event['options']) &&
                            $event['options'][$options_key] === $value
                        ){
                            unset($list[$key]);
                            break;
                        }
                        elseif(
                            is_object($event['options']) &&
                            property_exists($event['options'], $options_key) &&
                            $event['options']->{$options_key} === $value
                        ){
                            unset($list[$key]);
                            break;
                        }
                    }
                }
            }
        }
        $object->get(App::EVENT)->set('event', $list);
        */
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function trigger(App $object, $action, $options=[]): void
    {
        $events = $object->get(App::EVENT)->select(Event::OBJECT, [
            'action' => $action
        ]);
        if(empty($events)){
            return;
        }
        if(is_array($events)){
            foreach($events as $event){
                if(is_object($event)) {
                    if(
                        property_exists($event, 'options') &&
                        property_exists($event->options, 'command') &&
                        is_array($event->options->command)
                    ){
                        foreach($event->options->command as $command){
                            $command = str_replace('{{binary()}}', Core::binary($object), $command);
                            Core::execute($object, $command, $output, $notification);
                        }
                    }
                    if(
                        property_exists($event, 'options') &&
                        property_exists($event->options, 'controller') &&
                        is_array($event->options->controller)
                    ){
                        foreach($event->options->controller as $controller){
                            $route = new stdClass();
                            $route->controller = $controller;
                            $route = Route::controller($route);
                            if(
                                property_exists($route, 'controller') &&
                                property_exists($route, 'function')
                            ){
                                $route_event = new Storage($event);
                                try {
                                    $response = $route->controller::{$route->function}($object, $route_event, $options);
                                }
                                catch (LocateException $exception){
                                    if($object->config('project.log.error')){
                                        $object->logger($object->config('project.log.error'))->error('LocateException', [ $route, (string) $exception ]);
                                    }
                                    elseif($object->config('project.log.app')){
                                        $object->logger($object->config('project.log.app'))->error('LocateException', [ $route, (string) $exception ]);
                                    }
                                }
                                catch(Exception $exception){
                                    if($object->config('project.log.error')){
                                        $object->logger($object->config('project.log.error'))->error('Exception', [ $route, (string) $exception ]);
                                    }
                                    elseif($object->config('project.log.app')){
                                        $object->logger($object->config('project.log.app'))->error('Exception', [ $route, (string) $exception ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
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
        if(!$node->role_has_permission($role_system, Event::ROLE_HAS_PERMISSION)){
            return;
        }
        $response = $node->list(
            Event::OBJECT,
            $role_system,
            [
                'sort' => [
                    'action' => 'ASC',
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
            Event::on($object, $response['list']);
        }
    }
}
