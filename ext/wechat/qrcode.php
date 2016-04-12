<?php
/**
 * Cellular Framework
 * 微信二维码接口
 * @copyright Cellular Team
 */
namespace ext\wechat;
class QRCode
{
    /**
     * 创建二维码 ticket
     * @param $expire_seconds
     * @param $action_name
     * @param $action_info
     * @param $scene_id
     * @param null $scene_str
     */
    public static function ticket($param)
    {
        /***
         * expire_seconds    该二维码有效时间，以秒为单位。 最大不超过604800（即7天）。
         * action_name    二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
         * scene_id    场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
         * scene_str    场景值ID（字符串形式的ID），字符串类型，长度限制为1到64，仅永久二维码支持此字段
         * token        access_token
         ***/

        /***
         * 临时二维码请求说明
         * http请求方式: POST
         * URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
         * POST数据格式：json
         * POST数据例子：{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
         *
         * 永久二维码请求说明
         * http请求方式: POST
         * URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
         * POST数据格式：json
         * POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
         * 或者也可以使用以下POST数据创建字符串形式的二维码参数：
         * {"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
         ***/
        if (!array_key_exists('expire_seconds', $param)) die('expire_seconds is empty');
        if (!array_key_exists('action_name', $param)) die('action_name is empty');
        if (!array_key_exists('scene_id', $param)) die('scene_id is empty');
        if ($param['action_name'] == 'QR_LIMIT_SCENE' && !array_key_exists('scene_id', $param)) die('scene_str is empty when action_name is QR_LIMIT_SCENE');
        if (!array_key_exists('token', $param)) die('token is empty');
        $param['action_info']['scene']['scene_id'] = $param['scene_id'];
        unset($param['scene_id']);
        $token = $param['token'];
        unset($param['token']);
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $token;
        $param = json_encode($param);
        $callback = self::curl($url, $param);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }

    /**
     * 通过 ticket 换取二维码
     * @param $ticket
     * @return bool
     */
    public static function code($ticket)
    {
        $ticket = urlencode($ticket);
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket;
        $callback = self::curlCode($url);
        if(strlen($callback) == 0) die('wechat error: 404');
        return $callback;
    }

    private static function curl($url, $param)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);//$data JSON类型字符串
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($param)));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        #curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        #curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;

        /*$ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, 'http://www.domain.com/');
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        echo $file_contents;*/
    }

    private static function curlCode($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        #curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        #curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
}