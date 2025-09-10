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
namespace Raxon\Cli\Update\Controller;

use Raxon\App;
use Raxon\Config;

use Raxon\Module\Core;
use Raxon\Module\Event;
use Raxon\Module\File;
use Raxon\Module\Data;
use Raxon\Module\Controller;
use Raxon\Module\Parse;
use Raxon\Node\Module\Node;

use Exception;

use Raxon\Exception\FileWriteException;
use Raxon\Exception\ObjectException;
use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;

class Update extends Controller {
    const NAME = 'Update';
    const DIR = __DIR__;

    const COMMAND_INFO = 'info';
    const COMMAND_EXECUTE = 'execute';
    const COMMAND = [
        Update::COMMAND_INFO,
        Update::COMMAND_EXECUTE
    ];

    const DEFAULT_COMMAND = Update::COMMAND_EXECUTE;

    const UPDATE_COMMAND = [
        '{{binary()}} update info',
    ];

    const INFO = '{{binary()}} update                         | Update all installed packages using the -path option' . PHP_EOL;                 
    const INFO_RUN = [
        '{{binary()}} update                         | Update all installed packages using the -path option'        
    ];    

    const DATA_FRAMEWORK_VERSION = 'framework.version';
    const DATA_FRAMEWORK_BUILT = 'framework.built';
    const DATA_FRAMEWORK_MAJOR = 'framework.major';
    const DATA_FRAMEWORK_MINOR = 'framework.minor';
    const DATA_FRAMEWORK_PATCH = 'framework.patch';

    const EXCEPTION_COMMAND_PARAMETER = '{$command}';
    const EXCEPTION_COMMAND = 'invalid command (' . Version::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL;

    /**
     * @throws Exception
     */
    public static function run(App $object){
        $command = $object->parameter($object, Update::NAME, 1);
        if($command === null){
            $command = Update::DEFAULT_COMMAND;
        }
        if(
            !in_array(
                $command,
                Update::COMMAND,
                true
            )
        ){
            $exception = str_replace(
                Update::EXCEPTION_COMMAND_PARAMETER,
                $command,
                Update::EXCEPTION_COMMAND
            );
            $exception = new Exception($exception);
            Event::trigger($object, 'cli.' . strtolower(Update::NAME) . '.' . __FUNCTION__, [
                'command' => $command,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $response = Update::{$command}($object);
        Event::trigger($object, 'cli.' . strtolower(Update::NAME) . '.' . __FUNCTION__, [
            'command' => $command
        ]);
        return $response;
    }

    private static function info(App $object){
        $name = false;
        $url = false;
        try {
            $name = Update::name(__FUNCTION__, Update::NAME);
            $url = Update::locate($object, $name);
            $response = Update::response($object, $url);
            Event::trigger($object, 'cli.' . strtolower(Update::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . strtolower(Update::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    private static function execute(App $object){        
        $class = 'System.Installation';                
        $node = new Node($object);
        $response = $node->list($class, $node->role_system(), [
            'limit' => 100000
        ]);
        if($response && array_key_exists('list', $response)){
            foreach($response['list'] as $item){
                if(property_exists($item, 'name')){
                    $command = Core::binary() . ' install ' . $item->name . ' -patch';
                    Core::execute($object, $command, $output, $notification);
                    if($output){
                        echo $output . PHP_EOL;
                    }
                    if($notification){
                        echo $notification . PHP_EOL;
                    }
                }
            }
        }
        $name = Update::name(__FUNCTION__, Update::NAME);
        ddd($name);
        Event::trigger($object, 'cli.' . strtolower(Update::NAME) . '.' . __FUNCTION__, [
            'name' => $name,
            'class' => $class
        ]);
    }

}