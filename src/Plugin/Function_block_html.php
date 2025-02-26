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

function function_block_html(Parse $parse, Data $data, $name='', $value=null){
    if($value === null){
        $value = $name;
        $name = null;
    }
    $search = [" ", "\t", "\n", "\r", "\r\n"];
    $replace = ['','','','',''];
    $content = trim($value);
    $content = explode('<', $content);
    foreach ($content as $nr => $row){
        $dataRow = explode('>', $row);
        if(count($dataRow)>=2){
            foreach ($dataRow as $dataRowNr => $dataR){
                if($dataRowNr > 0){
                    $tmp = str_replace($search, $replace, $dataR);
                } else {
                    $tmp = $dataR;
                }
                if(empty($tmp)){
                    $dataRow[$dataRowNr] = '';
                }
            }
            $content[$nr] = implode('>', $dataRow);
        }
    }
    $value = implode('<', $content);    
    if(empty($name)){
        return $value;
    } else {
        $data->data($name, $value);
        return '';
    }
}