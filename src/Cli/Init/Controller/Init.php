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
namespace Raxon\Cli\Init\Controller;

use Raxon\App;

use Raxon\Module\Controller;
use Raxon\Module\Event;

use Exception;

use Raxon\Exception\ObjectException;

class Init extends Controller {
    const DIR = __DIR__;
    const NAME = 'Init';
    const INFO = '{{binary()}} init                           | Init events with flags / options';

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function run(App $object): void
    {
        // app init --restart-system
        Event::trigger($object, 'cli.init.run', [
            'flags' => App::flags($object),
            'options' => App::options($object)
        ]);
    }
}