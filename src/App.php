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
namespace Raxon;

use Raxon\Exception\RouteNotExistException;
use Raxon\Parse\Module\Build;
use Raxon\Module\Autoload;
use Raxon\Module\Cache;
use Raxon\Module\Cli;
use Raxon\Module\Controller;
use Raxon\Module\Core;
use Raxon\Module\Data;
use Raxon\Module\Database;
use Raxon\Module\Destination;
use Raxon\Module\Dir;
use Raxon\Module\Domain;
use Raxon\Module\Event;
use Raxon\Module\File;
use Raxon\Module\FileRequest;
use Raxon\Module\Filter;
use Raxon\Module\Handler;
use Raxon\Module\Host;
use Raxon\Module\Logger;
use Raxon\Module\Middleware;
use Raxon\Module\OutputFilter;
use Raxon\Parse\Module\Parse;
use Raxon\Module\Response;
use Raxon\Module\Route;
use Raxon\Module\Server;

use Raxon\Parse\Module\Parse as ParseModule;

use Psr\Log\LoggerInterface;

use Exception;

use Raxon\Exception\ObjectException;
use Raxon\Exception\FileWriteException;
use Raxon\Exception\LocateException;

class App extends Data {
    const NAMESPACE = __NAMESPACE__;
    const NAME = 'App';
    const RAXON = 'Raxon';

    const SCRIPT = 'script';
    const LINK = 'link';

    const CONTENT_TYPE = 'contentType';
    const CONTENT_TYPE_CSS = 'text/css';
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_CLI = 'text/cli';
    const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';

    const RESPONSE_JSON = 'json';
    const RESPONSE_HTML = 'html';
    const RESPONSE_FILE = 'file';
    const RESPONSE_OBJECT = 'object';

    const LOGGER_NAME = 'App';

    const ROUTE = App::NAMESPACE . '.' . Route::NAME;
    const CONFIG = App::NAMESPACE . '.' . Config::NAME;
    const EVENT = App::NAMESPACE . '.' . Event::NAME;
    const MIDDLEWARE = App::NAMESPACE . '.' . Middleware::NAME;
    const OUTPUTFILTER = App::NAMESPACE . '.' . OutputFilter::NAME;
    const CACHE = App::NAMESPACE . '.' . Cache::NAME;
    const FILTER = App::NAMESPACE . '.' . Filter::NAME;
    const FLAGS = App::NAMESPACE . '.' . Data::FLAGS;
    const OPTIONS = App::NAMESPACE . '.' . Data::OPTIONS;
    const REQUEST = App::NAMESPACE . '.' . Handler::NAME_REQUEST . '.' . Handler::NAME_INPUT;
    const REQUEST_HEADER = App::NAMESPACE . '.' . Handler::NAME_REQUEST . '.' . Handler::NAME_HEADER;
    const REQUEST_FILE = App::NAMESPACE . '.' . Handler::NAME_REQUEST . '.' . Handler::NAME_FILE;
    const DATABASE = App::NAMESPACE . '.' . Database::NAME;

    const AUTOLOAD_COMPOSER = App::NAMESPACE . '.' . 'Autoload' . '.' . 'Composer';
    const AUTOLOAD_RAXON = App::NAMESPACE . '.' . 'Autoload' . '.' . App::RAXON;

    const DIR = __DIR__ . DIRECTORY_SEPARATOR;

    private $logger = [];

    /**
     * @throws Exception
     */
    public function __construct($autoload, $config){
        $this->data(App::AUTOLOAD_COMPOSER, $autoload);
        $this->data(App::CONFIG, $config);
        $data = new Data();
        $this->data(App::EVENT, clone $data);
        $this->data(App::MIDDLEWARE, clone $data);
        $this->data(App::OUTPUTFILTER, clone $data);
        $this->data(App::CACHE, clone $data);
        App::is_cli();
        require_once __DIR__ . '/Debug.php';
        require_once __DIR__ . '/Error.php';
        Config::configure($this);
        Logger::configure($this);
        Host::configure($this);
        Domain::configure($this);
        Event::configure($this);
        Middleware::configure($this);
        OutputFilter::configure($this);
        Autoload::configure($this);
        Autoload::ramdisk_configure($this);
    }

    /**
     * @throws Exception
     */
    public static function configure(App $object): void
    {
        $info = 'Logger: App initialised.';
        if(App::is_cli() === false){
            $domains = $object->config('server.cors.domain');
            if(!empty($domains)){
                $object->logger($object->config('project.log.debug'))->info('enable-cors');
                $info .= ' and enabling cors';
                Server::cors($object);
            }
        }
        $logger = false;
        if($object->config('framework.environment') === Config::MODE_DEVELOPMENT){
            $logger = $object->config('project.log.debug');
        }
        if(
            $logger &&
            empty($object->request('request'))
        ){
            $object->logger($logger)->info($info, [Host::subdomain()]);
        }
        elseif($logger) {
            $object->logger($logger)->info($info . ' with request: ' . $object->request('request'), [Host::subdomain()]);
        }
        $options = $object->config('server.http.cookie');
        if(
            is_object($options) &&
            property_exists($options, 'domain') &&
            $options->domain === true
        ){
            if(App::is_cli()){
                unset($options->domain);
            } else {
                $options->domain = Server::url($object,Host::domain() . '.' . Host::extension());
                if(!$options->domain){
                    $options->domain = Host::domain() . '.' . Host::extension();
                }
            }
            $options->secure = null;
            if(Host::scheme() === Host::SCHEME_HTTPS){
                $options->secure = true;
            }
            Handler::session_set_cookie_params($options);
        }
    }

