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
namespace Raxon\Cli\Bin\Controller;

use Exception;
use Raxon\App;
use Raxon\Config;
use Raxon\Exception\ObjectException;
use Raxon\Module\Controller;
use Raxon\Module\Dir;
use Raxon\Module\File;

class Bin extends Controller {

    const DIR = __DIR__;
    const NAME = 'Bin';

    const DEFAULT_NAME = 'app';
    const TARGET = '/usr/bin/';
    const EXE = 'Raxon.php';
    const BINARY = 'Binary';

    const INFO = '{{binary()}} bin                            | Creates binary';

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function run(App $object){
        $name = $object->parameter($object, Bin::NAME, 1);
        if(empty($name)){
            $name = Bin::DEFAULT_NAME;
        }
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
        $execute = $object->config(Config::DATA_PROJECT_DIR_BINARY) . Bin::EXE;
        Dir::create($object->config(Config::DATA_PROJECT_DIR_BINARY), Dir::CHMOD);
        $dir = Dir::name(Bin::DIR) .
            $object->config(
                Config::DICTIONARY .
                '.' .
                Config::DATA
            ) .
            $object->config('ds');
        $source = $dir . Bin::EXE;
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
}