<?php
namespace Plugin;

use Exception;

use Raxon\Module\Core;
use Raxon\Module\Data;

trait Info_Output
{

    /**
     * @throws Exception
     */
    protected function info_output(array $list =[]): void
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
        foreach($command as $nr => $line){
            $counter = $nr + 1;
            echo '[' . $counter . ']' . $this->info_parse_string($line . PHP_EOL);
        }
        foreach($description as $nr => $line){
            $counter = $nr + 1;
            echo '[' . $counter . ']' . $this->info_parse_string($line . PHP_EOL);
        }
    }

    /**
     * @throws Exception
     */
    protected function info_parse_string(mixed $mixed, array|object $data = null): mixed
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
        if(!empty($parseData)){
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