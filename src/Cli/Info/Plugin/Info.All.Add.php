<?php
namespace Plugin;

use Exception;

use Raxon\Module\Cli;

trait Info_All_Add
{

    /**
     * @throws Exception
     */
    protected function info_all_add(array $list =[]): array
    {
        $object = $this->object();
        $result = [];
        foreach($list as $nr => $record){
            if(
                property_exists($record, 'controller') &&
                property_exists($record, 'function')
            ){
                try {
                    $class = $record->controller;
                    $constant =  $class . '::INFO_' . strtoupper($record->function);
                    $info = false;
                    if(defined($constant)) {
                        $info = constant($constant);
                    }
                    elseif(defined($class . '::INFO')){
                        $info = constant($class . '::INFO');
                    }
                    $record->info = $info;
                    $result[] = $record;
                } catch (Exception $exception){
                    Cli::tput('init');
                    echo Cli::tput('background', CLI::COLOR_RED);
                    echo PHP_EOL;
                    echo PHP_EOL;
                    echo $exception->getMessage() . PHP_EOL;
                    echo Cli::tput('reset');
                    echo PHP_EOL;
                    continue;
                }
            }
        }
        return $result;
    }
}