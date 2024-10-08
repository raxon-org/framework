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

function modifier_file_size(Parse $parse, Data $data, $value){
    $bytes = 1024;
    $value += 0;
    if($value > $bytes * $bytes * $bytes * $bytes * $bytes){
        $value = round($value / ($bytes * $bytes * $bytes * $bytes * $bytes), 2) . ' PB';
    }
    elseif($value > $bytes * $bytes * $bytes * $bytes){
        $value = round($value / ($bytes * $bytes * $bytes * $bytes), 2) . ' TB';
    }
    elseif($value > $bytes * $bytes * $bytes){
        $value = round($value / ($bytes * $bytes * $bytes), 2) . ' GB';
    }
    elseif($value > $bytes * $bytes){
        $value = round($value / ($bytes * $bytes), 2) . ' MB';
    }
    elseif($value > $bytes){
        $value = round($value / $bytes, 2) . ' KB';
    } else {
        $value .= ' B';
    }
    return $value;
}