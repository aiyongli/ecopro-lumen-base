<?php

namespace Ecopro\Base\Middleware;

use Closure;
use Ecopro\Base\Events\RequestEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @author liaiyong
 */
class Http
{
    public function handle(Request $request, Closure $next)
    {
        // 唯一请求ID
        $request->requestId = $this->createRequestId();
        // HTTP请求日志处理
        RequestEvent::instance()->setRequest($request)->fire();

        return $next($request);
    }

    /**
     * 生成唯一请求ID
     */
    private function createRequestId()
    {
        return Str::uuid()->toString();
    }
}
