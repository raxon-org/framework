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

function function_dd(Parse $parse, Data $data, $debug=null){
    if(
        $debug !== true &&
        in_array(
            $debug,
            [
                '$this',
                '{$this}'
            ],
            true
        )
    ){
        $debug = $data->data();
    }
    $trace = debug_backtrace(1);
    if(!defined('IS_CLI')){
        echo '<pre class="priya-debug">' . PHP_EOL;
    }
    echo $trace[0]['file'] . ':' . $trace[0]['line'] . PHP_EOL;
    var_dump($debug);
    if(!defined('IS_CLI')){
        echo '</pre>' . PHP_EOL;
    }
    exit;    
}
