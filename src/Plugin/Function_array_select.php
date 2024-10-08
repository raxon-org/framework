    <?php
/**
 * @author          Remco van der Velde
 * @since           2020-09-19
 * @copyright       Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *     -            all
 */
use Raxon\Module\Parse;
use Raxon\Module\Data;

function function_array_select(Parse $parse, Data $data, $array=[], $key=0){
    if(array_key_exists($key, $array)){
        return $array[$key];
    }
}
