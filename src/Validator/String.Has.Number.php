<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-18
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\App;

use Raxon\Module\Parse\Token;

/**
 * @throws Exception
 */
function validate_string_has_number(App $object, object|null $record=null, mixed $string='', mixed $field='', mixed $argument='', mixed $function=false): bool
{
    $split = str_split($string);
    $test = [];
    $nr = 0;
    foreach($split as $char){
        if(
            in_array(
                $char,
                [
                    '0' ,
                    '1',
                    '2',
                    '3',
                    '4',
                    '5',
                    '6',
                    '7',
                    '8',
                    '9'
                ],
                 true
            )
        ){
            if(array_key_exists($nr, $test) === false){
                $test[$nr] = $char;
            } else {
                $test[$nr] .= $char;
            }
        } else {
            $nr++;
        }
    }
    $length = count($test);
    $argument = Token::tree('{if($argument ' . $argument . ')}{/if}');
    $left = null;
    $equation = null;
    $right = null;
    foreach($argument[1]['method']['attribute'][0] as $nr => $record_argument){
        if(empty($left)){
            $left = $record_argument;
        }
        elseif(empty($equation)){
            $equation = $record_argument['value'];
        }
        elseif(empty($right)){
            $right = $record_argument['execute'];
            break;
        }
    }
    $result = false;
    switch($equation){
        case '>' :
            $result = $length > $right;
        break;
        case '<' :
            $result = $length < $right;
        break;
        case '>=' :
            $result = $length >= $right;
        break;
        case '<=' :
            $result = $length <= $right;
        break;                
        case '==' :
            $result = $length == $right;
        break;
        case '!=' :
            $result = $length != $right;
        break;
        case '===' :
            $result = $length === $right;
            break;
        case '!==' :
            $result = $length !== $right;
            break;
        default:
            throw new Exception('Unknown equation');
    }
    return $result;    
}
