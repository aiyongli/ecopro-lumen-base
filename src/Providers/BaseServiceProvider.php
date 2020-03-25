<?php

namespace Ecopro\Base\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Ecopro\Base\Concerns\Helper;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * @author liaiyong
 */
class BaseServiceProvider extends ServiceProvider
{
    use Helper;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 异常日志
        $this->app->singleton('error-log', function($app) {
            return new Logger('SYSTEM');
        });
        // SQL日志
        $this->app->singleton('sql-log', function($app) {
            return new Logger('SYSTEM');
        });
    }

    /**
     * 启动所有应用服务
     *
     * @return void
     */
    public function boot()
    {
        // SQL日志
        DB::listen(function(QueryExecuted $query) {
            $log = "{$query->sql} - Bindings: ". json_encode($query->bindings) . " - Time: {$query->time}ms";
            //
            $this->logHelper()->sql($log, 'SQL');
        });
        // 日志配置，自定义日期格式
        $handler = new RotatingFileHandler(env('LOG_FILE', storage_path("logs/lumen.log")));
        $handler->setFilenameFormat('{date}', 'Ym/d');
        app('log')->setHandlers([$handler]);
        // $handlers = app('log')->getHandlers();
        // foreach ($handlers as $handler) {
        //     $handler->setFilenameFormat('{date}', 'Ym/d');
        // }
        // 异常日志配置
        $handler = new RotatingFileHandler(env('LOG_FILE_ERROR', storage_path("logs/error.log")));
        $handler->setFilenameFormat('{date}.{filename}', 'Ym/d');
        app('error-log')->setHandlers([$handler]);
        // SQL日志配置
        $handler = new RotatingFileHandler(env('LOG_FILE_SQL', storage_path("logs/sql.log")));
        $handler->setFilenameFormat('{date}.{filename}', 'Ym/d');
        app('sql-log')->setHandlers([$handler]);
    }
}
