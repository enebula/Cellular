<?php
/**
 * Cellular Framework
 * 微信现金红包
 * @copyright Cellular Team
 */

namespace ext\wechat;

class RedPack
{
    /**
     * 发送现金红包
     * @param $param
     */
    public static function send($param)
    {
        /***
        字段                    名称          必填 示例值 类型 说明
        $param['nonce_str']    # 随机字符串     是 5K8264ILTKCH16CQ2502SI8ZNMTM67VS String(32) 随机字符串，不长于32位
        $param['sign']         # 签名          是 C380BEC2BFD727A4B6845133519F3AD6 String(32) 详见签名生成算法
        $param['mch_billno']   # 商户订单号     是 10000098201411111234567890 String(28) 商户订单号（每个订单号必须唯一）
                               # 组成：mch_id+yyyymmdd+10位一天内不能重复的数字。
                               # 接口根据商户订单号支持重入，如出现超时可再调用。
        $param['mch_id']       # 商户号         是 10000098 String(32) 微信支付分配的商户号
        $param['wxappid']      # 公众账号appid  是 wx8888888888888888 String(32) 微信分配的公众账号ID（企业号corpid即为此appId）。
                               # 接口传入的所有appid应该为公众号的appid（在mp.weixin.qq.com申请的），不能为APP的appid（在open.weixin.qq.com申请的）。
        $param['send_name']    # 商户名称       是 天虹百货 String(32) 红包发送者名称
        $param['re_openid']    # 用户openid    是 oxTWIuGaIt6gTKsQRLau2M0yL16E String(32) 接受红包的用户
                               # 用户在wxappid下的openid
        $param['total_amount'] # 付款金额      是 1000 int 付款金额，单位分
        $param['total_num']    # 红包发放总人数 是 1 int 红包发放总人数
                               # total_num=1
        $param['wishing']      # 红包祝福语    是 感谢您参加猜灯谜活动，祝您元宵节快乐！ String(128) 红包祝福语
        $param['client_ip']    # IP 地址      是 192.168.0.1 String(15) 调用接口的机器Ip地址
        $param['act_name']     # 活动名称 是 猜灯谜抢红包活动 String(32) 活动名称
        $param['remark']       # 备注 是 猜越多得越多，快来抢！ String(256) 备注信息
        ***/
        $param['nonce_str'] = '';
        if (!array_key_exists('mch_billno', $param)) die('mch_billno is empty');
        if (!array_key_exists('mch_id', $param)) die('mch_id is empty');
        if (!array_key_exists('wxappid', $param)) die('wxappid is empty');
        if (!array_key_exists('send_name', $param)) die('send_name is empty');
        if (!array_key_exists('re_openid', $param)) die('re_openid is empty');
        if (!array_key_exists('total_amount', $param)) die('total_amount is empty');
        if (!array_key_exists('total_num', $param)) die('total_num is empty');
        if (!array_key_exists('wishing', $param)) die('wishing is empty');
        $param['client_ip'] = '';
        if (!array_key_exists('act_name', $param)) die('act_name is empty');
        if (!array_key_exists('remark', $param)) die('remark is empty');
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
    }

