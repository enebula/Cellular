<?php
/**
 *
 * Cellular Framework
 * 辅助类
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-9
 *
 */

namespace core;

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

    /**
     * URI生成
     */
    public static function URL($controller = null, $action = null, $param = null)
    {
        $url = WEBROOTPATH;
        if ($controller != null) {
            $url .= DIRECTORY_SEPARATOR.$controller;
        }
        if ($action != null) {
            $url .= DIRECTORY_SEPARATOR.$action;
        }
        if ($param != null) {
            $url .= DIRECTORY_SEPARATOR.'?';
            $_var = null;
            foreach ($param as $key => $value) {
                $_var .= '&'.$key.'='.$value;
            }
            $url .= substr($_var, 1);
        }
        return $url;
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
}

?>
