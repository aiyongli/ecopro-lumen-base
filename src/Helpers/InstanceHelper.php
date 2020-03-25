<?php

namespace Ecopro\Base\Helpers;

/**
 * @author liaiyong
 */
class InstanceHelper
{
    private static $singletons = [];
    /**
     * 单例实例化，支持自动注入参数，不支持其他参数
     *
     * @param string $name
     * @param array  $parameters
     * @return mixed|\Laravel\Lumen\Application
     */
    public static function singleton($name, array $parameters = [])
    {
        if(empty(static::$singletons[$name])) {
            static::$singletons[$name] = static::instance($name, $parameters);
        }
        return static::$singletons[$name];
    }
    /**
     * 实例化（使用lumen的app函数，如果在lumn配置中定义为单例，这里实例化是也是单例），支持自动注入参数，不支持其他参数
     *
     * @param string $name
     * @param array  $parameters
     * @return mixed|\Laravel\Lumen\Application
     */
    public static function instance($name, array $parameters = [])
    {
        return app($name, $parameters);
    }
}
