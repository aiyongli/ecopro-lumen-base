<?php
namespace Ecopro\Base\Concerns;

use Ecopro\Base\Helpers\InstanceHelper;

trait Instance
{
    /**
     * 实例化，支持自动注入参数，不支持其他参数
     * @return static
     */
    public static function instance()
    {
        return InstanceHelper::instance(get_called_class(), func_get_args());
    }
}
