<?php
namespace Raxon\Cli\Info\Trait;

use Raxon\App;
use Raxon\Config;

use Raxon\Doctrine\Module\Database;
use Raxon\Doctrine\Module\Entity;
use Raxon\Exception\FileWriteException;
use Raxon\Exception\DirectoryCreateException;
use Raxon\Exception\ObjectException;

use Raxon\Module\Cli;
use Raxon\Module\Controller;
use Raxon\Module\Data;
use Raxon\Module\Dir;
use Raxon\Module\Core;
use Raxon\Module\Event;
use Raxon\Module\File;
use Raxon\Module\Host;
use Raxon\Module\Sort;
use Raxon\Parse\Module\Parse;

use Raxon\Node\Module\Node;

use Exception;


trait Main {
    /**
     * @throws DirectoryCreateException
     * @throws Exception
     */
    public function reset($flags, $options): void
    {
        $object = $this->object();
        if($object->config(Config::POSIX_ID) !== 0){
            return;
        }
        $patch = $options->patch ?? false;
        if($patch === false){
            throw new Exception('Option patch is required to patch the system...');
        }
        $has_frontend = false;
        if(property_exists($options, 'frontend')){
            if(property_exists($options->frontend, 'host')){                
                $has_frontend = true;
                $frontend_options = [
                    'where' => [
                        [
                            'value' => $options->frontend->host,
                            'attribute' => 'name',
                            'operator' => 'partial',
                        ]
                    ]
                ];
            }                
        }        
        $has_backend = false;
        if(property_exists($options, 'backend')){
            if(property_exists($options->backend, 'host')){                
                $has_backend = true;
                $backend_options = [
                    'where' => [
                        [
                            'value' => $options->backend->host,
                            'attribute' => 'name',
                            'operator' => 'partial',
                        ]
                    ]
                ];                
            }
        }
        if($has_frontend === false){
            throw new Exception('Frontend.host option is required and must be defined in Node/System.Host.json aborting...');
        }
        if($has_backend === false){
            throw new Exception('Backend.host option is required and must be defined in Node/System.Host.json aborting...');
        }
        $class = 'System.Host';
        $node = new Node($object);
        $response_frontend = $node->record($class, $node->role_system(), $frontend_options);
        $response_backend = $node->record($class, $node->role_system(), $backend_options);

        d($response_frontend);
        d($response_backend);
    }


}