<?php

use Raxon\App;

use Raxon\Module\Cli;
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Core;

function function_cache_status(Parse $parse, Data $data){
    $object = $parse->object();
    // Get OPcache status
    $status = opcache_get_status();

    // Get OPcache configuration
    $config = opcache_get_configuration();

    if(App::is_cli()){
        echo Cli::info('OPcache Status') . PHP_EOL;
        echo Core::object($status, Core::OBJECT_JSON) . PHP_EOL;
        echo Cli::info('OPcache Configuration') . PHP_EOL;
        echo Core::object($config, Core::OBJECT_JSON) . PHP_EOL;
    } else {
        // Output OPcache status
        echo "<h2>OPcache Status</h2>";
        echo "<pre>" . print_r($status, true) . "</pre>";

        // Output OPcache configuration
        echo "<h2>OPcache Configuration</h2>";
        echo "<pre>" . print_r($config, true) . "</pre>";
    }

}
