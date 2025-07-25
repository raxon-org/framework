<?php
namespace Plugin;

use Exception;

use Raxon\Module\Core;
use Raxon\Module\CLi;
use Raxon\Module\Data;

trait Info_Output
{

    /**
     * @throws Exception
     */
    protected function info_output(array $list =[]): array
    {
        $object = $this->object();
        $result = [];
        $output = [];
        foreach($list as $uuid => $route){
            $info = $route->info;
            if(is_array($info)){
                foreach($info as $line){
                    $output[] = $line;
                }
            } else {
                $output[] = $info;
            }
        }
        $command = [];
        $description = [];
        foreach($output as $line){
            $explode = explode('|', $line, 2);
            if(count($explode) > 1){
                $command[] = trim($explode[0]);
                $description[] = trim($explode[1]);
            } else {
                $command[] = trim($explode[0]);
                $description[] = '';
            }
        }
        $result[] = Cli::alert('Commands:') . PHP_EOL;
        $command = $this->info_parse($command);
        foreach($command as $nr => $line){
            $counter = $nr + 1;
            $result[] = '[' . $counter . '] ' . $line . PHP_EOL;
        }
        $result[] = Cli::alert('Descriptions:') . PHP_EOL;
        $description = $this->info_parse($description);
        foreach($description as $nr => $line){
            $counter = $nr + 1;
            $result[] = '[' . $counter . '] ' . $line . PHP_EOL;
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    protected function info_parse(mixed $mixed, array|object|null $data = null): mixed
    {
        $parse = $this->parse();

        if($data === null){
            $data = $this->data();
        }
        elseif(is_array($data)){
            $data = new Data($data);
        }
        elseif(is_object($data)) {
            if(get_class($data) === Data::class){
                $data = $data;
            } else {
                $data = new Data($data);
            }
        }
        $options = $parse->options();
        $old_source = $options->source ?? null;
        if(is_scalar($mixed) || is_null($mixed)){
            if(is_string($mixed)){
                $hash = 'scalar_' . hash('sha256', '{"scalar": "' . $mixed . '"}');
            } else {
                $hash = 'scalar_' . hash('sha256', '{"scalar":' . $mixed . '}');
            }
        } else {
            $hash = hash('sha256', Core::object($mixed, Core::JSON_LINE));
        }
        $options->source = 'Internal_' . $hash;
        $parse->options($options);
        if(!empty($data)){
            $object = $this->object();
            $literal = $object->data('literal');
            if(is_array($literal) || is_object($literal)){
                foreach($literal as $key => $value){
                    $data->set('literal.' . $key, $value);
                }               
            }      
            $result = $parse->compile($mixed, $data);
        } else {
            $result = $parse->compile($mixed, []);
        }
        if($old_source !== null){
            $options->source = $old_source;
        } else {
            unset($options->source);
        }
        $parse->options($options);
        return $result;
    }
}