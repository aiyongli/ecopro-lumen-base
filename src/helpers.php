<?php

if(!function_exists('empty_func')) {
    function empty_func($var)
    {
        return empty($var);
    }
}

if(!function_exists('not_empty_func')) {
    function not_empty_func($var)
    {
        return !empty_func($var);
    }
}

if(!function_exists('empty_object_func')) {
    function empty_object_func($var)
    {
        return empty($var) || (is_object($var) && empty(get_object_vars($var)));
    }
}

if(!function_exists('not_empty_object_func')) {
    function not_empty_object_func($var)
    {
        return !empty_object_func($var);
    }
}

if(!function_exists('create_directory')) {
    /**
     * 创建目录
     * @param string $dir
     * @param boolean $recursive 是否递归创建
     */
    function create_directory($dir, $recursive = true)
    {
        $sub = dirname($dir);
        if(file_exists($dir)) {
            return is_dir($dir) ? true : false;
        } else if($recursive && is_dir($sub)) {
            return is_writable($sub) && mkdir($dir);
        } else if($recursive) {
            return create_directory($sub, $recursive) && mkdir($dir);
        }
        return is_dir($sub) && is_writable($sub) && mkdir($dir);
    }
}

if(!function_exists('auto_string_array')) {
    /**
     * 如果字符串自动分割成数组
     * @param string|array $str
     * @param string $delimiter
     * @return array
     */
    function auto_string_array($str, $delimiter = ',')
    {
        return empty($str) ? [] : ( is_string($str) ? explode($delimiter, $str) : $str );
    }
}

if(!function_exists('auto_string_integer')) {
    /**
     * 字符串转数值
     * @param string $str
     * @param string $invalid 如果非数值空字符串时
     * @return array
     */
    function auto_string_integer($str, $invalid = null)
    {
        return is_numeric($str) ? intval($str) : $invalid;
    }
}

if(!function_exists('auto_datetime')) {
    /**
     * 字符串日期时间格式
     * @param string $str
     * @param string $invalid 如果非时间字符串时
     * @return string
     */
    function auto_datetime($str, $invalid = null)
    {
        return empty($str) ? $invalid : (strtotime($str) === false ? $invalid : date('Y-m-d H:i:s', strtotime($str)));
    }
}

if(!function_exists('auto_date')) {
    /**
     * 字符串日期格式
     * @param string $str
     * @param string $invalid 如果非时间字符串时
     * @return string
     */
    function auto_date($str, $invalid = null)
    {
        return empty($str) ? $invalid : (strtotime($str) === false ? $invalid : date('Y-m-d', strtotime($str)));
    }
}

if(!function_exists('auto_empty_zero')) {
    /**
     * 如果值为空，使用返回0
     * @param string|int $str
     * @param string|int $zero
     * @return string|int
     */
    function auto_empty_zero($number, $zero = 0)
    {
        return empty($number) ? $zero : $number;
    }
}

if(!function_exists('auto_file_extension')) {
    /**
     * 文件后缀加点号
     * @param string $ext
     * @param string $dot
     * @return string
     */
    function auto_file_extension($ext, $lower = true)
    {
        $dot = ".";
        $extension = strpos($ext, $dot) === 0 ? $ext : "$dot{$ext}";
        return empty($ext) ? " " : $lower ? strtolower($extension) : $extension;
    }
}

if(!function_exists('auto_array_object')) {
    /**
     * 数组转对象
     * @param array $arr
     * @param mixed $empty_value 判断为空时的默认值，默认：\stdClass
     * @return array|object
     */
    function auto_array_object($arr, $empty_value = null)
    {
        $empty_value = isset($empty_value) ? $empty_value : new \stdClass;
        return empty($arr) ? $empty_value : json_decode(json_encode($arr));
    }
}

if(!function_exists('auto_json_array_object')) {
    /**
     * JSON字符串转对象，非JSON字符串、非JSON对象字符串、非JSON数组字符串原样返回
     * @param string|array|object $json
     * @param mixed $empty_value 判断为空时的默认值，默认：\stdClass
     * @return array|object|string
     */
    function auto_json_array_object($json, $empty_value = null)
    {
        $empty_value = isset($empty_value) ? $empty_value : new \stdClass;
        $obj = empty($json) ? $empty_value : (is_scalar($json) ? json_decode($json) : json_decode(json_encode($json)));

        return is_null($obj) || is_scalar($obj) ? $json : $obj;
    }
}

