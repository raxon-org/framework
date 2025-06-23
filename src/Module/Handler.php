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


use stdClass;
use Raxon\App;
use DateTimeImmutable;

use Exception;

use Raxon\Exception\ObjectException;

class Handler {
    const NAMESPACE = __NAMESPACE__;
    const NAME = 'Handler';
    const NAME_SESSION = 'Session';
    const NAME_REQUEST = 'Request';
    const NAME_COOKIE = 'Cookie';

    const NAME_HEADER = 'Header';
    const NAME_INPUT = 'Input';
    const NAME_FILE = 'File';

    const SESSION = 'session';
    const SESSION_HAS = 'has';
    const SESSION_START = 'start';
    const SESSION_CLOSE = 'close';
    const SESSION_DELETE = 'delete';

    const REQUEST = 'request';
    const REQUEST_HEADER = 'request.header';
    const REQUEST_INPUT = 'request.input';
    const REQUEST_FILE = 'request.file';

    const COOKIE_DELETE = 'delete';

    const METHOD_CLI = 'CLI';
    const DELETE = 'DELETE';
    const GET = 'GET';
    const PATCH = 'PATCH';
    const POST = 'POST';
    const PUT = 'PUT';
    const HEAD = 'HEAD';
    const CONNECT = 'CONNECT';
    const OPTIONS = 'OPTIONS';
    const TRACE = 'TRACE';

    const METHODS = [
        Handler::DELETE,
        Handler::GET,
        Handler::PATCH,
        Handler::POST,
        Handler::PUT,
        Handler::HEAD,
        Handler::CONNECT,
        Handler::OPTIONS,
        Handler::TRACE
    ];


    const UPLOAD_ERR_INI_SIZE = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
    const UPLOAD_ERR_FORM_SIZE = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
    const UPLOAD_ERR_PARTIAL = 'The uploaded file was only partially uploaded.';
    const UPLOAD_ERR_NO_FILE = 'No file was uploaded.';
    const UPLOAD_ERR_NO_TMP_DIR = 'Missing a temporary folder.';
    const UPLOAD_ERR_CANT_WRITE = 'Failed to write file to disk.';
    const UPLOAD_ERR_EXTENSION = 'A PHP extension stopped the file upload.';


    /**
     * @throws ObjectException
     * @throws Exception
     */
    public static function request_configure(App $object): void
    {
        $object->data(
            App::NAMESPACE . '.' .
            Handler::NAME_REQUEST . '.' .
            Handler::NAME_HEADER,
            Handler::request_header()
        );
        $object->data(
            App::NAMESPACE . '.' .
            Handler::NAME_REQUEST . '.' .
            Handler::NAME_INPUT,
            Handler::request_input()
        );
        $object->data(
            App::NAMESPACE . '.' .
            Handler::NAME_REQUEST . '.' .
            Handler::NAME_FILE,
            Handler::request_file($object)
        );
    }

    private static function request_header(): object
    {
        //check if cli
        if(defined('IS_CLI')){
            //In Cli mode apache functions aren't defined
            return Core::array_object($_SERVER);
        } else {
            return Core::array_object(apache_request_headers());
        }
    }

    public static function header(string $string='', int|string|null $http_response_code=null, bool $replace=true): mixed
    {
        if(empty($string)){
            return headers_list();
        }
        if(
            $string == 'delete' &&
            is_string($http_response_code)
        ){
            header_remove($http_response_code);
        }
        elseif(
            $string == 'has' &&
            is_string($http_response_code)
        ){
          $list = headers_list();
          $header_list = [];
          foreach($list as $nr => $record){
              $tmp = explode(':', $record, 2);
              $key = rtrim($tmp[0], ' ');
              $value = ltrim($tmp[1], ' ');
              $header_list[$key] = $value;
          }
          if(array_key_exists($http_response_code, $header_list)){
              return true;
          }
          return false;
        }
        elseif(
            $string == 'get' &&
            is_string($http_response_code)
        ){
            $list = headers_list();
            $header_list = [];
            foreach($list as $nr => $record){
                $tmp = explode(':', $record, 2);
                $key = rtrim($tmp[0], ' ');
                $value = ltrim($tmp[1], ' ');
                $header_list[$key] = $value;
            }
            if(array_key_exists($http_response_code, $header_list)){
                return $header_list[$http_response_code];
            }
            return null;
        }
        elseif($http_response_code !== null){
            if(!headers_sent()){
                header($string, $replace, $http_response_code);
            }
        } else {
            if(!headers_sent()) {
                header($string, $replace);
            }
        }
        return null;
    }

