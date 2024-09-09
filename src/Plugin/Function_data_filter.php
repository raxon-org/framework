<?php

use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;
use Raxon\Org\Module\Filter;

function function_data_filter(Parse $parse, Data $data, $list, $where=[]){
    return Filter::list($list)->where($where);
}
