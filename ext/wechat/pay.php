<?php
/**
 * Cellular Framework
 * 微信支付接口
 * @copyright Cellular Team
 */

namespace ext\wechat;

class Pay
{
    /**
     * 统一下单
     * 除被扫支付场景以外，商户系统先调用该接口在微信支付服务后台生成预支付交易单，返回正确的预支付交易回话标识后再按扫码、JSAPI、APP等不同场景生成交易串调起支付。
     * @param $appid       string(32) * 公众账号ID 微信分配的公众账号ID（企业号corpid即为此appId）
     * @param $mch_id      string(32) * 商户号     微信支付分配的商户号
     * @param $device_info string(32)   设备号     终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"
     * @return bool
     */
    public function unifiedOrder($appid, $mch_id, $device_info = 'WEB')
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
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
     * 查询订单
     * 该接口提供所有微信支付订单的查询，商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑。
     * 需要调用查询接口的情况：
     * 当商户后台、网络、服务器等出现异常，商户系统最终未接收到支付通知；
     * 调用支付接口后，返回系统错误或未知交易状态情况；
     * 调用被扫支付API，返回USERPAYING的状态；
     * 调用关单或撤销接口API之前，需确认支付状态；
     * @return bool
     */
    public function orderQuery()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
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
     * 关闭订单
     * 以下情况需要调用关单接口：商户订单支付失败需要生成新单号重新发起支付，要对原订单号调用关单，避免重复支付；系统下单后，用户支付超时，系统退出不再受理，避免用户继续，请调用关单接口。
     * 注意：订单生成后不能马上调用关单接口，最短调用时间间隔为5分钟。
     * @return bool
     */
    public function closeOrder()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/closeorder';
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
     * 申请退款
     * 当交易发生之后一段时间内，由于买家或者卖家的原因需要退款时，卖家可以通过退款接口将支付款退还给买家，微信支付将在收到退款请求并且验证成功之后，按照退款规则将支付款按原路退到买家帐号上。
     * 注意：
     * 1、交易时间超过一年的订单无法提交退款；
     * 2、微信支付退款支持单笔交易分多次退款，多次退款需要提交原支付订单的商户订单号和设置不同的退款单号。一笔退款失败后重新提交，要采用原来的退款单号。总退款金额不能超过用户实际支付金额。
     * @return bool
     */
    public function refund()
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
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
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * @return bool
     */
    public function refundQuery()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
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
     * 下载对账单
     * 商户可以通过该接口下载历史交易清单。比如掉单、系统错误等导致商户侧和微信侧数据不一致，通过对账单核对后可校正支付状态。
     * 注意：
     * 1、微信侧未成功下单的交易不会出现在对账单中。支付成功后撤销的交易会出现在对账单中，跟原支付单订单号一致，bill_type为REVOKED；
     * 2、微信在次日9点启动生成前一天的对账单，建议商户10点后再获取；
     * 3、对账单中涉及金额的字段单位为“元”。
     * @return bool
     */
    public function downloadBill()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/downloadbill';
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
     * 测速上报
     * 商户在调用微信支付提供的相关接口时，会得到微信支付返回的相关信息以及获得整个接口的响应时间。为提高整体的服务水平，协助商户一起提高服务质量，微信支付提供了相关接口调用耗时和返回信息的主动上报接口，微信支付可以根据商户侧上报的数据进一步优化网络部署，完善服务监控，和商户更好的协作为用户提供更好的业务体验。
     * @return bool
     */
    public function report()
    {
        $url = 'https://api.mch.weixin.qq.com/payitil/report';
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
     * 转换短链接
     * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，减小二维码数据量，提升扫描速度和精确度。
     * @return bool
     */
    public function shortURL()
    {
        $url = 'https://api.mch.weixin.qq.com/tools/shorturl';
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