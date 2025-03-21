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

function function_block_code(Parse $parse, Data $data, $name='', $value=null){    
    if($value === null){
        $value = $name;
        $name = null;        
    }
    $explode = explode("\n", $value);
    foreach($explode as $nr => $value){
        $part = trim($value);
        if(empty($part)){
            unset($explode[$nr]);
        }
    }    
    $value = '{' . implode('}' . "\n" . '{', $explode) . '}';        
    if(empty($name)){
        $compile =  $parse->compile(
            $value,
            [],
            $data            
        );
        $content = $data->data('#content');
        $content[] = $compile;
        $data->data('#content', $content);
    } else {
        $data->data($name, $parse->compile(
            $value,
            [],
            $data            
        )); 
    }        
    return '';
}
