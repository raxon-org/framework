<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-13
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Cli;

use Raxon\Exception\ObjectException;

/**
 * @throws ObjectException
 */
function function_terminal_readline(Parse $parse, Data $data, $text='', $type=null){
    if(
        $text === Cli::STREAM &&
        $type === null
    ){
        return Cli::read($text);
    }
    if($type === null){
        $type = Cli::INPUT;
    }
    return Cli::read($type, $text);
}
