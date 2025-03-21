<?php
/**
 * @author          Remco van der Velde
 * @since           04-01-2019
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */
namespace Raxon\Cli\Parse\Controller;

use Raxon\App;
use Raxon\Exception\ObjectException;
use Raxon\Module\Core;
use Raxon\Module\Event;
use Raxon\Module\File;
use Raxon\Module\Controller;
use Raxon\Module\Parse as Parser;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;
use Raxon\Module\Parse\Token;

class Parse extends Controller {
    const NAME = 'Parse';
    const DIR = __DIR__;

    const COMMAND_INFO = 'info';
    const COMMAND_RESTART = 'restart';
    const COMMAND_COMPILE = 'compile';
    const COMMAND = [
        Parse::COMMAND_INFO,
        Parse::COMMAND_RESTART,
        Parse::COMMAND_COMPILE
    ];

    const DEFAULT_COMMAND = Parse::COMMAND_INFO;

    const INFO = [
        '{{binary()}} parse info                     | parse information',
        '{{binary()}} parse compile                  | compiles <source> with <data>',
        '{{binary()}} parse restart                  | removes parse compile cache files',
    ];

    const EXCEPTION_COMMAND_PARAMETER = '{{$command}}';
    const EXCEPTION_COMMAND = 'invalid command (' . Parse::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL;

    /**
     * @throws Exception
     */
    public static function run(App $object){
        $command = $object->parameter($object, Parse::NAME, 1);

        if($command === null){
            $command = Parse::DEFAULT_COMMAND;
        }
        if(!in_array($command, Parse::COMMAND, true)){
            $exception = str_replace(
                Parse::EXCEPTION_COMMAND_PARAMETER,
                $command,
                Parse::EXCEPTION_COMMAND
            );
            $exception = new Exception($exception);
            Event::trigger($object, 'cli.' . strtolower(Parse::NAME) . '.' . __FUNCTION__, [
                'command' => $command,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $response = Parse::{$command}($object);
        Event::trigger($object, 'cli.' . strtolower(Parse::NAME) . '.' . __FUNCTION__, [
            'command' => $command,
        ]);
        return $response;
    }

    /**
     * @throws ObjectException
     */
    private static function info(App $object){
        $name = false;
        $url = false;
        try {
            $name = Parse::name(__FUNCTION__, Parse::NAME);
            $url = Parse::locate($object, $name);
            $response = Parse::response($object, $url);
            Event::trigger($object,  'cli.parse.info', [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.parse.info', [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }

    /**
     * @throws ObjectException
     */
    private static function restart(App $object){
        $name = false;
        $url = false;
        try {
            $name = Parse::name(__FUNCTION__, Parse::NAME);
            $url = Parse::locate($object, $name);
            $response = Parse::response($object, $url);
            Event::trigger($object, 'cli.parse.restart', [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.parse.restart', [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }

    /**
     * @throws Exception
     */
    private static function compile(App $object)
    {
        $template_url = false;
        $data_url = false;
        $is_json = false;
        try {
            $template_url = $object->parameter($object, __FUNCTION__, 1);
            $data_url = $object->parameter($object, __FUNCTION__, 2);
            if (File::exist($template_url)) {
                $extension = File::extension($template_url);
                if($object->config('extension.json') === '.' . $extension) {
                    $read = $object->data_read($template_url);
                    if($read){
                        $read = $read->data();
                        $is_json = true;
                    }
                } else {
                    $read = File::read($template_url);
                }
                if ($read) {
                    $mtime = File::mtime($template_url);
                    $data = $object->parse_read($data_url);
                    $object->data('ldelim', '{');
                    $object->data('rdelim', '}');
                    if($data){
                        $request = $data->get('request');
                        if($request){
                            $object->request($request);
                        }
                        $session = $data->get('session');
                        if($session){
                            $object->session($session);
                        }
                        $cookie = $data->get('cookie');
                        if($cookie){
                            $object->cookie($cookie);
                        }
                        $data = Core::object_merge(clone $object->data(), $data->data());
                    }
                    $parse = new Parser($object);
                    $parse->storage()->data('raxon.org.parse.view.url', $template_url);
                    $parse->storage()->data('raxon.org.parse.view.mtime', $mtime);

                    unset($data->{App::NAMESPACE});
                    $read = $parse->compile($read, $data, $parse->storage());
                    $object->set('script', \Raxon\Module\Parse::readback($object, $parse, App::SCRIPT));
                    $object->set('link', \Raxon\Module\Parse::readback($object, $parse, App::LINK));
                    if($is_json){
                        $read = Core::object($read, Core::OBJECT_JSON);
                    }
                    Event::trigger($object, 'cli.parse.compile', [
                        'template_url' => $template_url,
                        'data_url' => $data_url,
                        'is_json' => $is_json
                    ]);
                    return $read;
                }
            }
            Event::trigger($object, 'cli.parse.compile', [
                'template_url' => $template_url,
                'data_url' => $data_url,
                'is_template' => false,
                'is_json' => $is_json
            ]);
        } catch (Exception $exception){
            Event::trigger($object,'cli.parse.compile', [
                'template_url' => $template_url,
                'data_url' => $data_url,
                'is_template' => false,
                'is_json' => $is_json,
                'exception' => $exception
            ]);
            return $exception;
        }
    }
}