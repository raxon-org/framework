<?php

use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Sort;

function function_data_sort(Parse $parse, Data $data, $list, $sort=[], $options=[]){
    return Sort::list($list)->with($sort, $options);
}
