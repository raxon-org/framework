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
        $data = $object->data_read($object->config('controller.dir.data') . 'Reset.json' );

        if(property_exists($options, 'application') && !empty($options->application)){            
            //frankenphp  init ?    
            $commands = $data->get('application.command');            
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
        if(property_exists($options, 'ollama') && !empty($options->ollama)){                        
            $commands = $data->get('ollama.command');            
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