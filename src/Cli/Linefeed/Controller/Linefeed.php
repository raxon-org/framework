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
namespace Raxon\Cli\Linefeed\Controller;

use Raxon\App;

use Raxon\Module\Cli;
use Raxon\Module\Dir;
use Raxon\Module\Event;
use Raxon\Module\File;
use Raxon\Module\Controller;

use Exception;

class Linefeed extends Controller {
    const DIR = __DIR__;
    const NAME = 'Linefeed';
    const INFO = '{{binary()}} linefeed                       | Linefeed';

    /**
     * @throws Exception
     */
    public static function run($object): string
    {
        $url = $object->config('controller.dir.data') . 'Linefeed' . $object->config('extension.json');
        $config = $object->data_read($url, sha1($url));
        $counter = 0;
        $directory = false;
        if($config){
            $directory = App::parameter($object, Linefeed::NAME, 1);
            while(empty($directory)){
                $directory = Cli::read('input', 'Input directory: ');
            }
            if(!Dir::is($directory)){
                $exception = new Exception('Not a directory.');
                Event::trigger($object, 'cli.' . strtolower(Linefeed::NAME) . '.' . __FUNCTION__, [
                    'directory' => $directory,
                    'counter' => $counter,
                    'exception' => $exception
                ]);
                throw $exception;
            }
            $dir = new Dir();
            $list = $dir->read($directory, true);
            foreach($list as $file){
                $extension = File::extension($file->url);
                if(
                    in_array(
                        $extension,
                        $config->get('extension'),
                        true
                    )
                ){
                    $read = File::read($file->url);
                    $explode = explode("\n", $read);
                    $is_write = false;
                    foreach($explode as $nr => $line){
                        if(substr($line, -1, 1) === "\r"){
                            $explode[$nr] = substr($line, 0, -1);
                            $is_write = true;
                        }
                    }
                    if($is_write){
                        $write = implode("\n", $explode);
                        File::write($file->url, $write);
                        $counter++;
                    }
                }
            }
        }
        Event::trigger($object, 'cli.' . strtolower(Linefeed::NAME) . '.' . __FUNCTION__, [
            'directory' => $directory,
            'counter' => $counter
        ]);
        return 'Linefeed: number of changes: ' . $counter . PHP_EOL;
    }
}