<?php
namespace Plugin;

use Exception;
use Raxon\App;
use Raxon\Cli\Cache\Controller\Cache;
use Raxon\Config;
use Raxon\Module\Core;
use Raxon\Module\Data;
use Raxon\Module\Dir;
use Raxon\Module\File;
use Raxon\Parse\Module\Parse;

trait Cache_Clear
{

    /**
     * @throws Exception
     */
    protected function cache_clear($fallback = null): void
    {
        $object = $this->object();
        $dir_temp = $object->config('framework.dir.temp');
        $dir = new Dir();
        $read = $dir->read($dir_temp, true);
        $data  = new Data();
        $options = (object) [];
        $options->source = 'Internal_' . str_replace('-', '_', Core::uuid());
        $parse = new Parse($object, $data, App::flags($object), $options);
        if(
            $object->config('ramdisk.size') &&
            empty($object->config(Config::POSIX_ID))
        ){
            $command = Cache::RAMDISK_CLEAR_COMMAND;
            $execute = $parse->compile($command, $data);
            echo 'Executing: ' . $execute . "...\n";
            Core::execute($object, $execute, $output);
            echo $output . PHP_EOL;
//            ob_flush();
        }
        if($read){
            $id = $object->config(Config::POSIX_ID);
            foreach($read as $file){
                if($file->type === Dir::TYPE){
                    $file->number = false;
                    if(is_numeric($file->name)){
                        $file->number = $file->name + 0;
                    }
                    if(
                        $file->number !== false &&
                        file_exists($file->url) &&
                        empty($id)
                    ){
                        Dir::remove($file->url);
                        echo 'Removed: ' . $file->url . PHP_EOL;
                    }
                    elseif(
                        $file->number !== false &&
                        file_exists($file->url) &&
                        $id === $file->number
                    ){
                        Dir::remove($file->url);
                        echo 'Removed: ' . $file->url . PHP_EOL;
                    }
                }
            }
        }
        if(File::exist($object->config('project.dir.vendor') . 'Doctrine')){
            $cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
            $cacheDriver->deleteAll();
        }
        opcache_reset();
        opcache_invalidate('/Application/vendor/raxon/framework/src/Module/Parse.php', true);
        Dir::create($dir_temp, Dir::CHMOD);
        $dir_www = $dir_temp . '33/';
        Dir::create($dir_www, Dir::CHMOD);
        File::permission($object, [
            'dir' => $dir_temp,
            'dir_www' => $dir_www
        ]);
    }
}