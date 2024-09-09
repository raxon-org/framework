<?php

use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;
use Raxon\Org\Module\Sort;

function function_data_sort(Parse $parse, Data $data, $list, $sort=[], $options=[]){
    return Sort::list($list)->with($sort, $options);
}