    /**
     * @throws Exception
     * @throws ObjectException
     * @throws LocateException
     */
    public static function run(App $object): mixed
    {
        Handler::request_configure($object);
        $destination = false;
        $logger = false;
        if($object->config('framework.environment') === Config::MODE_DEVELOPMENT){
            $logger = $object->config('project.log.debug');
        }
        $logger_error =  $object->config('project.log.error');
        try {
            $file = FileRequest::get($object);
            if ($file === false) {
                App::configure($object);
                Route::configure($object);
                $destination = Route::request($object);
                if(
                    is_object($destination) &&
                    property_exists($destination, 'name') &&
                    $destination->name !== 'index'
                ){
                    ddd($destination);
                }

                if ($destination === false) {
                    $object->config('framework.environment', Config::MODE_PRODUCTION);
                    if ($object->config('framework.environment') === Config::MODE_DEVELOPMENT) {
                        if($logger){
                            $object->logger($logger)->error('Couldn\'t determine route (' . $object->request('request') . ')...');
                        }
                        $subdomain = $object->config('host.subdomain');
                        $domain = $object->config('host.domain');
                        $extension = $object->config('host.extension');
                        $port = $object->config('host.port');
                        $url = $object->config('project.dir.domain');
                        if($subdomain){
                            $host = $subdomain . '.' . $domain . '.' . $extension;
                            if(!in_array($port, [80, 443], true)){
                                $host .= ':' . $port;
                            }
                            $url .= ucfirst($subdomain) . '.' . ucfirst($domain) . '.' . ucfirst($extension);
                        } else {
                            $host = $domain . '.' . $extension;
                            if(!in_array($port, [80, 443], true)){
                                $host .= ':' . $port;
                            }
                            $url .= ucfirst($domain) . '.' . ucfirst($extension);
                        }
                        $route = $host . '/';
                        if($object->request('request') !== '/'){
                            $route .= $object->request('request');
                        }
                        $url .= $object->config('ds') .
                            'View' .
                            $object->config('ds') .
                            'Http' .
                            $object->config('ds') .
                            'Exception' .
                            $object->config('ds') .
                            '404.tpl';
                        if(!File::exist($url)){
                            $url = $object->config('framework.dir.view') .
                                'Http' .
                                $object->config('ds') .
                                'Exception' .
                                $object->config('ds') .
                                '404.tpl';
                        }
                        $object->config(
                            'controller.dir.root',
                            $object->config('project.dir.root') .
                            'vendor' .
                            $object->config('ds') .
                            'raxon' .
                            $object->config('ds') .
                            'framework' .
                            $object->config('ds') .
                            'src' .
                            $object->config('ds')
                        );
                        $exception = new RouteNotExistException('404 Not Found (route: '. $route .')', 404);
                        $response = new Response(
                            Controller::response(
                                $object,
                                $url,
                                (object) [
                                    'exception' => (object) [
                                        'className' => get_class($exception),
                                        'message' => $exception->getMessage(),
                                        'route' => $route,
                                        'file' => $exception->getFile(),
                                        'line' => $exception->getLine(),
                                        'code' => $exception->getCode(),
                                        'trace' => $exception->getTrace()
                                    ]
                                ]
                            ),
                            Response::TYPE_HTML
                        );
                        Event::trigger($object, 'app.run.route.error', [
                            'destination' => false,
                            'exception' => $exception
                        ]);
                        return Response::output($object, $response);
                    } else {
                        $destination = Route::wildcard($object);
                        if ($destination === false) {
                            if($logger_error){
                                $object->logger($logger_error)->error('Couldn\'t determine route (wildcard) (' . $object->request('request') . ')...');
                            }
                            $subdomain = $object->config('host.subdomain');
                            $domain = $object->config('host.domain');
                            $extension = $object->config('host.extension');
                            $url = $object->config('project.dir.domain');
                            if($subdomain){
                                $host = $subdomain . '.' . $domain . '.' . $extension;
                                $url .= ucfirst($subdomain) . '.' . ucfirst($domain) . '.' . ucfirst($extension);
                            } else {
                                $host = $domain . '.' . $extension;
                                $url .= ucfirst($domain) . '.' . ucfirst($extension);
                            }
                            $url .= $object->config('ds') .
                                'View' .
                                $object->config('ds') .
                                'Http' .
                                $object->config('ds') .
                                'Error' .
                                $object->config('ds') .
                                '404.tpl';
                            if(!File::exist($url)){
                                $url = $object->config('framework.dir.view') .
                                    'Http' .
                                    $object->config('ds') .
                                    'Exception' .
                                    $object->config('ds') .
                                    '404.tpl';
                            }
                            $object->config(
                                'controller.dir.root',
                                $object->config('project.dir.root') .
                                'vendor' .
                                $object->config('ds') .
                                'raxon' .
                                $object->config('ds') .
                                'framework' .
                                $object->config('ds') .
                                'src' .
                                $object->config('ds')
                            );
                            $route = $host . '/';
                            if($object->request('request') !== '/'){
                                $route .= $object->request('request');
                            }
                            $exception = new RouteNotExistException('404 Not Found (route: '. $route .')', 404);
                            $response = new Response(
                                Controller::response(
                                    $object,
                                    $url,
                                    (object) [
                                        'exception' => (object) [
                                            'className' => get_class($exception),
                                            'message' => $exception->getMessage(),
                                            'route' => $route,
                                            'file' => $exception->getFile(),
                                            'line' => $exception->getLine(),
                                            'code' => $exception->getCode(),
                                            'trace' => $exception->getTrace()
                                        ]
                                    ]
                                ),
                                Response::TYPE_HTML
                            );
                            Event::trigger($object, 'app.run.route.wildcard.error', [
                                'destination' => false,
                                'exception' => $exception
                            ]);
                            return Response::output($object, $response);
                        }
                    }
                }
                if(
                    !empty($destination->get('redirect')) &&
                    !empty($destination->get('method')) &&
                    is_array($destination->get('method')) &&
                    in_array(
                        Handler::method(),
                        $destination->get('method'),
                        true
                    )
                ){
                    if($logger){
                        $object->logger($logger)->info('Request (' . $object->request('request') . ') Redirect: ' . $destination->get('redirect') . ' Method: ' . implode(', ', $destination->get('method')));
                    }
                    Event::trigger($object, 'app.run.route.redirect', [
                        'destination' => $destination,
                    ]);
                    Core::redirect($destination->get('redirect'));
                }
                elseif(
                    !empty($destination->get('redirect')) &&
                    empty($destination->get('method'))
                ){
                    if($logger){
                        $object->logger($logger)->info('Redirect: ' . $destination->has('redirect'));
                    }
                    Event::trigger($object, 'app.run.route.redirect', [
                        'destination' => $destination,
                    ]);
                    Core::redirect($destination->get('redirect'));
                }
                elseif(!empty($destination->get('url'))){
                    $object->config(
                        'controller.dir.root',
                        $object->config('project.dir.root') .
                        'vendor' .
                        $object->config('ds') .
                        'raxon' .
                        $object->config('ds') .
                        'framework' .
                        $object->config('ds') .
                        'src' .
                        $object->config('ds')
                    );
                    $parameters = [$destination->get('url')];
                    $parameters = Config::parameters($object, $parameters);
                    $url = $parameters[0];
                    $destination->set('url', $url);
                    $extension = File::extension($url);
                    $extension_lowercase = strtolower($extension);
                    if ($extension_lowercase === $object->config('extension.json')) {
                        $response = new Response(
                            File::read($url),
                            Response::TYPE_JSON,
                        );
                        Event::trigger($object, 'app.run.route.file', [
                            'destination' => $destination,
                            'extension' => $extension,
                            'content_type' => $object->config('contentType.' . $object->config('extension.json'))
                        ]);
                        return Response::output($object, $response);
                    } else {
                        $contentType = $object->config('contentType.' . $extension_lowercase);
                        if ($contentType) {
                            $response = new Response(
                                File::read($url),
                                Response::TYPE_FILE,
                            );
                            $response->header([
                                'Content-Type: ' . $contentType
                            ]);
                            Event::trigger($object, 'app.run.route.file', [
                                'destination' => $destination,
                                'extension' => $extension,
                                'content_type' => $contentType
                            ]);
                            return Response::output($object, $response);
                        }
                        throw new Exception('Extension (' . $extension . ') not supported...');
                    }
                }
                elseif(!empty($destination->get('controller'))){
                    $duration = microtime(true) - $object->config('time.start');
                    if($logger){
                        $object->logger($logger)->info('Controller duration: ' . $duration * 1000 . ' msec');
                    }
                    App::contentType($object);
                    App::controller($object, $destination);
                    $controller = $destination->get('controller');
                    $methods = get_class_methods($controller);
                    if (empty($methods)) {
                        if($logger_error){
                            $object->logger($logger_error)->error('Couldn\'t determine controller (' . $destination->get('controller') . ') with request (' . $object->request('request') . ')');
                        }
                        $exception = new Exception(
                            'Couldn\'t determine controller (' . $destination->get('controller') . ')'
                        );
                        $response = new Response(
                            App::exception_to_json($exception),
                            Response::TYPE_JSON,
                            Response::STATUS_NOT_IMPLEMENTED
                        );
                        Event::trigger($object, 'app.run.route.file', [
                            'destination' => $destination,
                            'exception' => $exception
                        ]);
                        return Response::output($object, $response);
                    }
                    $functions = [];
                    if (in_array('controller', $methods, true)) {
                        $functions[] = 'controller';
                        $controller::controller($object);
                    }
                    if (in_array('configure', $methods, true)) {
                        $functions[] = 'configure';
                        $controller::configure($object);
                    }
                    $destination = Middleware::trigger($object, $destination, [
                        'methods' => $methods,
                    ]);
                    $controller = $destination->get('controller');
                    $function = $destination->get('function');
                    $methods = get_class_methods($controller);
                    if(
                        $destination &&
                        $function &&
                        $methods &&
                        in_array($function, $methods, true)
                    ){
                        $functions[] = $function;
                        $object->config('controller.function', $function);
                        $request = Core::deep_clone(
                            $object->get(
                                App::NAMESPACE . '.' .
                                Handler::NAME_REQUEST . '.' .
                                Handler::NAME_INPUT
                            )->data()
                        );
                        $object->config('request', $request);
                        if($logger){
                            $object->logger($logger)->info(
                                'Controller (' .
                                $controller .
                                ') function (' .
                                $function .
                                ') triggered.'
                            );
                        }
                        $result = $controller::{$function}($object);
                        Event::trigger($object, 'app.run.route.controller', [
                            'destination' => $destination,
                            'response' => $result
                        ]);
                        $result = OutputFilter::trigger($object, $destination, [
                            'methods' => $methods,
                            'response' => $result
                        ]);
                    } else {
                        if($logger_error){
                            $object->logger($logger_error)->error(
                                'Controller (' .
                                $controller .
                                ') function (' .
                                $function .
                                ') does not exist.'
                            );
                        }
                        $exception = new Exception(
                            'Controller (' .
                            $controller .
                            ') function (' .
                            $function .
                            ') does not exist.'
                        );
                        $response = new Response(
                            App::exception_to_json($exception),
                            Response::TYPE_JSON,
                            Response::STATUS_NOT_IMPLEMENTED
                        );
                        Event::trigger($object, 'app.run.route.controller', [
                            'destination' => $destination,
                            'exception' => $exception
                        ]);
                        return Response::output($object, $response);
                    }
                    $functions[] = 'result';
                    $result = App::result($object, $result);
                    if($logger){
                        $object->logger($logger)->info('Functions: [' . implode(', ', $functions) . '] called in controller: ' . $controller);
                    }
                    return $result;
                }
            }  else {
                if($logger){
                    $object->logger($logger)->info('File request: ' . $object->request('request') . ' called...');
                }
                Event::trigger($object, 'app.run.file.request', []);
                return $file;
            }
        }
        catch (Exception | LocateException $exception) {
            try {
                $code = $exception->getCode();
                if(empty($code)){
                    $code = Response::STATUS_NOT_IMPLEMENTED;
                }
                if($object->data(App::CONTENT_TYPE) === App::CONTENT_TYPE_JSON){
                    if(!headers_sent()){
                        header('Status: ' . $code);
                        header('Content-Type: application/json');
                    }
                    if($logger_error){
                        $object->logger($logger_error)->error($exception->getMessage());
                    }
                    Event::trigger($object, 'app.route.exception', [
                        'destination' => $destination,
                        'exception' => $exception
                    ]);
                    return App::exception_to_json($exception);
                }
                elseif($object->data(App::CONTENT_TYPE) === App::CONTENT_TYPE_CLI){
                    if($logger_error){
                        $object->logger($logger_error)->error($exception->getMessage());
                    }
                    Event::trigger($object, 'app.route.exception', [
                        'destination' => $destination,
                        'exception' => $exception
                    ]);
                    fwrite(STDERR, App::exception_to_cli($object, $exception));
                    exit(1);
                } else {
                    Controller::configure($object, __CLASS__); //initialize plugin directories
                    $location = [];
                    $location[] = $object->config('domain.dir.view') .
                        'Http' .
                        $object->config('ds') .
                        'Exception' .
                        $object->config('ds') .
                        $code .
                        $object->config('extension.tpl')
                    ;
                    $location[] = $object->config('framework.dir.view') .
                        'Http' .
                        $object->config('ds') .
                        'Exception' .
                        $object->config('ds') .
                        $code .
                        $object->config('extension.tpl')
                    ;
                    $is_url = false;
                    foreach($location as $nr => $url){
                        if(File::exist($url)){
                            $is_url = $url;
                            break;
                        }
                    }
                    if($is_url === false){
                        Event::trigger($object, 'app.route.exception', [
                            'destination' => $destination,
                            'exception' => $exception
                        ]);
                        echo $exception;
                        return null;
                    } else {
                        $data = new Data($object->data());
                        $flags = App::flags($object);
                        $options = (object) [];
                        $options->source = $url;
                        $temp_source = $options->source ?? 'source';
//                        $options->source = 'internal_' . Core::uuid();
                        $options->source = $is_url;
                        $options->source_root = $temp_source;
                        $options->class = Build::class_name($options->source);
                        $parse = new Parse($object, $data, $flags, $options);
                        $read = File::read($is_url);
                        $data = (object) [];
                        $data->exception = (object) Core::object_array($exception);
                        $data->exception->className = get_class($exception);
                        Event::trigger($object, 'app.route.exception', [
                            'destination' => $destination,
                            'url' => $url,
                            'exception' => $exception
                        ]);
                        return $parse->compile($read, $data);
                    }
                }
            } catch (ObjectException $exception){
                return $exception;
            }
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public static function controller(App $object, Destination $destination): void
    {
        $controller = $destination->get('controller');
        if(!empty($controller)){
            $check = class_exists($controller);
            if(empty($check)){
                throw new Exception('Cannot call controller (' . $controller .')');
            }
        } else {
            throw new Exception('Missing controller in destination');
        }
    }

    /**
     * @throws Exception
     */
    public static function contentType(App $object): string
    {
        $contentType = $object->data(App::CONTENT_TYPE);
        if(empty($contentType)){
            $contentType = App::CONTENT_TYPE_HTML;
            if(property_exists($object->data(App::REQUEST_HEADER), '_')){
                $contentType = App::CONTENT_TYPE_CLI;
            }
            elseif(property_exists($object->data(App::REQUEST_HEADER), 'Content-Type')){
                $contentType = $object->data(App::REQUEST_HEADER)->{'Content-Type'};
            }
            if(empty($contentType)){
                throw new Exception('Couldn\'t determine contentType');
            }
            $object->data(App::CONTENT_TYPE, $contentType);
            return $contentType;
        } else {
            return $contentType;
        }
    }

    /**
     * @throws Exception
     */
    public static function exception_to_json(Exception $exception): string
    {
        $class = get_class($exception);
        $array = [];
        $array['class'] = $class;
        $array['message'] = $exception->getMessage();
        if(stristr($class, 'locateException') !== false){
            $array['location'] = $exception->getLocation($array);
        }
        $array['line'] = $exception->getLine();
        $array['file'] = $exception->getFile();
        $array['code'] = $exception->getCode();
        $array['previous'] = $exception->getPrevious();
        $array['trace'] = $exception->getTrace();
        //$array['trace_as_string'] = $exception->getTraceAsString(); //not needed is unclear...
        try {
            return Core::object($array, Core::OBJECT_JSON);
        } catch (ObjectException $objectException) {
            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    public static function exception_to_cli(App $object, Exception $exception): string
    {
        $class = get_class($exception);
        $width = Cli::tput('width');
        $background = '200;0;0';
        $output = chr(27) . '[48;2;' . $background . 'm';
        $output .= str_repeat(' ', $width);
        $output .= PHP_EOL;
        $output .= $class . PHP_EOL;
        $output .= PHP_EOL;
        $output .= $exception->getMessage() . PHP_EOL;
        $output .= 'file: ' . $exception->getFile() . PHP_EOL;
        $output .= 'line: ' . $exception->getLine() . PHP_EOL;
        $output .= chr(27) . "[0m";
        $output .= PHP_EOL;
        if(
            $object->config('framework.environment') === Config::MODE_INIT ||
            $object->config('framework.environment') === Config::MODE_DEVELOPMENT
        ){
            $output .= (string) $exception;
        }
        return $output;
    }

    /**
     * @throws Exception
     */
    public static function response_output(App $object, $output=App::CONTENT_TYPE_HTML): void
    {
        $object->config('response.output', $output);
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    private static function result(App $object, $output): mixed
    {
        $logger_error = $object->config('project.log.error');
        if($output instanceof Exception){
            if(App::is_cli()){
                if($logger_error){
                    $object->logger($logger_error)->error($output->getMessage());
                }
                fwrite(STDERR, App::exception_to_cli($object, $output));
                exit(1);
            } else {
                if(!headers_sent()){
                    header('Content-Type: application/json');
                }
                return App::exception_to_json($output);
            }
        }
        elseif($output instanceof Response){
            return Response::output($object, $output);
        } else {
            $response = new Response($output, $object->config('response.output'));
            return Response::output($object, $response);
        }
    }

    /**
     * @throws Exception
     */
    public function logger($name='', $logger=null): LoggerInterface
    {
        if($logger !== null){
            $this->setLogger($name, $logger);
        }
        return $this->getLogger($name);
    }

    /**
     * @throws Exception
     */
    private function setLogger($name='', LoggerInterface $logger=null): void
    {
        if(empty($name)){
            $name = $this->config('project.log.debug');
        }
        if(empty($name)){
            throw new Exception('PLease configure project.log.debug or provide a name');
        }
        $name = ucfirst($name);
        $this->logger[$name] = $logger;
    }

    /**
     * @throws Exception
     */
    private function getLogger($name=''): LoggerInterface
    {
        if(empty($name)){
            $name = $this->config('project.log.debug');
        }
        if(empty($name)){
            throw new Exception('PLease configure project.log.debug or provide a name');
        }
        $name = ucfirst($name);
        if(array_key_exists($name, $this->logger)){
            return $this->logger[$name];
        }
        throw new Exception('Logger with name: ' . $name . ' not initialised.');

    }

    /**
     * @throws Exception
     */
    public function route(): mixed
    {
        return $this->data(App::ROUTE);
    }

    /**
     * @throws Exception
     */
    public function config($attribute=null, $value=null): mixed
    {
        return $this->data(App::CONFIG)->data($attribute, $value);
    }

    /**
     * @throws Exception
     */
    public function event($attribute=null, $value=null): mixed
    {
        return $this->data(App::EVENT)->data($attribute, $value);
    }

    /**
     * @throws Exception
     */
    public function middleware($attribute=null, $value=null): mixed
    {
        return $this->data(App::MIDDLEWARE)->data($attribute, $value);
    }

    /**
     * @throws Exception
     */
    public function request($attribute=null, $value=null): mixed
    {
        return $this->data(App::REQUEST)->data($attribute, $value);        
    }

    public static function parameter($data, $parameter, $offset=0): mixed
    {
        return parent::parameter($data->data(App::REQUEST)->data(), $parameter, $offset);
    }

    /**
     * @throws Exception
     */
    private static function flags_options(App $object): void
    {
        $data = $object->data(App::REQUEST)->data();
        $options = (object) [];
        $flags = (object) [];
        foreach($data as $nr => $parameter){
            $is_flag = false;
            $is_option = false;
            $is_array = false;
            if(
                is_string($parameter)
            ){
                if(substr($parameter, 0, 2) === '--'){
                    $parameter = substr($parameter, 2);
                    $is_flag = true;
                }
                elseif(substr($parameter, 0, 1) === '-'){
                    $parameter = substr($parameter, 1);
                    $is_option = true;
                }
                $value = true;
                $tmp = explode('=', $parameter, 2);
                if(array_key_exists(1, $tmp)){
                    $parameter = $tmp[0];
                    if(substr($parameter, -2, 2) === '[]'){
                        $parameter = substr($parameter, 0, -2);
                        $is_array = true;
                    }
                    elseif(str_contains($parameter, '[') && str_contains($parameter, ']')){
                        $explode = explode('[', $parameter);
                        $count = count($explode);
                        for($i = 0; $i <$count; $i++){
                            $explode[$i] = str_replace(']', '', $explode[$i]);
                        }
                        $parameter = array_shift($explode);
                        $count--;
                        $is_continue = false;
                        $value = $tmp[1];
                        if(is_numeric($value)){
                            $value = $value + 0;
                        } else {
                            switch($value){
                                case 'true':
                                    $value = true;
                                    break;
                                case 'false':
                                    $value = false;
                                    break;
                                case 'null':
                                    $value = null;
                                    break;
                                case '[]':
                                    $value = [];
                                    break;
                                case '{}':
                                    $value = (object) [];
                                    break;
                                case '\true':
                                    $value = 'true';
                                    break;
                                case '\false':
                                    $value = 'false';
                                    break;
                                case '\null':
                                    $value = 'null';
                                    break;
                                case '\[]':
                                    $value = '[]';
                                    break;
                                case '\{}':
                                    $value = '{}';
                                    break;
                            }
                        }
                        switch($count){
                            case 10:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]][$explode[3]][$explode[4]][$explode[5]][$explode[6]][$explode[7]][$explode[8]][$explode[9]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 9:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]][$explode[3]][$explode[4]][$explode[5]][$explode[6]][$explode[7]][$explode[8]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 8:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]][$explode[3]][$explode[4]][$explode[5]][$explode[6]][$explode[7]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 7:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]][$explode[3]][$explode[4]][$explode[5]][$explode[6]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 6:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]][$explode[3]][$explode[4]][$explode[5]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 5:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]][$explode[3]][$explode[4]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 4:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]][$explode[3]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 3:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]][$explode[2]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 2:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]][$explode[1]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                            case 1:
                                $get = Core::object_get($parameter, $options);
                                if(!is_array($get)){
                                    $get = [];
                                }
                                $get[$explode[0]] = $value;
                                Core::object_set($parameter, $get, $options, 'child');
                                $is_continue = true;
                                break;
                        }
                        if($is_continue){
                            continue;
                        }
                    }
                    $value = $tmp[1];
                    if(is_numeric($value)){
                        $value = $value + 0;
                    } else {
                        switch($value){
                            case 'true':
                                $value = true;
                            break;
                            case 'false':
                                $value = false;
                            break;
                            case 'null':
                                $value = null;
                            break;
                            case '[]':
                                $value = [];
                            break;
                            case '{}':
                                $value = (object) [];
                            break;
                            case '\true':
                                $value = 'true';
                                break;
                            case '\false':
                                $value = 'false';
                                break;
                            case '\null':
                                $value = 'null';
                            break;
                            case '\[]':
                                $value = '[]';
                            break;
                            case '\{}':
                                $value = '{}';
                            break;
                        }
                    }
                } elseif(substr($parameter, -2, 2) === '[]'){
                    $parameter = substr($parameter, 0, -2);
                    $is_array = true;
                }
                if($is_option){
                    if($is_array){
                        $get = Core::object_get($parameter, $options);
                        if(!is_array($get)){
                            $get = [];
                        }
                        $get[] = $value;
                        Core::object_set($parameter, $get, $options, 'child');
                    } else {
                        Core::object_set($parameter, $value, $options, 'child');
                    }
                }
                elseif($is_flag){
                    if($is_array){
                        $get = Core::object_get($parameter, $options);
                        if(!is_array($get)){
                            $get = [];
                        }
                        $get[] = $value;
                        Core::object_set($parameter, $get, $flags, 'child');
                    } else {
                        Core::object_set($parameter, $value, $flags, 'child');
                    }
                }
            }
        }
        $object->data(App::FLAGS, $flags);
        $object->data(App::OPTIONS, $options);
    }

    /**
     * @throws Exception
     */
    public static function flags($object): object
    {
        $flags = $object->data(App::FLAGS);
        if(empty($flags)){
            App::flags_options($object);
            $flags = $object->data(App::FLAGS);
        }
        return $flags;
    }

    /**
     * @throws Exception
     */
    public static function options($object, $type='default'): mixed
    {
        $options = $object->data(App::OPTIONS);
        if(empty($options)){
            App::flags_options($object);
            $options = $object->data(App::OPTIONS);
        }
        switch($type) {
            case 'default':
                return $options;
            case 'command':
                $command_options = [];
                foreach($options as $option => $value){
                    if($value === false){
                        $value = 'false';
                    }
                    elseif($value === true){
                        $value = 'true';
                    }
                    elseif($value === null){
                        $value = 'null';
                    }
                    if(
                        in_array(
                            $value,
                            [
                                'false',
                                'true',
                                'null'
                            ],
                            true
                        ) ||
                        is_numeric($value)
                    ){
                        $command_options[] = '-' . $option . '=' . $value;
                    } else {
                        if(is_array($value)){
                            foreach ($value as $val){
                                if($value === false){
                                    $val = 'false';
                                }
                                elseif($value === true){
                                    $val = 'true';
                                }
                                elseif($value === null){
                                    $val = 'null';
                                }
                                if(
                                    in_array(
                                        $val,
                                        [
                                            'false',
                                            'true',
                                            'null'
                                        ],
                                        true
                                    ) ||
                                    is_numeric($val)
                                ){
                                    $command_options[] = '-' . $option . '[]=' . $val;
                                } else {
                                    $command_options[] = '-' . $option . '[]=\'' . $val . '\'';
                                }
                            }
                        } elseif(!is_object($value)) {
                            $command_options[] = '-' . $option . '=\'' . $value . '\'';
                        } else {
                            foreach($value as $key => $val){
                                if($value === false){
                                    $val = 'false';
                                }
                                elseif($value === true){
                                    $val = 'true';
                                }
                                elseif($value === null){
                                    $val = 'null';
                                }
                                if(
                                    in_array(
                                        $val,
                                        [
                                            'false',
                                            'true',
                                            'null'
                                        ],
                                        true
                                    ) ||
                                    is_numeric($val)
                                ){
                                    $command_options[] = '-' . $option . '.' . $key . '=' . $val;
                                }
                                elseif(is_object($val)){
                                   foreach($val as $k => $v){
                                        if($value === false){
                                             $v = 'false';
                                        }
                                        elseif($value === true){
                                             $v = 'true';
                                        }
                                        elseif($value === null){
                                             $v = 'null';
                                        }
                                        if(
                                             in_array(
                                                  $v,
                                                  [
                                                    'false',
                                                    'true',
                                                    'null'
                                                  ],
                                                  true
                                             ) ||
                                             is_numeric($v)
                                        ){
                                             $command_options[] = '-' . $option . '.' . $key . '.' . $k . '=' . $v;
                                        } else {
                                             $command_options[] = '-' . $option . '.' . $key . '.' . $k . '=\'' . $v . '\'';
                                        }
                                   }
                                } else {
                                    $command_options[] = '-' . $option . '.' . $key . '=\'' . $val . '\'';
                                }
                            }
                        }
                    }
                }
                return $command_options;
        }
        return $options;
    }

    /**
     * @throws Exception
     */
    public function session($attribute=null, $value=null): mixed
    {
        return Handler::session($attribute, $value);
    }

    public function cookie($attribute=null, $value=null, $duration=null): mixed
    {
        if($attribute === 'http'){
            $cookie = $this->server('HTTP_COOKIE');
            return explode('; ', $cookie);
        }
        return Handler::cookie($attribute, $value, $duration);
    }

    /**
     * @throws Exception
     */
    public function upload($number=null): Data
    {
        if($number === null){
            return new Data($this->data(
                App::NAMESPACE . '.' .
                Handler::NAME_REQUEST . '.' .
                Handler::NAME_FILE
            ));
        } else {
            return new Data($this->data(
                App::NAMESPACE . '.' .
                Handler::NAME_REQUEST . '.' .
                Handler::NAME_FILE . '.' .
                $number
            ));
        }
    }

    public function server($attribute=null): mixed
    {
        if($attribute===null){
            return $_SERVER;
        }
        if(is_scalar($attribute)){
            if(array_key_exists($attribute, $_SERVER)){
                return $_SERVER[$attribute];
            }
        }
        return null;
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function data_select($url, $select=null): Data
    {
        if(File::exist($url)){
            $mtime = File::mtime($url);
        } else {
            throw new Exception('File not found: ' . $url);
        }
        $require_disabled = $this->config('require.disabled');
        if($require_disabled){
            //nothing
        } else {
            $require_url = $this->config('require.url');
            $require_mtime = $this->config('require.mtime');
            if(empty($require_url)){
                $require_url = [];
                $require_mtime = [];
            }
            if(
                !in_array(
                    $url,
                    $require_url,
                    true
                )
            ){
                $require_url[] = $url;
                $require_mtime[] = $mtime;
                $this->config('require.url', $require_url);
                $this->config('require.mtime', $require_mtime);
            }
        }
        $flags = App::flags($this);
        $data = new Data();
        $data->data($this->data());
        $parse = new Parse($this, $data);
        $node = new Data();
        $logger = false;
        if($this->config('framework.environment') === Config::MODE_DEVELOPMENT){
            $logger = $this->config('project.log.debug');
        }
        if($logger){
            $this->logger($logger)->info('parse_select: ' . $url, [$select]);
        }
        $node->data(
            Core::object_select(
                $parse,
                $data,
                $url,
                $select,
                false,
            )
        );
        return $node;
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function parse_select($url, $select=null, $scope='scope:object'): Data
    {
        if(File::exist($url)){
            $mtime = File::mtime($url);
        } else {
            throw new Exception('File not found: ' . $url);
        }
        $require_disabled = $this->config('require.disabled');
        if($require_disabled){
            //nothing
        } else {
            $require_url = $this->config('require.url');
            $require_mtime = $this->config('require.mtime');
            if(empty($require_url)){
                $require_url = [];
                $require_mtime = [];
            }
            if(
                !in_array(
                    $url,
                    $require_url,
                    true
                )
            ){
                $require_url[] = $url;
                $require_mtime[] = $mtime;
                $this->config('require.url', $require_url);
                $this->config('require.mtime', $require_mtime);
            }
        }
        $data = new Data();
        $data->data($this->data());
        $parse = new Parse($this, $data);
        $node = new Data();
        $logger = false;
        if($this->config('framework.environment') === Config::MODE_DEVELOPMENT){
            $logger = $this->config('project.log.debug');
        }
        if($logger){
            $this->logger($logger)->info(' parse_select: ' . $url, [$select ,$scope]);
        }
        $parse->storage()->data('raxon.org.parse.view.url', $url);
        $parse->storage()->data('raxon.org.parse.view.mtime', $mtime);
        $node->data(
            Core::object_select(
                $parse,
                $data,
                $url,
                $select,
                true,
                $scope
            )
        );
        return $node;
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function object_select($url, $select=null, $compile=false, $scope='scope:object'): mixed
    {
        if(File::exist($url)){
            $mtime = File::mtime($url);
        } else {
            throw new Exception('File not found: ' . $url);
        }
        $require_disabled = $this->config('require.disabled');
        if($require_disabled){
            //nothing
        } else {
            $require_url = $this->config('require.url');
            $require_mtime = $this->config('require.mtime');
            if(empty($require_url)){
                $require_url = [];
                $require_mtime = [];
            }
            if(
                !in_array(
                    $url,
                    $require_url,
                    true
                )
            ){
                $require_url[] = $url;
                $require_mtime[] = $mtime;
                $this->config('require.url', $require_url);
                $this->config('require.mtime', $require_mtime);
            }
        }
        $data = new Data();
        $data->data($this->data());
        $parse = new Parse($this, $data);
        $logger = false;
        if($this->config('framework.environment') === Config::MODE_DEVELOPMENT){
            $logger = $this->config('project.log.debug');
        }
        if($logger){
            $this->logger($logger)->info(' object_select: ' . $url, [$select ,$compile, $scope]);
        }
        $parse->storage()->data('raxon.org.parse.view.url', $url);
        $parse->storage()->data('raxon.org.parse.view.mtime', $mtime);
        return Core::object_select(
            $parse,
            $data,
            $url,
            $select,
            $compile,
            $scope
        );
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function data_read($url, $attribute=null, $options=false): mixed
    {
        if(is_bool($options)){
            $options = [
                'do_not_nest_key' => $options
            ];
        }
        elseif(!array_key_exists('do_not_nest_key', $options)) {
            $options['do_not_nest_key'] = false;
        }
        if(!array_key_exists('counter', $options)){
            $options['counter'] = false;
        }
        $logger_error = $this->config('project.log.error');
        $cache = $this->data(App::CACHE);
        if($attribute !== null){
            if($cache){
                $data = $cache->get($attribute);
            }
            if(!empty($data)){
                return $data;
            }
        }
        if(File::exist($url)){
            if($options['counter'] === true){
                echo 'Loading data: ' . $url . PHP_EOL;
            }
            $read = File::read($url);
            $mtime = File::mtime($url);
            $require_disabled = $this->config('require.disabled');
            if($require_disabled){
                //nothing
            } else {
                $require_url = $this->config('require.url');
                $require_mtime = $this->config('require.mtime');
                if(empty($require_url)){
                    $require_url = [];
                    $require_mtime = [];
                }
                if(
                    !in_array(
                        $url,
                        $require_url,
                        true
                    )
                ){
                    $require_url[] = $url;
                    $require_mtime[] = $mtime;
                    $this->config('require.url', $require_url);
                    $this->config('require.mtime', $require_mtime);
                }
            }
            if($read){
                try {
                    $data = new Data();
                    $data->do_not_nest_key($options['do_not_nest_key']);
                    $data->data(Core::object($read, Core::OBJECT_OBJECT));
                }
                catch(ObjectException $exception){
                    if($logger_error){
                        $this->logger($logger_error)->error('Syntax error in ' . $url . PHP_EOL . (string) $exception);
                    }
                    throw new ObjectException('Syntax error in ' . $url . PHP_EOL . (string) $exception );
                }
            } else {
                $data = new Data();
                $data->do_not_nest_key($options['do_not_nest_key']);
            }
            if(
                $attribute !== null &&
                $cache
            ){
                if(
                    array_key_exists('index', $options) &&
                    $options['index'] === 'create' &&
                    array_key_exists('class', $options) &&
                    is_string($options['class'])
                ){
                    $dir_ramdisk_record = $this->config('ramdisk.url') .
                        $this->config(Config::POSIX_ID) .
                        $this->config('ds') .
                        'Node' .
                        $this->config('ds') .
                        'Record' .
                        $this->config('ds')
                    ;
                    $dir_ramdisk_count = $this->config('ramdisk.url') .
                        $this->config(Config::POSIX_ID) .
                        $this->config('ds') .
                        'Node' .
                        $this->config('ds') .
                        'Count' .
                        $this->config('ds')
                    ;
                    Dir::create($dir_ramdisk_count, Dir::CHMOD);
                    Dir::create($dir_ramdisk_record, Dir::CHMOD);

                    $url_ramdisk_count = $dir_ramdisk_count . $attribute . $this->config('extension.txt');

                    File::permission($this, [
                        'ramdisk_dir_count' => $dir_ramdisk_count,
                        'ramdisk_dir_record' => $dir_ramdisk_record,
                    ]);
                    $count = 0;
                    $list = $data->get($options['class']);
                    $total = count($list);
                    $size = 0;
                    $mtime_count = false;
                    $filename = [];
                    if(File::exist($url_ramdisk_count)){
                        $mtime_count = File::mtime($url_ramdisk_count);;
                    }
                    if(
                        is_array($list) &&
                        $mtime !==
                        $mtime_count
                    ){
                        foreach($list as $nr => $record){
                            if(
                                is_object($record) &&
                                property_exists($record, 'uuid')
                            ){
                                /**
                                 * it is faster todo first all the objects to json-line and then write them to disk
                                 * instead of writing them one by one to disk
                                 */
                                $filename[$nr] = $dir_ramdisk_record . $record->uuid . $this->config('extension.json');
                                $file = Core::object($record, Core::OBJECT_JSON_LINE);
                                $list[$nr] = $file;
                                $size = mb_strlen($file);
                                $count++;
                                if($options['counter'] === true){
                                    if($count % 1000 === 0){
                                        echo Cli::tput('cursor.up');
                                        echo str_repeat(' ', Cli::tput('columns')) . PHP_EOL;
                                        echo Cli::tput('cursor.up');
                                        $item_per_second = $count / ((microtime(true) - $this->config('time.start')));
                                        $size_format = $item_per_second * $size;
                                        echo 'Prepare: ' . $count . '/', ($total) . ', percentage: ' . round(($count / ($total)) * 100, 2) . ' %, item per second: ' . round($item_per_second, 2) . ', ' . File::size_format($size_format) . '/sec' . PHP_EOL;
                                    }
                                }
                            }
                        }
                        if(!Dir::exist($dir_ramdisk_count)){
                            Dir::create($dir_ramdisk_count, Dir::CHMOD);
                        }
                        File::write($url_ramdisk_count, $count);
                        File::permission($this, [
                            'ramdisk_url_count' => $url_ramdisk_count,
                        ]);
                        File::touch($url_ramdisk_count, $mtime);
                    }
                    $count = 0;
                    foreach($list as $nr => $file){
                        if(!array_key_exists($nr, $filename)){
                                continue;
                        }
                        File::write($filename[$nr], $file);
                        $count++;
                        if($options['counter'] === true){
                            if($count % 1000 === 0){
                                echo Cli::tput('cursor.up');
                                echo str_repeat(' ', Cli::tput('columns')) . PHP_EOL;
                                echo Cli::tput('cursor.up');
                                $item_per_second = $count / ((microtime(true) - $this->config('time.start')));
                                $size_format = $item_per_second * $size;
                                echo 'Writing : ' . $count . '/', ($total) . ', percentage: ' . round(($count / ($total)) * 100, 2) . ' %, item per second: ' . round($item_per_second, 2) . ', ' . File::size_format($size_format) . '/sec' . PHP_EOL;
                            }
                        }
                    }
                    if($options['counter'] === true && $count >= 1){
                        echo Cli::tput('cursor.up');
                        echo str_repeat(' ', Cli::tput('columns')) . PHP_EOL;
                        echo Cli::tput('cursor.up');
                        $item_per_second = $count / ((microtime(true) - $this->config('time.start')));
                        $size_format = $item_per_second * $size;
                        echo 'Finalizing: ' . $count . '/', ($total) . ', percentage: ' . round(($count / ($total)) * 100, 2) . ' %, item per second: ' . round($item_per_second, 2) . ', ' . File::size_format($size_format) . '/sec' . PHP_EOL;
                    }
                }
                $cache->set($attribute, $data);
            }
            return $data;
        } else {
            return false;
        }
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function compile_read($url, $attribute=null, $flags=null, $options=null): mixed
    {
        if($options === null){
            $options = (object) [];
        }
        if($flags === null){
            $flags = (object) [];
        }
        $cache = $this->data(App::CACHE);
        if($attribute !== null){
            if($cache){
                $data = $cache->get($attribute);
                if(!empty($data)){
                    return $data;
                }
            }
        }
        if(File::exist($url)){
            $options->source = $url;
            $read = File::read($url);
            if($read){
                $mtime = File::mtime($url);
                $require_disabled = $this->config('require.disabled');
                if($require_disabled){
                    //nothing
                } else {
                    $require_url = $this->config('require.url');
                    $require_mtime = $this->config('require.mtime');
                    if(empty($require_url)){
                        $require_url = [];
                        $require_mtime = [];
                    }
                    if(
                        !in_array(
                            $url,
                            $require_url,
                            true
                        )
                    ){
                        $require_url[] = $url;
                        $require_mtime[] = $mtime;
                        $this->config('require.url', $require_url);
                        $this->config('require.mtime', $require_mtime);
                    }
                }
                $data = clone $this->data();
                unset($data->{App::NAMESPACE});
                $data = new Data($data);
                $parse = new ParseModule($this, $data, $flags, $options);
                $is_json = $this->config('package.raxon/parse.build.state.source.is.json');
                $this->config('package.raxon/parse.build.state.source.is.json', true);
                $read = Core::object($read);
                $read = $parse->compile($read, $data);

                $script = $this->data('script') ?? [];
                $script_merge = $data->get('script') ?? [];
                $script = array_merge($script, $script_merge);
                if(array_key_exists(0, $script)){
                    $this->data('script', $script);
                }
                $link = $this->data('link') ?? [];
                $link_merge = $data->get('link') ?? [];
                $link = array_merge($link, $link_merge);
                if(array_key_exists(0, $link)){
                    $this->data('link', $link);
                }
//                d($url);
//                d($read);
//                d($script);
                $data = new Data($read);
                /*
                $readback = [
                    'script',
                    'link'
                ];
                foreach($readback as $name){
                    $temp = ParseModule::readback($this, $parse, $name);
                    if(!empty($temp)){
                        $temp_old = $this->data($name);
                        if(empty($temp_old)){
                            $temp_old = [];
                        }
                        $temp = array_merge($temp_old, $temp);
                        $this->data($name, $temp);
                    }
                }
                */
                if($is_json !== null){
                    $this->config('package.raxon/parse.build.state.source.is.json', $is_json);
                } else {
                    $this->config('delete', 'package.raxon/parse.build.state.source.is.json');
                }
            } else {
                $data = new Data();
            }
            if($attribute !== null && $cache){
                $cache->set($attribute, $data);
            }
            return $data;
        } else {
            return false;
        }
    }

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public function parse_read($url, $attribute=null): mixed
    {
        $cache = $this->data(App::CACHE);
        if($attribute !== null){
            if($cache){
                $data = $cache->get($attribute);
                if(!empty($data)){
                    return $data;
                }
            }
        }
        if(File::exist($url)){
            $read = File::read($url);
            if($read){
                $mtime = File::mtime($url);
//                $parse = new Parse($this);
                $require_disabled = $this->config('require.disabled');
                if($require_disabled){
                    //nothing
                } else {
                    $require_url = $this->config('require.url');
                    $require_mtime = $this->config('require.mtime');
                    if(empty($require_url)){
                        $require_url = [];
                        $require_mtime = [];
                    }
                    if(
                        !in_array(
                            $url,
                            $require_url,
                            true
                        )
                    ){
                        $require_url[] = $url;
                        $require_mtime[] = $mtime;
                        $this->config('require.url', $require_url);
                        $this->config('require.mtime', $require_mtime);
                    }
                }
//                $parse->storage()->data('raxon.org.parse.view.url', $url);
//                $parse->storage()->data('raxon.org.parse.view.mtime', $mtime);
                $this->data('ldelim', '{');
                $this->data('rdelim', '}');
                $data = clone $this->data();
                unset($data->{App::NAMESPACE});
                $data = new Data($data);
                $flags = App::flags($this);
                $options = (object) [];
                $options->source = $url;
                $temp_source = $options->source ?? 'source';
                $options->source = 'internal_' . Core::uuid();
                $options->source_root = $temp_source;
                $options->class = Build::class_name($options->source);
                $parse = new ParseModule($this, $data, $flags, $options);
                $read = $parse->compile(Core::object($read), $data);
                $data = new Data($read);
                $readback = [
                    'script',
                    'link'
                ];
                foreach($readback as $name){
                    $temp = Parse::readback($this, $parse, $name);
                    if(!empty($temp)){
                        $temp_old = $data->data($name);
                        if(empty($temp_old)){
                            $temp_old = [];
                        }
                        $temp = array_merge($temp_old, $temp);
                        $data->data($name, $temp);
                    }
                }
            } else {
                $data = new Data();
            }
            if($attribute !== null && $cache){
                $cache->set($attribute, $data);
            }
            return $data;
        } else {
            return false;
        }
    }

    public static function is_cli() : bool
    {
        if(!defined('IS_CLI')){
            return Core::is_cli();
        } else {
            return true;
        }
    }

    /**
     * @throws Exception
     */
    public static function instance($configuration=[]): App
    {
        $dir_vendor = Dir::name(__DIR__, 3);
        $autoload = $dir_vendor . 'autoload.php';
        $autoload = require $autoload;
        $config = new Config([
            'dir.vendor' => $dir_vendor,
            ...$configuration
        ]);
        return new App($autoload, $config);
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    public function ramdisk_load($load=''): mixed
    {
        $prefixes = $this->config('ramdisk.autoload.prefix');
        if(
            !empty($prefixes) &&
            is_array($prefixes)
        ){
            foreach($prefixes as $prefix){
                $is_not = false;
                if(substr($prefix, 0, 1) === '!'){
                    $prefix = substr($prefix, 1);
                    $is_not = true;
                }
                $load_part = substr($load, 0, strlen($prefix));
                if($is_not && $load_part === $prefix){
                    return false;
                }
                if($load_part !== $prefix){
                    continue;
                }
                $part = str_replace('Raxon\\Org\\', '', $load);
                $part = str_replace('\\', '/', $part);
                $url = $this->config('framework.dir.source') . $part . $this->config('extension.php');
                $ramdisk_dir = false;
                $ramdisk_url = false;
                if(
                    $this->config('ramdisk.url') &&
                    empty($this->config('ramdisk.is.disabled'))
                ){
                    $ramdisk_dir = $this->config('ramdisk.url') .
                        $this->config(Config::POSIX_ID) .
                        $this->config('ds') .
                        App::NAME .
                        $this->config('ds')
                    ;
                    $ramdisk_url = $ramdisk_dir .
                        str_replace('/', '_', $part) .
                        $this->config('extension.php')
                    ;
                }
                $config_dir = $this->config('ramdisk.url') .
                    $this->config(Config::POSIX_ID) .
                    $this->config('ds') .
                    App::NAME .
                    $this->config('ds')
                ;
                $config_url = $config_dir .
                    'File.Mtime' .
                    $this->config('extension.json')
                ;
                $mtime = $this->get(sha1($config_url));
                if(empty($mtime)){
                    $mtime = [];
                    if(file_exists($config_url)){
                        $mtime = file_get_contents($config_url);
                        if($mtime){
                            $mtime = json_decode($mtime, true);
                            $this->set(sha1($config_url), $mtime);
                        }
                    }
                }
                if(
                    file_exists($ramdisk_url) &&
                    array_key_exists(sha1($ramdisk_url), $mtime) &&
                    file_exists($mtime[sha1($ramdisk_url)]) &&
                    filemtime($ramdisk_url) === filemtime($mtime[sha1($ramdisk_url)])
                ){
                    require_once $ramdisk_url;
                }
                elseif(file_exists($url)){
                    require_once $url;
                    if(
                        $ramdisk_dir &&
                        $ramdisk_url &&
                        $config_dir &&
                        $config_url
                    ){
                        //copy to ramdisk
                        //save filemtime
                        if(!is_dir($ramdisk_dir)){
                            mkdir($ramdisk_dir, 0750, true);
                        }
                        $read = file_get_contents($url);
                        $require = $this->config('ramdisk.autoload.require');
                        $is_require = false;
                        if(
                            !empty($require) &&
                            is_array($require) &&
                            in_array($load, $require, true)
                        ) {
                            $is_require = true;
                        }
                        if($is_require === false && Autoload::ramdisk_exclude_load($this, $load)){
                            //nothing to do...
                        }
                        elseif($is_require === false && Autoload::ramdisk_exclude_content($this, $read, $url)){
                            d($read);
                            d($load);
                            d($url);
                            //files with content __DIR__, __FILE__ cannot be cached
                            //save to /tmp/raxon/org/.../Autoload/Disable.Cache.json
                            ddd('exclude_content');
                        } else {
                            file_put_contents($ramdisk_url, $read);
                            touch($ramdisk_url, filemtime($url));
                            $mtime[sha1($ramdisk_url)] = $url;
                            if(!is_dir($config_dir)){
                                mkdir($config_dir, 0750, true);
                            }
                            file_put_contents($config_url, json_encode($mtime, JSON_PRETTY_PRINT));
                            $this->set(sha1($config_url), $mtime);
                            exec('chmod 640 ' . $ramdisk_url);
                            exec('chmod 640 ' . $config_url);
                        }
                    }
                }
            }
        }
        return false;
    }
}
