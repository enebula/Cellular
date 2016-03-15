<?php
/**
 * Cellular Framework
 * 辅助功能类
 * @copyright Cellular Team
 */

namespace core;
use Cellular;


class Helper {
    /**
    * 加载实例类
    */
    public static function token($len = 32, $md5 = true)
    {
        mt_srand((double)microtime() * 1000000);
        $chars = array(
            'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
            'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
            '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
            'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
            '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
            '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
            'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
        );
        $numChars = count($chars) - 1;
        $token = '';
        # Create random token at the specified length
        for ($i=0; $i<$len; $i++) $token .= $chars[mt_rand(0, $numChars)];
        # Should token be run through md5?
        if ( $md5 ) {
            # Number of 32 char chunks
            $chunks = ceil(strlen($token) / 32);
            $md5token = '';
            # Run each chunk through md5
            for ( $i=1; $i<=$chunks; $i++ ) $md5token .= md5(substr($token, $i * 32 - 32, 32));
            # Trim the token
            $token = substr($md5token, 0, $len);
        }
        return $token;
    }

    public static function randNum($length = 6)
    {
        $number = null;
        while ($length) {
            $number .= mt_rand(0, 9);
            $length--;
        }
        return $number;
    }

    /**
     * 字符串转换为数组格式
     */
    public function strArray($str, $split = ',', $assign = '=')
    {
        if ($str) {
            $arr = array();
            $temp = strpos($str, $split) ? explode($split, $str) : [$str];
            foreach ($temp as $value) {
                $value = explode($assign, $value);
                $arr[$value[0]] = $value[1];
            }
            return $arr;
        }
        return null;
    }

    /**
     * URI生成
     */
    public static function URL($controller = null, $action = null, $param = null)
    {
        return Cellular::getURL($controller, $action, $param);
    }

    public static function location($controller = null, $action = null, $param = null)
    {
        header('location: '.self::URL($controller, $action, $param));
        exit();
    }


    public static function UUID()
    {
        //有重复的可能行，建议谨慎使用
        return md5(uniqid(md5(microtime(true)),true));
    }

    /**
     * 获取访问IP
     * @return string IP地址
     */
    public static function ip() {
    	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    	{
    		$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    		$pos = array_search('unknown', $arr);
    		if(false !== $pos) unset($arr[$pos]);
    		$ip = trim($arr[0]);
    	}
    	elseif(isset($_SERVER['HTTP_CLIENT_IP']))
    	{
    		$ip = $_SERVER['HTTP_CLIENT_IP'];
    	}
    	elseif (isset($_SERVER['REMOTE_ADDR']))
    	{
    		$ip = $_SERVER['REMOTE_ADDR'];
    	}
        if (sprintf("%u",ip2long($ip))) {
            return $ip;
        }
        return '0.0.0.0';
    }

    /**
     * 字符转义
     * 列内容如存在半角逗号 , 则用半角双引号 " 将该字段值包含起来
     * 列内容如存在半角双引号 " 则应替换成两个半角双引号 "" 并用半角双引号 " 将该字段值包含起来
     * 禁止某些字段被自动转换为十六进制科学计算法 1.在列内容前面加 "\t" 制表符（实测加在前面实际文件中是一个空格 加在后面是tab制表符 2.用双引号 "" 将列包含起来 并在前面加上 = 号 即 ="0123"
     */
    public static function escape($str) {
        if (stripos($str, ',') !== false || stripos($str, '"') !== false) {
            if (stripos($str, '"') !== false) {
                $str = str_replace('"', '""', $str);
            }
            $str =  '"' . $str . '"';
        }
        return $str;
    }

    public static function randName () {
        return date('YmdHis') . substr(microtime(), 2, 6);
    }

    public static function floatToInteger($float, $precision)
    {
        $number = sprintf('%.' . $precision . 'f', $float); //补零操作
        $number = str_replace('.', '', $number); //在取小数点
        return intval($number);
    }
}

?>
