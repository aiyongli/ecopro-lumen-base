<?php

namespace Ecopro\Base\Listeners;

use Illuminate\Http\Request;
use Ecopro\Base\Concerns\Helper;
use Ecopro\Base\Events\RequestEvent;

class RequestListener
{
    use Helper;

    /**
     * Handle the event.
     *
     * @param  RequestEvent  $event
     * @return void
     */
    public function handle(RequestEvent $event)
    {
        // 记录Request日志
        $this->logRequest($event->request);
    }

    /**
     * 记录请求日志
     */
    private function logRequest(Request $request)
    {
        $method = strtoupper($request->getMethod());

        $uri = $request->getPathInfo();
        $ip = $request->ip();
        $ua = $request->header('User-Agent');

        $post = $request->request->all();
        $query = $request->query->all();
        $content = $request->getContent();
        if(!empty($content) && empty($post)) {
            // 非标准请求体，如XML
            $bodyAsJson = str_replace("\n", '', $content);
        } else {
            $body = $request->except(config('http.except')) ? : new \stdClass;
            $bodyAsJson = json_encode($body);
        }
        $queryAsJson = json_encode($query);
        $header = [];
        $appid = $request->header('Appid');
        $token = $request->header('Authorization');
        $originAppid = $request->header('Origin-Appid');
        if($appid) {
            $header[] = "Appid: {$appid}";
        }
        if($token) {
            $header[] = "Authorization: *";
        }
        if($originAppid) {
            $header[] = "Origin-Appid: {$originAppid}";
        }
        $headerAsJson = json_encode($header);

        $files = [];
        foreach($request->files->all() as $file) {
            if(is_array($file)) {
                foreach($file as $f) {
                    $files[] = $f->getRealPath();
                }
            } else {
                $files[] = $file->getRealPath();
            }
        }

        $message = "IP: {$ip} - {$method} {$uri} - Query: {$queryAsJson} - Header: {$headerAsJson} - Body: {$bodyAsJson} - Files: ".implode(', ', $files)." - User-Agent: {$ua}";

        $this->logHelper()->info($message, 'REQUEST');
    }
}
