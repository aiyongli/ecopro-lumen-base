<?php
namespace Ecopro\Base\Concerns;

use Ecopro\Base\Events\OutputEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Output
{
    private function convert($data = null, ...$args)
    {
        if(is_null($data)) {
            return '';
        } else if(is_scalar($data)) {
            return $data;
        } else if(is_callable($data)) {
            return call_user_func($data, ...$args);
        } else if(is_array($data)) {
            return $this->convertArray($data);
        } else if(is_object($data) && method_exists($data, 'convert')) {
            return $this->convert($data->convert());
        } else if($data instanceof Collection) {
            return $this->convert($data->all());
        } else if($data instanceof Model) {
            return $this->convert($data->toArray());
        } else if($data instanceof Arrayable) {
            return $this->convert($data->toArray());
        } else if(is_object($data)) {
            return $this->convertObject($data);
        }

        throw new \Exception('Unsupported data for output!');
    }

    private function convertArray(&$arr)
    {
        foreach($arr as $key => $value) {
            // if($key === 'id') {
            //     $value=(string) $value;
            // }
            $arr[$key] = $this->convert($value);
        }

        return $arr;
    }

    private function convertObject(&$obj)
    {
        foreach($obj as $key => $value) {
            $obj->$key = $this->convert($value);
        }

        return $obj;
    }

    /**
     * @param string|object|array|LengthAwarePaginator|null $data
     * @return array
     */
    public function success($data = '', $message = GLOBAL_SUCCESS, ...$args)
    {
        if($data instanceof LengthAwarePaginator) {
            $result = $this->paginate($data->total(), $data->items(), ...$args);
        } else {
            $reqid = app()->request->requestId;
            preg_match('/^(\d+)\|(.+)$/', $message, $result);
            list(, $code, $msg) = $result;

            $result = ['result' => 'success', 'code' => $code, 'msg' => $msg, 'data' => ['dataInfo' => $this->convert($data, ...$args)], 'reqid' => $reqid, 'timestamp' => time()];
        }
        OutputEvent::instance()->setResult($result)->fire();

        return $result;
    }

    /**
     * @param int $total
     * @param string|array $data
     * @return array
     */
    protected function paginate($total, $data = [], ...$args)
    {
        $pageName = PAGINATE_PAGE_NAME;
        $pageSizeName = PAGINATE_PAGE_SIZE_NAME;
        $pageTotalName = PAGINATE_PAGE_TOTAL_NAME;
        $totalName = PAGINATE_TOTAL_NAME;
        $page = app()->request->input($pageName, 1);
        $pageSize = app()->request->input($pageSizeName, 10);
        if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
            $page = (int) $page;
        } else {
            $page = 1;
        }
        if (filter_var($pageSize, FILTER_VALIDATE_INT) !== false && (int) $pageSize >= 1) {
            $pageSize = (int) $pageSize;
        } else {
            $pageSize = 10;
        }

        $reqid = app()->request->requestId;
        preg_match('/^(\d+)\|(.+)$/', GLOBAL_SUCCESS, $result);
        list(, $code, $msg) = $result;

        $result = ['result' => 'success', 'code' => $code, 'msg' => $msg, 'data' => [ 'pageInfo' => [ $pageName => $page, $pageSizeName => $pageSize, $pageTotalName => ceil($total / $pageSize), $totalName => $total, 'data' => $this->convert($data, ...$args)] ], 'reqid' => $reqid, 'timestamp' => time()];

        return $result;
    }

    /**
     * @param int $total
     * @param string|array $data
     * @return array
     */
    public function error($message, $data = '', ...$args)
    {
        $reqid = app()->request->requestId;
        if($message instanceof \Exception) {
            $e = $message;
            $code = $e->getCode();
            $message = $e->getMessage();
            $file = Str::replaceFirst(base_path(), '', $e->getFile());
            $line = $e->getLine();
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
        }
        if(preg_match('/^(\d+)\|(.+)$/', $message, $result) > 0) {
            list(, $code, $msg) = $result;
            $data = in_array(env('APP_ENV'), ['prod', 'pro']) && is_array($data) ? reset($data) : $data;
            return ['result' => 'error', 'code' => $code, 'msg' => $msg, 'data' => ['errorInfo' => $this->convert($data, ...$args)], 'reqid' => $reqid, 'timestamp' => time()];
        } else {
            if(!empty($e)) {
                return $this->error(GLOBAL_ERR_1001, $data);
            }
            $trace = debug_backtrace();
            // $file = $trace[0]['file'];
            // $line = $trace[0]['line'];
            // $data = "{$message} in {$file}:{$line}";
            $data = [];
            foreach ($trace as $key => $value) {
                if(isset($value['file'])) {
                    $file = Str::replaceFirst(base_path(), '', $value['file']);
                    $line = $value['line'];
                    $data[] = empty($key) ? "{$message} in {$file}:{$line}" : "{$file}:{$line}";
                } elseif(isset($value['class']))  {
                    $class = $value['class'];
                    $type = $value['type'];
                    $function = $value['function'];
                    $data[] = empty($key) ? "{$message} in \\{$class}{$type}{$function}" : "\\{$class}{$type}{$function}";
                } else {
                    $function = $value['function'];
                    $data[] = empty($key) ? "{$message} in {$function}" : "{$function}";
                }
            }

            return $this->error(GLOBAL_ERR_1001, $data);
        }
    }
}
