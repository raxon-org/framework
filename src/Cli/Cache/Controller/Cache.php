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
namespace Raxon\Cli\Cache\Controller;

use Raxon\App;
use Raxon\Config;
use Raxon\Module\Controller;
use Raxon\Module\Core;
use Raxon\Module\Dir;
use Raxon\Module\Event;
use Raxon\Module\File;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;
use Raxon\Exception\ObjectException;

class Cache extends Controller {
    const NAME = 'Cache';
    const DIR = __DIR__;

    const COMMAND_INFO = 'info';
    const COMMAND_CLEAR = 'clear';
    const COMMAND_STATUS = 'status';
    const COMMAND_GARBAGE = 'garbage';
    const COMMAND_COLLECTOR = 'collector';
    const COMMAND = [
        Cache::COMMAND_INFO,
        Cache::COMMAND_CLEAR,
        Cache::COMMAND_STATUS,
        Cache::COMMAND_GARBAGE
    ];

    const DEFAULT_COMMAND = Cache::COMMAND_INFO;

    const EXCEPTION_COMMAND_PARAMETER = '{{$command}}';
    const EXCEPTION_COMMAND = 'invalid command (' . Cache::EXCEPTION_COMMAND_PARAMETER . ')' . PHP_EOL;

    const CLEAR_COMMAND = [
        '{{binary()}} autoload restart',
        '{{binary()}} parse restart'
    ];

    const RAMDISK_CLEAR_COMMAND = '{{binary()}} ramdisk clear';

    const INFO = [
        '{{binary()}} cache clear                    | Clears the app cache',
        '{{binary()}} cache garbage collector        | Cronjob for cleaning up the cache'
    ];

    /**
     * @throws Exception
     */
    public static function run(App $object){
        $command = $object->parameter($object, Cache::NAME, 1);
        if($command === null){
            $command = Cache::DEFAULT_COMMAND;
        }
        if(!in_array($command, Cache::COMMAND, true)){
            $exception = str_replace(
                Cache::EXCEPTION_COMMAND_PARAMETER,
                $command,
                Cache::EXCEPTION_COMMAND
            );
            $exception = new Exception($exception);
            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
                'command' => $command,
                'exception' => $exception
            ]);
            throw $exception;
        }
        $response = Cache::{$command}($object);
        Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
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
            $name = Cache::name(__FUNCTION__, Cache::NAME);
            $url = Cache::locate($object, $name);
            $response = Cache::response($object, $url);
            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
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
            $name = Cache::name(__FUNCTION__, Cache::NAME);
            $url = Cache::locate($object, $name);
            $response = Cache::response($object, $url);
            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
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
    private static function status(App $object){
        $name = false;
        $url = false;
        try {
            $object->config('ramdisk.is.disabled', true);
            $name = Cache::name(__FUNCTION__, Cache::NAME);
            $url = Cache::locate($object, $name);
            $response = Cache::response($object, $url);
            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    private static function garbage(App $object): void
    {
        $start = microtime(true);
        $command = $object->parameter($object, __FUNCTION__, 1);
        $options = App::options($object);
        $flags = App::flags($object);
        switch($command){
            case Cache::COMMAND_COLLECTOR :
                if($object->config('ramdisk.url')){
                    $dir = new Dir();
                    $dir_user_id = $dir->read($object->config('ramdisk.url'));
                    if(is_array($dir_user_id)){
                        foreach($dir_user_id as $file){
                            $dir_cache = $file->url .
                                'Cache' .
                                $object->config('ds')
                            ;
                            $read_dir = $dir->read($dir_cache);
                            $size_freed = 0;
                            $counter = 0;
                            $seconds = false;
                            if(is_array($read_dir)){
                                foreach($read_dir as $directory){
                                    if($directory->type === Dir::TYPE){
                                        if(is_numeric($directory->name)){
                                            $seconds = $directory->name + 0;
                                            $url_cache = $dir_cache . $directory->name . $object->config('extension.json');
                                            if(File::exist($url_cache)){
                                                $cache_mtime = File::mtime($url_cache);
                                                if($cache_mtime > (time() - (24 * 60 * 60))){ //cache files younger than 24 hours
                                                    $read_cache = $object->data_read($url_cache);
                                                    $read = $dir->read($directory->url);
                                                    $result = [];
                                                    if(
                                                        $read_cache &&
                                                        is_array($read)
                                                    ){
                                                        foreach($read_cache->data() as $value){
                                                            $result[] = $value;
                                                            foreach($read as $nr => $file){
                                                                if($file->url === $value->url){
                                                                    unset($read[$nr]);
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        foreach($read as $file){
                                                            $result[] = $file;
                                                        }
                                                    }
                                                    $read = $result;
                                                } else {
                                                    $read = $dir->read($directory->url);
                                                }
                                            } else {
                                                $read = $dir->read($directory->url);
                                            }
                                            foreach($read as $nr => $file){
                                                if($file->type === File::TYPE){
                                                    if(!property_exists($file, 'mtime')){
                                                        $file->mtime = File::mtime($file->url);
                                                    }
                                                    if($file->mtime < (time() - $seconds)){
                                                        $size_freed += File::size($file->url);
                                                        File::delete($file->url);
                                                        unset($read[$nr]);
                                                        $counter++;
                                                    }
                                                }
                                            }
                                            File::write($url_cache, Core::object($read, Core::OBJECT_JSON));
                                            $command = 'chmod 640 ' . $url_cache;
                                            exec($command);
                                            $command = 'chown www-data:www-data ' . $url_cache;
                                            exec($command);
                                        }
                                    }
                                }
                            }
                            $duration = microtime(true) - $start;
                            if($seconds){
                                echo 'Garbage Collector: amount freed: ' . $counter . ' size: ' . $size_freed . ' bytes seconds: ' . $seconds . PHP_EOL;
                                if($object->config('project.log.name')){
                                    $object->logger($object->config('project.log.name'))->info('Garbage Collector: amount freed: ' . $counter . ' size: ' . $size_freed . ' bytes' . PHP_EOL, [ $dir_cache, $duration * 1000 . ' ms', $seconds ]);
                                }
                            } else {
                                echo 'Garbage Collector: amount freed: ' . $counter . ' size: ' . $size_freed . ' bytes' . PHP_EOL;
                                if($object->config('project.log.name')){
                                    $object->logger($object->config('project.log.name'))->info('Garbage Collector: amount freed: ' . $counter . ' size: ' . $size_freed . ' bytes' . PHP_EOL, [ $dir_cache, $duration * 1000 . ' ms' ]);
                                }
                            }
                            Event::trigger($object, 'cli.' . strtolower(Cache::NAME) . '.' . __FUNCTION__, [
                                'command' => $command,
                                'url' => $dir_cache,
                                'options' => $options,
                                'flags' => $flags,
                                'amount' => $counter,
                                'size' => $size_freed
                            ]);
                        }
                    }
                }
            break;
        }
    }
}
