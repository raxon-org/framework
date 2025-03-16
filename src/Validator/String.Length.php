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
function validate_string_length(App $object, array $record=[], mixed $string='', mixed $field='', mixed $argument='', mixed $function=false): bool
{
    $length = strlen($string);
    if(is_array($argument)){
        $arguments = $argument;
        foreach($arguments as $argument){
            if(
                $argument === null)
            {
                if($string === null){
                    return true;
                }
                continue;
            }
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
            if($result === false){
                return false;
            }
        }
        return true;
    }
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
