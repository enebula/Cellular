<?php
/**
 * Cellular Framework
 * 微信企业付款接口
 * @copyright Cellular Team
 */

namespace ext\wechat;

class Enterprise
{
    /**
     * 企业付款
     * @return bool
     */
    public static function payment()
    {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
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
     * 结果查询
     * @param $nonce_str
     * @param $sign
     * @param $partner_trade_no
     * @param $mch_id
     * @param $appid
     * @return bool
     */
    public static function info($nonce_str ,$sign, $partner_trade_no, $mch_id, $appid)
    {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo ';
        $callback = file_get_contents($url);
        $callback = json_decode($callback);
        if (empty($callback->errcode)) {
            return $callback->access_token;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }
}