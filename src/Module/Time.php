<?php
/**
 * @author          Remco van der Velde
 * @since           05-06-2025
 * @copyright       (c) Remco van der Velde
 * @license         MIT
 * @version         1.0
 * @changeLog
 *  -    all
 */
namespace Raxon\Module;

class Time {
    const IN = 'In ';
    const ALMOST_THERE = 'Almost there';
    const MSEC = 'msec';
    const SEC = 'sec';
    const SECOND = 'second';
    const SECONDS = 'seconds';
    const MIN = 'min';
    const MINUTE = 'minute';
    const MINUTES = 'minutes';
    const H = 'h';
    const HOUR = 'hour';
    const HOURS = 'hours';
    const D = 'd';
    const DAY = 'day';
    const DAYS = 'days';
    const _AND_ = 'and';

    public static function format(int|float $seconds=0, string $string=Time::IN, $compact=false): string
    {
        $days = floor($seconds / (3600 * 24));
        $hours = floor((int) ($seconds / 3600) % 24);
        $minutes = floor((int) ($seconds / 60) % 60);
        $explode = explode('.', $seconds);
        $msec = (int) $explode[1] ?? 0;
        $msec = (float) ('0' . '.' . $msec);
        $msec = round(($msec), 3) * 1000;
        $seconds = (int) $seconds % 60;
        if($days > 0){
            if($compact){
                $string .= $days . ' ' . Time::D . ' ';
            } else {
                if($days === 1){
                    $string .= $days . ' ' . Time::DAY . ' ' . Time::_AND_ . ' ';
                } else {
                    $string .= $days . ' ' . Time::DAYS . ' ' . Time::_AND_ . ' ';
                }
            }
        }
        if($hours > 0){
            if($compact){
                $string .= $hours . ' ' . Time::H . ' ';
            } else {
                if($hours === 1){
                    $string .= $hours . ' ' . Time::HOUR . ' ' . Time::_AND_ . ' ';
                } else {
                    $string .= $hours . ' ' . Time::HOURS . ' ' . Time::_AND_ . ' ';
                }
            }
        }
        if ($minutes > 0){
            if($compact){
                $string .= $minutes . ' ' . Time::MIN . ' ';
            } else {
                if($minutes === 1){
                    $string .= $minutes . ' ' . Time::MINUTE . ' ' . Time::_AND_ . ' ';
                } else {
                    $string .= $minutes . ' ' . Time::MINUTES . ' ' . Time::_AND_ . ' ';
                }
            }

        }
        if($seconds < 1){
            if($days === 0 && $hours === 0 && $minutes === 0){
                if($compact){
                    $string = round($seconds, 3) * 1000 . ' ' . Time::MSEC;
                } else {
                    $string = Time::ALMOST_THERE;
                }
            } else {
                if($compact){
                    $string .= $seconds . '.' . $msec . ' ' . Time::SEC;
                } else {
                    $string .= $seconds . '.' . $msec . ' ' . Time::SECONDS;
                }
            }

        } else {
            if($compact){
                $string .= $seconds . '.' . $msec . ' ' . Time::SEC;
            } else {
                if($seconds === 1){
                    $string .= $seconds . '.' . $msec . ' ' . Time::SECOND;
                } else {
                    $string .= $seconds . '.' . $msec . ' ' . Time::SECONDS;
                }
            }
        }
        return $string;
    }
}