    private static function addErrorMessage(App $object, array $record): array
    {
        if(!array_key_exists('error', $record)){
            return $record;
        }
        $errorMessage = $object->request('error-' . $record['error']);
        if($errorMessage){
            $record['errorMessage'] = $errorMessage;
            return $record;
        } else {
            switch($record['error']){
                case UPLOAD_ERR_OK :
                    return $record;
                case UPLOAD_ERR_INI_SIZE :
                    $record['errorMessage'] = Handler::UPLOAD_ERR_INI_SIZE;
                    return $record;
                case UPLOAD_ERR_FORM_SIZE :
                    $record['errorMessage'] = Handler::UPLOAD_ERR_FORM_SIZE;
                    return $record;
                case UPLOAD_ERR_PARTIAL :
                    $record['errorMessage'] = Handler::UPLOAD_ERR_PARTIAL;
                    return $record;
                case UPLOAD_ERR_NO_FILE :
                    $record['errorMessage'] = Handler::UPLOAD_ERR_NO_FILE;
                    return $record;
                case UPLOAD_ERR_NO_TMP_DIR :
                    $record['errorMessage'] = Handler::UPLOAD_ERR_NO_TMP_DIR;
                    return $record;
                case UPLOAD_ERR_CANT_WRITE :
                    $record['errorMessage'] = Handler::UPLOAD_ERR_CANT_WRITE;
                    return $record;
                case UPLOAD_ERR_EXTENSION :
                    $record['errorMessage'] = Handler::UPLOAD_ERR_EXTENSION;
                    return $record;
            }
        }
        return $record;
    }

    private static function request_file(App $object): object
    {
        $nodeList = array();
        foreach ($_FILES as $category => $list){
            if(is_array($list)){
                foreach($list as $attribute => $subList){
                    if(is_array($subList)){
                        $nr = false;
                        foreach ($subList as $nr => $value){
                            $nodeList[$nr][$attribute] = $value;
                        }
                        if($nr){
                            $nodeList[$nr]['input_name'] = $category;
                            $nodelist[$nr] = Handler::addErrorMessage($object, $nodeList[$nr]);
                        }
                    } else {
                        $list['input_name'] = $category;
                        $list = Handler::addErrorMessage($object, $list);
                        $nodeList[] = $list;
                        break;
                    }
                }
            }
        }
        return Core::array_object($nodeList);
    }

    /**
     * @throws ObjectException
     */
    private static function request_key_group(array|object $data): object
    {
        $result = (object) [];
        foreach($data as $key => $value){
            $explode = explode('.', $key, 4);
            if(!isset($explode[1])){
                $result->{$key} = $value;
                continue;
            }
            $temp = Core::object_horizontal($explode, $value);
            $result = Core::object_merge($result, $temp);
        }
        return $result;
    }

