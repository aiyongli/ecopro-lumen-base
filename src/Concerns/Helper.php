<?php
namespace Ecopro\Base\Concerns;

use Ecopro\Base\Helpers\InstanceHelper;
use Ecopro\Base\Helpers\LogHelper;

trait Helper
{
    /**
     * @return LogHelper
     */
    public static function logHelper()
    {
        return InstanceHelper::instance(LogHelper::class);
    }
}
