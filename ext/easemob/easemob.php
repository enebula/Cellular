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
    private $url;
    private $token;

    public function __construct()
    {
        $config = Cellular::config('easemob');
        $this->org_name = $config['org_name'];
        $this->app_name = $config['app_name'];
        $this->org_admin = $config['org_admin'];
        $this->app_admin = $config['app_admin'];
        $this->appkey = $config['appkey'];
        $this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
        var_dump($this);
        # 此处获取 token 并缓存至 redis

    }

    public function token()
    {
        if ($this->token != null) {
            return $this->token;
        }
        $url = $this->url . 'token';
        $param['grant_type'] = 'client_credentials';
        $param['client_id'];
        $param['client_secret'];
        $result = $this->curl($url, $param);
        return $result;
    }

    /**
     * 注册用户
     * @param $param ['username'] 用户名
     * @param $param ['password'] 密码
     * @return mixed
     */
    public function register($param)
    {
        $url = $this->url . 'users';
        $result = $this->curl($url, $param);
        return $result;
    }

    /**
     * 批量注册用户
     * @param $param
     */
    public function batchRegister($param)
    {
        $url = $this->url . 'users';
    }

    /**
     * 获取用户信息
     * @param $param
     * @return bool|mixed
     */
    public function user($param)
    {
        if (count($param) > 1) {
            $url = $this->url . 'users';
        } else {
            $url = $this->url . 'users/' . $param[0];
        }
    }

    /**
     * 删除用户
     * @param $param
     */
    public function deleteUser($param)
    {
        $url = $this->url . 'users';
    }

    /**
     * 修改用户密码
     * @param $param ['username'] 用户名
     * @param $param ['newpassword'] 新密码
     */
    public function password($param)
    {
        $url = $this->url . 'users/' . $param['username'] . '/password';
    }

    /**
     * 修改用户昵称
     * @param $param ['username'] 用户名
     * @param $param ['nickname'] 用户昵称
     */
    public function nickname($param)
    {
        $url = $this->url . 'users/' . $param['username'];
    }

    /**
     * 添加好友
     * @param $param ['owner_username'] 要添加好友的用户名
     * @param $param ['friend_username'] 被添加的用户名
     * @return bool|mixed
     */
    public function addFriend($param)
    {
        $url = $this->url . 'users/' . $param['owner_username'] . 'contacts/users/' . $param['friend_username'];
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 删除好友
     * @param $param ['owner_username'] 要删除好友的用户名
     * @param $param ['friend_username'] 被删除的用户名
     * @return bool|mixed
     */
    public function deleteFriend($param)
    {
        $url = $this->url . 'users/' . $param['owner_username'] . '/contacts/users/' . $param['friend_username'];
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 查看好友
     * @param $param ['owner_username'] 用户名
     * @return bool|mixed
     */
    public function friend($param)
    {
        $url = $this->url . 'users/' . $param['owner_username'] . '/contacts/users';
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 获取用户黑名单
     * @param $param ['owner_username'] 用户名
     * @return bool|mixed
     */
    public function block($param)
    {
        $url = $this->url . 'users/' . $param['owner_username'] . 'blocks/users';
        $result = $this->curl($url, $param);
        return $result;
    }

    /**
     * 加入黑名单
     * @param $param ['owner_username'] 用户名
     * @param $param ['usernames'] 加入黑名单的用户名 数组
     * @return bool|mixed
     */
    public function joinBlock($param)
    {
        $url = $this->url . 'users/' . $param['owner_username'] . 'blocks/users';
        $result = $this->curl($url, $param);
        return $result;
    }

    /**
     * 退出黑名单
     * @param $param ['owner_username'] 用户名
     * @param $param ['blocked_username'] 移除黑名单的用户名
     * @return bool|mixed
     */
    public function exitBlock($param)
    {
        $url = $this->url . 'users/' . $param['owner_username'] . 'blocks/users/' . $param['blocked_username'];
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 查看用户在线状态
     * @param $param ['username'] 用户名
     * @return bool|mixed
     */
    public function online($param)
    {
        $url = $this->url . 'users/' . $param['username'] . '/status';
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 查询离线消息数量
     * @param $param ['owner_username'] 用户名
     * @return bool|mixed
     */
    public function offline($param)
    {
        $url = $this->url . 'users/' . $param['owner_username'] . '/offline_msg_count';
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 查询某条离线消息状态
     * @param $param ['username'] 用户名
     * @param $param ['msg_id'] 离线消息 ID
     * @return bool|mixed
     */
    public function offlineMsg($param)
    {
        $url = $this->url . 'users/' . $param['username'] . '/offline_msg_status/' . $param['msg_id'];
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 用户帐号禁用
     * @param $param ['username'] 用户名
     * @return bool|mixed
     */
    public function disable($param)
    {
        $url = $this->url . 'users/' . $param['username'] . '/deactivate';
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 用户帐号解禁
     * @param $param ['username'] 用户名
     * @return bool|mixed
     */
    public function enable($param)
    {
        $url = $this->url . 'users/' . $param['username'] . '/activate';
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 强制用户下线
     * @param $param ['username'] 用户名
     * @return bool|mixed
     */
    public function kick($param)
    {
        $url = $this->url . 'users/' . $param['username'] . '/disconnect';
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 导出聊天记录
     * @param $param ['ql'] 查询语句
     * @param $param ['limit'] 显示数量
     * @param $param ['cursor'] 分页游标
     * @return bool|mixed
     */
    public function message($param)
    {
        $url = null;
        if ($param['ql'] != null) {
            $url .= '&ql=' . $param['ql'];
        }
        if ($param['limit'] != null) {
            $url .= '&limit=' . $param['limit'];
        }
        if ($param['cursor'] != null) {
            $url .= '&cursor=' . $param['cursor'];
        }
        if ($url != null) {
            $url .= '?' . substr($url, 1);
        }
        $url = $this->url . 'chatmessages' . $url;
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 发送消息
     * @param $param ['target_type'] users 给用户发消息, chatgroups 给群发消息, chatrooms 给聊天室发消息
     * @param $param ['target'] 数组元素是用户名,给群组发送时数组元素是groupid
     * @param $param ['msg'] 消息内容
     * @param $param ['from'] 消息发送者, 无此字段Server会默认设置为"from":"admin",有from字段但值为空串("")时请求失败
     * @param $param ['ext'] 扩展属性 可选 由 APP 自己定义 可以没有这个字段，但是如果有，值不能是“ext:null“这种形式，否则出错
     * @return bool|mixed
     */
    public function send($param)
    {
        $url = $this->url . 'messages';
        $result = $this->curl($url);
        return $result;
    }

    /**
     * 获取群组
     * @param $param ['limit'] 获取数量 可选
     * @param $param ['cursor'] 游标 可选
     * @return bool|mixed
     */
    public function group($param)
    {
        $url = null;
        if ($param['limit'] != null) {
            $url .= '&limit=' . $param['limit'];
        }
        if ($param['cursor'] != null) {
            $url .= '&cursor=' . $param['cursor'];
        }
        if ($url != null) {
            $url .= '?' . substr($url, 1);
        }
        $url = $this->url . 'chatgroups' . $url;
        $result = $this->curl($url, null, null, 'GET');
        return $result;
    }

    /**
     * 获取一个或者多个群组详情
     * @param $param 群组 ID 数组
     * @return bool|mixed
     */
    public function groupDetail($param)
    {
        $url = $this->url . 'chatgroups' . implode(',' . $param);
        $result = $this->curl($url, null, null, 'GET');
        return $result;
    }

    /**
     * 创建一个群组
     * @param $param ['groupname'] 群组名称 必填
     * @param $param ['desc'] 群组描述 必填
     * @param $param ['public'] bool 是否是公开群 必填 true|false
     * @param $param ['maxusers'] int 群组成员最大数（包括群主） 可选 默认值：200
     * @param $param ['approval'] bool 加入公开群是否需要批准, 默认值是false（假如公开群不需要群主批准）, 此属性为必选的，私有群必须为true
     * @param $param ['owner'] 群组的管理员, 此属性为必须的
     * @param $param ['members'] 群组成员,此属性为可选的,但是如果加了此项,数组元素至少一个（注：群主jma1不需要写入到members里面）
     * @return bool|mixed
     */
    public function createGroup($param)
    {
        /***
         * {
         * "groupname":"testrestgrp12", # 群组名称 必填
         * "desc":"server create group", # 群组描述 必填
         * "public":true, //是否是公开群, 此属性为必须的
         * "maxusers":300, //群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
         * "approval":true, //加入公开群是否需要批准, 默认值是false（加如公开群不需要群主批准）, 此属性为必选的，私有群必须为true
         * "owner":"jma1", //群组的管理员, 此属性为必须的
         * "members":["jma2","jma3"] //群组成员,此属性为可选的,但是如果加了此项,数组元素至少一个（注：群主jma1不需要写入到members里面）
         * }
         ***/

        $url = $this->url . 'chatgroups';
        $result = $this->curl($url, $param);
        return $result;
    }

    /**
     * 修改群组信息
     * @param $param ['group_id'] 群组 ID 必填
     * @param $param ['groupname'] 群组名称 修改时值不能包含斜杠("/")
     * @param $param ['description'] 群组描述 修改时值不能包含斜杠("/")。有空格时需要用“+”代替。
     * @param $param ['maxusers'] 群组成员最大数(包括群主) 值为数值类型
     * @return bool|mixed
     */
    public function editGroup($param)
    {
        $url = $this->url . 'chatgroups/' . $param['group_id'];
        unset($param['group_id']);
        $result = $this->curl($url, $param, null, 'PUT');
        return $result;
    }

    /**
     * @param $param ['group_id'] 群组 ID 必填
     * @return bool|mixed
     */
    public function deleteGroup($param)
    {
        $url = $this->url . 'chatgroups/' . $param['group_id'];
        $result = $this->curl($url, null, null, 'DELETE');
        return $result;
    }

    /**
     * 获取群组中的所有成员
     * @param $param['group_id'] 群组 ID 必填
     * @return bool|mixed
     */
    public function groupUser($param)
    {
        $url = $this->url . 'chatgroups/' . $param['group_id'] . '/users';
        $result = $this->curl($url, null, null, 'GET');
        return $result;
    }

    /**
     * curl 获取数据
     * @param $url
     * @param null $param
     * @param null $header
     * @param string $method
     * @param int $second
     * @return bool|mixed
     */
    public function curl($url, $param = null, $header = null, $method = 'POST', $second = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($header === null) {
            curl_setopt($ch, CURLOPT_HEADER, false);
        } else {
            curl_setopt($ch, CURLOPT_HEADER, $header);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($param !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        $r = curl_exec($ch);
        if (curl_errno($ch) !== 0) {
            return false;
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            return false;
        }
        curl_close($ch);
        return $r;
    }
}