<?php
/**
 * @author          Remco van der Velde
 * @since           04-01-2019
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */
namespace Raxon\Cli\Bin\Controller;

use Exception;
use Raxon\App;
use Raxon\Exception\ObjectException;
use Raxon\Module\Controller;

class Bin extends Controller {
    const DIR = __DIR__;
    const NAME = 'Bin';

    const DEFAULT_NAME = 'app';
    const TARGET = '/usr/bin/';
    const EXE = 'Raxon.php';
    const BINARY = 'Binary';

    const INFO = '{{binary()}} bin                            | Creates binary';

    use \Raxon\Parse\Plugin\Binary\Binary_Create;

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function run(App $object){
        $name = $object->parameter($object, Bin::NAME, 1);
        if(empty($name)){
            $name = Bin::DEFAULT_NAME;
        }
        (new Bin)->binary_create($name);
    }
}