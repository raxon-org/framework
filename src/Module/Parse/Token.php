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
namespace Raxon\Module\Parse;

use Raxon\Config;

use Raxon\Module\Core;
use Raxon\Module\Data;
use Raxon\Module\File;

use Exception;

class Token {
    const TYPE_NULL = 'null';
    const TYPE_STRING = 'string';
    const TYPE_CODE = 'code';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_BOOLEAN_AND = 'boolean-and';
    const TYPE_BOOLEAN_OR = 'boolean-or';
    const TYPE_INT = 'integer';
    const TYPE_WORD = 'word';
    const TYPE_OCT = 'octal';
    const TYPE_HEX = 'hexadecimal';
    const TYPE_FLOAT = 'float';
    const TYPE_FOR = 'for';
    const TYPE_FOREACH = 'foreach';
    const TYPE_BREAK = 'break';
    const TYPE_CONTINUE = 'continue';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_VARIABLE = 'variable';
    const TYPE_OPERATOR = 'operator';
    const TYPE_DOT = 'dot';
    const TYPE_COLON = 'colon';
    const TYPE_DOUBLE_COLON = 'double-colon';
    const TYPE_DOUBLE_ARROW = 'double-arrow';
    const TYPE_AS = 'as';
    const TYPE_SEMI_COLON = 'semi-colon';
    const TYPE_COMMA = 'comma';
    const TYPE_MIXED = 'mixed';
    const TYPE_WHITESPACE = 'whitespace';
    const TYPE_STATEMENT = 'statement';
    const TYPE_PARENTHESE = 'parenthese';
    const TYPE_BRACKET = 'bracket';
    const TYPE_NUMBER = 'number';
    const TYPE_SET = 'set';
    const TYPE_METHOD = 'method';
    const TYPE_FUNCTION = 'function';
    const TYPE_MODIFIER = 'modifier';
    const TYPE_CLASS = 'class';
    const TYPE_TRAIT = 'trait';
    const TYPE_COLLECTION = 'collection';
    const TYPE_EXCLAMATION = 'exclamation';
    const TYPE_CONTROL = 'control';
    const TYPE_WHILE = 'while';
    const TYPE_QUOTE_SINGLE = 'quote-single';
    const TYPE_QUOTE_DOUBLE = 'quote-double';
    const TYPE_QUOTE_SINGLE_STRING = 'quote-single-string';
    const TYPE_QUOTE_DOUBLE_STRING = 'quote-double-string';
    const TYPE_BACKSLASH = 'backslash';
    const TYPE_BRACKET_SQUARE_OPEN = 'bracket-square-open';
    const TYPE_BRACKET_SQUARE_CLOSE = 'bracket-square-close';
    const TYPE_CURLY_OPEN = 'curly-open';
    const TYPE_CURLY_CLOSE = 'curly-close';
    const TYPE_PARENTHESE_OPEN = 'parenthese-open';
    const TYPE_PARENTHESE_CLOSE = 'parenthese-close';
    const TYPE_COMMENT_OPEN = 'comment-open';
    const TYPE_COMMENT_CLOSE = 'comment-close';
    const TYPE_DOC_COMMENT_OPEN = 'doc-comment-open';
    const TYPE_COMMENT_SINGLE_LINE = 'comment-single-line';
    const TYPE_COMMENT = 'comment';
    const TYPE_DOC_COMMENT = 'doc-comment';
    const TYPE_AMPERSAND = 'ampersand';
    const TYPE_QUESTION = 'question';
    const TYPE_PIPE = 'pipe';
    const TYPE_LITERAL = 'tag-literal';
    const TYPE_IS_OBJECT_OPERATOR = 'is-object-operator';
    const TYPE_IS_ARRAY_OPERATOR = 'is-array-operator';
    const TYPE_IS_EQUAL = 'is-equal';
    const TYPE_IS_NOT_EQUAL = 'is-not-equal';
    const TYPE_IS_GREATER_EQUAL = 'is-greater-equal';
    const TYPE_IS_SMALLER_EQUAL = 'is-smaller-equal';
    const TYPE_IS_GREATER = 'is-greater';
    const TYPE_IS_SMALLER = 'is-smaller';
    const TYPE_IS_IDENTICAL = 'is-identical';
    const TYPE_IS_NOT_IDENTICAL = 'is-not-identical';
    const TYPE_IS_GREATER_GREATER = 'is-greater-greater';
    const TYPE_IS_SMALLER_SMALLER = 'is-smaller-smaller';
    const TYPE_IS = 'is';
    const TYPE_IS_PLUS_EQUAL = 'is-plus-equal';
    const TYPE_IS_MINUS_EQUAL = 'is-minus-equal';
    const TYPE_IS_MULTIPLY_EQUAL = 'is-multiply-equal';
    const TYPE_IS_DIVIDE_EQUAL = 'is-divide-equal';
    const TYPE_IS_OR_EQUAL = 'is-or-equal';
    const TYPE_IS_MODULO_EQUAL = 'is-modulo-equal';
    const TYPE_IS_POWER_EQUAL = 'is-power-equal';
    const TYPE_IS_XOR_EQUAL = 'is-xor-equal';
    const TYPE_IS_AND_EQUAL = 'is-and-equal';
    const TYPE_IS_PLUS = 'is-plus';
    const TYPE_IS_MINUS = 'is-minus';
    const TYPE_IS_MULTIPLY = 'is-multiply';
    const TYPE_IS_DIVIDE = 'is-divide';
    const TYPE_IS_MODULO = 'is-modulo';
    const TYPE_IS_PLUS_PLUS = 'is-plus-plus';
    const TYPE_IS_MINUS_MINUS = 'is-minus-minus';
    const TYPE_IS_SPACESHIP = 'is-spaceship';
    const TYPE_IS_POWER = 'is-power';
    const TYPE_IS_COALESCE = 'is-coalesce';
    const TYPE_REM = 'rem';
    const TYPE_CAST = 'cast';
    const LITERAL_OPEN = '{literal}';
    const LITERAL_CLOSE = '{/literal}';
    const TYPE_TAG_CLOSE = 'tag-close';

    const DIRECTION_LTR = 'ltr';
    const DIRECTION_RTL = 'rtl';

    const MODIFIER_DIRECTION = 'direction';

    const NOT_TYPE_ECHO = [
        Token::TYPE_CURLY_OPEN,
        Token::TYPE_CURLY_CLOSE,
        Token::TYPE_QUOTE_DOUBLE_STRING,
        Token::TYPE_DOC_COMMENT,
        Token::TYPE_COMMENT,
    ];

    const TYPE_SINGLE = [
        Token::TYPE_PARENTHESE_OPEN,
        Token::TYPE_PARENTHESE_CLOSE,
        Token::TYPE_BRACKET_SQUARE_OPEN,
        Token::TYPE_BRACKET_SQUARE_CLOSE,
        Token::TYPE_CURLY_OPEN,
        Token::TYPE_CURLY_CLOSE,
        Token::TYPE_DOT,
        Token::TYPE_COMMA,
        Token::TYPE_SEMI_COLON,
        Token::TYPE_EXCLAMATION,
        Token::TYPE_QUOTE_SINGLE,
        Token::TYPE_QUOTE_DOUBLE
    ];

    const TYPE_NAME_BREAK = [
        Token::TYPE_WHITESPACE,
        Token::TYPE_PARENTHESE_OPEN,
        Token::TYPE_PARENTHESE_CLOSE,
        Token::TYPE_BRACKET_SQUARE_OPEN,
        Token::TYPE_BRACKET_SQUARE_CLOSE,
        Token::TYPE_CURLY_OPEN,
        Token::TYPE_CURLY_CLOSE,
        Token::TYPE_QUOTE_SINGLE,
        Token::TYPE_QUOTE_DOUBLE,
        Token::TYPE_COMMA,
        Token::TYPE_SEMI_COLON,
        Token::TYPE_COLON,
        Token::TYPE_DOUBLE_COLON,
        Token::TYPE_EXCLAMATION,
        Token::TYPE_IS
    ];

    const TYPE_NAME_BREAK_METHOD = [
        Token::TYPE_WHITESPACE,
        Token::TYPE_PARENTHESE_OPEN,
        Token::TYPE_PARENTHESE_CLOSE,
        Token::TYPE_BRACKET_SQUARE_OPEN,
        Token::TYPE_BRACKET_SQUARE_CLOSE,
        Token::TYPE_CURLY_OPEN,
        Token::TYPE_CURLY_CLOSE,
        Token::TYPE_QUOTE_SINGLE,
        Token::TYPE_QUOTE_DOUBLE,
        Token::TYPE_COMMA,
        Token::TYPE_SEMI_COLON,
        Token::TYPE_DOUBLE_COLON,
        Token::TYPE_EXCLAMATION,
        Token::TYPE_IS
    ];

    const TYPE_STRING_BREAK = [
        Token::TYPE_METHOD,
        Token::TYPE_VARIABLE,
        Token::TYPE_OPERATOR,
        Token::TYPE_COMMA,
        Token::TYPE_SEMI_COLON,
        Token::TYPE_CURLY_OPEN,
        Token::TYPE_CURLY_CLOSE,
    ];

    const TYPE_ASSIGN = [
        '=',
        '+=',
        '-=',
        '*=',
        '%=',
        '/=',
        '++',
        '--',
        '**',
        '**=',
        '^='.
        '&=',
        '|='
    ];

    const TYPE_AS_OPERATOR = [
        '+',
        '-',
        '/',
        '*',
        '%',
    ];

    const PLUGIN_RENAME = [
        'require',
        'default',
        'object',
        'constant',
        'echo',
        'exit',
        'unset'
    ];

    /**
     * @throws Exception
     */
    public static function split($string='', $length=1, $encoding='UTF-8'): array
    {
        $array = [];
        $strlen = mb_strlen($string);
        for($i=0; $i<$strlen; $i=$i+$length){
            $array[] = mb_substr($string, $i, $length, $encoding);
        }
        return $array;
    }

