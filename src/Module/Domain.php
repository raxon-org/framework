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
namespace Raxon\Module;

use Raxon\App;
use Raxon\Config;

use Raxon\Node\Module\Node;

use Exception;

class Domain {
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    const PORT_DEFAULT = [
        80,
        443
    ];

    /**
     * @throws Exception
     */
    public static function configure(App $object): bool
    {
        if(defined('IS_CLI')){
            return false;
        }
        $key = 'domain.url';
        $value = Host::url();
        $object->config($key, $value);
        $port = Host::port();
        $key = 'domain.dir.root';
        $value = $object->config('project.dir.domain') .
            $object->config('host.name') .
            $object->config('ds')
        ;
        $value_with_port = $value;
        if(
            !in_array(
                $port,
                $object->config('server.default.port'),
                true
            )){
            $value_with_port .= $port . $object->config('ds');
        }
        if(File::exist($value_with_port)){
            $value = $value_with_port;
        }
        $object->config($key, $value);
        $key = 'domain.dir.data';
        $value =
            $object->config('domain.dir.root') .
            $object->config(Config::DICTIONARY . '.' . Config::DATA) .
            $object->config('ds');
        $value_with_port = $value;
        if(
            !in_array(
                $port,
                $object->config('server.default.port'),
                true
            )
        ){
            $value_with_port .= $port . $object->config('ds');
        }
        if(File::exist($value_with_port)){
            $value = $value_with_port;
        }
        $object->config($key, $value);
        $key = 'domain.dir.cache';
        $value =
            $object->config('framework.dir.temp') .
            $object->config(Config::DICTIONARY . '.' . Config::DOMAIN) .
            $object->config('ds');
        $value_with_port = $value;
        if(
            !in_array(
                $port,
                $object->config('server.default.port'),
                true
            )
        ){
            $value_with_port .= $port . $object->config('ds');
        }
        if(File::exist($value_with_port)){
            $value = $value_with_port;
        }
        $object->config($key, $value);
        $key = 'domain.dir.ramdisk';
        $value =
            $object->config('ramdisk.url') .
            $object->config('posix.id') .
            $object->config('ds') .
            $object->config(Config::DICTIONARY . '.' . Config::DOMAIN) .
            $object->config('ds');
        $value_with_port = $value;
        if(
            !in_array(
                $port,
                $object->config('server.default.port'),
                true
            )
        ){
            $value_with_port .= $port . $object->config('ds');
            if(File::exist($value_with_port)){
                $value = $value_with_port;
            }
        }
        $object->config($key, $value);
        $key = 'domain.dir.public';
        $value =
            $object->config('domain.dir.root') .
            $object->config(Config::DICTIONARY . '.' . Config::PUBLIC) .
            $object->config('ds');
        $value_with_port = $value;
        if(
            !in_array(
                $port,
                $object->config('server.default.port'),
                true
            )
        ){
            $value_with_port .= $port . $object->config('ds');
            if(File::exist($value_with_port)){
                $value = $value_with_port;
            }
        }
        $object->config($key, $value);
        $key = 'domain.dir.source';
        $value =
            $object->config('domain.dir.root') .
            $object->config(Config::DICTIONARY . '.' . Config::SOURCE) .
            $object->config('ds');
        $value_with_port = $value;
        if(
            !in_array(
                $port,
                $object->config('server.default.port'),
                true
            )
        ){
            $value_with_port .= $port . $object->config('ds');
            if(File::exist($value_with_port)){
                $value = $value_with_port;
            }
        }
        $object->config($key, $value);
        $key = 'domain.dir.view';
        $value =
            $object->config('domain.dir.root') .
            $object->config(Config::DICTIONARY . '.' . Config::VIEW) .
            $object->config('ds');
        $value_with_port = $value;
        if(
            !in_array(
                $port,
                $object->config('server.default.port'),
                true
            )
        ){
            $value_with_port .= $port . $object->config('ds');
            if(File::exist($value_with_port)){
                $value = $value_with_port;
            }
        }
        $object->config($key, $value);
        return true;
    }
}