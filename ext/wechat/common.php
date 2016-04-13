<?php
/**
 * Cellular Framework
 * 微信接口
 * @copyright Cellular Team
 */

namespace ext\wechat;

class Common
{
    public static function signature($token)
    {
        # 服务器配置 Token
        if (!$token) {
            throw new Exception('$token is not defined!');
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取 access token
     * @param $appid 第三方用户唯一凭证
     * @param $secret 第三方用户唯一凭证密钥 appsecret
     * @return mixed
     */
    public static function accessToken($appid, $secret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $callback = self::curl($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
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
    public static function unionID($token, $openID, $lang = 'zh-CN')
    {
        if (empty($token)) die('token is not defined');
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $token . '&openid=' . $openID . '&lang=' . $lang;
        $callback = self::curl($url);
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
     * @param string $appid 第三方用户唯一凭证
     * @param string $url 授权后重定向的回调链接地址 请使用urlencode对链接进行处理
     * @param string $scope 应用授权作用域 snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
     * @param null $state 重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
     */
    public static function code($appid, $url, $scope = 'snsapi_base', $state = null)
    {
        $url = urlencode($url);
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $url . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
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
        /*
		stdClass Object
		(
		[access_token] => OezXcEiiBSKSxW0eoylIeH24MzR6QFiao-MpWLIYDiNr6aLMAT9UNumXHJvhj83Pd_DsLWF12DUr4aU2iF1k7U4L2k33aa7TNZDbzdZIdNVF9lQoO2AyE6IYYaDotM_J3r05FRvtMlFBIqtZWVfcgg
		[expires_in] => 7200
		[refresh_token] => OezXcEiiBSKSxW0eoylIeH24MzR6QFiao-MpWLIYDiNr6aLMAT9UNumXHJvhj83PBd4FKP6X653u8lnEI9qPsuAd5V1LosxsAcWs6ZaAi45Gvuhmun7fMZZdHpwhI1kJU5CgjHi5MAFZrFgQx-Y5jQ
		[openid] => oicL-s-7L4EJkbIk_4i1Epaps0CU
		[scope] => snsapi_userinfo
		)
		*/
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
        $callback = self::curl($url);
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
        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $refreshToken;
        $callback = self::curl($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }

    /**
     * 拉取用户信息(需scope为 snsapi_userinfo)
     * @param $token 网页授权接口调用凭证 注意：此access_token与基础支持的access_token不同
     * @param $openID
     * @param string $lang
     * @return bool
     */
    public static function userInfo($token, $openID, $lang = 'zh_CN')
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $token . '&openid=' . $openID . '&lang=' . $lang;
        $callback = self::curl($url);
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
        $callback = self::curl($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }

    /**
     * curl 获取数据
     * @param $url
     * @return mixed
     */
    public static function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    /**
     * 随机字符串
     * @return null|string
     */
    public static function nonceStr()
    {
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        $temp = null;
        for ($i = 0; $i < 32; $i++) {
            $num = rand(0, 35);
            $temp .= $str[$num];
        }
        return $temp;
    }

    /**
     * 生成签名
     * @param $param
     * @param $key
     * @return mixed 签名
     */
    public static function sign($param, $key)
    {
        # 签名步骤一：按字典顺序排序参数
        ksort($param);
        $string = self::arrayToQuery($param);
        # 签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $key;
        # 签名步骤三：MD5加密
        $string = md5($string);
        # 签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $param
     * @return string
     */
    public static function arrayToQuery($param)
    {
        $buff = '';
        foreach ($param as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }
        $buff = trim($buff, '&');
        return $buff;
    }

    /**
     * array 转 xml
     * @param array $arr
     * @return string
     */
    public static function arrayToXml($arr)
    {
        if (!is_array($arr)) die('$arr is not array');
        $xml = '<xml>';
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * xml 转 array
     * @param $xml
     * @return mixed
     */
    public static function xmlToArray($xml)
    {
        if (!$xml) die('$xml is null');
        # 禁止引用外部 xml 实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     * @return mixed
     * @throws WxPayException
     */
    public static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        /***
         * //如果有配置代理这里就设置代理
         * if (WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
         * && WxPayConfig::CURL_PROXY_PORT != 0
         * ) {
         * curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
         * curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
         * }
         ***/

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, realpath('./config/cert') . DIRECTORY_SEPARATOR . 'apiclient_cert.pem');
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, realpath('./config/cert') . DIRECTORY_SEPARATOR . 'apiclient_key.pem');
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            die("curl出错，错误码:$error");
        }
    }
}
?>