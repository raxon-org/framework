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
namespace Raxon\Cli\License\Controller;

use Raxon\App;
use Raxon\Exception\ObjectException;
use Raxon\Module\Controller;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;
use Raxon\Module\Event;

class License extends Controller {
    const NAME = 'License';
    const DIR = __DIR__;

    const COMMAND_INFO = 'info';
    const COMMAND = [
        License::COMMAND_INFO
    ];
    const DEFAULT_COMMAND = License::COMMAND_INFO;

    const EXCEPTION_COMMAND_PARAMETER = '{{$command}}';
    const EXCEPTION_COMMAND = 'invalid command (' . License::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL;

    const INFO = '{{binary()}} license                        | raxon/framework license';

    /**
     * @throws Exception
     */
    public static function run(App $object){
        $command = $object->parameter($object, License::NAME, 1);

        if($command === null){
            $command = License::DEFAULT_COMMAND;
        }
        if(!in_array($command, License::COMMAND, true)){
            $exception = str_replace(
                License::EXCEPTION_COMMAND_PARAMETER,
                $command,
                License::EXCEPTION_COMMAND
            );
            $exception = new Exception($exception);
            Event::trigger($object, 'cli.' . strtolower(License::NAME) . '.' . __FUNCTION__, [
                'command' => $command,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $response = License::{$command}($object);
        Event::trigger($object, 'cli.' . strtolower(License::NAME) . '.' . __FUNCTION__, [
            'command' => $command
        ]);
        return $response;
    }

    /**
     * @throws ObjectException
     */
    private static function info(App $object)
    {
        $name = false;
        $url = false;
        try {
            $name = License::name(__FUNCTION__, License::NAME);
            $url = License::locate($object, $name);
            $result = License::response($object, $url);
            Event::trigger($object, 'cli.' . strtolower(License::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $result;
        } catch (Exception | LocateException | UrlEmptyException | UrlNotExistException $exception) {
            Event::trigger($object, 'cli.' . strtolower(License::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }
}
