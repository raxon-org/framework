<?php

use Raxon\Module\Dir;
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\File;
use Raxon\App;

function function_zip_archive(Parse $parse, Data $data){
    $object = $parse->object();
    $source = App::parameter($object, 'archive', 1);
    $target = App::parameter($object, 'archive', 2);
    $limit = $parse->limit();
    $parse->limit([
        'function' => [
            'date'
        ]
    ]);
    try {
        $target = $parse->compile($target, [], $data);
        $parse->setLimit($limit);
    } catch (Exception $exception) {
        echo $exception->getMessage() . PHP_EOL;
        return;
    }
    if(
        Dir::is($source) &&
        !File::exist($target)
    ){
        $dir = new Dir();
        $read = $dir->read($source, true);
        $host = [];
        if(!is_array($read)){
            return null;
        }
        foreach($read as $file){
            $host[] = $file;
        }
        $dir = Dir::name($target);
        if(
            $dir &&
            !in_array(
                $dir,
                [
                    $object->config('ds')
                ],
                true
            )
        ){
            Dir::create($dir);
        }
        $zip = new \ZipArchive();
        $zip->open($target, \ZipArchive::CREATE);
        foreach($host as $file){
            $location = false;
            if(substr($file->url, 0, 1) === $object->config('ds')){
                $location = substr($file->url, 1);
            } else {
                $location = $file->url;
            }
            if(!empty($location)){
                if($file->type === Dir::TYPE){
                    $zip->addEmptyDir($location);
                } else {
                    $zip->addFile($source, $location);
                }
            }
            d($location);
        }
        $zip->close();
        echo $target;
    }
    elseif(
        File::is($source) &&
        !File::exist($target)
    ) {
        $dir = Dir::name($target);
        if(
            $dir &&
            !in_array(
                $dir,
                [
                    $object->config('ds')
                ],
                true
            )
        ){
            Dir::create($dir);
        }
        $zip = new \ZipArchive();
        $zip->open($target, \ZipArchive::CREATE);
        $location = false;
        if(substr($source, 0, 1) === $object->config('ds')){
            $location = substr($source, 1);
        } else {
            $location = $source;
        }
        if(!empty($location)){
            $zip->addFile($source, $location);
        }
        $zip->close();
        echo $target;
    }
}
