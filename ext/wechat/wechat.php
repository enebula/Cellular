<?php
/**
 * Cellular Framework
 * 微信接口
 * @copyright Cellular Team
 */

namespace ext\wechat;

class wechat
{
    /**
     * 获取 access token
     * @param $appid 第三方用户唯一凭证
     * @param $secret 第三方用户唯一凭证密钥 即 appsecret
     * @return mixed
     */
    function accessToken($appid, $secret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $response = file_get_contents($url);
        $response = json_decode($response);
        if (empty($response->errcode)) {
            return $response->access_token;
        } else {
            die('wechat-error: [' . $response->errcode . '] ' . $response->errmsg);
        }
    }
}
?>