    /**
     * 发送裂变红包
     * @param $param
     */
    public static function sendGroup($param)
    {
        /***
        字段                      名称 必填 示例值 类型 描述
        $param['nonce_str']    # 随机字符串    是  5K8264ILTKCH16CQ2502SI8ZNMTM67VS  String(32)  随机字符串，不长于32位
        $param['sign']         # 签名    是  C380BEC2BFD727A4B6845133519F3AD6  String(32)  详见签名生成算法
        $param['mch_billno']   # 商户订单号    是  10000098201411111234567890  String(28)  商户订单号（每个订单号必须唯一）
                               # 组成： mch_id+yyyymmdd+10位一天内不能重复的数字。
                               # 接口根据商户订单号支持重入， 如出现超时可再调用。
        $param['mch_id']       # 商户号    是  10000098  String(32)  微信支付分配的商户号
        $param['wxappid']      # 公众账号appid    是  wx8888888888888888  String(32)  微信分配的公众账号ID（企业号corpid即为此appId）。
                               # 接口传入的所有appid应该为公众号的appid（在mp.weixin.qq.com申请的），不能为APP的appid（在open.weixin.qq.com申请的）。
        $param['send_name']    # 商户名称    是  天虹百货  String(32)  红包发送者名称
        $param['re_openid']    # 用户openid    是  oxTWIuGaIt6gTKsQRLau2M0yL16E  String(32)  接收红包的种子用户（首个用户）
                               # 用户在wxappid下的openid
        $param['total_amount'] # 总金额    是  1000  int  红包发放总金额，即一组红包金额总和，包括分享者的红包和裂变的红包，单位分
        $param['total_num']    # 红包发放总人数    是  3  int  红包发放总人数，即总共有多少人可以领到该组红包（包括分享者）
        $param['amt_type']     # 红包金额设置方式    是  ALL_RAND  String(32)  红包金额设置方式
                               # ALL_RAND—全部随机,商户指定总金额和红包发放总人数，由微信支付随机计算出各红包金额
        $param['wishing']      # 红包祝福语    是  感谢您参加猜灯谜活动，祝您元宵节快乐！  String(128)  红包祝福语
        $param['act_name']     # 活动名称    是  猜灯谜抢红包活动  String(32)  活动名称
        $param['remark']       # 备注    是  猜越多得越多，快来抢！  String(256)  备注信息
        ***/

        $param['nonce_str'] = '';
        if (!array_key_exists('mch_billno', $param)) die('mch_billno is empty');
        if (!array_key_exists('mch_id', $param)) die('mch_id is empty');
        if (!array_key_exists('wxappid', $param)) die('wxappid is empty');
        if (!array_key_exists('send_name', $param)) die('send_name is empty');
        if (!array_key_exists('re_openid', $param)) die('re_openid is empty');
        if (!array_key_exists('total_amount', $param)) die('total_amount is empty');
        if (!array_key_exists('total_num', $param)) die('total_num is empty');
        if (!array_key_exists('amt_type', $param)) die('amt_type is empty');
        if (!array_key_exists('wishing', $param)) die('wishing is empty');
        if (!array_key_exists('act_name', $param)) die('act_name is empty');
        if (!array_key_exists('remark', $param)) die('remark is empty');
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';
    }

    /**
     * 查询红包信息 支持普通红包和裂变红包
     * @param $param
     */
    public static function search($param)
    {
        /***
        字段                    名称      必填 示例值 类型 说明
        $param['nonce_str']  # 随机字符串 是  5K8264ILTKCH16CQ2502SI8ZNMTM67VS String(32) 随机字符串，不长于32位
        $param['sign']       # 签名      是  C380BEC2BFD727A4B6845133519F3AD6 String(32) 详见签名生成算法
        $param['mch_billno'] # 商户订单号 是  10000098201411111234567890 String(28) 商户发放红包的商户订单号
        $param['mch_id']     # 商户号    是  10000098 String(32) 微信支付分配的商户号
        $param['appid']      # Appid    是  wxe062425f740d30d8 String(32) 微信分配的公众账号ID（企业号corpid即为此appId）。
                             # 接口传入的所有appid应该为公众号的appid（在mp.weixin.qq.com申请的），不能为APP的appid（在open.weixin.qq.com申请的）。
        $param['bill_type']  # 订单类型  是  MCHT String(32) MCHT:通过商户订单号获取红包信息。
        ***/

        $param['nonce_str'] = '';
        if (!array_key_exists('mch_billno', $param)) die('mch_billno is empty');
        if (!array_key_exists('mch_id', $param)) die('mch_id is empty');
        if (!array_key_exists('appid', $param)) die('appid is empty');
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';
    }
}