    private static function operator($record=[], $level=1, $options=[]): array
    {
        if($record['type'] != Token::TYPE_OPERATOR){
            return $record;
        }
        $record['is_operator'] = true;
        switch($level){
            case 1 :
                switch($record['value']){
                    case '=' :
                        $record['type'] = Token::TYPE_IS;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '+' :
                        $record['type'] = Token::TYPE_IS_PLUS;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '-' :
                        $record['type'] = Token::TYPE_IS_MINUS;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '*' :
                        $record['type'] = Token::TYPE_IS_MULTIPLY;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '/' :
                        $record['type'] = Token::TYPE_IS_DIVIDE;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '%' :
                        $record['type'] = Token::TYPE_IS_MODULO;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '>' :
                        $record['type'] = Token::TYPE_IS_GREATER;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '<' :
                        $record['type'] = Token::TYPE_IS_SMALLER;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case ':' :
                        $record['type'] = Token::TYPE_COLON;
                        return $record;
                    case '!' :
                        $record['type'] = Token::TYPE_EXCLAMATION;
                        $record['is_operator'] = false;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '?' :
                        $record['type'] = Token::TYPE_QUESTION;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '&' :
                        $record['type'] = Token::TYPE_AMPERSAND;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '|' :
                        $record['type'] = Token::TYPE_PIPE;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                }
                $record['is_operator'] = false;
                break;
            case 2 :
                switch($record['value']){
                    case '==' :
                        $record['type'] = Token::TYPE_IS_EQUAL;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '!=' :
                        $record['type'] = Token::TYPE_IS_NOT_EQUAL;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '=>' :
                        $record['type'] = Token::TYPE_IS_ARRAY_OPERATOR;
                        return $record;
                    case '->' :
                        $record['type'] = Token::TYPE_IS_OBJECT_OPERATOR;
                        return $record;
                    case '<=' :
                        $record['type'] = Token::TYPE_IS_SMALLER_EQUAL;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '>=' :
                        $record['type']= Token::TYPE_IS_GREATER_EQUAL;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '<>' :
                        $record['type'] = Token::TYPE_IS_NOT_EQUAL;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '+=' :
                        $record['type'] = Token::TYPE_IS_PLUS_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '-=' :
                        $record['type'] = Token::TYPE_IS_MINUS_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '*=' :
                        $record['type'] = Token::TYPE_IS_MULTIPLY_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '/=' :
                        $record['type'] = Token::TYPE_IS_DIVIDE_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '%=' :
                        $record['type'] = Token::TYPE_IS_MODULO_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '^=' :
                        $record['type'] = Token::TYPE_IS_XOR_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '&=' :
                        $record['type'] = Token::TYPE_IS_AND_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '|=' :
                        $record['type'] = Token::TYPE_IS_OR_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '<<' :
                        $record['type'] = Token::TYPE_IS_SMALLER_SMALLER;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '>>' :
                        $record['type'] = Token::TYPE_IS_GREATER_GREATER;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '++' :
                        $record['type'] = Token::TYPE_IS_PLUS_PLUS;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '--' :
                        $record['type'] = Token::TYPE_IS_MINUS_MINUS;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '**' :
                        $record['type'] = Token::TYPE_IS_POWER;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                    case '::' :
                        $record['type'] = Token::TYPE_DOUBLE_COLON;
                        return $record;
                    case '&&' :
                        $record['type'] = Token::TYPE_BOOLEAN_AND;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '||' :
                        $record['type'] = Token::TYPE_BOOLEAN_OR;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '??' :
                        $record['type'] = Token::TYPE_IS_COALESCE;
                        return $record;
                    case '//' :
                        $record['is_operator'] = false;
                        $record['type'] = Token::TYPE_COMMENT_SINGLE_LINE;
                        return $record;
                    case '/*' :
                        $record['is_operator'] = false;
                        $record['type'] = Token::TYPE_COMMENT;
                        return $record;
                    case '*/' :
                        $record['is_operator'] = false;
                        $record['type'] = Token::TYPE_COMMENT_CLOSE;
                        return $record;
                }
                $record['is_operator'] = false;
                break;
            case 3 :
                switch($record['value']){
                    case '===' :
                        $record['type'] = Token::TYPE_IS_IDENTICAL;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '!==' :
                        $record['type'] = Token::TYPE_IS_NOT_IDENTICAL;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '/**' :
                        $record['is_operator'] = false;
                        $record['type'] = Token::TYPE_DOC_COMMENT;
                        return $record;
                    case '<=>' :
                        $record['type'] = Token::TYPE_IS_SPACESHIP;
                        $record['direction'] = Token::DIRECTION_LTR;
                        return $record;
                    case '**=' :
                        $record['type'] = Token::TYPE_IS_POWER_EQUAL;
                        $record['direction'] = Token::DIRECTION_RTL;
                        return $record;
                }
                $record['is_operator'] = false;
                break;
        }
        return $record;
    }

    /**
     * @throws Exception
     */
    public static function tree_prepare($string='', &$count=0, $options=[]): array
    {
        $array = Token::split($string);
        $token = array();
        $row = 1;
        if(array_key_exists('object', $options)){
            $object = $options['object'];
            $row += $object->config('parse.read.row_mismatch') ?? 0;
        }
        $column = 1;
        $nr = -1;
        foreach($array as $nr => $char){
            $type = Token::type($char);
            $record = [];
            $record['value'] = $char;
            $record['type'] = $type;
            $record['column'] = $column;
            $record['row'] = $row;
            $record['is_operator'] = false;
            $token[$nr] = $record;
            $column++;
            if($record['value'] === "\n"){
                $row++;
                $column = 1;
            }
        }
        $count = $nr + 1;
        $count_begin = $count;
        $previous_nr = null;
        $skip = 0;
        foreach($token as $nr => $record){
            if($skip > 0){
                $skip--;
                continue;
            }
            $operator = null;
            $check = null;
            $check2 = null;
            $next = null;
            $next_next = null;
            if($nr + 1 < $count_begin){
                $next = $nr + 1;
            }
            if($nr + 2 < $count_begin){
                $next_next = $nr + 2;
            }
            if(array_key_exists('extra_operators', $options)){
                if($next !== null && $next_next !== null){
                    $test_value = $record['value'] . $token[$next]['value'] . $token[$next_next]['value'];
                    if(
                        in_array(
                            strtolower($test_value),
                            $options['extra_operators'],
                            true
                        )
                    ){
                        $token[$nr]['value'] = $test_value;
                        $token[$nr]['type'] = strtolower($test_value);
                        $token[$nr]['direction'] = Token::DIRECTION_LTR;
                        $token[$nr]['is_operator'] = true;
                        unset($token[$next]);
                        unset($token[$next_next]);
                        $previous_nr = $nr;
                        $count -= 2;
                        $skip = 2;
                        continue;
                    }
                }
                elseif($next !== null){
                    $test_value = $record['value'] . $token[$next]['value'];
                    if(
                        in_array(
                            strtolower($test_value),
                            $options['extra_operators'],
                            true
                        )
                    ){
                        $token[$nr]['value'] = $test_value;
                        $token[$nr]['type'] = strtolower($test_value);
                        $token[$nr]['direction'] = Token::DIRECTION_LTR;
                        $token[$nr]['is_operator'] = true;
                        unset($token[$next]);
                        $previous_nr = $nr;
                        $count -= 1;
                        $skip = 1;
                        continue;
                    }
                }
            }
            if(
            in_array(
                $record['type'],
                Token::TYPE_SINGLE,
                true
            )
            ){
                //1
                $previous_nr = $nr;
                continue;
            }
            elseif(
                $next !== null &&
                $next_next !== null &&
                $record['type'] === $token[$next]['type'] &&
                $record['type'] === $token[$next_next]['type']
            ){
                //3
                if($record['type'] === Token::TYPE_OPERATOR){
                    $operator = $record;
                    $operator['value'] .= $token[$next]['value'] . $token[$next_next]['value'];
                    $operator = Token::operator($operator, 3, $options);
                    if($operator['type'] === Token::TYPE_OPERATOR){
                        $operator['value'] = $record['value'] . $token[$next]['value'];
                        $operator = Token::operator($operator, 2);
                        if($operator['type'] === Token::TYPE_OPERATOR){
                            $operator = $record;
                            $operator['value'] = $record['value'];
                            $operator = Token::operator($operator, 1);
                            $check = $record;
                            $check['value'] = $token[$next]['value'] . $token[$next_next]['value'];
                            $check = Token::operator($check, 2);
                            if($check['type'] === Token::TYPE_OPERATOR){
                                $check['value'] = $token[$next]['value'];
                                $check2 = $record;
                                $check2['value'] = $token[$next_next]['value'];
                                $check = Token::operator($check, 1);
                                $check2 = Token::operator($check2, 1);
                            }
                        } else {
                            $check = $record;
                            $check['value'] = $token[$next]['value'] . $token[$next_next]['value'];
                            $check = Token::operator($check, 2);
                            if($check['type'] === Token::TYPE_OPERATOR){
                                $check['value'] = $token[$next_next]['value'];
                                $check = Token::operator($check, 1);
                            } else {
                                if(
                                    $check['type'] === Token::TYPE_COMMENT_CLOSE &&
                                    $operator['type'] === Token::TYPE_COMMENT
                                ){
                                    $check = $record;
                                    $check['value'] = $token[$next_next]['value'];
                                    $check = Token::operator($check, 1);
                                }
                                elseif(
                                    $check['type'] === Token::TYPE_COMMENT_CLOSE &&
                                    $operator['type'] === Token::TYPE_IS_POWER
                                ){
                                    $operator = $record;
                                    $operator['value'] = $record['value'];
                                    $operator = Token::operator($operator, 1);
                                } else {
                                    $check = $record;
                                    $check['value'] = $token[$next_next]['value'];
                                    $check = Token::operator($check, 1);
                                }
                            }
                        }
                        $token[$nr] = $operator;
                        $token[$next] = $check;
                        if($check2 === null){
                            unset($token[$next_next]);
                            $count--;
                            $skip = 2;
                        } else {
                            $token[$next_next] = $check2;
                        }
                        $previous_nr = $nr;
                        continue;
                    } else {
                        $token[$nr] = $operator;
                        unset($token[$next]);
                        unset($token[$next_next]);
                        $previous_nr = $nr;
                        $count -= 2;
                        $skip = 2;
                        continue;
                    }
                } else {
                    if($previous_nr !== null){
                        if($token[$previous_nr]['type'] === $record['type']){
                            $token[$previous_nr]['value'] .= $record['value'] . $token[$next]['value'] . $token[$next_next]['value'];
                            unset($token[$nr]);
                            unset($token[$next]);
                            unset($token[$next_next]);
                            $count -= 3;
                            $skip = 2;
                            continue;
                        }
                    }
                    $token[$nr]['value'] .= $token[$next]['value'] . $token[$next_next]['value'];
                    unset($token[$next]);
                    unset($token[$next_next]);
                    $previous_nr = $nr;
                    $count -= 2;
                    $skip = 2;
                }
            }
            elseif(
                $next !== null &&
                $next_next !== null &&
                $record['type'] === $token[$next]['type'] &&
                $record['type'] != $token[$next_next]['type']
            ){
                //2
                if($previous_nr !== null){
                    if($token[$previous_nr]['type'] === $record['type']){
                        $token[$previous_nr]['value'] .= $record['value'] . $token[$next]['value'];
                        unset($token[$nr]);
                        unset($token[$next]);
                        $count -= 2;
                        $skip = 1;
                        continue;
                    }
                }
                $token[$nr]['value'] .= $token[$next]['value'];
                if($record['type'] === Token::TYPE_OPERATOR){
                    $token[$nr] = Token::operator($token[$nr], 2);
                    if($token[$nr]['type'] === Token::TYPE_OPERATOR){
                        $token[$nr] = Token::operator($record, 1);
                        $token[$next] = Token::operator($token[$next], 1);
                        $previous_nr = $nr;
                        $skip = 1;
                        continue;
                    }
                }
                unset($token[$next]);
                $previous_nr = $nr;
                $count--;
                $skip = 1;
            }
            elseif(
                $next !== null &&
                $record['type'] === $token[$next]['type']
            ){
                //2
                if($previous_nr !== null){
                    if($token[$previous_nr]['type'] === $record['type']){
                        $token[$previous_nr]['value'] .= $record['value'] . $token[$next]['value'];
                        unset($token[$nr]);
                        unset($token[$next]);
                        $count -= 2;
                        $skip = 1;
                        continue;
                    }
                }
                $token[$nr]['value'] .= $token[$next]['value'];
                if($record['type'] === Token::TYPE_OPERATOR){
                    $token[$nr] = Token::operator($token[$nr], 2);
                    if($token[$nr]['type'] === Token::TYPE_OPERATOR){
                        $token[$nr] = Token::operator($record, 1);
                        $token[$next] = Token::operator($token[$next], 1);
                        $previous_nr = $nr;
                        $skip = 1;
                        continue;
                    }
                }
                unset($token[$next]);
                $previous_nr = $nr;
                $count--;
                $skip = 1;
            } else {
                //1
                if($previous_nr !== null){
                    if($token[$previous_nr]['type'] === $record['type']){
                        $token[$previous_nr]['value'] .= $record['value'];
                        unset($token[$nr]);
                        $count--;
                        continue;
                    }
                }
                if($record['type'] === Token::TYPE_OPERATOR){
                    $token[$nr] = Token::operator($record, 1);
                }
                $previous_nr = $nr;
            }
        }
        $prepare = [];
        foreach($token as $nr => $record){
            $prepare[] = $record;
            unset($token[$nr]);
        }
        return $prepare;
    }

