<?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-14
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Org\App;
use Raxon\Org\Module\Parse;
use Raxon\Org\Module\Data;

function function_plugin_prepend(Parse $parse, Data $data, $url=null){
    $config = $parse->object()->data(App::CONFIG);
    $plugin = $config->data('parse.dir.plugin');
    array_unshift($plugin, $url);
    $config->data('parse.dir.plugin', $plugin);
    return '';
}
