<?php
namespace Ecopro\Base\Concerns;

use Ecopro\Base\Helpers\InstanceHelper;

trait Singleton
{
    /**
     * 单例实例化，支持自动注入参数，不支持其他参数
     * @return static
     */
    public static function singleton()
    {
        return InstanceHelper::singleton(get_called_class(), func_get_args());
    }
}
