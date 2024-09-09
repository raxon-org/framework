<?php
/**
 * @author          Remco van der Velde
 * @since           10-02-2021
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */
namespace Raxon\Org\Exception;

use Raxon\Org\App;
use Raxon\Org\Module\File;
use Raxon\Org\Module\Parse;

use Throwable;

use Exception;

class RouteExistException extends Exception {

    const MESSAGE = 'Route resource already exists...';

}
