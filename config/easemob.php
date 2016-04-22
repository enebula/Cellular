<?php
/**
 * Cellular Framework
 * easemob.com 环信 API 配置文件
 * @copyright Cellular Team
 */
return [
    'org_name'  => 'cellular', # 企业的唯一标识,开发者在环信开发者管理后台注册账号时填写的企业ID
    'app_name'  => 'test', # 同一”企业”下”app”唯一标识,开发者在环信开发者管理后台创建应用时填写的”应用名称”
    'org_admin' => '', # 开发者在环信开发者管理后台注册时填写的”用户名”.企业管理员拥有对该企业账号下所有资源的操作权限
    'app_admin' => '', # 应用管理员,具有admin权限的一个特殊IM用户，拥有对该应用下所有资源的操作权限
    'appkey'    => '',  # 一个app的唯一标识,规则是 ${org_name}#${app_name}
    'client_id' => 'YXA6hjINoAhyEeaM8Y2_VE5ovg',
    'client_secret' => 'YXA69V4-tHzufHyfJrgmaKn3S36lzms'
]
?>