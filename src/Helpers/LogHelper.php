<?php

namespace Ecopro\Base\Helpers;

/**
 * @author liaiyong
 */
class LogHelper
{
    public function info($log, $name = 'LUMEN')
    {
        if(env("LOG_$name", env("LOG_GLOBAL", true))) {
            $request = app()->request;
            $log = empty($request->requestId) ? $log : "{$request->requestId} - $log";
            app('log')->withName($name)->info($log);
        }
    }

    public function error($log, $name = 'LUMEN')
    {
        $request = app()->request;
        $log = empty($request->requestId) ? $log : "{$request->requestId} - $log";
        app('error-log')->withName($name)->error($log);
    }

    public function debug($log, $name = 'DEBUG')
    {
        if(env("APP_DEBUG") && env("LOG_$name", env("LOG_GLOBAL", true))) {
            $request = app()->request;
            $log = empty($request->requestId) ? $log : "{$request->requestId} - $log";
            app('log')->withName($name)->debug($log);
        }
    }

    public function sql($log, $name = 'SQL')
    {
        if(env("LOG_$name", env("LOG_GLOBAL", true))) {
            $request = app()->request;
            $log = empty($request->requestId) ? $log : "{$request->requestId} - $log";
            app('sql-log')->withName($name)->info($log);
        }
    }
}
