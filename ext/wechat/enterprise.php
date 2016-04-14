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
         * $param['mch_appid']        # 公众账号 appid    是 wx8888888888888888                String    微信分配的公众账号ID（企业号corpid即为此appId）
         * $param['mchid']            # 商户号            是 1900000109                        String(32)    微信支付分配的商户号
         * $param['device_info']      # 设备号            否     013467007045764                  String(32)    微信支付分配的终端设备号
         * $param['nonce_str']        # 随机字符串        是 5K8264ILTKCH16CQ2502SI8ZNMTM67VS String(32)    随机字符串，不长于32位
         * $param['sign']             # 签名             是 C380BEC2BFD727A4B6845133519F3AD6 String(32)    签名，详见签名算法
         * $param['partner_trade_no'] # 商户订单号        是 10000098201411111234567890    String    商户订单号，需保持唯一性
         * $param['openid']           # 用户 openid      是 oxTWIuGaIt6gTKsQRLau2M0yL16E    String    商户appid下，某用户的openid
         * $param['check_name']       # 校验用户姓名选项   是 OPTION_CHECK    String    NO_CHECK：不校验真实姓名
         * # FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）
         * # OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
         * $param['re_user_name']     # 收款用户姓名      否 马花花    String    收款用户真实姓名。如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
         * $param['amount']           # 金额             是 10099    int    企业付款金额，单位为分
         * $param['desc']             # 企业付款描述信息   是 理赔    String    企业付款操作说明信息。必填。
         * $param['spbill_create_ip'] # IP 地址          是 192.168.0.1    String(32)    调用接口的机器Ip地址
         ***/

        # 检测参数
        if (!array_key_exists('mch_appid', $param)) die('mch_appid is empty');
        if (!array_key_exists('mchid', $param)) die('mchid is empty');
        if (!array_key_exists('nonce_str', $param)) die('nonce_str is empty');
        if (!array_key_exists('sign', $param)) die('sign is empty');
        if (!array_key_exists('partner_trade_no', $param)) die('partner_trade_no is empty');
        if (!array_key_exists('openid', $param)) die('openid is empty');
        if (!array_key_exists('check_name', $param)) die('check_name is empty');
        if (!array_key_exists('amount', $param)) die('amount is empty');
        if (!array_key_exists('desc', $param)) die('desc is empty');
        if (!array_key_exists('spbill_create_ip', $param)) die('spbill_create_ip is empty');
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $callback = common::postXmlCurl(common::arrayToXml($param), $url, true);
        $callback = simplexml_load_string($callback);
        if ($callback->return_code == 'SUCCESS' && $callback->result_code == 'SUCCESS') {
            return $callback;
        } else {
            die('wechat.enterprise.payment error: ' . $callback->return_msg);
        }
        return false;
    }

    /**
     * 结果查询
     * @param $param['nonce_str']        string(32) *
     * @param $param['sign']             string(32) *
     * @param $param['partner_trade_no'] string(28) *
     * @param $param['mch_id']           string(32) *
     * @param $param['appid']            string(32) *
     * @return bool
     */
    public static function info($param)
    {
        /***
         * 字段                       名称        必填   示例值                             类型        说明
         * $param['nonce_str']        #随机字符串 是     5K8264ILTKCH16CQ2502SI8ZNMTM67VS  String(32)  随机字符串，不长于32位
         * $param['sign']             #签名      是     C380BEC2BFD727A4B6845133519F3AD6  String(32)  生成签名方式查看3.2.1节
         * $param['partner_trade_no'] #商户订单号 是     10000098201411111234567890        String(28)  商户调用企业付款API时使用的商户订单号
         * $param['mch_id']           #商户号    是     10000098                          String(32)  微信支付分配的商户号
         * $param['appid']            #Appid    是     wxe062425f740d30d8                String(32)  商户号的appid
         ***/

        /***
         * 数据示例：
         * <xml>
         * <sign><![CDATA[E1EE61A91C8E90F299DE6AE075D60A2D]]></sign>
         * <mch_billno><![CDATA[0010010404201411170000046545]]></mch_billno>
         * <mch_id><![CDATA[10000097]]></mch_id>
         * <appid><![CDATA[wxe062425f740c30d8]]></appid>
         * <bill_type><![CDATA[MCHT]]></ bill_type>
         * <nonce_str><![CDATA[50780e0cca98c8c8e814883e5caa672e]]></nonce_str>
         * </xml>
         ***/

        /***
         * 成功示例：
         * <xml> // 按照格式补充
         * <return_code><![CDATA[SUCCESS]]></return_code>
         * <return_msg><![CDATA[获取成功]]></return_msg>
         * <result_code><![CDATA[SUCCESS]]></result_code>
         * <mch_id>10000098</mch_id>
         * <appid><![CDATA[wxe062425f740c30d8]]></appid>
         * <detail_id><![CDATA[1000000000201503283103439304]]></detail_id>
         * <mch_billno><![CDATA[1000005901201407261446939628]]></mch_billno>
         * <status><![CDATA[RECEIVED]]></status>
         * <send_type><![CDATA[API]]></send_type>
         * <hb_type><![CDATA[GROUP]]></hb_type>
         * <total_num>4</total_num>
         * <total_amount>650</total_amount>
         * <send_time><![CDATA[2015-04-21 20:00:00]]></send_time>
         * <wishing><![CDATA[开开心心]]></wishing>
         * <remark><![CDATA[福利]]></remark>
         * <act_name><![CDATA[福利测试]]></act_name>
         * </xml>
         ***/

        /***
         * 失败示例：
         * <xml> // 按照格式补充
         * <return_code><![CDATA[FAIL]]></return_code>
         * <return_msg><![CDATA[指定单号数据不存在]]></return_msg>
         * <result_code><![CDATA[FAIL]]></result_code>
         * <err_code><![CDATA[SYSTEMERROR]]></err_code>
         * <err_code_des><![CDATA[指定单号数据不存在]]></err_code_des>
         * <mch_id>666</mch_id>
         * <mch_billno><![CDATA[1000005901201407261446939688]]></mch_billno>
         * </xml>
         ***/

        # 检测参数
        if (!array_key_exists('nonce_str', $param)) die('nonce_str is empty');
        if (!array_key_exists('sign', $param)) die('sign is empty');
        if (!array_key_exists('partner_trade_no', $param)) die('partner_trade_no is empty');
        if (!array_key_exists('mch_id', $param)) die('mch_id is empty');
        if (!array_key_exists('appid', $param)) die('appid is empty');
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';
        $callback = common::postXmlCurl(common::arrayToXml($param), $url, true);
        $callback = simplexml_load_string($callback);
        if ($callback->return_code == 'SUCCESS' && $callback->result_code == 'SUCCESS') {
            return $callback;
        } else {
            die('wechat.enterprise.info error: ' . $callback->return_msg);
        }
        return false;
    }
}