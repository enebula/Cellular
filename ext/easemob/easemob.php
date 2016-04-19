<?php
/**
 * Cellular Framework
 * 环信接口
 * @copyright Cellular Team
 */
namespace ext\easemob;
use Cellular;
class Easemob
{
    private $org_name; # 企业的唯一标识,开发者在环信开发者管理后台注册账号时填写的企业ID
    private $app_name; # 同一”企业”下”app”唯一标识,开发者在环信开发者管理后台创建应用时填写的”应用名称”
    private $org_admin; # 开发者在环信开发者管理后台注册时填写的”用户名”.企业管理员拥有对该企业账号下所有资源的操作权限
    private $app_admin; # 应用管理员,具有admin权限的一个特殊IM用户，拥有对该应用下所有资源的操作权限
    private $appkey; # 一个app的唯一标识,规则是 ${org_name}#${app_name}

    public function __construct()
    {
        $config = Cellular::config('easemob');
        $this->org_name = $config['org_name'];
        $this->app_name = $config['app_name'];
        $this->org_admin = $config['org_admin'];
        $this->app_admin = $config['app_admin'];
        $this->appkey = $config['appkey'];
        var_dump($this);
    }

    public function token()
    {
        $url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/token';
    }
}