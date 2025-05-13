<?php
namespace Plugin;

use Exception;

use Raxon\Module\Cli;

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
        d($command);
        ddd($description);
        return $result;
    }
}