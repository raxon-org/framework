<?php
namespace Plugin;

use Exception;

use Raxon\Config;
use Raxon\Module\Core;
use Raxon\Module\Dir;
use Raxon\Module\File;

use Raxon\Cli\Bin\Controller\Bin;

trait Reset
{

    /**
     * @throws Exception
     */
    protected function reset(object $flags, object $options): void
    {        
        if(property_exists($options, 'app')){
            $options->application = $options->app;
            unset($options->app);
        }
        $object = $this->object();
        $commands = $object->data_read($object->config('controller.dir.data') . 'Reset.json' );
ddd($commands);

        if(property_exists($options, 'application') && !empty($options->application)){
            $name = $options->application;
            //frankenphp  init ?
            



            $commands = [
                'app raxon/basic apache2 setup',
                'app raxon/basic apache2 restore',
                'app raxon/basic apache2 restart',
                'app raxon/basic cron restore',
                'app raxon/basic cron restart',
                'app raxon/basic php restore',
                'app raxon/basic php restart',
                'app cache clear'
            ];
            
            foreach($commands as $command){
                Core::execute($object, $command, $output, $notification);
                if($output){
                    echo $output;
                }
                if($notification){
                    echo $notification;
                }
            }
        }
    }
}