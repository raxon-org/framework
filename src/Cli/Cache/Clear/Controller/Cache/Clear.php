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
namespace Raxon\Cli\Cache\Clear\Controller\Cache;

use Raxon\App;
use Raxon\Exception\ObjectException;
use Raxon\Module\Controller;
use Raxon\Module\Event;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;


class Clear extends Controller {
    const NAME = 'Cache';
    const DIR = __DIR__;

    const COMMAND_INFO = 'info';
    const COMMAND_CLEAR = 'clear';
    const COMMAND = [
        Clear::COMMAND_INFO,
        Clear::COMMAND_CLEAR
    ];

    const DEFAULT_COMMAND = Clear::COMMAND_INFO;

    const EXCEPTION_COMMAND_PARAMETER = '{{$command}}';
    const EXCEPTION_COMMAND = 'invalid command (' . Clear::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL;

    const CLEAR_COMMAND = [
        '{{binary()}} autoload restart',
        '{{binary()}} parse restart'
    ];

    const INFO = '{{binary()}} cache:clear                    | Clears the app cache';

    /**
     * @throws Exception
     */
    public static function run(App $object){
        $command = Clear::COMMAND_CLEAR;
        if(!in_array($command, Clear::COMMAND, true)){
            $exception = str_replace(
                Clear::EXCEPTION_COMMAND_PARAMETER,
                $command,
                Clear::EXCEPTION_COMMAND
            );
            $exception = new Exception($exception);
            Event::trigger($object, 'cli.' . mb_strtolower(Clear::NAME) . '.' . __FUNCTION__, [
                'command' => $command,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $response = Clear::{$command}($object);
        Event::trigger($object, 'cli.' . mb_strtolower(Clear::NAME) . '.' . __FUNCTION__, [
            'command' => $command
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
            $name = Clear::name(__FUNCTION__, Clear::NAME);
            $url = Clear::locate($object, $name);
            $response = Clear::response($object, $url);
            Event::trigger($object, 'cli.' . mb_strtolower(Clear::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . mb_strtolower(Clear::NAME) . '.' . __FUNCTION__, [
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
    private static function clear(App $object){
        $name = false;
        $url = false;
        try {
            $object->config('ramdisk.is.disabled', true);
            $name = Clear::name(__FUNCTION__, Clear::NAME);
            $url = Clear::locate($object, $name);
            $response = Clear::response($object, $url);
            Event::trigger($object, 'cli.' . mb_strtolower(Clear::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . mb_strtolower(Clear::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }
}
