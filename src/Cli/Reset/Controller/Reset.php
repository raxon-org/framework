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
namespace Raxon\Cli\Reset\Controller;

use Raxon\App;
use Raxon\Exception\ObjectException;
use Raxon\Module\Controller;
use Raxon\Module\Event;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;

class Reset extends Controller {
    const DIR = __DIR__;
    const NAME = 'Reset';

    const DEFAULT_NAME = 'app';
    
    const INFO = '{{binary()}} reset                            | After re-installation run this command...';

    /**
     * @throws ObjectException
     */
    public static function run(App $object){        
        try {
            $name = Reset::name(Reset::NAME);
            $url = Reset::locate($object, $name);
            $result = Reset::response($object, $url);
            Event::trigger($object, 'cli.' . strtolower(Reset::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url
            ]);
            return $result;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.' . strtolower(Reset::NAME) . '.' . __FUNCTION__, [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }
}