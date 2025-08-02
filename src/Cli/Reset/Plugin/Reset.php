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
        d($flags);
        d($options);
    }
}