    /**
     * @throws Exception
     */
    public static function tree($string='', $options=[]): array
    {
        $object = false;
        if(array_key_exists('object', $options)){
            $object = $options['object'];
        }
        $url = false;
        $dir = false;
        if(
            $object &&
            $object->config('ramdisk.parse.tree') &&
            $object->config('ramdisk.url') &&
            array_key_exists('url', $options)
        ){
            $dir = $object->config('ramdisk.url') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Parse' .
                $object->config('ds')
            ;
            $url =  $dir .
                sha1($string) .
                $object->config('extension.json');
            $object->config('require.disabled', true);
            $read = $object->data_read($url);
            $object->config('delete', 'require.disabled');
            if(
                File::exist($options['url']) &&
                File::exist($url) &&
                File::mtime($options['url']) === File::mtime($url) &&
                $read && $read->get('string') === $string
            ){
                return Core::object($read->get('token'), Core::OBJECT_ARRAY);
            }
        }
        $token = Token::tree_prepare($string, $count, $options);
        $token = Token::prepare($token, $count, $options);
        $token = Token::define($token, $options);
        $token = Token::group($token, $options);

        $token = Token::cast($token);
        $token = Token::method($token, $options);
        if(
            $object &&
            $object->config('ramdisk.parse.tree') &&
            $url &&
            array_key_exists('url', $options)
        ) {
            $data = new Data();
            $data->set('string', $string);
            $data->set('token', $token);
            $data->set('url', $options['url']);
            $data->write($url);
            if (File::exist($options['url'])) {
                File::touch($url, File::mtime($options['url']));
            }
            if(
                Config::posix_id() === 0 &&
                Config::posix_id() !== $object->config(Config::POSIX_ID)
            ){
                if($dir){
                    exec('chown www-data:www-data ' . $dir);
                }
                exec('chown www-data:www-data ' . $url);
            }
        }
        return $token;
    }

    public static function cast($token=[]): array
    {
        $previous_nr = null;
        $previous_previous_nr = null;
        foreach($token as $nr => $record){
            if(!array_key_exists('type', $record)){
                $token[$nr] = Token::cast($record);
                continue;
            }
            if(
                $previous_nr !== null &&
                $previous_previous_nr !== null &&
                $record['value'] === ')' &&
                $token[$previous_previous_nr]['value'] === '(' &&
                $token[$previous_nr]['type'] === Token::TYPE_STRING
            ){
                $token[$previous_nr]['type'] = Token::TYPE_CAST;
                --$token[$previous_nr]['depth'];
                unset($token[$nr]);
                unset($token[$previous_previous_nr]);
            }
            $previous_previous_nr = $previous_nr;
            $previous_nr = $nr;
        }
        return $token;
    }

    public static function attribute($token=[]): array
    {
        foreach($token as $nr => $record){
            $token[$nr]['is_attribute'] = true;
        }
        return $token;
    }

