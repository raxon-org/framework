<?php

use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Config;
use Raxon\Module\Dir;
use Raxon\Module\File;

function function_binary_create(Parse $parse, Data $data, $name=null){
    $id = posix_geteuid();
    if(
        !in_array(
            $id,
            [
                0
            ],
            true
        )
    ){
        throw new Exception('Only root can execute bin...');
    }
    if(empty($name)){
        $name = \Raxon\Cli\Bin\Controller\Bin::DEFAULT_NAME;
    }
    $object = $parse->object();
    $execute = $object->config(Config::DATA_PROJECT_DIR_BINARY) . \Raxon\Cli\Bin\Controller\Bin::EXE;
    Dir::create($object->config(Config::DATA_PROJECT_DIR_BINARY), Dir::CHMOD);
    $dir = Dir::name(\Raxon\Cli\Bin\Controller\Bin::DIR) .
        $object->config(
            Config::DICTIONARY .
            '.' .
            Config::DATA
        ) .
        $object->config('ds');
    $source = $dir . \Raxon\Cli\Bin\Controller\Bin::EXE;      
    if(File::exist($execute)){
        File::delete($execute);
    }    
    File::copy($source, $execute);
    $url_binary = $object->config(Config::DATA_PROJECT_DIR_BINARY) . \Raxon\Cli\Bin\Controller\Bin::BINARY;
    File::write($url_binary, $name . PHP_EOL);
    $url = \Raxon\Cli\Bin\Controller\Bin::TARGET . $name;
    $content = [];
    $content[] = '#!/bin/bash';
    # added $name as this was a bug in updating the cms
    $content[] = '_=' . $name . ' php ' . $execute . ' "$@"';
    $content = implode(PHP_EOL, $content);
    File::write($url, $content);
    shell_exec('chmod +x ' . $url);
    echo 'Binary created...' . PHP_EOL;
}
