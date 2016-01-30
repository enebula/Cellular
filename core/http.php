<?php
/**
 *
 * Cellular base http
 * HTTP请求访问类
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-22
 *
 */

namespace core;

class http {

    public function get($url, $port = 80)
    {
        $curl = \curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_PORT, $port);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //跳过SSL检查
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);;
        $header = array();
        //$header[] = 'Content-Type: text/xml; charset=GBK';
        //$header[] = 'Content-Length: '.strlen($value);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function post($url, $value, $port = 80)
    {
        $curl = \curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_PORT, $port);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //跳过SSL检查
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        if (is_array($value)) $value = http_build_query($value);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $value);
        $header = array();
        //$header[] = 'Content-Type: text/xml; charset=GBK';
        //$header[] = 'Content-Length: '.strlen($value);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}

?>
