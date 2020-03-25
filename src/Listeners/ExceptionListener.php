<?php

namespace Ecopro\Base\Listeners;

use Illuminate\Support\Str;
use Ecopro\Base\Concerns\Helper;
use Ecopro\Base\Events\ExceptionEvent;

class ExceptionListener
{
    use Helper;

    /**
     * Handle the event.
     *
     * @param  ExceptionEvent  $event
     * @return void
     */
    public function handle(ExceptionEvent $event)
    {
        $e = $event->exception;
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        if(preg_match('/^(\d+)\|(.+)$/',$message,$result) > 0) {
            // 业务错误信息
            list(,$code, $msg) = $result;
            $log = $message;
        } else {
            $trace = $e->getTrace();//dd($trace);
            $data = [];
            $data[] = "{$message} in {$file}:{$line}";
            foreach ($trace as $key => $value) {
                if(isset($value['file'])) {
                    $file = Str::replaceFirst(base_path(), '', $value['file']);
                    $line = $value['line'];
                    $data[] = "{$file}:{$line}";
                } elseif(isset($value['class'])) {
                    $class = $value['class'];
                    $type = $value['type'];
                    $function = $value['function'];
                    $data[] = "\\{$class}{$type}{$function}";
                } else {
                    $function = $value['function'];
                    $data[] = "{$function}";
                }
            }
            $log = implode(" ", $data);
        }

        // $log = "$message in $file:$line";
        $this->logHelper()->error($log, 'REQUEST');
    }
}
