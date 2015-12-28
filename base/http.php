<?php
/**
 *
 * Cellular base http
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-22
 *
 */

namespace base;

class http
{
  public function get()
  {
      echo 'ok';
  }

  public function post($url, $value)
  {
    $header = array();
    //$header[] = 'Content-Type: text/xml; charset=GBK';
    //$header[] = 'Content-Length: '.strlen($value);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //跳过SSL检查
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $value);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
  }
}

?>
