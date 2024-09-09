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
namespace Raxon\Org\Cli\Password\Controller;

use Raxon\Org\App;
use Raxon\Org\Exception\ObjectException;
use Raxon\Org\Module\Controller;

use Exception;

use Raxon\Org\Exception\LocateException;
use Raxon\Org\Exception\UrlEmptyException;
use Raxon\Org\Exception\UrlNotExistException;
use Raxon\Org\Module\Event;

class Password extends Controller {
    const DIR = __DIR__;
    const NAME = 'Password';
    const INFO = '{{binary()}} password                       | Password hash generation';

    /**
     * @throws ObjectException
     */
    public static function run(App $object){
        $name = false;
        $url = false;
        try {
            $name = Password::name('hash', Password::NAME);
            $url = Password::locate($object, $name);
            $response = Password::response($object, $url);
            Event::trigger($object, 'cli.password.hash', [
                'name' => $name,
                'url' => $url
            ]);
            return $response;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.password.hash', [
                'name' => $name,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }
}