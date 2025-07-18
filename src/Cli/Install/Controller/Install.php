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
namespace Raxon\Cli\Install\Controller;

use Exception;
use Raxon\App;
use Raxon\Config;
use Raxon\Module\Cli;
use Raxon\Module\Core;
use Raxon\Module\Controller;
use Raxon\Module\Data;
use Raxon\Module\Dir;
use Raxon\Module\Event;
use Raxon\Module\File;
use Raxon\Node\Module\Node;
use Raxon\Parse\Module\Parse;

class Install extends Controller {
    const DIR = __DIR__;
    const NAME = 'Install';
    const INFO = '{{binary()}} install                        | Install packages';

    /**
     * @throws Exception
     */
    public static function run(App $object): void
    {
        //what can be done is:
        /**
         * - create symlink Plugin to /Application/vendor/raxon/parse/src/Plugin
         */
        $autoload = $object->data(App::AUTOLOAD_RAXON);
        $id = $object->config(Config::POSIX_ID);
        $options = App::options($object);
        if(property_exists($options, 'skip')){
            if(!is_array($options->skip)){
                $options->skip = explode(',', $options->skip);
                foreach($options->skip as $nr => $skip){
                    $options->skip[$nr] = trim($skip);
                }
            }
        }        
        $key = App::parameter($object, 'install', 1);
        if(
            !in_array(
                $id,
                [
                    0,
                    33
                ],
                true
            )
        ){
            $exception = new Exception('Only root & www-data can install packages...');
            Event::trigger($object, 'cli.install', [
                'key' => $key,
                'exception' => $exception
            ]);
            throw $exception;
        }
        Core::interactive();
        $url = $object->config('framework.dir.data') .
            $object->config('dictionary.package') .
            $object->config('extension.json')
        ;
        $object->set(Controller::PROPERTY_VIEW_URL, $url);
        $package = $object->parse_select(
            $url,
            'package.' . $key
        );
        if($package->has('composer')){
            Dir::change($object->config('project.dir.root'));
            Core::execute($object, $package->get('composer'), $output, $notification);
            if($output){
                echo $output;
            }
            if($notification){
                $explode = explode(PHP_EOL, $notification);
                foreach($explode as $nr => $line){
                    if(str_contains($line, 'Nothing')){
                        $explode[$nr] = Cli::debug($line);
                    }
                    if(str_contains($line, '  - Downloading')){
                        $explode[$nr] = Cli::error($line);
                    }
                    if(str_contains($line, '  - Upgrading')){
                        $explode[$nr] = Cli::critical($line);
                    }
                }
                $notification = implode(PHP_EOL, $explode);
                echo $notification;
            }
        }
        $node = new Node($object);
        $role_system = $node->role_system();
        if(empty($role_system)){
            //install role system...
            $node->role_system_create('raxon/boot');
            $node->role_system_create('raxon/node');
            $node->role_system_create('raxon/route');
        }
        $role_system = $node->role_system();
        d($package);
        if(
            $package->has('copy') &&
            is_array($package->get('copy'))
        ){
            foreach($package->get('copy') as $copy){
                if(
                    property_exists($copy, 'from') &&
                    property_exists($copy, 'to') &&
                    property_exists($copy, 'recursive') &&
                    $copy->recursive === true &&
                    !empty($copy->from) &&
                    !empty($copy->to)
                ){
                    if(File::exist($copy->from)){
                        if(Dir::is($copy->from)){
                            echo 'Creating directory: ' . $copy->to . PHP_EOL;
                            Dir::create($copy->to, Dir::CHMOD);
                            File::permission($object, [
                                'to' => $copy->to
                            ]);
                            $dir = new Dir();
                            $read = $dir->read($copy->from, true);
                            if(is_array($read)){
                                foreach($read as $file){
                                    if($file->type === Dir::TYPE){
                                        $create = str_replace($copy->from, $copy->to, $file->url);
                                        Dir::create($create, Dir::CHMOD);
                                        File::permission($object, [
                                            'create' => $create
                                        ]);
                                    }
                                }
                                foreach($read as $file){
                                    if($file->type === File::TYPE){
                                        $to = str_replace($copy->from, $copy->to, $file->url);
                                        if(
                                            !File::exist($to) ||
                                            property_exists($options, 'force') ||
                                            property_exists($options, 'patch')
                                        ){
                                            if(
                                                property_exists($options, 'force') ||
                                                property_exists($options, 'patch')
                                            ){
                                                if(File::exist($to)){
                                                    File::delete($to);
                                                }
                                            }
                                            File::copy($file->url, $to);
                                            File::permission($object, [
                                                'to' => $to
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                elseif(
                    property_exists($copy, 'from') &&
                    property_exists($copy, 'to')
                ){
                    if(File::exist($copy->from)){
                        if(Dir::is($copy->from)){
                            if(empty($copy->to)){
                                throw new Exception('No destination found... (from: '. $copy->from .')');
                            }
                            Dir::create($copy->to, Dir::CHMOD);
                            File::permission($object, ['to' => $copy->to]);
                            $dir = new Dir();
                            $read = $dir->read($copy->from, true);
                            foreach($read as $file){
                                if($file->type === Dir::TYPE){
                                    Dir::create($file->url, Dir::CHMOD);
                                    File::permission($object, ['url' => $file->url]);
                                }
                            }
                            foreach($read as $file){
                                if($file->type === File::TYPE){
                                    $to = str_replace($copy->from, $copy->to, $file->url);
                                    if(
                                        !File::exist($to) ||
                                        (
                                            property_exists($options, 'force') ||
                                            property_exists($options, 'patch')
                                        )

                                    ){
                                        if(
                                            property_exists($options, 'force') ||
                                            property_exists($options, 'patch')
                                        ){
                                            File::delete($to);
                                        }
                                        File::copy($file->url, $to);
                                        File::permission($object, ['to' => $to]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if(
            $package->has('route') &&
            is_array($package->get('route'))
        ){
            foreach($package->get('route') as $url_route){
                if(File::exist($url_route)){
                    $class = Controller::name(File::basename($url_route, $object->config('extension.json')));
                    $read = $object->data_read($url_route);
                    if($read){
                        $count = 0;
                        foreach($read->data($class) as $import){
                            if(!property_exists($import, 'name')){
                                continue;
                            }
                            if(property_exists($import, 'host')){
                                $record = $node->record(
                                    $class,
                                    $role_system,
                                    [
                                        'filter' => [
                                            'name' => [
                                                'operator' => '===',
                                                'value' => $import->name
                                            ],
                                            'host' => [
                                                'operator' => '===',
                                                'value' => $import->host
                                            ]
                                        ]
                                    ]
                                );
                            } else {
                                $record = $node->record(
                                    $class,
                                    $role_system,
                                    [
                                        'filter' => [
                                            'name' => [
                                                'operator' => '===',
                                                'value' => $import->name
                                            ]
                                        ]
                                    ]
                                );
                            }
                            if(!$record){
                                $response = $node->create(
                                    $class,
                                    $role_system,
                                    $import,
                                    []
                                );
                                $count++;
                            }
                            elseif(
                                property_exists($options, 'force') &&
                                is_array($record) &&
                                array_key_exists('node', $record) &&
                                property_exists($record['node'], 'uuid')
                            ){
                                $import->uuid = $record['node']->uuid;
                                $response = $node->put(
                                    $class,
                                    $node->role_system(),
                                    $import,
                                    []
                                );
                                $count++;
                            }
                            elseif(
                                property_exists($options, 'patch') &&
                                is_array($record) &&
                                array_key_exists('node', $record) &&
                                property_exists($record['node'], 'uuid')
                            ){
                                $import->uuid = $record['node']->uuid;
                                $response = $node->patch(
                                    $class,
                                    $node->role_system(),
                                    $import,
                                    []
                                );
                                $count++;
                            }
                        }
                        if($count > 0){
                            echo 'Routes: ' . $count . ' for route ('. $url_route .')...' . PHP_EOL;
                        }
                    }
                } else {
                    throw new Exception('Route ('. $url_route .') not found...');

                }
            }
        }
        elseif(
            $package->has('route') &&
            is_string($package->get('route'))
        ){
            if(File::exist($package->get('route'))){
                $node = new Node($object);
                $class = Controller::name(File::basename($package->get('route'), $object->config('extension.json')));
                $read = $object->data_read($package->get('route'));
                if($read){
                    foreach($read->data($class) as $import){
                        if(!property_exists($import, 'name')){
                            continue;
                        }
                        if(property_exists($import, 'host')){
                            $record = $node->record(
                                $class,
                                $node->role_system(),
                                [
                                    'filter' => [
                                        'name' => [
                                            'operator' => '===',
                                            'value' => $import->name
                                        ],
                                        'host' => [
                                            'operator' => '===',
                                            'value' => $import->host
                                        ]
                                    ]
                                ]
                            );
                        } else {
                            $record = $node->record(
                                $class,
                                $node->role_system(),
                                [
                                    'filter' => [
                                        'name' => [
                                            'operator' => '===',
                                            'value' => $import->name
                                        ]
                                    ]
                                ]
                            );
                        }
                        if(!$record){
                            unset($import->uuid);
                            $response = $node->create(
                                $class,
                                $node->role_system(),
                                $import,
                                []
                            );
                            ddd($response);
                        }
                        elseif(
                            property_exists($options, 'force') &&
                            is_array($record) &&
                            array_key_exists('node', $record) &&
                            property_exists($record['node'], 'uuid')
                        ){
                            $import->uuid = $record['node']->uuid;
                            $response = $node->put(
                                $class,
                                $node->role_system(),
                                $import,
                                []
                            );
                            ddd($response);
                        }
                        elseif(
                            property_exists($options, 'patch') &&
                            is_array($record) &&
                            array_key_exists('node', $record) &&
                            property_exists($record['node'], 'uuid')
                        ){
                            $import->uuid = $record['node']->uuid;
                            $response = $node->patch(
                                $class,
                                $node->role_system(),
                                $import,
                                []
                            );
                            ddd($response);
                        }
                    }
                }
            } else {
                throw new Exception('Route ('. $package->get('route') .') not found...');
            }
        }
        if(
            !property_exists($options, 'skip') ||
            (
                property_exists($options, 'skip') &&
                !in_array('cache-clear', $options->skip, true)
            )
        ){
            $command = '{{binary()}} cache:clear';
            $flags = App::flags($object);

            $parse_options = (object) [
                'source' => 'Internal_' . hash('sha256', $command)
            ];
            $data = new Data($object->data());
            $parse = new Parse($object, $data, $flags, $parse_options);
            $command = $parse->compile($command, $data);
            Core::execute($object, $command, $output);
            if($output){
                echo $output;
            }
        }
        echo 'Press ctrl-c to stop the installation...' . PHP_EOL;
        $command_options = App::options($object, 'command');
        if(
            $package->has('command') &&
            is_array($package->get('command'))
        ){
            foreach($package->get('command') as $command){
                if(!empty($command_options)){
                    $command .= ' ' . implode(' ', $command_options);
                }
                echo $command . PHP_EOL;
                $code = Core::execute($object, $command, $output, $notification);
                if(!empty($output)){
                    echo rtrim($output, PHP_EOL) . PHP_EOL;
                }
                if(!empty($notification)){
                    echo rtrim($notification, PHP_EOL) . PHP_EOL;
                }
                if($code > 0){
                    throw new Exception('Command ('. $command . ') returned with exit code: ' . $code . '.');
                }
            }
        }
        elseif(
            $package->has('command') &&
            is_string($package->get('command'))
        ){
            $command = $package->get('command');
            if(!empty($command_options)){
                $command .= ' ' . implode(' ', $command_options);
            }
            echo $command . PHP_EOL;
            $code = Core::execute($object, $command, $output, $notification);
            if(!empty($output)){
                echo rtrim($output, PHP_EOL) . PHP_EOL;
            }
            if(!empty($notification)){
                echo rtrim($notification, PHP_EOL) . PHP_EOL;
            }
            if($code > 0){
                throw new Exception('Command ('. $command . ') returned with exit code: ' . $code . '.');
            }
        }
        Event::trigger($object, 'cli.install', [
            'key' => $key,
        ]);
    }
}