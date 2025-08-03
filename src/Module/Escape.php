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

use Raxon\App;
use Raxon\Config;

use Exception;

use Raxon\Exception\DirectoryCreateException;
use Raxon\Exception\FileWriteException;
use Raxon\Exception\ObjectException;

class Escape {
    const NAME = 'Escape';
    
    public static function single_quote(mixed $input) {
        if (is_string($input)) {
            $input = str_replace([
                    '\\',
                    '\''               
                ],[
                    '\\\\',
                    '\\\''                    

                ], $input);                          
        } elseif (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = Escape::single_quote($value);
            }
        } elseif (is_object($input)) {
            foreach ($input as $key => $value) {
                $input->$key = Escape::single_quote($value);
            }
        }
        return $input;
    }

    public static function double_quote(mixed $input) {
        if (is_string($input)) {
            $input = str_replace(
                [
                    '\\',
                    '"',
                    '$'                               
                ],
                [
                    '\\\\',
                    '\"',
                    '\$'                                    
                ],
                $input
            );    
            $input = str_replace(
                [
                    '\\\\/',
                    '\\\\n',
                    '\\\\t',
                    '\\\\r',                              
                    '\\\\v', 
                    '\\\\0',
                ],
                [
                    '\/',
                    '\n',
                    '\t',
                    '\r',
                    '\v',
                    '\0'
                ],
                $input
            );                             
        } elseif (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = Escape::single_quote($value);
            }
        } elseif (is_object($input)) {
            foreach ($input as $key => $value) {
                $input->$key = Escape::single_quote($value);
            }
        }
        return $input;
    }
}