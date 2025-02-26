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

use Raxon\App;
use Raxon\Exception\ObjectException;
use Raxon\Module\Controller;
use Raxon\Module\Event;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;

class Bin extends Controller {
    const DIR = __DIR__;
    const NAME = 'Bin';

    const DEFAULT_NAME = 'app';
    const TARGET = '/usr/bin/';
    const EXE = 'Raxon.php';
    const BINARY = 'Binary';

    const INFO = '{{binary()}} bin                            | Creates binary';

    /**
     * @throws ObjectException
     */
    public static function run(App $object){
        $name = $object->parameter($object, Bin::NAME, 1);
        if(empty($name)){
            $name = Bin::DEFAULT_NAME;
        }
        $url = false;
        $object->data('name', $name);
        try {
            $name = Bin::name('create', Bin::NAME);
            $url = Bin::locate($object, $name);
            $result = Bin::response($object, $url);
            Event::trigger($object, 'cli.' . strtolower(Bin::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $result;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . strtolower(Bin::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }
}