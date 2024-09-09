<?php

use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;
use Raxon\Org\Module\Limit;

function function_data_limit(Parse $parse, Data $data, $list, $limit=[]){
    return Limit::list($list)->with($limit);
}