    public static function method($token=[], $options=[]): array
    {
        $object = false;
        if(array_key_exists('object', $options)){
            $object = $options['object'];
        }
        $selection = [];
        $collect = false;
        $depth = null;
        $square_depth = 0;
        $curly_depth = 0;
        $target = null;
        $skip = 0;
        $skip_unset = 0;
        $attribute_nr = 0;
        $assign_nr = 0;
        $value = '';
        foreach($token as $nr => $record){
            if(!array_key_exists('type', $record)){
                $token[$nr] = Token::method($record);
                continue;
            }
            if($skip > 0){
                $skip--;
                $value .= $record['value'];
                continue;
            }
            elseif($skip_unset > 0){
                unset($token[$nr]);
                $skip_unset--;
                $value .= $record['value'];
                continue;
            }
            if(
                $target === null &&
                $record['type'] == Token::TYPE_METHOD
            ){
                $target = $nr;
                $depth = $record['depth'];
                $skip_unset = 1;
                $value = $record['value'];
                $attribute = [];
            }
            elseif(
                array_key_exists($target, $token) &&
                $record['value'] === ')' &&
                $depth == $record['depth'] - 1
            ){
                if(!empty($attribute)){
                    $attribute = Token::method($attribute);
                    $is_literal = false;
                    if($object){
                        $literal = $object->config('parse.plugin.literal');
                        foreach($literal as $literal_key => $literal_value){
                            if(property_exists($literal_value, 'name')){
                                if($token[$target]['method']['name'] === $literal_value->name){
                                    $is_literal = $literal_value;
                                    if(!property_exists($is_literal, 'index')) {
                                        $is_literal->index = 0;
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    foreach($attribute as $attribute_key => $attribute_value){
                        if($attribute_value['type'] === Token::TYPE_CURLY_OPEN){
                            $curly_depth++;
                        }
                        elseif($attribute_value['type'] == Token::TYPE_CURLY_CLOSE){
                            $curly_depth--;
                        }
                        elseif($attribute_value['type'] == Token::TYPE_BRACKET_SQUARE_OPEN){
                            $square_depth++;
                            //possible array
                        }
                        elseif($attribute_value['type'] == Token::TYPE_BRACKET_SQUARE_CLOSE){
                            $square_depth--;
                            //possible array
                        }
                        elseif(
                            $square_depth == 0 &&
                            $curly_depth == 0 &&
                            $attribute_value['type'] == Token::TYPE_COMMA
                        ){
                            $attribute_nr++;                            
                            continue;
                        }
                        $attribute_value['array_depth'] = $square_depth;
                        if(
                            $is_literal
                        ){
                            if(
                                property_exists($is_literal, 'count') &&
                                $is_literal->count === '*'
                            ){
                                //all arguments are literal
                                $attribute_value['is_literal'] = true;
                            } elseif(
                                is_array($is_literal->index) &&
                                in_array(
                                    $attribute_nr,
                                    $is_literal->index,
                                    true
                                )
                            ){
                                //we have multiple indexes
                                $attribute_value['is_literal'] = true;
                            } elseif($is_literal->index === $attribute_nr){
                                //we have a single index
                                $attribute_value['is_literal'] = true;
                            }
                        }
                        $token[$target]['method']['attribute'][$attribute_nr][$attribute_key] = $attribute_value;
                    }                    
                }                
                $value .= $record['value'];
                $token[$target]['parse'] =  $value;                                
                $target = null;
                $depth = null;
                $attribute_nr = 0;
                $assign_nr = 0;
                unset($token[$nr]);
            }
            elseif($target !== null){                
                if($token[$target]['method']['name'] == 'foreach'){
                    $has_as = false;
                    $has_explain = false;
                    if(
                        $has_as === false &&
                        $record['value'] == 'as'
                    ){
                        $value .= ' ' . $record['value'] . ' ';
                        $has_as = true;
                    }
                    elseif(
                        $has_explain === false &&
                        $record['value'] == '=>'
                    ){
                        $value .= ' ' . $record['value'] . ' ';
                        $has_explain = true;
                    }
                    else {
                        $value .= $record['value'];        
                    }
                } else {
                    $value .= $record['value'];
                }                
                if($token[$target]['method']['name'] == Token::TYPE_FOR){
                    if($record['type'] == Token::TYPE_SEMI_COLON){
                        $attribute_nr++;
                        unset($token[$nr]);
                        continue;
                    }
                    $token[$target]['method']['attribute'][$attribute_nr][$nr] = $record;
                } else {
                    $attribute[$nr] = $record;                
                }
                unset($token[$nr]);
            }
        }                
        return $token;
    }

    public static function group($token=[], $options=[]): array
    {
        $is_outside = true;
        $curly_depth = 0;
        foreach($token as $nr => $record){
            if(!array_key_exists('type', $record)){
                $token[$nr] = Token::group($record, $options);
            }
            elseif($record['type'] === Token::TYPE_CURLY_OPEN){
                $curly_depth++;
                $is_outside = false;
                continue;
            }
            elseif(
                $record['type'] === Token::TYPE_CURLY_CLOSE &&
                $curly_depth > 0
            ){
                $curly_depth--;
                if($curly_depth === 0){
                    $is_outside = true;
                }
                continue;
            }
            elseif(
                in_array(
                    $record['type'],
                    [
                        Token::TYPE_CURLY_CLOSE,
                        Token::TYPE_COMMENT,
                        Token::TYPE_DOC_COMMENT,
                        Token::TYPE_COMMENT_CLOSE,
                    ],
                    true
                )
            ){
                $is_outside = true;
                continue;
            }
            if($is_outside === true){
                $is_outside = $nr;
            }
            if(
                $is_outside === false &&
                $record['type'] === Token::TYPE_WHITESPACE &&
                empty($options['with_whitespace'])
            ) {
                unset($token[$nr]);
                continue;
            }
            if(
                array_key_exists('type', $record) &&
                is_int($is_outside)
            ){
                if(
                    in_array(
                        $record['type'],
                        [
                            Token::TYPE_QUOTE_DOUBLE_STRING,
//                            Token::TYPE_WHITESPACE
                        ],
                        true
                    )
                ){
                    $is_outside = true;
                    continue;
                }
                else {
                    $token[$is_outside]['type'] = Token::TYPE_STRING;
                    $token[$is_outside]['is_operator'] = false;
                    unset($token[$is_outside]['direction']);
                    if($nr != $is_outside){
                        if(
                            $record['type'] === Token::TYPE_VARIABLE &&
                            !empty($record['variable']['is_assign'])
                        ){
                            if(isset($record['variable']['operator_whitespace'])){
                                $token[$is_outside]['value'] .= $record['value'] . $record['variable']['operator_whitespace']['value'] . $record['variable']['operator'];
                            } else {
                                $token[$is_outside]['value'] .= $record['value'] . $record['variable']['operator'];
                            }                                                                                    
                        } else {
                            $token[$is_outside]['value'] .= $record['value'];
                        }
                        unset($token[$nr]);
                    }
                }
            }
        }
        return $token;
    }

    private static function array_finalize($array=[], $options=null): array
    {
        $result = [];
        $count = 0;
        foreach($array as $nr => $record){
            if(!array_key_exists($count, $result)){
                $result[$count] = [];
            }
            if(
                is_array($record) &&
                !array_key_exists('value', $record)
            ){
                $result[$count][] = Token::array_finalize($record, $options);
            }
            elseif($record['type'] === Token::TYPE_BRACKET_SQUARE_OPEN){
                if(
                    array_key_exists($options['remove_bracket'], $options) &&
                    $options['remove_bracket'] === true
                ){
                    // nothing
                } else {
                    $result[$count][] = $record;
                    $count++;
                }
            }
            elseif($record['type'] === Token::TYPE_BRACKET_SQUARE_CLOSE){
                if(
                    array_key_exists($options['remove_bracket'], $options) &&
                    $options['remove_bracket'] === true
                ){
                    // nothing
                } else {
                    $result[$count][] = $record;
                    $count++;
                }
            }
            elseif($record['type'] === Token::TYPE_COMMA){
                $count++;
            } else {
                $result[$count][] = $record;
            }
        }
        return $result;
    }

    private static function nested_array($array=[], $options=[], $depth=1): array
    {

        $pop = array_pop($array); //remove square_close
        $shift = array_shift($array); //remove square_open
        if(
            $pop &&
            $pop['type'] !== Token::TYPE_BRACKET_SQUARE_CLOSE
        ){
            $array[] = $pop;
        }
        if(
            $shift &&
            $shift['type'] !== Token::TYPE_BRACKET_SQUARE_OPEN
        ){
            array_unshift($array, $shift);
        }
        $count = count($array);
        foreach($array as $nr => $record){
            if($record['type'] === Token::TYPE_BRACKET_SQUARE_OPEN){
                $selection = [];
                $depth_match = false;
                for($i = $nr; $i < $count; $i++) {
                    if(!array_key_exists($i, $array)){
                        continue;
                    }
                    $selection[$i] = $array[$i];
                    if ($array[$i]['type'] === Token::TYPE_BRACKET_SQUARE_OPEN) {
                        if($depth_match === false){
                            $depth_match = $depth;
                        }
                        $selection[$i]['array_depth'] = $depth;
                        $depth++;
                    } elseif ($array[$i]['type'] === Token::TYPE_BRACKET_SQUARE_CLOSE) {
                        $depth--;
                        $selection[$i]['array_depth'] = $depth;
                    } else {
                        $selection[$i]['array_depth'] = $depth;
                    }
                    if ($depth === $depth_match) {
                        foreach($selection as $key => $unused){
                            unset($array[$key]);
                        }
                        $selection = Token::array($selection, $options);
                        $selection = array_values($selection);
                        $array[$nr] = Token::nested_array($selection, $options, $depth);
                        ksort($array, SORT_NATURAL);
                        break;
                    }
                }
                continue;
            }
            if($record['type'] === Token::TYPE_BRACKET_SQUARE_CLOSE){
                $depth--;
            }
        }
        return $array;
    }

    public static function array($token=[], $options=[]): array
    {
        $array = [];
        $array_start = null;
        $count = count($token);
        $array_depth = 0;
        $is_nested_array = 0;
        $key = false;
        foreach($token as $nr => $record){
            if($record['type'] === Token::TYPE_BRACKET_SQUARE_OPEN){
                $array_depth++;
                if($array_start === null){
                    $array_start = $nr;
                    for($i = $nr + 1; $i < $count; $i++){
                        //only check the first array
                        if($token[$i]['type'] === Token::TYPE_COMMA){
                            break;
                        }
                        if($token[$i]['type'] == Token::TYPE_IS_ARRAY_OPERATOR){
                            $is_nested_array = 1;
                            break;
                        }
                    }
                }
                if($is_nested_array > 0){
                    $array[] = $record;
                }
            }
            elseif($record['type'] === Token::TYPE_BRACKET_SQUARE_CLOSE){
                $array_depth--;
                if($is_nested_array > 0) {
                    $array[] = $record;
                }
            }
            if($array_depth > 0){
                if(
                    in_array(
                        $record['type'],
                        [
                            Token::TYPE_BRACKET_SQUARE_OPEN,
                            Token::TYPE_BRACKET_SQUARE_CLOSE
                        ]
                    )
                ){
                    continue;
                }
                $array[] = $record;
            }
            elseif(
                $array_depth === 0 &&
                (
                    $array_start ||
                    $array_start === 0
                )
            ){
                if($is_nested_array > 0){
                    $array = Token::nested_array($array, $options);
                    $array = Token::array_finalize($array, $options);
                    $array = Token::cast($array);
                    $array = Token::define($array, $options);
                    $array = Token::method($array);
                    $token[$array_start]['type'] = Token::TYPE_ARRAY;
                    $token[$array_start]['value'] = $array;
                    $token[$array_start]['is_nested'] = $is_nested_array;
                    $token[$array_start]['array_depth'] = $array_depth;
                    for($i = $array_start + 1; $i <= $nr; $i++){
                        unset($token[$i]);
                    }
                } else {
                    $array = Token::array_finalize($array, $options);
                    $array = Token::cast($array);
                    $array = Token::define($array, $options);
                    $array = Token::method($array);
                    $token[$array_start]['type'] = Token::TYPE_ARRAY;
                    $token[$array_start]['value'] = $array;
                    $token[$array_start]['is_nested'] = $is_nested_array;
                    $token[$array_start]['array_depth'] = $array_depth;
                    for($i = $array_start + 1; $i <= $nr; $i++){
                        unset($token[$i]);
                    }
                }
                $array_start = null;
                $array = [];
                $is_nested_array = 0;
            }
        }
//        d($token);
        return $token;
    }

    public static function modifier($token=[], $options=[]): array
    {
        foreach($token as $token_nr => $modifier_list){
            $modifier = null;
            $is_attribute = -1;
            $parse = '';
            $check_attribute = false;
            $count = 0;
            foreach($modifier_list as $modifier_nr => $modifier_record){
                if($modifier === null){
                    $modifier = $modifier_nr;
                    $parse = $modifier_record['value'];                    
                    continue;
                }
                if($modifier_record['value'] == ':'){
                    $is_attribute++;
                    $parse .= $modifier_record['value'];
                    unset($token[$token_nr][$modifier_nr]);
                    continue;
                }
                if($is_attribute == -1){
                    $token[$token_nr][$modifier]['value'] .= $modifier_record['value'];
                    $token[$token_nr][$modifier]['has_attribute'] = false;
                    $parse .= $modifier_record['value'];
                    unset($token[$token_nr][$modifier_nr]);
                } else {
                    if(!array_key_exists('attribute', $token[$token_nr][$modifier])){
                        $token[$token_nr][$modifier]['attribute'] = [];
                    }
                    if(!array_key_exists($is_attribute, $token[$token_nr][$modifier]['attribute'])){
                        $token[$token_nr][$modifier]['attribute'][$is_attribute] = [];
                    }
                    $token[$token_nr][$modifier]['attribute'][$is_attribute][] = $modifier_record;
                    $token[$token_nr][$modifier]['has_attribute'] = true;
                    $parse .= $modifier_record['value'];
                    unset($token[$token_nr][$modifier_nr]);
                    $check_attribute = true;
                    $count++;
                }
            }
            if($check_attribute === true){
                foreach($token[$token_nr][$modifier]['attribute'] as $attribute_nr => $attribute){
                    $attribute = Token::cast($attribute);
                    $token[$token_nr][$modifier]['attribute'][$attribute_nr] = Token::array($attribute);
                }
            }
            $token[$token_nr][$modifier]['parse'] = $parse;            
        }
        return $token;
    }

    /*
    public static function define($token=[]): array
    {
        $define = [];
        $method = [];
        $variable = [];
        $unset = [];
        $is_method = null;
        $is_variable = null;
        $depth = null;
        $attribute_nr = 0;
        $variable_nr = 0;
        foreach($token as $nr => $record){
            if(
                $record['type'] === Token::TYPE_VARIABLE &&
                key_exists('variable', $record) &&
                key_exists('has_modifier', $record['variable']) &&
                $record['variable']['has_modifier'] === true
            ){
                $is_variable = $nr;
            }
            elseif($is_variable !== null) {
                if ($record['type'] === Token::TYPE_WHITESPACE) {
                    unset($token[$nr]);
                    continue;
                }
                elseif($record['type'] === Token::TYPE_PIPE){
                    $variable_nr++;
                    unset($token[$nr]);
                    continue;
                }
                elseif($record['type'] === Token::TYPE_METHOD){
                    $is_method = $nr;
                    $depth = $record['depth'];
                }
                elseif(
                    $is_method !== null &&
                    $record['type'] !== Token::TYPE_CURLY_CLOSE
                ){
                    if(
                        $record['value'] === '(' &&
                        $record['depth'] === $depth + 1
                    ){
                        if(!empty($method)){
                            foreach($method as $unset => $item){
                                $token[$is_method]['value'] .= $item['value'];
                                unset($token[$unset]);
                            }
                        }
                        $method = [];
                        $is_method = null;
                        $depth = null;
                        continue;
                    }
                    $method[$nr] = $record;
//                    d($method);
                }
                if(
                    (
                        $record['type'] === Token::TYPE_CURLY_CLOSE
                    ) ||
                    (
                        $record['is_operator'] === true &&
                        $record['type'] !== Token::TYPE_COLON
                    )
                ){
                    d($method);
                    d($variable);
                    $token[$is_variable]['type'] = Token::TYPE_VARIABLE;
                    $variable = Token::modifier($variable);
                    $token[$is_variable]['variable']['modifier'] = $variable;
                    $token[$is_variable]['parse'] = $token[$is_variable]['value'];
                    foreach($token[$is_variable]['variable']['modifier'] as $modifier_nr => $modifier_list){
                        foreach($modifier_list as $modifier_key => $modifier){
                            $token[$is_variable]['parse'] .= $token[$is_variable]['variable']['operator'] . $modifier['parse'];
                        }
                    }
                    $is_variable = null;
                    $variable_nr = 0;
                    $variable = [];
                    continue;
                }
                if(empty($variable[$variable_nr])){
                    $variable[$variable_nr] = [];
                    $record['type'] = Token::TYPE_MODIFIER;
                }
                $variable[$variable_nr][] = $record;
                unset($token[$nr]);
            }
        }
        return $token;
    }
    */


    public static function define($token=[], $options=[]): array
    {
        $define = [];
        $method = [];
        $variable = [];
        $unset = [];
        $is_method = null;
        $is_variable = null;
        $depth = null;
        $set_depth = 0;
        $in_set = null;
        $attribute_nr = 0;
        $variable_nr = 0;
        foreach($token as $nr => $record){
            if(
                !array_key_exists('type', $record) &&
                is_array($record)
            ){
                $token[$nr] = Token::define($record, $options);
            } else {
                if($record['value'] === '('){
                    $set_depth++;
                }
                elseif($record['value'] === ')'){
                    $set_depth--;
                }
                if(
                    $is_variable === null &&
                    $record['type'] === Token::TYPE_METHOD
                ){
                    $is_method = $nr;
                    $depth = $record['depth'];
                }
                elseif($is_method !== null){
                    if(
                        $record['value'] === '(' &&
                        $record['depth'] === $depth + 1
                    ){
                        if(!empty($method)){
                            foreach($method as $unset => $item){
                                $token[$is_method]['value'] .= $item['value'];
                                unset($token[$unset]);
                            }
                        }
                        $method = [];
                        $is_method = null;
                        $depth = null;
                        continue;
                    }
                    if($record['type'] === Token::TYPE_WHITESPACE){
                        continue;
                    }
                    $method[$nr] = $record;
                    if(
                        $set_depth > 0 &&
                        $in_set === null
                    ){
                        $in_set = true;
                    }
                    elseif($in_set === null){
                        $in_set = false;
                    }
                }
                elseif(
                    $record['type'] === Token::TYPE_VARIABLE &&
                    key_exists('variable', $record) &&
                    key_exists('has_modifier', $record['variable']) &&
                    $record['variable']['has_modifier'] === true
                ){
                    $is_variable = $nr;
                }
                elseif($is_variable !== null){
                    if($record['type'] === Token::TYPE_WHITESPACE){
                        unset($token[$nr]);
                        continue;
                    }
                    elseif($record['type'] === Token::TYPE_PIPE){
                        $variable_nr++;
                        unset($token[$nr]);
                        continue;
                    }
                    elseif(
                        $record['type'] === Token::TYPE_CURLY_CLOSE
                        ||
                        (
                            $in_set === true &&
                            $set_depth === 0 &&
                            $record['value'] === ')'
                        )
                    ){
                        $token[$is_variable]['type'] = Token::TYPE_VARIABLE;
//                    d($variable);
                        $variable = Token::modifier($variable, $options);
//                    trace();
                        $token[$is_variable]['variable']['modifier'] = $variable;
                        $token[$is_variable]['parse'] = $token[$is_variable]['value'];
                        foreach($token[$is_variable]['variable']['modifier'] as $modifier_nr => $modifier_list){
                            foreach($modifier_list as $modifier_key => $modifier){
                                $token[$is_variable]['parse'] .= $token[$is_variable]['variable']['operator'] . $modifier['parse'];
                            }
                        }
                        $is_variable = null;
                        $in_set = null;
                        $variable_nr = 0;
                        $variable = [];
                        continue;
                    }
                    if(empty($variable[$variable_nr])){
                        $variable[$variable_nr] = [];
                        $record['type'] = Token::TYPE_MODIFIER;
                    }
                    $variable[$variable_nr][] = $record;
                    if(
                        $set_depth > 0 &&
                        $in_set === null
                    ){
                        $in_set = true;
                    }
                    elseif($in_set === null){
                        $in_set = false;
                    }
                    unset($token[$nr]);
                }
            }
        }
        return $token;
    }

    public static function to_string($token=[]): string
    {
        $string = '';
        foreach($token as $record){
            $string .= $record['value'];
        }
        return $string;
    }

    public static function filter($token=[], $filter=[]){
        if(
            array_key_exists('where', $filter) &&
            !empty($filter['where']) &&
            is_array(($filter['where']))
        ){
            foreach($filter['where'] as $where){
                if(
                    array_key_exists('key', $where) &&
                    array_key_exists($where['key'], $where) &&
                    array_key_exists('operator', $where)
                ){
                    if(
                        $where['operator'] === 'in.array' &&
                        !empty($where[$where['key']]) &&
                        is_array($where[$where['key']])
                    ){
                        foreach($token as $nr => $record){
                            if(
                                !in_array(
                                    $record[$where['key']],
                                    $where[$where['key']],
                                    true
                                )
                            ){
                                 unset($token[$nr]);
                            }
                        }
                    }
                    elseif(
                        $where['operator'] === '!in.array' &&
                        !empty($where[$where['key']]) &&
                        is_array($where[$where['key']])
                    ){
                        foreach($token as $nr => $record){
                            if(
                                in_array(
                                    $record[$where['key']],
                                    $where[$where['key']],
                                    true
                                )
                            ){
                                unset($token[$nr]);
                            }
                        }
                    }
                    elseif(
                        $where['operator'] === '===' &&
                        !empty($where[$where['key']])
                    ){
                        foreach($token as $nr => $record){
                            if($record[$where['key']] !== $where[$where['key']]){
                                unset($token[$nr]);
                            }
                        }
                    }
                    elseif(
                        $where['operator'] === '!==' &&
                        !empty($where[$where['key']])
                    ){
                        foreach($token as $nr => $record){
                            if($record[$where['key']] === $where[$where['key']]){
                                unset($token[$nr]);
                            }
                        }
                    }
                }
            }
        }
        return $token;
    }

    /**
     * @throws Exception
     */
    public static function prepare($token=[], $count=0, $options=[]): array
    {
        $hex = null;
        $start = null;
        $skip = 0;
        $skip_unset = 0;
        $depth = 0;
        $array_depth = 0;
        $curly_count = 0;
        $parenthese_open = null;
        $quote_single = null;
        $quote_single_toggle = false;
        $quote_double = null;
        $quote_double_toggle = false;
        $previous_nr = null;
        $method_nr = null;
        $variable_nr = null;
        $variable_array_value = null;
        $variable_array_start = null;
        $variable_array_depth = 0;
        $variable_array_level = 0;
        $value = null;
        $comment_open_nr = null;
        $doc_comment_open_nr = null;
        $comment_single_line_nr = null;
        $is_tag_close_nr = null;
        $tag_close = '';
        if(array_key_exists('debug', $options)){
//            d($token);
        }
        foreach($token as $nr => $record){
            $record['depth'] = $depth;
            $token[$nr]['depth'] = $depth;
            $next = null;
            $next_next = null;
            if($skip > 0){
                $skip--;
                $previous_nr = $nr;
                continue;
            }
            if($skip_unset > 0){
                unset($token[$nr]);
                $skip_unset--;
                continue;
            }
            if($nr < ($count - 1)){
                $next = $nr + 1;
            }
            if($nr < ($count - 2)){
                $next_next = $nr + 2;
            }
            $record['curly_count'] = $curly_count;
            if($record['type'] === Token::TYPE_CURLY_OPEN){
                $curly_count++;
                $record['curly_count'] = $curly_count;
            }
            elseif($record['type'] === Token::TYPE_CURLY_CLOSE){
                $curly_count--;
            }
            if($record['curly_count'] > 0){
                if(
                    $record['type'] === Token::TYPE_COMMENT_CLOSE &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    if($comment_open_nr !== null){
                        $token[$comment_open_nr]['value'] .= $record['value'];
                        $comment_open_nr = null;
                        unset($token[$nr]);
                        $previous_nr = $comment_open_nr;
                        continue;
                    }
                    elseif($doc_comment_open_nr !== null){
                        $token[$doc_comment_open_nr]['value'] .= $record['value'];
                        $doc_comment_open_nr = null;
                        unset($token[$nr]);
                        $previous_nr = $doc_comment_open_nr;
                        continue;
                    }
                }
                elseif($comment_open_nr !== null){
                    $token[$comment_open_nr]['value'] .= $record['value'];
                    unset($token[$nr]);
                    $previous_nr = $comment_open_nr;
                    continue;
                }
                elseif($doc_comment_open_nr !== null){
                    $token[$doc_comment_open_nr]['value'] .= $record['value'];
                    unset($token[$nr]);
                    $previous_nr = $doc_comment_open_nr;
                    continue;
                }
                elseif($comment_single_line_nr !== null){
                    if(
                        $record['type'] === Token::TYPE_WHITESPACE &&
                        stristr($record['value'], "\n") !== false
                    ){
                        $comment_single_line_nr = null;
                    } else {
                        $token[$comment_single_line_nr]['value'] .= $record['value'];
                        unset($token[$nr]);
                        $previous_nr = $nr;
                        continue;
                    }
                }
                elseif($is_tag_close_nr !== null){
                    if(
                        in_array(
                            $record['type'],
                            Token::TYPE_NAME_BREAK,
                            true
                        )
                    ){
                        $token[$is_tag_close_nr]['tag']['name'] = strtolower($tag_close);
                        $is_tag_close_nr = null;
                    } else {
                        $tag_close .= $record['value'];
                        $token[$is_tag_close_nr]['value'] .= $record['value'];
                        unset($token[$nr]);
                        $previous_nr = $is_tag_close_nr;
                        continue;
                    }
                }
                elseif($variable_nr !== null){
                    if(
                        $quote_double_toggle === false &&
                        $quote_single_toggle === false &&
                        $variable_array_depth ===0 &&
                        in_array(
                            $record['type'],
                            [
                                Token::TYPE_PARENTHESE_OPEN, //used by modifier
                                Token::TYPE_PARENTHESE_CLOSE,
                                Token::TYPE_COMMA,
                                Token::TYPE_CURLY_CLOSE,
                                Token::TYPE_CURLY_OPEN
                            ],
                            true
                        )
                    ){
                        $variable_nr = null;
                        $variable_array_level = 0;
                    }
                    if(
                        $quote_double_toggle === false &&
                        $quote_single_toggle === false &&
                        in_array(
                            $record['type'],
                            [
                                Token::TYPE_BRACKET_SQUARE_OPEN
                            ],
                            true
                        )
                    ){
                        if($variable_array_depth === 0){
                            $variable_array_start = $nr;
                        }
                        $variable_array_value .= $record['value'];
                        $variable_array_depth++;
                        $array_depth++;
                        $token[$nr]['array_depth'] = $array_depth;
                        $previous_nr = $nr;
                        continue;
                    }
                    elseif(
                        $quote_double_toggle === false &&
                        $quote_single_toggle === false &&
                        in_array(
                            $record['type'],
                            [
                                Token::TYPE_BRACKET_SQUARE_CLOSE
                            ],
                            true
                        )
                    ){
                        $variable_array_depth--;
                        $array_depth--;
                        $token[$nr]['array_depth'] = $array_depth;
                        if($variable_array_depth === 0){
                            $token[$variable_nr]['variable']['is_array'] = true;
                            for($i = $variable_array_start; $i <= $nr; $i++){
                                if(array_key_exists($i, $token)){
                                    if($token[$i]['type'] === Token::TYPE_BRACKET_SQUARE_OPEN){
                                        $array_depth++;
                                        $token[$i]['array_depth'] = $array_depth;
                                        if($variable_array_depth === 0){
                                            unset($token[$i]);
                                        } else {
                                            $token[$variable_nr]['variable']['array'][$variable_array_level][] = $token[$i];
                                            unset($token[$i]);
                                        }
                                        $variable_array_depth++;
                                    }
                                    elseif($token[$i]['type'] === Token::TYPE_BRACKET_SQUARE_CLOSE){
                                        $variable_array_depth--;
                                        $array_depth--;
                                        $token[$i]['array_depth'] = $array_depth;
                                        if($variable_array_depth === 0){
                                            if(array_key_exists('array', $token[$variable_nr]['variable'])){
                                                if(array_key_exists($variable_array_level, $token[$variable_nr]['variable']['array'])){
                                                    $prepare = $token[$variable_nr]['variable']['array'][$variable_array_level];
                                                    $prepare = [
                                                        [
                                                            'type' => Token::TYPE_CURLY_OPEN,
                                                            'value' => '{',
                                                            'is_operator' => false
                                                        ],
                                                        ...$prepare,
                                                        [
                                                            'type' => Token::TYPE_CURLY_CLOSE,
                                                            'value' => '}',
                                                            'is_operator' => false
                                                        ]
                                                    ];
                                                    $prepare = Token::prepare(
                                                        $prepare,
                                                        count($prepare)
                                                    );
                                                    $prepare = Token::define($prepare);
                                                    $prepare = Token::group($prepare);
                                                    $prepare = Token::cast($prepare);
                                                    $prepare = Token::method($prepare);
                                                    array_shift($prepare); // remove curly_open
                                                    array_pop($prepare); //remove curly_close
                                                    $token[$variable_nr]['variable']['array'][$variable_array_level] = $prepare;
                                                } else {
                                                    $token[$variable_nr]['variable']['array'][$variable_array_level][] = [
                                                        'type' => Token::TYPE_NULL,
                                                        'value' => 'null',
                                                        'execute' => null,
                                                        'is_operator' => false
                                                    ];
                                                }
                                            } else {
                                                $token[$variable_nr]['variable']['array'][$variable_array_level][] = [
                                                    'type' => Token::TYPE_NULL,
                                                    'value' => 'null',
                                                    'execute' => null,
                                                    'is_operator' => false
                                                ];
                                            }
                                            $variable_array_level++;
                                            unset($token[$i]);
                                        } else {
                                            $token[$variable_nr]['variable']['array'][$variable_array_level][] = $token[$i];
                                            unset($token[$i]);
                                        }
                                    } else {
                                        if(
                                            array_key_exists('variable', $token[$i]) &&
                                            $token[$i]['type'] !== Token::TYPE_VARIABLE
                                        ){
                                            $token[$i]['type'] = Token::TYPE_VARIABLE;
                                        }
                                        $token[$variable_nr]['variable']['array'][$variable_array_level][] = $token[$i];
                                        unset($token[$i]);
                                    }
                                }
                            }
                            //empty array
                            if(!array_key_exists('array', $token[$variable_nr]['variable'])){
                                $token[$variable_nr]['variable']['array'][$variable_array_level] = null;
                            }
                            $variable_array_start = null;
                        }
                        $variable_array_value .= $record['value'];
                        $previous_nr = $nr;
                        continue;
                    }
                    elseif(
                        $variable_array_depth > 0 &&
                        $quote_double_toggle === false &&
                        $quote_single_toggle === false
                    ){
                        $variable_array_value .= $record['value'];
                        $previous_nr = $nr;
                        continue;
                    }
                    if(
                        $next !== null &&
                        $variable_nr !== null &&
                        $token[$next]['is_operator'] === true &&
                        $quote_double_toggle === false &&
                        $quote_single_toggle === false
                    ){
                        if($token[$next]['value'] === '|'){
                            $value .= $record['value'];
                            $token[$variable_nr]['variable']['name'] .= $record['value'];
                            $token[$variable_nr]['variable']['attribute'] .= $record['value'];
                            $token[$variable_nr]['variable']['operator'] = $token[$next]['value'];
                            $check_1 = $next + 1;
                            $check_2 = $next + 2;
                            if(
                                isset($token[$check_1]) &&
                                isset($token[$check_2]) &&
                                $token[$check_1]['type'] === Token::TYPE_WHITESPACE &&
                                $token[$check_2]['type'] === Token::TYPE_STRING
                            ){
                                $token[$variable_nr]['variable']['has_modifier'] = true;
                            }
                            elseif(
                                isset($token[$check_1]) &&
                                $token[$check_1]['type'] === Token::TYPE_STRING
                            ){
                                $token[$variable_nr]['variable']['has_modifier'] = true;
                            } else {
                                $token[$variable_nr]['variable']['has_modifier'] = false;
                            }
                            $token[$variable_nr]['value'] = $value;
                            $variable_nr = null;
                            $variable_array_level = 0;
                            $skip += 1;
                            unset($token[$nr]);
                            $previous_nr = $nr;
                            continue;
                        }
                        elseif(
                            in_array(
                                $token[$next]['value'],
                                Token::TYPE_ASSIGN,
                                true
                            )
                        ){
                            if($record['type'] !== Token::TYPE_WHITESPACE){
                                $value .= $record['value'];
                                $token[$variable_nr]['variable']['name'] .= $record['value'];
                                $token[$variable_nr]['variable']['attribute'] .= $record['value'];
                            }
                            if($variable_array_value){
                                $value .= $variable_array_value;
                            }
                            $token[$variable_nr]['variable']['is_assign'] = true;
                            $token[$variable_nr]['variable']['operator'] = $token[$next]['value'];
                            $token[$variable_nr]['value'] = $value;
                            $token[$variable_nr]['parse'] = $value . ' ' . $token[$variable_nr]['variable']['operator'] . ' ';
                            unset($token[$variable_nr]['variable']['has_modifier']);
                            $variable_nr = null;
                            $variable_array_level = 0;
                            $skip_unset += 1;
                            unset($token[$nr]);
                            $previous_nr = $nr;
                            continue;
                        } else {
                            if(
                                !in_array(
                                    $record['type'],
                                    [
                                        Token::TYPE_WHITESPACE,
                                        Token::TYPE_CURLY_CLOSE
                                    ],
                                    true
                                )
                            ){
                                $value .= $record['value'];
                                $token[$variable_nr]['variable']['name'] .= $record['value'];
                                $token[$variable_nr]['variable']['attribute'] .= $record['value'];
                                $token[$variable_nr]['value'] = $value;
                            }
                            unset($token[$variable_nr]['variable']['has_modifier']);
                            $variable_nr = null;
                            $variable_array_level = 0;
                            $skip += 1;
                            if($record['type'] !== Token::TYPE_CURLY_CLOSE){
                                unset($token[$nr]);
                            }
                            $previous_nr = $nr;
                            continue;
                        }
                    }
                    elseif(
                        $next !== null &&
                        $next_next !== null &&
                        $variable_nr !== null &&
                        $token[$next]['type'] === Token::TYPE_WHITESPACE &&
                        $token[$next_next]['is_operator'] === true
                    ){
                        if($token[$next_next]['value'] === '|'){
                            $value .= $record['value'];
                            $token[$variable_nr]['variable']['name'] .= $record['value'];
                            $token[$variable_nr]['variable']['attribute'] .= $record['value'];
                            $token[$variable_nr]['variable']['operator'] = $token[$next_next]['value'];
                            $check_1 = $next_next + 1;
                            $check_2 = $next_next + 2;
                            if(
                                isset($token[$check_1]) &&
                                isset($token[$check_2]) &&
                                $token[$check_1]['type'] === Token::TYPE_WHITESPACE &&
                                $token[$check_2]['type'] === Token::TYPE_STRING
                            ){
                                $token[$variable_nr]['variable']['has_modifier'] = true;
                            }
                            elseif(
                                isset($token[$check_1]) &&
                                $token[$check_1]['type'] === Token::TYPE_STRING
                            ){
                                $token[$variable_nr]['variable']['has_modifier'] = true;
                            } else {
                                $token[$variable_nr]['variable']['has_modifier'] = false;
                            }
                            $token[$variable_nr]['value'] = $value;
                            $variable_nr = null;
                            $variable_array_level = 0;
                            $skip += 1;
                            unset($token[$nr]);
                            $previous_nr = $nr;
                            continue;
                        }
                        elseif(
                            in_array(
                                $token[$next_next]['value'],
                                Token::TYPE_ASSIGN,
                                true
                            )
                        ){
                            $value .= $record['value'];
                            $token[$variable_nr]['variable']['name'] .= $record['value'];
                            $token[$variable_nr]['variable']['attribute'] .= $record['value'];
                            $token[$variable_nr]['variable']['is_assign'] = true;
                            $token[$variable_nr]['variable']['operator'] = $token[$next_next]['value'];
                            $token[$variable_nr]['variable']['operator_whitespace'] = $token[$next];
                            $token[$variable_nr]['value'] = $value;
                            $token[$variable_nr]['parse'] = $value . ' ' . $token[$variable_nr]['variable']['operator'] . ' ';
                            unset($token[$variable_nr]['variable']['has_modifier']);
                            $variable_nr = null;
                            $variable_array_level = 0;
                            $skip_unset += 2;
                            unset($token[$nr]);
                            $previous_nr = $nr;
                            continue;
                        } else {
                            if($record['type'] !== Token::TYPE_CURLY_CLOSE){
                                $value .= $record['value'];
                                $token[$variable_nr]['variable']['name'] .= $record['value'];
                                $token[$variable_nr]['variable']['attribute'] .= $record['value'];
                                $token[$variable_nr]['value'] = $value;
                            }
                            unset($token[$variable_nr]['variable']['has_modifier']);
                            $variable_nr = null;
                            $variable_array_level = 0;
                            $skip += 2;
                            if($record['type'] !== Token::TYPE_CURLY_CLOSE){
                                unset($token[$nr]);
                            }
                            $previous_nr = $nr;
                            continue;
                        }
                    }
                    elseif(
                        (
                            in_array(
                                $record['type'],
                                Token::TYPE_NAME_BREAK,
                                true
                            ) ||
                            $record['is_operator'] === true
                        ) &&
                        $quote_double_toggle === false &&
                        $quote_single_toggle === false
                    ){
                        $variable_nr = null;
                        $variable_array_level = 0;
                    }
                    elseif($variable_nr !== null) {
                        $token[$variable_nr]['variable']['name'] .= $record['value'];
                        $token[$variable_nr]['variable']['attribute'] .= $record['value'];
                        $value .= $record['value'];
                        $token[$variable_nr]['value'] = $value;
                        unset($token[$nr]);
                        $previous_nr = $nr;
                        continue;
                    }
                }
                if(
                    $record['type'] === Token::TYPE_VARIABLE &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    $variable_nr = $nr;
                    $token[$variable_nr]['variable']['name'] = $record['value'];
                    $token[$variable_nr]['variable']['attribute'] = substr($record['value'], 1);
                    $token[$variable_nr]['variable']['is_assign'] = false;
                    $value = $record['value'];
                    $variable_array_value = '';
                    $previous_nr = $nr;
                    continue;
                }
                elseif(
                    $record['type'] === Token::TYPE_COMMENT &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    $comment_open_nr = $nr;
                    $previous_nr = $nr;
                    continue;
                }
                elseif(
                    $record['type'] === Token::TYPE_DOC_COMMENT &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    $doc_comment_open_nr = $nr;
                    $previous_nr = $nr;
                    continue;
                }
                elseif(
                    (
                        $record['type'] === Token::TYPE_COMMENT_SINGLE_LINE &&
                        $quote_single_toggle === false &&
                        $quote_double_toggle === false &&
                        !isset($previous_nr)
                    ) ||
                    (
                        $record['type'] === Token::TYPE_COMMENT_SINGLE_LINE &&
                        $quote_single_toggle === false &&
                        $quote_double_toggle === false &&
                        isset($previous_nr) &&
                        $token[$previous_nr]['type'] !== Token::TYPE_COLON //make exception for uris
                    )
                ){
                    $comment_single_line_nr = $nr;
                    $previous_nr = $nr;
                    continue;
                }
                elseif(
                    $record['type'] === Token::TYPE_IS_DIVIDE &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    $tag_close = $record['value'];
                    if(
                        $next !== null &&
                        $token[$next]['type'] === Token::TYPE_STRING
                    ){
                        $is_tag_close_nr = $nr;
                        $tag_close .= $token[$next]['value'];
                        $token[$nr]['value'] .= $token[$next]['value'];
                        $token[$nr]['type'] = Token::TYPE_TAG_CLOSE;
                        $token[$nr]['is_operator'] = false;
                        $token[$nr]['tag']['name'] = strtolower($tag_close);
                        $previous_nr = $nr;
                        $skip_unset += 1;
                    }
                    elseif(
                        $next !== null &&
                        $token[$next]['type'] === Token::TYPE_WHITESPACE &&
                        $token[$next_next]['type'] === Token::TYPE_STRING
                    ){
                        $is_tag_close_nr = $nr;
                        $tag_close .= $token[$next_next]['value'];
                        $token[$nr]['value'] .=
                            $token[$next]['value'] .
                            $token[$next_next]['value'];
                        $token[$nr]['type'] = Token::TYPE_TAG_CLOSE;
                        $token[$nr]['is_operator'] = false;
                        $token[$nr]['tag']['name'] = strtolower($tag_close);
                        $previous_nr = $nr;
                        $skip_unset += 2;
                    }
                }
                if(
                    $record['value'] === '\'' &&
                    $quote_double_toggle === false
                ){
                    if($quote_single_toggle === false){
                        $quote_single_toggle = true;
                    } else {
                        $quote_single_toggle = false;
                    }
                }
                elseif(
                    $record['value'] === '"' &&
                    $quote_single_toggle === false
                ){
                    if($quote_double_toggle === false){
                        $quote_double_toggle = true;
                    } else {
                        $quote_double_toggle = false;
                    }
                }
                if($quote_single_toggle === true){
                    if($quote_single === null){
                        $quote_single = $record;
                        $quote_single['nr'] = $nr;
                        $previous_nr = $nr;
                        continue;
                    }
                    if($record['value'] === '\\' && $next !== null && $token[$next]['value'] === '\''){
                        $quote_single['value'] .= $record['value'] . $token[$next]['value'];
                        $skip += 1;
                        $previous_nr = $nr;
                        continue;
                    } else {
                        $quote_single['value'] .= $record['value'];
                        $previous_nr = $nr;
                        continue;
                    }
                } else {
                    if($quote_single !== null){
                        $quote_single['value'] .= $record['value'];
                        $token[$quote_single['nr']]['type'] = Token::TYPE_QUOTE_SINGLE_STRING;
                        $token[$quote_single['nr']]['value'] = $quote_single['value'];
                        for($i = ($quote_single['nr'] + 1); $i <= $nr; $i++){
                            unset($token[$i]);
                        }
                        $token[$quote_single['nr']]['execute'] = str_replace(['\\\'', '\\\\'],['\'', '\\'], substr($token[$quote_single['nr']]['value'], 1, -1));
                        $token[$quote_single['nr']]['is_executed'] = true;
                        $token[$quote_single['nr']]['is_quote_single'] = true;
                        $quote_single = null;
                        $previous_nr = $nr;
                        continue;
                    }
                }
                if($quote_double_toggle === true){
                    if($quote_double === null){
                        $quote_double = $record;
                        $quote_double['nr'] = $nr;
                        $previous_nr = $nr;
                        continue;
                    }
                    if($record['value'] === '\\' && $next !== null && $token[$next]['value'] === '"'){
                        $skip += 1;
                        $previous_nr = $nr;
                        if(
                            !empty($quote_double) &&
                            array_key_exists('value', $quote_double)
                        ){
                            $quote_double['value'] .= '\"';
                        }
                        continue;
                    } else {
                        $quote_double['value'] .= $record['value'];
                        $previous_nr = $nr;
                        continue;
                    }
                } else {
                    if($quote_double !== null){
                        $quote_double['value'] .= $record['value'];
                        $token[$quote_double['nr']]['type'] = Token::TYPE_QUOTE_DOUBLE_STRING;
                        $token[$quote_double['nr']]['value'] = $quote_double['value'];
                        for($i = ($quote_double['nr'] + 1); $i <= $nr; $i++){
                            unset($token[$i]);
                        }
                        $token[$quote_double['nr']]['is_quote_double'] = true;
                        $quote_double = null;
                        $previous_nr = $nr;
                        continue;
                    }
                }
                if($record['type'] === Token::TYPE_STRING){
                    if($record['value'] === 'null'){
                        $token[$nr]['execute'] = null;
                        $token[$nr]['is_executed'] = true;
                        $token[$nr]['type'] = Token::TYPE_NULL;
                    }
                    elseif($record['value'] === 'true'){
                        $token[$nr]['execute'] = true;
                        $token[$nr]['is_executed'] = true;
                        $token[$nr]['type'] = Token::TYPE_BOOLEAN;
                    }
                    elseif($record['value'] === 'false'){
                        $token[$nr]['execute'] = false;
                        $token[$nr]['is_executed'] = true;
                        $token[$nr]['type'] = Token::TYPE_BOOLEAN;
                    }
                    elseif(
                        Token::is_hex($record['value']) &&
                        $hex === null
                    ){
                        $token[$nr]['execute'] = strtoupper($record['value']);
                        $token[$nr]['is_executed'] = true;
                        $token[$nr]['type'] = Token::TYPE_HEX;
                        $hex = $token[$nr];
                        $start = $nr;
                        $previous_nr = $nr;
                        continue;
                    }
                }
                elseif($record['type'] === Token::TYPE_PARENTHESE_OPEN){
                    if($record['curly_count'] > 0){
                        $depth++;
                    }
                }
                $token[$nr]['depth'] = $depth;
                if($record['type'] === Token::TYPE_PARENTHESE_CLOSE){
                    if($record['curly_count'] > 0){
                        $depth--;
                    } else {
                        $previous_nr = $nr;
                        continue; //no curly tags means no method
                    }
                    $is_start_method = false;
                    $is_whitespace = false;
                    $before_reverse = [];
                    for($i = $nr; $i >= 0; $i--){
                        if(isset($token[$i])){
                            if(
                                $token[$i]['type'] === Token::TYPE_PARENTHESE_OPEN &&
                                $token[$i]['depth'] === $token[$nr]['depth']
                            ){
                                $is_start_method = true;
                                continue;
                            }
                            if($is_start_method === false){
                                continue;
                                //catch parameter?
                            } else {
                                if(
                                    $is_whitespace === false &&
                                    !isset($before_reverse[0]) &&
                                    $token[$i]['type'] === Token::TYPE_WHITESPACE
                                ){
                                    $is_whitespace = true;
                                    continue;
                                }
                                elseif(
                                    in_array(
                                        $token[$i]['type'],
                                        Token::TYPE_NAME_BREAK_METHOD,
                                        true
                                    ) ||
                                    $token[$i]['is_operator'] === true &&
                                    $token[$i]['type'] !== Token::TYPE_COLON
                                ){
                                    break;
                                }
                                $before_reverse[] = $token[$i]['value'];
                                $method_nr = $i;
                            }
                        }
                    }
                    if(
                        $method_nr !== null &&
                        isset($before_reverse[0]) &&
                        $token[$method_nr]['type'] !== Token::TYPE_VARIABLE
                    ){
                        $value = implode('', array_reverse($before_reverse));
                        $explode = explode(':', $value);
                        $method_count = count($explode);
                        $token[$method_nr]['type'] = Token::TYPE_METHOD;
                        if($method_count === 1){
                            $token[$method_nr]['method']['namespace'] = null;
                            $token[$method_nr]['method']['trait'] = null;
                            $token[$method_nr]['method']['name'] = strtolower(trim($value));
                        } elseif($method_count === 2) {
                            $token[$method_nr]['method']['namespace'] = null;
                            $token[$method_nr]['method']['trait'] = $explode[0];
                            $token[$method_nr]['method']['name'] = strtolower(trim($explode[1]));
                        } elseif($method_count === 3){
                            $temp = explode('.', $explode[0]);
                            $temp_count = count($temp);
                            if(strtolower($temp[$temp_count - 1]) !== 'trait'){
                                $temp[] = 'Trait';
                            }
                            $token[$method_nr]['method']['namespace'] = implode('.', $temp);
                            $token[$method_nr]['method']['trait'] = $explode[1];
                            $token[$method_nr]['method']['name'] = strtolower(trim($explode[2]));
                        } else {
                            throw new Exception('wrong amount of ":" in traited function');
                        }
                        //add attributes...
                    }
                }
                if($hex){
                    $is_hex = Token::is_hex($record['value']);
                    if($is_hex){
                        $hex['value'] .= $record['value'];
                        $hex['execute'] .= strtoupper($record['value']);
                        unset($token[$nr]);
                        $previous_nr = $nr;
                        continue;
                    } else {
                        $token[$start] = $hex;
                        $start = null;
                        $hex = null;
                    }
                }
                if(
                    $record['type'] === Token::TYPE_NUMBER &&
                    $next !== null &&
                    $next_next !== null &&
                    $token[$next]['type'] === Token::TYPE_DOT &&
                    $token[$next_next]['type'] === Token::TYPE_NUMBER
                ){
                    $token[$nr]['value'] .= $token[$next]['value'] . $token[$next_next]['value'];
                    $token[$nr]['type'] = Token::TYPE_FLOAT;
                    $token[$nr]['execute'] = $token[$nr]['value'] + 0;
                    $token[$nr]['is_executed'] = true;
                    if(
                        isset($previous_nr) &&
                        isset($token[$previous_nr]) &&
                        $token[$previous_nr]['type'] === Token::TYPE_IS_MINUS
                    ){
                        $token[$nr]['execute'] = -$token[$nr]['execute'];
                        $token[$nr]['value'] = '-' . $token[$nr]['value'];
                        unset($token[$previous_nr]);
                    }
                    $skip_unset += 2;
                    $previous_nr = $nr;
                    continue;
                }
                elseif(
                    $record['value'] == '0' &&
                    $next !== null &&
                    $token[$next]['type'] === Token::TYPE_STRING &&
                    strtolower(substr($token[$next]['value'], 0, 1)) == 'x'
                ){
                    $hex = $record;
                    $hex['value'] .= substr($token[$next]['value'], 0, 1);
                    $hex['execute'] = $record['value'] . 'x';
                    $hex['is_executed'] = true;
                    $tmp = $token[$next];
                    $tmp['value'] = substr($token[$next]['value'], 1);
                    if(!empty($tmp['value'])){
                        $is_hex = Token::is_hex($tmp['value']);
                        if($is_hex){
                            $hex['value'] .= $tmp['value'];
                            $hex['execute'] .= strtoupper($tmp['value']);
                            $hex['type'] = Token::TYPE_HEX;
                            if(
                                isset($previous_nr) &&
                                isset($token[$previous_nr]) &&
                                $token[$previous_nr]['type'] === Token::TYPE_IS_MINUS
                            ){
                                $hex['execute'] = '-' . $hex['execute'];
                                $hex['value'] = '-' . $hex['value'];
                                unset($token[$previous_nr]);
                            }
                            $start = $nr;
                            $skip_unset += 1;
                            $previous_nr = $nr;
                            continue;
                        } else {
                            $hex = null;
                        }
                    }
                }
                /* wrong interpertation of octal.... only in string \[0-7]{1,3} or parameter
                 elseif(
                 $record['type'] == Token::TYPE_NUMBER &&
                 substr($record['value'], 0, 1) == '0' &&
                 strlen($record['value']) > 1
                 ){
                 //octal
                 $token[$nr]['execute'] = $record['value'];
                 $token[$nr]['type'] = Token::TYPE_OCT;
                 if(
                 isset($previous_nr) &&
                 isset($token[$previous_nr]) &&
                 $token[$previous_nr]['type'] == Token::TYPE_IS_MINUS
                 ){
                 $token[$nr]['execute'] = -$token[$nr]['execute'];
                 $token[$nr]['value'] = '-' . $token[$nr]['value'];
                 unset($token[$previous_nr]);
                 }
                 }*/
                elseif(
                    $record['type'] === Token::TYPE_NUMBER
                ) {
                    if(
                        //hex
                        isset($previous_nr) &&
                        array_key_exists($previous_nr, $token) &&
                        $token[$previous_nr]['type'] === Token::TYPE_STRING &&
                        Token::is_hex($token[$previous_nr]['value'])
                    ){
                        $hex = $token[$previous_nr];
                        $hex['type'] = Token::TYPE_HEX;
                        $hex['execute'] = strtoupper($token[$previous_nr]['value']);
                        $hex['execute'] .= $record['value'];
                        $hex['value'] .= $record['value'];
                        $start = $previous_nr;
                        unset($token[$nr]);
                    }
                    elseif(
                        //hex
                        isset($token[$next]) &&
                        array_key_exists($next, $token) &&
                        $token[$next]['type'] === Token::TYPE_STRING &&
                        Token::is_hex($token[$next]['value'])
                    ){
                        $hex = $record;
                        $hex['type'] = Token::TYPE_HEX;
                        $hex['execute'] = (string) $record['value'];
                        $hex['value'] .= $token[$next]['value'];
                        $hex['execute'] .= strtoupper($token[$next]['value']);
                        $skip_unset += 1;
                        $start = $nr;
                    }
                    else {
                        //int
                        $token[$nr]['execute'] = $record['value'] + 0;
                        $token[$nr]['is_executed'] = true;
                        $token[$nr]['type'] = Token::TYPE_INT;
                        if(
                            isset($previous_nr) &&
                            isset($token[$previous_nr]) &&
                            $token[$previous_nr]['type'] === Token::TYPE_IS_MINUS
                        ){
                            $token[$nr]['execute'] = -$token[$nr]['execute'];
                            $token[$nr]['value'] = '-' . $token[$nr]['value'];
                            unset($token[$previous_nr]);
                        }
                    }
                }
            } else {
                if(
                    $record['type'] === Token::TYPE_COMMENT_CLOSE &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    if($comment_open_nr !== null){
                        $token[$comment_open_nr]['value'] .= $record['value'];
                        unset($token[$comment_open_nr]); //@2024-07-21 added, don't want comment in generated code
//                        d($token[$comment_open_nr]);
                        $comment_open_nr = null;
                        unset($token[$nr]);
                        $previous_nr = $comment_open_nr;
                        continue;
                    }
                    elseif($doc_comment_open_nr !== null){
                        $token[$doc_comment_open_nr]['value'] .= $record['value'];
                        unset($token[$doc_comment_open_nr]); //@2024-07-21 added, don't want comment in generated code
                        $doc_comment_open_nr = null;
                        unset($token[$nr]);
                        $previous_nr = $doc_comment_open_nr;
                        continue;
                    }
                }
                elseif($comment_open_nr !== null){
                    $token[$comment_open_nr]['value'] .= $record['value'];
                    unset($token[$nr]);
                    $previous_nr = $comment_open_nr;
                    continue;
                }
                elseif($doc_comment_open_nr !== null){
                    $token[$doc_comment_open_nr]['value'] .= $record['value'];
                    unset($token[$nr]);
                    $previous_nr = $doc_comment_open_nr;
                    continue;
                }
                elseif($comment_single_line_nr !== null){
                    if(
                        $record['type'] === Token::TYPE_WHITESPACE &&
                        stristr($record['value'], "\n") !== false
                    ){
                        unset($token[$comment_single_line_nr]); //@2024-07-21 added, don't want comment in generated code
                        $comment_single_line_nr = null;
                    } else {
                        $token[$comment_single_line_nr]['value'] .= $record['value'];
                        unset($token[$nr]);
                        $previous_nr = $nr;
                        continue;
                    }
                }
                if(
                    $record['type'] === Token::TYPE_COMMENT &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    $comment_open_nr = $nr;
                    $previous_nr = $nr;
                    continue;
                }
                elseif(
                    $record['type'] === Token::TYPE_DOC_COMMENT &&
                    $quote_single_toggle === false &&
                    $quote_double_toggle === false
                ){
                    $doc_comment_open_nr = $nr;
                    $previous_nr = $nr;
                    continue;
                }
                elseif(
                    (
                        $record['type'] === Token::TYPE_COMMENT_SINGLE_LINE &&
                        $quote_single_toggle === false &&
                        $quote_double_toggle === false &&
                        !isset($previous_nr)
                    ) ||
                    (
                        $record['type'] === Token::TYPE_COMMENT_SINGLE_LINE &&
                        $quote_single_toggle === false &&
                        $quote_double_toggle === false &&
                        isset($previous_nr) &&
                        $token[$previous_nr]['type'] !== Token::TYPE_COLON //make exception for uris
                    )
                ){
                    $comment_single_line_nr = $nr;
                    $previous_nr = $nr;
                    continue;
                }
            }
            $previous_nr = $nr;
        }
        return $token;
    }

    /**
     * @throws Exception
     */
    public static function compare($record=[], $match=[], $options=[]): bool
    {
        if(array_key_exists('operator', $options)){
            switch ($options['operator']){
                case '===' :
                    $keys_record = array_keys($record);
                    $keys_match = array_keys($match);
                    foreach($keys_record as $key){
                        if(!in_array($key, $keys_match, true)){
                            return false;
                        }
                    }
                    foreach($keys_match as $key){
                        if(!in_array($key, $keys_record, true)){
                            return false;
                        }
                    }
                    if(
                        array_key_exists('whitespace', $options) &&
                        $options['whitespace'] === 'type' &&
                        $record['type'] === Token::TYPE_WHITESPACE
                    ){
                        return true;
                    } else {
                        foreach($record as $key => $value){

                            if($match[$key] !== $value){
                                return false;
                            }
                        }
                        return true;
                    }

                default:
                    throw new Exception('Compare: operator not found');
            }
        }
        return false;
    }

    private static function is_hex($hex=''): bool
    {
        if(strtolower($hex) == 'nan'){
            $hex = NAN;
        }
        return ctype_xdigit($hex);
    }

    private static function type($char=null): string
    {
        switch($char){
            case '.' :
                return Token::TYPE_DOT;
            case ',' :
                return Token::TYPE_COMMA;
            case '(' :
                return Token::TYPE_PARENTHESE_OPEN;
            case ')' :
                return Token::TYPE_PARENTHESE_CLOSE;
            case '[' :
                return Token::TYPE_BRACKET_SQUARE_OPEN;
            case ']' :
                return Token::TYPE_BRACKET_SQUARE_CLOSE;
            case '{' :
                return Token::TYPE_CURLY_OPEN;
            case '}' :
                return Token::TYPE_CURLY_CLOSE;
            case '$' :
                return Token::TYPE_VARIABLE;
            case '\'' :
                return Token::TYPE_QUOTE_SINGLE;
            case '"' :
                return Token::TYPE_QUOTE_DOUBLE;
            case '\\' :
                return Token::TYPE_BACKSLASH;
            case ';' :
                return Token::TYPE_SEMI_COLON;
            case '0' :
            case '1' :
            case '2' :
            case '3' :
            case '4' :
            case '5' :
            case '6' :
            case '7' :
            case '8' :
            case '9' :
                return Token::TYPE_NUMBER;
            case '>' :
            case '<' :
            case '=' :
            case '-' :
            case '+' :
            case '/' :
            case '*' :
            case '%' :
            case '^' :
            case '!' :
            case '?' :
            case '|' :
            case '&' :
            case ':' :
                return Token::TYPE_OPERATOR;
            case ' ' :
            case "\t" :
            case "\n" :
            case "\r" :
                return Token::TYPE_WHITESPACE;
            default:
                return Token::TYPE_STRING;
        }
    }
}