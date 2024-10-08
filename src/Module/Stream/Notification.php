<?php
/**
 * @author          Remco van der Velde
 * @since           19-01-2023
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */
namespace Raxon\Module\Stream;


use Raxon\App;

use Raxon\Config;
use Raxon\Module\Core;
use Raxon\Module\Dir;
use Raxon\Module\File;
use Raxon\Module\Parse\Token;

use Exception;

use Raxon\Exception\FileWriteException;
use Raxon\Exception\ObjectException;

class Notification {

    /**
     * @throws ObjectException
     * @throws FileWriteException
     * @throws Exception
     */
    public static function is_new(App $object, $action='', $options=[], &$config=false, &$tokens=[]): bool
    {
        $id = posix_geteuid();
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
            throw new Exception('Only root & www-data can check is_new...');
        }
        if(
            $object->config('ramdisk.url') &&
            empty($object->config('ramdisk.is.disabled'))
        ){
            $dir = $object->config('ramdisk.url') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds')
            ;
        } else {
            $dir = $object->config('project.dir.temp') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds')
            ;
        }
        $url = $dir .
            'Stream' .
            $object->config('extension.json');
        if(!File::exist($url)){
            Dir::create($dir, Dir::CHMOD);
            //init stream filter
            File::copy($object->config('framework.dir.data') . 'Stream' . $object->config('extension.json'), $url);
        }
        $config = $object->data_read($url);
        $tree = Token::tree('{' . $options['notification'] . '}', [
            'with_whitespace' => true
        ]);
        $notifications = [];
        foreach($tree as $notification){
            $notifications[] = $notification;
        }
        $tokens = $notifications;
        if($object->config('ramdisk.url') && empty($object->config('ramdisk.is.disabled'))){
            $dir_stream =
                $object->config('ramdisk.url') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds')
            ;
        } else {
            $dir_stream =
                $object->config('project.dir.temp') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds')
            ;
        }
        $dir_require =
            $dir_stream .
            'Document' .
            $object->config('ds')
        ;
        $is_new = true;
        if($config && $config->has('stream.notification')) {
            foreach ($config->data('stream.notification') as $stream) {
                if (
                    property_exists($stream, 'action') &&
                    $stream->action === $action &&
                    property_exists($stream, 'create') &&
                    property_exists($stream->create, 'filter') &&
                    !empty($stream->create->filter) &&
                    is_array($stream->create->filter)
                ) {
                    $filters = $stream->create->filter;
                    foreach ($filters as $filter) {
                        if (
                            property_exists($filter, 'document') &&
                            !empty($filter->document) &&
                            is_array($filter->document)
                        ) {
                            foreach ($filter->document as $uuid) {
                                $require = $dir_require . $uuid . '.stream';
                                if (File::exist($require)) {
                                    $read = File::read($require);
                                    $tree = Token::tree('{' . $read . '}', [
                                        'with_whitespace' => true
                                    ]);
                                    $require_tokens = [];
                                    foreach ($tree as $require_token) {
                                        $require_tokens[] = $require_token;
                                    }
                                    if (
                                        property_exists($filter, 'where') &&
                                        !empty($filter->where) &&
                                        is_array($filter->where)
                                    ) {
                                        foreach ($filter->where as $where) {
                                            if (
                                                property_exists($where, 'token') &&
                                                $where->token === 'token' &&
                                                property_exists($where, 'operator')
                                            ) {
                                                switch ($where->operator) {
                                                    case '===' :
                                                        foreach ($require_tokens as $nr => $record) {
                                                            $record = Core::object($record, Core::OBJECT_ARRAY);
                                                            $notification = [];
                                                            if (array_key_exists($nr, $notifications)) {
                                                                $notification = $notifications[$nr];
                                                            }
                                                            $is_match = Token::compare($record, $notification, [
                                                                'operator' => '==='
                                                            ]);
                                                            if ($is_match) {

                                                            } else {
                                                                unset($notifications[$nr]);
                                                                unset($require_tokens[$nr]);
                                                            }
                                                        }
                                                        break;
                                                    case '!==' :
                                                        foreach ($require_tokens as $nr => $record) {
                                                            $record = Core::object($record, Core::OBJECT_ARRAY);
                                                            $notification = [];
                                                            if (array_key_exists($nr, $notifications)) {
                                                                $notification = $notifications[$nr];
                                                            }
                                                            $is_match = Token::compare($record, $notification, [
                                                                'operator' => '==='
                                                            ]);
                                                            if ($is_match) {
                                                                unset($notifications[$nr]);
                                                                unset($require_tokens[$nr]);
                                                            }
                                                        }
                                                        break;
                                                }
                                            }
                                            if (
                                                property_exists($where, 'key') &&
                                                property_exists($where, $where->key) &&
                                                property_exists($where, 'operator')
                                            ) {
                                                $key = $where->key;
                                                switch ($where->operator) {
                                                    case '===' :
                                                        foreach ($require_tokens as $nr => $record) {
                                                            $record = Core::object($record, Core::OBJECT_ARRAY);
                                                            if ($record[$key] === $where->$key) {
                                                                unset($notifications[$nr]);
                                                                unset($require_tokens[$nr]);
                                                            }
                                                        }
                                                        break;
                                                    case '!==' :
                                                        foreach ($require_tokens as $nr => $record) {
                                                            $record = Core::object($record, Core::OBJECT_ARRAY);
                                                            if ($record[$key] !== $where->$key) {
                                                                unset($notifications[$nr]);
                                                                unset($require_tokens[$nr]);
                                                            }
                                                        }
                                                        break;
                                                    case 'in.array' :
                                                        foreach ($require_tokens as $nr => $record) {
                                                            $record = Core::object($record, Core::OBJECT_ARRAY);
                                                            if (
                                                                !in_array(
                                                                    $record[$key],
                                                                    $where->$key,
                                                                    true
                                                                )
                                                            ) {
                                                                unset($notifications[$nr]);
                                                                unset($require_tokens[$nr]);
                                                            }
                                                        }
                                                        break;
                                                    case '!in.array' :
                                                        foreach ($require_tokens as $nr => $record) {
                                                            $record = Core::object($record, Core::OBJECT_ARRAY);
                                                            if (
                                                                in_array(
                                                                    $record[$key],
                                                                    $where->$key,
                                                                    true
                                                                )
                                                            ) {
                                                                unset($notifications[$nr]);
                                                                unset($require_tokens[$nr]);
                                                            }
                                                        }
                                                        break;
                                                    default:
                                                        throw new Exception('Unknown operator in where filter...');
                                                }
                                            }
                                        }
                                    }
                                    if (count($notifications) >= 1) {
                                        //is_new = true
                                    } else {
                                        $is_new = false;
                                        break 3;
                                    }
                                } else {
                                    $documents = $filter->document;
                                    foreach ($documents as $nr => $document) {
                                        if ($document === $uuid) {
                                            unset($documents[$nr]);
                                        }
                                    }
                                    $filter->document = $documents;
                                    $config->write($url);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $is_new;
    }

    /**
     * @throws Exception
     */
    public static function clean(App $object, $action, $options=[]){
        $id = posix_geteuid();
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
            throw new Exception('Only root & www-data can clean notifications...');
        }
        if(
            $object->config('ramdisk.url') &&
            !empty($object->config('ramdisk.is.disabled'))
        ){
            $url = $object->config('ramdisk.url') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds') .
                'Stream' .
                $object->config('extension.json');
        } else {
            $url = $object->config('project.dir.temp') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds') .
                'Stream' .
                $object->config('extension.json');
        }
        $config = $object->data_read($url);
        $is_stream = false;
        if($config && $config->has('stream.notification')) {
            foreach ($config->data('stream.notification') as $stream) {
                if (
                    property_exists($stream, 'action') &&
                    $stream->action === $action &&
                    property_exists($stream, 'clean') &&
                    property_exists($stream->clean, 'frequency')
                ) {
                    $is_stream = $stream;
                    break;
                }
            }
        }
        if(empty($is_stream)){
            return;
        }
        if(
            property_exists($is_stream, 'create') &&
            property_exists($is_stream->create, 'filter')
        ){
            foreach($is_stream->create->filter as $filter){
                if(
                    property_exists($filter, 'document') &&
                    !empty($filter->document) &&
                    is_array($filter->document)
                ){
                    $document = [];
                    foreach($filter->document as $uuid){
                        if(
                            $object->config('ramdisk.url') &&
                            empty($object->config('ramdisk.is.disabled'))
                        ){
                            $document_url = $object->config('ramdisk.url') .
                                $object->config(Config::POSIX_ID) .
                                $object->config('ds') .
                                'Stream' .
                                $object->config('ds') .
                                'Document' .
                                $object->config('ds') .
                                $uuid .
                                '.stream'
                            ;
                        } else {
                            $document_url = $object->config('project.dir.temp') .
                                $object->config(Config::POSIX_ID) .
                                $object->config('ds') .
                                'Stream' .
                                $object->config('ds') .
                                'Document' .
                                $object->config('ds') .
                                $uuid .
                                '.stream'
                            ;
                        }
                        switch($is_stream->clean->frequency){
                            case 'direct':
                                File::delete($document_url);
                            break;
                            case 'hourly':
                                $hour_of_day = date('H');
                                $day_of_year = date('z');
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_hour_of_day = date('H', $mtime);
                                    $file_year = date('Y', $mtime);
                                    $file_day_of_year = date('z', $mtime);
                                    if(
                                        $hour_of_day <> $file_hour_of_day ||
                                        $year <> $file_year ||
                                        $day_of_year <> $file_day_of_year
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                            case 'daily':
                                $day_of_year = date('z');
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_year = date('Y', $mtime);
                                    $file_day_of_year = date('z', $mtime);
                                    if(
                                        $year <> $file_year ||
                                        $day_of_year <> $file_day_of_year
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                            case 'weekly':
                                $week_of_year = date('w');
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_year = date('Y', $mtime);
                                    $file_week_of_year = date('w', $mtime);
                                    if(
                                        $year <> $file_year ||
                                        $week_of_year <> $file_week_of_year
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                            case 'bi-weekly':
                                $week_of_year = date('w');
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_year = date('Y', $mtime);
                                    $file_week_of_year = date('w', $mtime);
                                    if(
                                        $year <> $file_year ||
                                        $week_of_year - $file_week_of_year >= 2
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                            case 'monthly':
                                $month_of_year = date('n');
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_year = date('Y', $mtime);
                                    $file_month_of_year = date('n', $mtime);
                                    if(
                                        $year <> $file_year ||
                                        $month_of_year <> $file_month_of_year
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                            case 'quarterly':
                                $season = [
                                    1 => 1,
                                    2 => 1,
                                    3 => 1,
                                    4 => 2,
                                    5 => 2,
                                    6 => 2,
                                    7 => 3,
                                    8 => 3,
                                    9 => 3,
                                    10 => 4,
                                    11 => 4,
                                    12 => 4
                                ];
                                $month_of_year = date('n');
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_year = date('Y', $mtime);
                                    $file_month_of_year = date('n', $mtime);
                                    if(
                                        $year <> $file_year ||
                                        $season[$month_of_year] <> $season[$file_month_of_year]
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                            case 'half-yearly':
                                $season = [
                                    1 => 1,
                                    2 => 1,
                                    3 => 1,
                                    4 => 1,
                                    5 => 1,
                                    6 => 1,
                                    7 => 2,
                                    8 => 2,
                                    9 => 2,
                                    10 => 2,
                                    11 => 2,
                                    12 => 2
                                ];
                                $month_of_year = date('n');
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_year = date('Y', $mtime);
                                    $file_month_of_year = date('n', $mtime);
                                    if(
                                        $year <> $file_year ||
                                        $season[$month_of_year] <> $season[$file_month_of_year]
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                            case 'yearly':
                                $year = date('Y');
                                if(File::exist($document_url)){
                                    $mtime = File::mtime($document_url);
                                    $file_year = date('Y', $mtime);
                                    if(
                                        $year <> $file_year
                                    ){
                                        File::delete($document_url);
                                    } else {
                                        $document[] = $uuid;
                                    }
                                }
                            break;
                        }
                    }
                    $filter->document = $document;
                }
            }
        }
        $config->write($url);
    }


    /**
     * @throws FileWriteException
     * @throws ObjectException
     * @throws Exception
     */
    public static function create(App $object, $action='', $config=false, $tokens=[], $notification='') {
        $id = posix_geteuid();
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
            throw new Exception('Only root & www-data can create notifications...');
        }
        if(
            $object->config('ramdisk.url') &&
            empty($object->config('ramdisk.is.disabled'))
        ){
            $dir_stream = $object->config('ramdisk.url') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds')
            ;
        } else {
            $dir_stream = $object->config('project.dir.temp') .
                $object->config(Config::POSIX_ID) .
                $object->config('ds') .
                'Stream' .
                $object->config('ds')
            ;
        }
        $url = $dir_stream .
            'Stream' .
            $object->config('extension.json');
        $uuid = Core::uuid();

        $dir_stream_document =
            $dir_stream .
            'Document' .
            $object->config('ds');
        Dir::create($dir_stream_document, Dir::CHMOD);
        $write = $notification;
        File::write($dir_stream_document . $uuid . '.stream', $write);
        $number_tokens = Token::filter($tokens, [
            'where' => [
                0 => [
                    'key' => 'type',
                    'type' => [
                        Token::TYPE_INT,
                        Token::TYPE_FLOAT,
                        Token::TYPE_HEX,
                    ],
                    'operator' => 'in.array'
                ]
            ]
        ]);
        $add_filter_not_in_array = false;
        if (!empty($number_tokens)) {
            $add_filter_not_in_array = true;
        }
        $config_notifications = $config->get('stream.notification');
        $is_found = false;
        $is_where = false;
        foreach ($config_notifications as $nr => $config_notification) {
            if (
                property_exists($config_notification, 'action') &&
                $config_notification->action === $action
            ) {
                $is_found = $config_notification;
                unset($config_notifications[$nr]);
                break;
            }
        }
        if ($is_found) {
            if (
                property_exists($is_found, 'create') &&
                property_exists($is_found->create, 'filter') &&
                !empty($is_found->create->filter) &&
                is_array($is_found->create->filter)
            ) {
                foreach ($is_found->create->filter as $filter) {
                    if (
                        property_exists($filter, 'where') &&
                        !empty($filter->where) &&
                        is_array($filter->where)
                    ) {
                        foreach ($filter->where as $where) {
                            if (
                                property_exists($where, 'token') &&
                                $where->token === 'token' &&
                                $add_filter_not_in_array === false
                            ) {
                                $is_where = $where;
                                break;
                            }
                            if ($add_filter_not_in_array) {
                                if (
                                    property_exists($where, 'operator') &&
                                    $where->operator === '!in.array'
                                ) {
                                    if(!is_array($filter->document)){
                                        $filter->document = [];
                                    }
                                    $filter->document[] = $uuid;
                                }
                            }
                        }
                        if (count($filter->where) === 1 && $is_where !== false) {
                            if(!is_array($filter->document)){
                                $filter->document = [];
                            }
                            $filter->document[] = $uuid;
                        }
                    }
                }
            }
            $config_notifications[] = $is_found;
            $result = [];
            foreach ($config_notifications as $config_notification) {
                $result[] = $config_notification;
            }
            $config->set('stream.notification', $result);
            $config->write($url);
        }
    }
}