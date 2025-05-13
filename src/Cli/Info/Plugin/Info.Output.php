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
        ddd($list);
        return $result;
    }
}