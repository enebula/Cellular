<?php
/**
 * Cellular Framework
 * api.map.baidu.com 百度地图接口
 * @copyright Cellular Team
 */
namespace ext\baidu;
use Cellular;
class Common
{
    /**
     * 百度 IP 定位 API
     * @param $ip
     * @return mixed
     */
    public static function location($ip)
    {
        $ip = '60.26.78.240';
        $config = Cellular::config('baidu');
        $content = file_get_contents('http://api.map.baidu.com/location/ip?ak=' . $config['ak'] . '&ip=' . $ip . '&coor=bd09ll');
        return json_decode($content);
    }
}