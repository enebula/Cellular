<?php
/**
 * Cellular Framework
 * 微信接口
 * @copyright Cellular Team
 */

namespace ext\wechat;

class Common
{
    /**
     * 获取 access token
     * @param $appid 第三方用户唯一凭证
     * @param $secret 第三方用户唯一凭证密钥 appsecret
     * @return mixed
     */
    public static function accessToken($appid, $secret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $callback = file_get_contents($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback->access_token;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }

    /**
     * 获取用户基本信息（包括UnionID机制）
     * @param string $token 调用接口凭证
     * @param string $openID 普通用户的标识，对当前公众号唯一
     * @param string $lang 返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
     * @return bool
     */
    public static function unionID($token, $openID, $lang = 'zh-CN') {
        if (empty($token)) die('token is not defined');
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $token . '&openid=' . $openID . '&lang=' . $lang;
        $callback = file_get_contents($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }

    /**
     * 引导用户进入授权页面同意授权 获取 code
     * @param $appid 第三方用户唯一凭证
     * @param $url 授权后重定向的回调链接地址 请使用urlencode对链接进行处理
     */
    public static function code($appid, $url)
    {
        $url = urlencode($url);
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $url . '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        header('Location: ' . $url);
    }

    /**
     * 通过 code 换取网页授权 access_token（与基础支持中的 access_token 不同）
     * @param $appid 第三方用户唯一凭证
     * @param $secret 第三方用户唯一凭证密钥 appsecret
     * @param $code 引导用户进入授权页面同意授权后获取的 code
     * @return bool
     * 正确时返回的JSON数据包如下：
     * {
     *     "access_token":"ACCESS_TOKEN", # 网页授权接口调用凭证，注意：此 access_token 与基础支持的 access_token 不同
     *     "expires_in":7200, # access_token接口调用凭证超时时间，单位（秒）
     *     "refresh_token":"REFRESH_TOKEN", # 用户刷新access_token
     *     "openid":"OPENID", # 用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
     *     "scope":"SCOPE" # 用户授权的作用域，使用逗号（,）分隔
     * }
     * 错误时微信会返回JSON数据包如下（示例为Code无效错误）:
     * {"errcode":40029,"errmsg":"invalid code"}
     */
    public static function authToken($appid, $secret, $code)
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
        $callback = file_get_contents($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }
    /**
     * 刷新 access_token（如果需要）
     * @param $appid 公众号的唯一标识
     * @param $refreshToken 填写通过 access_token 获取到的 refresh_token 参数
     * @return bool
     * 正确时返回的JSON数据包如下：
     * {
     *     "access_token":"ACCESS_TOKEN", # 网页授权接口调用凭证，注意：此 access_token 与基础支持的 access_token 不同
     *     "expires_in":7200, # access_token 接口调用凭证超时时间，单位（秒）
     *     "refresh_token":"REFRESH_TOKEN", # 用户刷新 access_token
     *     "openid":"OPENID", # 用户唯一标识
     *     "scope":"SCOPE" # 用户授权的作用域，使用逗号（,）分隔
     * }
     * 错误时微信会返回JSON数据包如下（示例为 Code 无效错误）:
     * {"errcode":40029,"errmsg":"invalid code"}
     */
    public static function refershToken($appid, $refreshToken)
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $$appid . '&grant_type=refresh_token&refresh_token=' . $refreshToken;
        $callback = file_get_contents($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }

    /**
     * 检验网页授权凭证 access_token 是否有效
     * @param $token 网页授权接口调用凭证，注意：此 access_token 与基础支持的 access_token 不同
     * @param $openID 用户的唯一标识
     * @return bool
     * 正确时的Json返回结果：{ "errcode":0,"errmsg":"ok"}
     * 错误时的Json返回示例：{ "errcode":40003,"errmsg":"invalid openid"}
     */
    public static function checkAuthToken($token, $openID)
    {
        $url = 'https://api.weixin.qq.com/sns/auth?access_token=' . $token . '&openid=' . $openID;
        $callback = file_get_contents($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }
}
?>