    /**
     * @throws ObjectException
     * @throws Exception
     */
    private static function request_input(): Data
    {
        $data = new Data();
        if(defined('IS_CLI')){
            global $argc, $argv;
            $temp = $argv;
            array_shift($temp);
            $request = $temp;
            $request = Core::array_object($request);
            foreach($request as $key => $value){
                $key = str_replace(['-', '_'], ['.', '.'], $key);
                $data->set($key, trim($value));
            }
        } else {
            $request = Handler::request_key_group($_REQUEST);
            if(!property_exists($request, 'request')){
                $uri = ltrim($_SERVER['REQUEST_URI'], '/');
                $uri = explode('?', $uri, 2);
                $request->request = $uri[0];
                $query = Handler::query($uri[1]);
                d($uri[1]);
                ddd($query);
                if(empty($request->request)){
                    $request->request = '/';
                }
            } else {
                $uri = ltrim($_SERVER['REQUEST_URI'], '/');
                $uri = explode('?', $uri, 2);
                $request->request = $uri[0];
                $query = Handler::query($uri[1]);
                d($uri[1]);
                ddd($query);
                if(empty($request->request)){
                    $request->request = '/';
                }                
            }
            ddd($query);
            foreach($request as $attribute => $value){
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
                    }
                }
                $data->set($attribute, $value);
            }
            /* --backend-disabled
            $input =
                htmlspecialchars(
                    htmlspecialchars_decode(
                        implode(
                            '',
                            file('php://input')
                        ),
                        ENT_NOQUOTES
                    ),
                    ENT_NOQUOTES,
                    'UTF-8'
                );
            */
            $input = implode('', file('php://input'));
            if(!empty($input)){
                $input = json_decode($input);
            }
            if(!empty($input)){
                if(is_object($input) || is_array($input)){
                    foreach($input as $key => $record){
                        if(
                            is_object($record) &&
                            property_exists($record, 'name') &&
                            property_exists($record, 'value') &&
                            $record->name != 'request'
                        ){
                            if($record->value !== null){
                                if(is_numeric($record->value)){
                                    $record->value = $record->value + 0;
                                } else {
                                    switch($record->value){
                                        case 'true':
                                            $record->value = true;
                                            break;
                                        case 'false':
                                            $record->value = false;
                                            break;
                                        case 'null':
                                            $record->value = null;
                                            break;
                                    }
                                }
                                //$record->name = str_replace(['-', '_'], ['.', '.'], $record->name);
                                $data->set($record->name, $record->value);
                            }
                        } else {
                            if($record !== null){
                                if(is_numeric($record)){
                                    $record = $record + 0;
                                } else {
                                    switch($record){
                                        case 'true':
                                            $record = true;
                                            break;
                                        case 'false':
                                            $record = false;
                                            break;
                                        case 'null':
                                            $record = null;
                                            break;
                                    }
                                }
                                //$key = str_replace(['-', '_'],  ['.', '.'], $key);
                                $data->set($key, $record);
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    public static function query($query=''): array
    {
        parse_str($query, $result);
        ddd($result);
        foreach($result as $key => $value){
            $key_original =  $key;
            if(
                in_array(
                    substr($key, 0, 1),
                    [
                        '\'',
                        '"'
                    ],
                    true
                )
            ){
                $key = substr($key, 1);
            }
            if(
                in_array(
                    substr($key, -1, 1),
                    [
                        '\'',
                        '"'
                    ],
                    true
                )
            ){
                $key = substr($key, 1);
            }
            unset($result[$key_original]);
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public static function method(): string
    {
        if(array_key_exists('REQUEST_METHOD', $_SERVER)){
            if(
                in_array(
                    $_SERVER['REQUEST_METHOD'],
                    Handler::METHODS,
                    true
                )
            ){
                return $_SERVER['REQUEST_METHOD'];
            }
        }
        elseif(defined('IS_CLI')){
            return Handler::METHOD_CLI;
        }
        throw new Exception('Method undefined');
    }

    /**
     * @throws ObjectException
     */
    public static function session_set_cookie_params(array|object $options=[]): bool
    {
        if(
            !empty($options) &&
            is_object($options)
        ){
            $options = Core::object($options, Core::OBJECT_ARRAY);
        }
        if(!headers_sent()){
            return session_set_cookie_params($options);
        }
        return false;
    }


    /**
     * @throws Exception
     */
    public static function session(mixed $attribute=null, mixed $value=null): mixed
    {
        if($attribute == Handler::SESSION_HAS && $value === null){
            return isset($_SESSION);
        }
        elseif($attribute == Handler::SESSION_CLOSE){
            session_write_close();
            return null;
        }
        if(!isset($_SESSION)){
            if(headers_sent()){
               return null;
            }
            session_start();
            $_SESSION['id'] = session_id();
            if(empty($_SESSION['csrf'])){
                $_SESSION['csrf'] =
                rand(1000,9999) . '-' .
                rand(1000,9999) . '-' .
                rand(1000,9999) . '-' .
                rand(1000,9999)
                ;
            }
        }
        if($attribute !== null){
            if(
                (is_object($attribute) || is_array($attribute)) &&
                $value === null
            ){
                foreach($attribute as $key => $value){
                    Handler::session($key, $value);
                    if(isset($_SESSION)){
                        return $_SESSION;
                    }
                }
            } else {
                $tmp = explode('.', $attribute);
                if($value !== null){
                    if($attribute === 'id'){
                        return session_id($value);
                    }
                    elseif($attribute === 'has'){
                        $get = Handler::session($value);
                        if($get === null){
                            return false;
                        } else {
                            return true;
                        }
                    }
                    elseif($attribute === 'extract'){
                        $get = Handler::session($value);
                        Handler::session('delete', $value);
                        return $get;
                    }
                    elseif(
                        $attribute == Handler::SESSION_DELETE &&
                        $value == Handler::SESSION
                    ){
                        $unset = session_unset();
                        if($unset === false){
                            throw new Exception('Could not unset session');
                        }
                        $destroy = session_destroy();
                        if($destroy === false){
                            throw new Exception('Could not destroy session');
                        }
                    }
                    elseif($attribute == Handler::SESSION_DELETE){
                        $tmp = explode('.', $value);
                        switch(count($tmp)){
                            case 1 :
                                unset(
                                    $_SESSION
                                    [$value]
                                );
                                break;
                            case 2 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                );
                                break;
                            case 3 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                );
                                break;
                            case 4 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                    [$tmp[3]]
                                );
                                break;
                            case 5 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                    [$tmp[3]]
                                    [$tmp[4]]
                                );
                                break;
                            case 6 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                    [$tmp[3]]
                                    [$tmp[4]]
                                    [$tmp[5]]
                                );
                                break;
                            case 7 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                    [$tmp[3]]
                                    [$tmp[4]]
                                    [$tmp[5]]
                                    [$tmp[6]]
                                );
                                break;
                            case 8 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                    [$tmp[3]]
                                    [$tmp[4]]
                                    [$tmp[5]]
                                    [$tmp[6]]
                                    [$tmp[7]]
                                );
                                break;
                            case 9 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                    [$tmp[3]]
                                    [$tmp[4]]
                                    [$tmp[5]]
                                    [$tmp[6]]
                                    [$tmp[7]]
                                    [$tmp[8]]
                                );
                                break;
                            case 10 :
                                unset(
                                    $_SESSION
                                    [$tmp[0]]
                                    [$tmp[1]]
                                    [$tmp[2]]
                                    [$tmp[3]]
                                    [$tmp[4]]
                                    [$tmp[5]]
                                    [$tmp[6]]
                                    [$tmp[7]]
                                    [$tmp[8]]
                                    [$tmp[9]]
                                );
                                break;
                        }
                        return true;
                    } else {
                        if(is_array($value) || is_object($value)){
                            $value = Core::object($value, Core::OBJECT_ARRAY); //session can only handle array
                        }
                        switch(count($tmp)){
                            case 1 :
                                $_SESSION
                                [$attribute] = $value;
                                break;
                            case 2 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]] = $value;
                                break;
                            case 3 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]] = $value;
                                break;
                            case 4 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]]
                                [$tmp[3]] = $value;
                                break;
                            case 5 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]]
                                [$tmp[3]]
                                [$tmp[4]] = $value;
                                break;
                            case 6 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]]
                                [$tmp[3]]
                                [$tmp[4]]
                                [$tmp[5]] = $value;
                                break;
                            case 7 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]]
                                [$tmp[3]]
                                [$tmp[4]]
                                [$tmp[5]]
                                [$tmp[6]] = $value;
                                break;
                            case 8 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]]
                                [$tmp[3]]
                                [$tmp[4]]
                                [$tmp[5]]
                                [$tmp[6]]
                                [$tmp[7]] = $value;
                                break;
                            case 9 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]]
                                [$tmp[3]]
                                [$tmp[4]]
                                [$tmp[5]]
                                [$tmp[6]]
                                [$tmp[7]]
                                [$tmp[8]] = $value;
                                break;
                            case 10 :
                                $_SESSION
                                [$tmp[0]]
                                [$tmp[1]]
                                [$tmp[2]]
                                [$tmp[3]]
                                [$tmp[4]]
                                [$tmp[5]]
                                [$tmp[6]]
                                [$tmp[7]]
                                [$tmp[8]]
                                [$tmp[9]] = $value;
                                break;
                        }
                    }
                }
                switch(count($tmp)){
                    case 1 :
                        if(isset($_SESSION[$attribute])){
                            return $_SESSION[$attribute];
                        } else {
                            return null;
                        }
                    case 2 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]];
                        } else {
                            return null;
                        }
                    case 3 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]];
                        } else {
                            return null;
                        }
                    case 4 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]];
                        } else {
                            return null;
                        }
                    case 5 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]];
                        } else {
                            return null;
                        }
                    case 6 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]];
                        } else {
                            return null;
                        }
                    case 7 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]];
                        } else {
                            return null;
                        }
                    case 8 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]];
                        } else {
                            return null;
                        }
                    case 9 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]][$tmp[8]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]][$tmp[8]];
                        } else {
                            return null;
                        }
                    case 10 :
                        if(
                            isset($_SESSION[$tmp[0]]) &&
                            is_array($_SESSION[$tmp[0]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]][$tmp[8]]) &&
                            is_array($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]][$tmp[8]]) &&
                            isset($_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]][$tmp[8]][$tmp[9]])
                        ){
                            return $_SESSION[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]][$tmp[5]][$tmp[6]][$tmp[7]][$tmp[8]][$tmp[9]];
                        } else {
                            return null;
                        }
                }
            }

        } else {
            return $_SESSION;
        }
        return null;
    }

    public static function cookie(mixed $attribute=null, mixed $value=null, int|null $duration=null): mixed
    {
        $result = '';
        $cookie = [];
        if($attribute !== null) {
            if(
                (is_object($attribute) || is_array($attribute)) &&
                $value === null
            ){
                foreach($attribute as $key => $value){
                    if(is_object($value)){
                        if(
                            property_exists($value, 'duration') &&
                            property_exists($value, 'value')
                        ){
                            Handler::cookie($key, $value->value, $value->duration);
                        }
                        elseif(
                            property_exists($value, 'params') &&
                            property_exists($value, 'value') &&
                            is_array($value->params)
                        ){
                            Handler::cookie($key, $value->value, $value->params);
                        }
                        elseif(
                            property_exists($value, 'parameters') &&
                            property_exists($value, 'value') &&
                            is_array($value->parameters)
                        ){
                            Handler::cookie($key, $value->value, $value->parameters);
                        }
                        elseif(
                            property_exists($value, 'value')
                        ){
                            Handler::cookie($key, $value->value);
                        }
                    }
                    return $_COOKIE;
                }
            } else {
                if ($value !== null) {
                    if ($attribute == Handler::COOKIE_DELETE) {
                        $result = @setcookie($value, '', 0, "/"); //ends at session
                        if (!empty($result) && defined('IS_CLI')) {
                            unset($_COOKIE[$value]);
                        }
                        return null;
                    } else {
                        if ($duration === null) {
                            $duration = 60 * 60 * 24 * 365 * 2; // 2 years
                        }
                        if(is_array($duration)){
                            $result = @setcookie($attribute, $value, $duration);
                        }
                        elseif(is_object($duration) && $duration instanceof DateTimeImmutable){
                            $result = @setcookie($attribute, $value, $duration->getTimestamp(), "/");
                        } else {
                            $result = @setcookie($attribute, $value, time() + $duration, "/");
                        }
                        if (!empty($result) && defined('IS_CLI')) {
                            $cookie[$attribute] = $value;
                        }
                    }
                }
                if($value === null && is_array($duration)){
                    $value = '';
                    $result = @setcookie($attribute, $value, $duration);
                }
            }

        }
        if(array_key_exists('HTTP_COOKIE', $_SERVER)){
            $explode = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($explode as $nr => $raw){
                $temp = explode('=', $raw, 2);
                $cookie[trim($temp[0], ' ')] = $temp[1];
            }
        }
        if($attribute === null){
            return $cookie;
        }
        if(array_key_exists($attribute, $cookie)){
            return $cookie[$attribute];

        } else {
            return $cookie;
        }
    }
}