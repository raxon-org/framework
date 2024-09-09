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
namespace Raxon\Cli\Info\Controller;

use Raxon\App;
use Raxon\Exception\ObjectException;
use Raxon\Module\Controller;
use Raxon\Module\Event;

use Exception;

use Raxon\Exception\LocateException;
use Raxon\Exception\UrlEmptyException;
use Raxon\Exception\UrlNotExistException;


class Info extends Controller {
    const DIR = __DIR__;
    const NAME = 'Info';
    const INFO = '{{binary()}} info                           | Info shortlist';
    const INFO_RUN = [
        '{{binary()}} info                           | Info shortlist',
        '{{binary()}} info all                       | This info'
    ];

    /**
     * @throws ObjectException
     */
    public static function run(App $object){
        $command = $object::parameter($object, Info::NAME, 1);
        $url = false;
        try {
            if(empty($command)){
                $url = Info::locate($object, Info::NAME);
            } else {
                $url = Info::locate($object, Info::NAME . '.' . $command);
                if (empty($url)) {
                    $url = Info::locate($object, Info::NAME);
                }
            }
            $result = Info::response($object, $url);
            if($command){
                Event::trigger($object, 'cli.info.' . $command, [
                    'command' => $command,
                    'url' => $url
                ]);
            } else {
                Event::trigger($object, 'cli.info', [
                    'command' => false,
                    'url' => $url,

                ]);
            }
            return $result;
        } catch(Exception | LocateException | UrlEmptyException | UrlNotExistException $exception){
            Event::trigger($object, 'cli.info', [
                'command' => $command,
                'url' => $url,
                'exception' => $exception
            ]);
            return $exception;
        }
    }
}