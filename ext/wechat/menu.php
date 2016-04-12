<?php
/**
 * Cellular Framework
 * 微信自定义菜单接口
 * @copyright Cellular Team
 */
namespace ext\wechat;
class Menu
{
    public static function create($token, $param)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';
    }

    public static function get($token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=ACCESS_TOKEN';
    }

    public static function delete($token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN';
    }

    public static function current($token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=ACCESS_TOKEN';
    }
}