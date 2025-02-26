<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-13
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Core;
use Raxon\Module\Data;
use Raxon\Module\File;

/**
 * @throws Exception
 */
function function_ramdisk_speedtest(Parse $parse, Data $data){
    $object = $parse->object();
    $id = posix_geteuid();
    if (!empty($id)){
        throw new Exception('RamDisk speedtest can only be run by root...');
    }
    if($object->config('ramdisk.url')){
        $url = $object->config('ramdisk.url') . 'speedtest';
        $command = 'dd if=/dev/zero of=' . $url . 'zero bs=4k count=100000';
        Core::execute($object, $command, $output, $notification);
        echo 'Write:' . PHP_EOL;
        if($output){
            echo $output . PHP_EOL;
        }
        if($notification){
            echo $notification . PHP_EOL;
        }
        $command = 'dd if=' . $url . 'zero of=/dev/null bs=4k count=100000';
        Core::execute($object, $command, $output, $notification);
        echo 'Read:' . PHP_EOL;
        if($output){
            echo $output . PHP_EOL;
        }
        if($notification){
            echo $notification . PHP_EOL;
        }
        File::delete($url . 'zero');
    }
}