if(!function_exists('integer_chinese')) {
    /**
     * 数字转换为中文
     * @param  integer  $num  目标数字
     */
    function integer_chinese($num)
    {
        $chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $chiUni = array('','十', '百', '千', '万', '亿', '十', '百', '千');

        $chiStr = '';

        $num_str = (string)$num;

        $count = strlen($num_str);
        $last_flag = true; //上一个 是否为0
        $zero_flag = true; //是否第一个
        $temp_num = null; //临时数字

        $chiStr = '';//拼接结果
        if ($count == 2) {//两位数
            $temp_num = $num_str[0];
            $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num].$chiUni[1];
            //  当以1开头 都是十一，十二，以十开头的 我们就取$chiUni[i]也就是十
            // 当不是以1开头时，而是以2,3,4,我们取这个数字相应的中文并拼接上十
            $temp_num = $num_str[1];
            $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
        // 取得第二个值并的到他的中文
        }else if($count > 2){
            $index = 0;
            for ($i=$count-1; $i >= 0 ; $i--) {
                $temp_num = $num_str[$i];         //获取的个位数
                if ($temp_num == 0) {
                    if (!$zero_flag && !$last_flag ) {
                        $chiStr = $chiNum[$temp_num]. $chiStr;
                        $last_flag = true;
                    }
                }else{
                    $chiStr = $chiNum[$temp_num].$chiUni[$index%9] .$chiStr;
                    //$index%9 index原始值为0，所以开头为0 后面根据循环得到：0,1,2,3...（不知道为什么直接用$index而是选择$index%9  毕竟两者结果是一样的）
                    //当输入的值为：1003 ，防止输出一千零零三的错误情况，$last_flag就起到作用了当翻译倒数第二个值时，将$last_flag设定为true;翻译第三值时在if(!$zero&&!$last_flag)的判断中会将其拦截，从而跳过
                    $zero_flag = false;
                    $last_flag = false;
                }
                $index ++;
            }
        }else{
            $chiStr = $chiNum[$num_str[0]];    //单个数字的直接取中文
        }
        return $chiStr;
    }
}

if(!function_exists('number_chinese')) {
    /**
     * 数字格式化成汉字
     *
     * @param number $figure 待格式化的数字
     * @param boolean $capital 使用汉字大写数字
     * @param boolean $mode 单字符转换模式
     * @return string
     */
    function number_chinese($figure, $capital = false, $mode = true) {

        $numberChar = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
        $unitChar = ['', '十', '百', '千', '', '万', '亿', '兆', '京', '垓', '秭', '穣', '沟', '涧', '正', '载', '极', '恒河沙', '阿僧祇', '那由他', '不可思议', '无量大数'];
        if ($capital !== false) {
            $numberChar = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
            $unitChar = ['', '拾', '佰', '仟', '', '万', '亿', '兆', '京', '垓', '秭', '穣', '沟', '涧', '正', '载', '极', '恒河沙', '阿僧祇', '那由他', '不可思议', '无量大数'];
        }

        $dec = "点";
        $target = '';
        $matches = [];

        if ($mode) {
            preg_match("/^0*(\d*)\.?(\d*)/", $figure, $matches);
        } else {
            preg_match("/(\d*)\.?(\d*)/", $figure, $matches);
        }

        list(, $number, $point) = $matches;

        if ($point) {
            $target = $dec . number_chinese($point, $capital, false);
        }

        if (!$number) {
            return $target;
        }

        $str = strrev($number);
        for ($i = 0; $i < strlen($str); $i++) {
            $out[$i] = $numberChar[$str[$i]];
            if ($mode === false) {
                continue;
            }
            $out[$i] .= $str[$i] != '0' ? $unitChar[$i % 4] : '';
            if ($i > 0 && $str[$i] + $str[$i - 1] == 0) {
                $out[$i] = '';
            }
            if ($i % 4 == 0) {
                $temp = substr($str, $i, 4);
                $out[$i] = str_replace($numberChar[0], '', $out[$i]);
                if (strrev($temp) > 0) {
                    $out[$i] .= $unitChar[4 + floor($i / 4)];
                } else {
                    $out[$i] .= $numberChar[0];
                }
            }
        }
        return join('', array_reverse($out)) . $target;
    }
}

if(!function_exists('alpha2int')) {
    /**
     * 将二十六进制字母串转换为十进制数字
     * @param string $strAlpha
     */
    function alpha2int($strAlpha)
    {
        if (ord($strAlpha{0}) > 90) {
            $startCode = 97;
            $reduceCode = 10;
        } else {
            $startCode = 65;
            $reduceCode = -22;
        }
        $num26 = '';
        for ($i=0; $i<strlen($strAlpha); $i++) {
            $code = ord($strAlpha{$i});
            if ($code < $startCode+10) {
                $num26 .= $code-$startCode;
            } else {
                $num26 .= chr($code-$reduceCode);
            }
        }
        return (int)base_convert($num26, 26, 10);
    }
}

