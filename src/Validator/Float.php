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

use Raxon\Parse\Module\Token;

/**
 * @throws Exception
 */
function validate_float(App $object, object|null $record=null, mixed $string='', mixed $field='', mixed $argument='', mixed $function=false): bool
{
    $float = floatval($string);
    $flags = (object) [];
    $options = (object) [];
    $tree = Token::tokenize($object, $flags, $options, '{{if($argument ' . $argument . ')}}{{/if}}');
    $tag = reset($tree);
    $if = reset($tag);
    $left = null;
    $equation = null;
    $right = null;
    foreach($if['method']['argument'][0]['array'] as $nr => $record_argument){
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
            $result = $float > $right;
            break;
        case '<' :
            $result = $float < $right;
            break;
        case '>>' :
            $result = $float >> $right;
            break;
        case '<<' :
            $result = $float << $right;
            break;
        case '>=' :
            $result = $float >= $right;
            break;
        case '<=' :
            $result = $float <= $right;
            break;
        case '==' :
            $result = $float == $right;
            break;
        case '!=' :
            $result = $float != $right;
            break;
        case '===' :
            $result = $float === $right;
            break;
        case '!==' :
            $result = $float !== $right;
            break;
        default:
            throw new Exception('Unknown equation');
    }
    return $result;
}
