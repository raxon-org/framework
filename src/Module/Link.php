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
namespace Raxon\Module;

use Package\Raxon\Raxon\Service\ForkMultiple;
use stdClass;

use Raxon\App;
use Raxon\Config;

use Raxon\Module\Parse\Build;
use Raxon\Module\Parse\Literal;
use Raxon\Module\Parse\Value;
use Raxon\Module\Parse\Token;

use Exception;
use ParseError;

use Raxon\Exception\ObjectException;
use Raxon\Exception\FileWriteException;

class Link {
    const TYPE = 'Link';

    public static function is(string $url=''): bool
    {
        $url = rtrim($url, '/');
        return is_link($url);
    }
}