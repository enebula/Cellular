<?php
/**
 * Cellular Framework
 * 微信刷卡支付接口
 * @copyright Cellular Team
 */
namespace ext\wechat;
class MicroPay
{
    /**
     * 提交刷卡支付 API
     * @param $param
     */
    public function pay($param)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/micropay';
    }
}