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
            foreach($info as $line){
                $output[] = $line;
            }
        }
        ddd($output);

        return $result;
    }
}