if(!function_exists('int2alpha')) {
    /**
     * 将十进制数字转换为二十六进制字母串
     * @param int $intNum
     */
    function int2alpha($intNum, $isLower = false)
    {
        $num26 = base_convert($intNum, 10, 26);
        $addcode = $isLower ? 49 : 17;
        $result = '';
        for ($i=0; $i<strlen($num26); $i++) {
            $code = ord($num26{$i});
            if ($code < 58) {
            $result .= chr($code+$addcode);
            } else {
            $result .= chr($code+$addcode-39);
            }
        }
        return $result;
    }
}

if(!function_exists('int2any')) {
    /**
     * 十进制转换三十六进制
     */
    function int2any($int, $format = 2, $dic = array(
        0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9',
        10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F', 16 => 'G', 17 => 'H', 18 => 'I',
        19 => 'J', 20 => 'K', 21 => 'L', 22 => 'M', 23 => 'N', 24 => 'O', 25 => 'P', 26 => 'Q', 27 => 'R',
        28 => 'S', 29 => 'T', 30 => 'U', 31 => 'V', 32 => 'W', 33 => 'X', 34 => 'Y', 35 => 'Z')) {
        $arr = array();
        $loop = true;
        while ($loop) {
            $arr[] = $dic[bcmod($int, 36)];
            $int = floor(bcdiv($int, 36));
            if ($int == 0) {
                $loop = false;
            }
        }
        $arr = array_pad($arr, $format, $dic[0]);
        return implode('', array_reverse($arr));
    }
}

if(!function_exists('any2int')) {
    /**
     * 三十六进制转换十进制
     */
    function any2int($id, $format = 2, $dic = array(
        0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9',
        10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F', 16 => 'G', 17 => 'H', 18 => 'I', 19 => 'J', 20 => 'K', 21 => 'L', 22 => 'M', 23 => 'N', 24 => 'O', 25 => 'P', 26 => 'Q', 27 => 'R', 28 => 'S', 29 => 'T', 30 => 'U', 31 => 'V', 32 => 'W', 33 => 'X', 34 => 'Y', 35 => 'Z'
        )) {
        // 键值交换
        $dedic = array_flip($dic);
        // 去零
        $id = ltrim($id, $dic[0]);
        // 反转
        $id = strrev($id);
        $v = 0;
        for($i = 0, $j = strlen($id); $i < $j; $i++) {
            $v = bcadd(bcmul($dedic[$id{$i}] , bcpow(36, $i)) , $v);
        }
        return $v;
    }
}

if(!function_exists('zero_format')) {
    /**
     * 字符串前置字符至指定长度
     */
    function zero_format($str, $len, $prefix = '0')
    {
        if(strlen($str) > $len) {
            return $str;
        }
        return zero_format("{$prefix}{$str}", $len, $prefix);
    }
}

if(!function_exists('array_pick')) {
    /**
     * 取出二维数组或对象数组中的某些属性值
     * @param array $arr
     * @param array|string $key
     */
    function array_pick($arr, $key)
    {
        return array_map(function($one) use ($key) {
            if(is_array($key)) {
                $tmp = [];
                foreach ($key as $k) {
                    $tmp[$k] = is_object($one) ? $one->$k : $one[$k];
                }
                return is_object($one) ? json_decode(json_encode($tmp)) : $tmp;
            } else {
                return is_object($one) ? $one->$key : $one[$key];
            }
        }, $arr);
    }
}

if(!function_exists('array_filter_empty')) {
    /**
     * 过滤数组中的空元素，返回非空的元素
     * @param array $arr
     * @return array
     */
    function array_filter_empty($arr)
    {
        return array_values(array_filter($arr, 'not_empty_func'));
    }
}

if(!function_exists('array_deep')) {
    /**
     * 多维数组转为一维数组
     * @param array $arr
     * @return array
     */
    function array_deep($arr)
    {
        $ret = [];
        if(!is_object($arr) && is_array($arr)) {
            array_map(function($v) use (&$ret) {
                if(!is_object($v) && is_array($v)) {
                    $tmps = array_deep($v);
                    foreach ($tmps as $tmp) {
                        $ret[] = $tmp;
                    }
                } else {
                    $ret[] = $v;
                }
            }, $arr);
        }
        return $ret;
    }
}

if(!function_exists('money')) {
    /**
     * 金额格式
     * @param number $number
     * @return string
     */
    function money($number, $decimal = 2)
    {
        $decimal = is_int($decimal) && $decimal >= 0 ? $decimal : 2;
        return is_numeric($number) ? sprintf("%.{$decimal}f", doubleval($number)) : sprintf("%.{$decimal}f", doubleval($number));
    }
}
