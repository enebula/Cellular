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
     * @param $param
     * @return bool
     */
    public static function payment($param)
    {
        /***
        $param['mch_appid']        # 公众账号 appid    是 wx8888888888888888                String	微信分配的公众账号ID（企业号corpid即为此appId）
        $param['mchid']            # 商户号            是 1900000109                        String(32)	微信支付分配的商户号
        $param['device_info']      # 设备号		      否	 013467007045764                  String(32)	微信支付分配的终端设备号
        $param['nonce_str']        # 随机字符串	      是 5K8264ILTKCH16CQ2502SI8ZNMTM67VS String(32)	随机字符串，不长于32位
        $param['sign']             # 签名		      是 C380BEC2BFD727A4B6845133519F3AD6 String(32)	签名，详见签名算法
        $param['partner_trade_no'] # 商户订单号		  是 10000098201411111234567890	String	商户订单号，需保持唯一性
        $param['openid']           # 用户 openid      是 oxTWIuGaIt6gTKsQRLau2M0yL16E	String	商户appid下，某用户的openid
        $param['check_name']       # 校验用户姓名选项   是 OPTION_CHECK	String	NO_CHECK：不校验真实姓名
        # FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）
        # OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
        $param['re_user_name']     # 收款用户姓名		  否 马花花	String	收款用户真实姓名。如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
        $param['amount']           # 金额	          是 10099	int	企业付款金额，单位为分
        $param['desc']             # 企业付款描述信息   是 理赔	String	企业付款操作说明信息。必填。
        $param['spbill_create_ip'] # IP 地址		      是 192.168.0.1	String(32)	调用接口的机器Ip地址
        ***/

        # 检测参数
        if (!array_key_exists('mch_appid', $param)) die('mch_appid is empty');
        if (!array_key_exists('mchid', $param)) die('mchid is empty');
        if (!array_key_exists('nonce_str', $param)) die('nonce_str is empty');
        if (!array_key_exists('partner_trade_no', $param)) die('partner_trade_no is empty');
        if (!array_key_exists('openid', $param)) die('openid is empty');
        if (!array_key_exists('check_name', $param)) die('check_name is empty');
        if (!array_key_exists('amount', $param)) die('amount is empty');
        if (!array_key_exists('desc', $param)) die('desc is empty');
        if (!array_key_exists('spbill_create_ip', $param)) die('spbill_create_ip is empty');

        # 生成签名

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
     * @param $nonce_str        随机字符串
     * @param $partner_trade_no 商户订单号
     * @param $mch_id           商户号
     * @param $appid            公众账号 appid
     * @return bool
     */
    public static function info($nonce_str, $partner_trade_no, $mch_id, $appid)
    {
        # 生成签名


        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';
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