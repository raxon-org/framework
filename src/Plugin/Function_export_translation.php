<?php
/**
 * @author          Remco van der Velde
 * @since           2021-03-05
 */
use Raxon\App;
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Core;
use Raxon\Module\Dir;
use Raxon\Module\File;

function function_export_translation(Parse $parse, Data $data, $type='object'){
    $object = $parse->object();
    $url = $object->config('controller.dir.data') . $object->config('dictionary.translation') . $object->config('ds');
    $dir = new Dir();
    $read = $dir->read($url);
    if($object->config('project.log.name')){
        $object->logger($object->config('project.log.name'))->info('export translation directory: ' . $url);
    }
    $export = new Data();
    if($read){
        foreach($read as $nr => $file){
            $file->basename = File::basename($file->name, $object->config('extension.json'));
            $export->data(
                'translation.' . strtolower($file->basename),
                $object->data('translation.' . strtolower($file->basename))
            );
        }
    }
    return Core::object($export->data(), $type);
}
