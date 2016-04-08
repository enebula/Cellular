<?php
/**
 * Cellular Framework
 * OpenSSL 封装
 * @copyright Cellular Team
 */

namespace core;


class OpenSSL {

    private $pubKey; //公钥
    private $priKey; //私钥

    /**
    * 设置私钥
    */
    public function setPriKey($key, $password = null)
    {
        openssl_pkcs12_read($key, $cert, $password); //读取密钥
        $this->priKey = $cert['pkey'];
    }

    /**
    * 设置公钥
    */
    public function setPubKey($key)
    {
        $pubKey = '-----BEGIN CERTIFICATE-----'.PHP_EOL;
        $pubKey .= chunk_split(base64_encode($key), 64, PHP_EOL);
        $pubKey .= '-----END CERTIFICATE-----'.PHP_EOL;
        $this->pubKey = openssl_get_publickey($pubKey);
    }

    /**
    * 创建签名
    */
    public function createSign($data)
    {
        //注册生成加密信息
        $result = openssl_sign($data, $sign, $this->priKey, OPENSSL_ALGO_SHA1);
        if ($result) {
            return bin2hex($sign);
        }
        return false;
    }

    /**
    * 验证签名
    * @param string $data 验签数据
    * @param string $sign
    */
    public function verifySign($data, $sign)
    {
        $sign = $this->hex2bin($sign);
        $res = openssl_verify($data, $sign, $this->pubKey);   //验证结果，1：验证成功，0：验证失败
        return (1 === $res) ? true : false;
    }

    public function hex2bin($hex)
    {
        $len = strlen($hex);
        $newdata = '';
        for($i=0; $i<$len; $i+=2)
        {
            $newdata .= pack("C", hexdec(substr($hex,$i,2)));
        }
        return $newdata;
    }

}

?>
