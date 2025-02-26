<?php

use Raxon\Module\Parse;
use Raxon\Module\Data;
use Raxon\Module\Limit;

function function_data_limit(Parse $parse, Data $data, $list, $limit=[]){
    return Limit::list($list)->with($limit);
}
