<?php
/**
 * Cellular Framework
 * 微信自定义菜单接口
 * @copyright Cellular Team
 */
namespace ext\wechat;
class Menu
{
    /**
     * 创建菜单(认证后的订阅号可用)
     * @param $token
     * @param $param
     * @return bool
     */
    public static function create($token, $param)
    {
        /***
         * type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
         * 1 click：点击推事件
         * 2 view：跳转URL
         * 3 scancode_push：扫码推事件
         * 4 scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
         * 5 pic_sysphoto：弹出系统拍照发图
         * 6 pic_photo_or_album：弹出拍照或者相册发图
         * 7 pic_weixin：弹出微信相册发图器
         * 8 location_select：弹出地理位置选择器
         *
         * 1  click：点击推事件
         * 用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event    的结构给开发者（参考消息接口指南），并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；
         * 2  view：跳转URL
         * 用户点击view类型按钮后，微信客户端将会打开开发者在按钮中填写的网页URL，可与网页授权获取用户基本信息接口结合，获得用户基本信息。
         * 3  scancode_push：扫码推事件
         * 用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后显示扫描结果（如果是URL，将进入URL），且会将扫码的结果传给开发者，开发者可以下发消息。
         * 4  scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
         * 用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后，将扫码的结果传给开发者，同时收起扫一扫工具，然后弹出“消息接收中”提示框，随后可能会收到开发者下发的消息。
         * 5  pic_sysphoto：弹出系统拍照发图
         * 用户点击按钮后，微信客户端将调起系统相机，完成拍照操作后，会将拍摄的相片发送给开发者，并推送事件给开发者，同时收起系统相机，随后可能会收到开发者下发的消息。
         * 6  pic_photo_or_album：弹出拍照或者相册发图
         * 用户点击按钮后，微信客户端将弹出选择器供用户选择“拍照”或者“从手机相册选择”。用户选择后即走其他两种流程。
         * 7  pic_weixin：弹出微信相册发图器
         * 用户点击按钮后，微信客户端将调起微信相册，完成选择操作后，将选择的相片发送给开发者的服务器，并推送事件给开发者，同时收起相册，随后可能会收到开发者下发的消息。
         * 8  location_select：弹出地理位置选择器
         * 用户点击按钮后，微信客户端将调起地理位置选择工具，完成选择操作后，将选择的地理位置发送给开发者的服务器，同时收起位置选择工具，随后可能会收到开发者下发的消息。
         * 9  media_id：下发消息（除文本消息）
         * 用户点击media_id类型按钮后，微信服务器会将开发者填写的永久素材id对应的素材下发给用户，永久素材类型可以是图片、音频、视频、图文消息。请注意：永久素材id必须是在“素材管理/新增永久素材”接口上传后获得的合法id。
         * 10 view_limited：跳转图文消息URL
         * 用户点击view_limited类型按钮后，微信客户端将打开开发者在按钮中填写的永久素材id对应的图文消息URL，永久素材类型只支持图文消息。请注意：永久素材id必须是在“素材管理/新增永久素材”接口上传后获得的合法id。
         *
         * 设置菜单
         * $param = [
         *     "button"=> [
         *         ['type' => 'click', 'name' => '最新消息', 'key' => 'MENU_KEY_NEWS'],
         *         ['type' => 'view', 'name' => '我要搜索', 'url' => 'http://www.baidu.com']
         *     ]
         * ];
         *
         * 设置二级菜单
         * $param = [
         *     'button' => [
         *         [
         *             'name' => '扫码',
         *             'sub_button' => [
         *                 [
         *                     'type' => 'scancode_waitmsg',
         *                     'name' => '扫码带提示',
         *                     'key' => 'rselfmenu_0_0'
         *                 ],
         *                 [
         *                     'type' => 'scancode_push',
         *                     'name' => '扫码推事件',
         *                     'key' => 'rselfmenu_0_1'
         *                 ],
         *             ],
         *         ],
         *         [
         *             'name' => '发图',
         *             'sub_button' => [
         *                 [
         *                     'type' => 'pic_sysphoto',
         *                     'name' => '系统拍照发图',
         *                     'key' => 'rselfmenu_1_0'
         *                 ],
         *                 [
         *                     'type' => 'pic_photo_or_album',
         *                     'name' => '拍照或者相册发图',
         *                     'key' => 'rselfmenu_1_1'
         *                 ]
         *             ],
         *         ],
         *         [
         *             'type' => 'location_select',
         *             'name' => '发送位置',
         *             'key' => 'rselfmenu_2_0'
         *         ],
         *     ],
         * ];
         ***/
        # JSON_UNESCAPED_SLASHES 不要编码 /。 自 PHP 5.4.0 起生效。
        # JSON_UNESCAPED_UNICODE 以字面编码多字节 Unicode 字符（默认是编码成 \uXXXX）。 自 PHP 5.4.0 起生效。
        $param = json_encode($param, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $token;
        $callback = common::curlPost($url, $param);
        $callback = json_decode($callback);
        if ($callback->errcode == 0) {
            return true;
        } else {
            die('wechat error: [' . $callback->errcode . '] ' . $callback->errmsg);
        }
        return false;
    }

    public static function get($token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . $token;
    }

    public static function delete($token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $token;
    }

    public static function current($token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=' . $token;
        $callback = Common::curl($url);
        $res = json_decode($callback);
        if (empty($res->errcode)) {
            return $callback;
        } else {
            die('wechat error: [' . $res->errcode . '] ' . $res->errmsg);
        }
        return false;
    }
}