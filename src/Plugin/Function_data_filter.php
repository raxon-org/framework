<?php

use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Filter;

function function_data_filter(Parse $parse, Data $data, $list, $where=[]){
    return Filter::list($list)->where($where);
}
