<?php
/**
 * @author          Remco van der Velde
 * @since           04-01-2019
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */

use Raxon\Module\Cli;

use Raxon\Exception\ObjectException;

if(!function_exists('d')){
    function d($data=null, $options=[]): void
    {
        if(!array_key_exists('trace', $options)){
            $options['trace'] = true;
        }
        $trace = debug_backtrace(1);
        if(ob_get_level() > 0){
            ob_end_flush();
        }
        if(!defined('IS_CLI')){
            echo '<pre class="priya-debug">' . PHP_EOL;
        }
        if(
            array_key_exists('trace', $options) &&
            $options['trace'] === true
        ){
            echo $trace[0]['file'] . ':' . $trace[0]['line'] . PHP_EOL;
        }
        elseif(
            array_key_exists('trace', $options)
        ){
            echo $options['trace'];
        }
        var_dump($data);
        if(!defined('IS_CLI')){
            echo '</pre>' . PHP_EOL;
        }
        flush();
    }
}

if(!function_exists('breakpoint')){
    function breakpoint($data=null, $options=[]): void
    {
        if(!array_key_exists('trace', $options)){
            $options['trace'] = true;
        }
        $trace = debug_backtrace(1);
        if(ob_get_level() > 0){
            ob_end_flush();
        }
        if(!defined('IS_CLI')){
            echo '<pre class="priya-debug">' . PHP_EOL;
            if(
                array_key_exists('trace', $options) &&
                $options['trace'] === true
            ){
                echo $trace[0]['file'] . ':' . $trace[0]['line'] . PHP_EOL;
            }
            var_dump($data);
            echo '</pre>' . PHP_EOL;
            flush();
        } else {
            $export = var_export($data, true);
            try {
                if(
                    array_key_exists('trace', $options) &&
                    $options['trace'] === true
                ){
                    Cli::read('input-hidden',$trace[0]['file'] . ':' . $trace[0]['line'] . PHP_EOL . $export . PHP_EOL . 'Press '. Cli::info('enter') . ' to continue or ' . Cli::error('ctrl-c') . ' to break...');
                }
                elseif(
                    array_key_exists('trace', $options) &&
                    is_string($options['trace'])
                ){
                    Cli::read('input-hidden',$options['trace'] . $export . PHP_EOL . 'Press '. Cli::info('enter') . ' to continue or ' . Cli::error('ctrl-c') . ' to break...');
                } else {
                    Cli::read('input-hidden', $export . PHP_EOL . 'Press '. Cli::info('enter') . ' to continue or ' . Cli::error('ctrl-c') . ' to break...');
                }
            }
            catch(Exception | ObjectException $exception){
                echo (string) $exception;
            }
        }
    }
}

if(!function_exists('dd')){
    function dd($data=null, $options=[]): void
    {
        if(!array_key_exists('trace', $options)){
            $options['trace'] = true;
        }
        $trace = debug_backtrace(1);
        if(ob_get_level() > 0){
            ob_end_flush();
        }
        if(!defined('IS_CLI')){
            echo '<pre class="priya-debug">' . PHP_EOL;
        }
        if(
            array_key_exists('trace', $options) &&
            $options['trace'] === true
        ){
            echo $trace[0]['file'] . ':' . $trace[0]['line'] . PHP_EOL;
        }
        elseif(
            array_key_exists('trace', $options)
        ){
            echo $options['trace'];
        }
        var_dump($data);
        if(!defined('IS_CLI')){
            echo '</pre>' . PHP_EOL;
        }
        exit;
    }
}

if(!function_exists('ddd')){
    function ddd($data=null, $options=[]): void
    {
        if(!array_key_exists('trace', $options)){
            $options['trace'] = true;
        }
        $trace = debug_backtrace(1);
        if(!defined('IS_CLI')){
            echo '<pre class="priya-debug">';
        }
        if(
            array_key_exists('trace', $options) &&
            $options['trace'] === true
        ){
            echo $trace[0]['file'] . ':' . $trace[0]['line'] . PHP_EOL;
        }
        elseif(
            array_key_exists('trace', $options)
        ){
            echo $options['trace'];
        }
        if(!defined('IS_CLI')){
            echo '</pre>';
        }
        dd($data);
    }
}

if(!function_exists('trace')){
    function trace($length=null): void
    {
        $trace = debug_backtrace(1);
        if(!is_numeric($length)){
            $length = count($trace);
        }
        if(!defined('IS_CLI')){
            echo '<pre class="priya-trace">';
        }
        // don't need the first one (0)
        // we do, where did we put it...

        echo Cli::debug('Trace') . PHP_EOL;
        for($i = 0; $i < $length; $i++){
            if(array_key_exists($i, $trace)){
                if(
                    array_key_exists('file', $trace[$i]) &&
                    array_key_exists('line', $trace[$i]) &&
                    array_key_exists('function', $trace[$i])
                ){
                    $list[] = $trace[$i]['function'] . ':' . $trace[$i]['file'] .':' . $trace[$i]['line'];
                    echo cli::notice($trace[$i]['function']) . ':' . $trace[$i]['file'] .':' . $trace[$i]['line']  . PHP_EOL;
                }
                elseif(
                    array_key_exists('file', $trace[$i]) &&
                    array_key_exists('line', $trace[$i])
                ) {
                    echo $trace[$i]['file'] . ':' . $trace[$i]['line'] . PHP_EOL;
                }
            }
        }
        if(!defined('IS_CLI')){
            echo '</pre>' . PHP_EOL;
        }
    }
}
