<?php

use Raxon\App;

use Raxon\Module\Event;
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\File;

use Raxon\Exception\ObjectException;

/**
 * @throws ObjectException
 * @throws Exception
 */
function function_ln(Parse $parse, Data $data): void
{
    $object = $parse->object();
    $options = App::options($object);
    if(property_exists($options, 'source')){
        $source = $options->source;
    } else {
        throw new Exception('Option "source" not found...');
    }
    if(property_exists($options, 'target')){
        $target = $options->target;
    }
    else {
        throw new Exception('Option "target" not found...');
    }
    if(File::exist($target)){
        $exception = new Exception('File exists...');
        Event::trigger($object, 'cli.ln', [
            'source' => $source,
            'target' => $target,
            'exception' => $exception
        ]);
        throw $exception;
    }
    exec('ln -s ' . escapeshellarg($source) . ' ' . escapeshellarg($target));
    Event::trigger($object, 'cli.ln', [
        'source' => $source,
        'target' => $target
    ]);
}
