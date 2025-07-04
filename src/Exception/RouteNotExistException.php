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
namespace Raxon\Exception;

use Raxon\App;
use Raxon\Module\File;
use Raxon\Module\Parse;

use Throwable;

use Exception;

class RouteNotExistException extends Exception {

    const MESSAGE = 'Route does not exist...';

}