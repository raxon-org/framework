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

/**
 * @throws Exception
 */
function validate_in_array_word_count(App $object, object|null $record=null, mixed $in='', mixed $field='', mixed $comparison='', mixed $function=false): bool
{
    $count = 0;
    if(is_array($in)){
        foreach($in as $text){
            $count += str_word_count($text);
        }
    }
    $argument = Token::tree('{if($argument ' . $comparison . ')}{/if}');
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
            $result = $count > $right;
        break;
        case '<' :
            $result = $count < $right;
        break;
        case '>=' :
            $result = $count >= $right;
        break;
        case '<=' :
            $result = $count <= $right;
        break;
        case '>>':
            $result = $count >> $right;
        break;
        case '<<':
            $result = $count << $right;
        break;
        case '==' :
            $result = $count == $right;
        break;
        case '!=' :
            $result = $count != $right;
        break;
        case '===' :
            $result = $count === $right;
        break;
        case '!==' :
            $result = $count !== $right;
        break;
        default:
            throw new Exception('Unknown equation');
    }
    if($result === false){
        return false;
    }




}
