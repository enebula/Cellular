<?php
/**
 * Cellular Framework
 * 微信公众号第三方平台接口
 * @copyright Cellular Team
 */
namespace ext\wechat;
class Platform
{
    /**
     * 获取第三方平台 component_access_token
     * 第三方平台compoment_access_token是第三方平台的下文中接口的调用凭据，也叫做令牌（component_access_token）。每个令牌是存在有效期（2小时）的，且令牌的调用不是无限制的，请第三方平台做好令牌的管理，在令牌快过期时（比如1小时50分）再进行刷新。
     */
    public static function token()
    {
        /*
         * component_appid         第三方平台appid
         * component_appsecret	   第三方平台appsecret
         * component_verify_ticket 微信后台推送的ticket，此ticket会定时推送，具体请见本页的推送说明
         */

        /*
         * POST数据示例:
         * {
         * "component_appid":"appid_value" ,
         * "component_appsecret": "appsecret_value",
         * "component_verify_ticket": "ticket_value"
         * }
         */

        /*
         * 结果参数说明
         * component_access_token 第三方平台access_token
         * expires_in             有效期
         */

        /*
         * 返回结果示例
         * {
         * "component_access_token":"61W3mEpU66027wgNZ_MhGHNQDHnFATkDa9-2llqrMBjUwxRSNPbVsMmyD-yq8wZETSoE5NQgecigDrSHkPtIYA",
         * "expires_in":7200
         * }
         */
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
        $param = [
            'component_appid' => '',
            'component_appsecret' => '',
            'component_verify_ticket' => ''
        ];
        $callback = curlPost($url, $param);
        return $callback;
    }

    /**
     * 获取预授权码 pre_auth_code
     * 该API用于获取预授权码。预授权码用于公众号授权时的第三方平台方安全验证。
     * @param $token
     * @return mixed
     */
    public static function authCode($token)
    {
        /*
         * component_appid 第三方平台方appid
         */

        /*
         * POST数据示例:
         * {
         * "component_appid":"appid_value"
         * }
         */

        /*
         * 结果参数说明
         * pre_auth_code 预授权码
         * expires_in    有效期，为20分钟
         */

        /*
         * 返回结果示例
         * {
         * "pre_auth_code":"Cx_Dk6qiBE0Dmx4EmlT3oRfArPvwSQ-oa3NL_fwHM7VI08r52wazoZX2Rhpz1dEw",
         * "expires_in":600
         * }
         */
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=' . $token;
        $param['component_appid'] = '';
        $callback = curlPost($url, $param);
        return $callback;
    }

    /**
     * 使用授权码换取公众号的接口调用凭据和授权信息
     * 该API用于使用授权码换取授权公众号的授权信息，并换取authorizer_access_token和authorizer_refresh_token。 授权码的获取，需要在用户在第三方平台授权页中完成授权流程后，在回调URI中通过URL参数提供给第三方平台方。
     * 请注意，由于现在公众号可以自定义选择部分权限授权给第三方平台，因此第三方平台开发者需要通过该接口来获取公众号具体授权了哪些权限，而不是简单地认为自己声明的权限就是公众号授权的权限。
     * @param $token
     * @return mixed
     */
    public static function auth($token)
    {
        /*
         * 请求参数说明
         * component_appid    第三方平台appid
         * authorization_code 授权code,会在授权成功时返回给第三方平台，详见第三方平台授权流程说明
         */

        /*
         * POST数据示例:
         * {
         * "component_appid":"appid_value" ,
         * "authorization_code": "auth_code_value"
         * }
         */

        /*
         * 结果参数说明
         * authorization_info       授权信息
         * authorizer_appid         授权方appid
         * authorizer_access_token  授权方接口调用凭据（在授权的公众号具备API权限时，才有此返回值），也简称为令牌
         * expires_in               有效期（在授权的公众号具备API权限时，才有此返回值）
         * authorizer_refresh_token 接口调用凭据刷新令牌（在授权的公众号具备API权限时，才有此返回值），刷新令牌主要用于公众号第三方平台获取和刷新已授权用户的access_token，只会在授权时刻提供，请妥善保存。 一旦丢失，只能让用户重新授权，才能再次拿到新的刷新令牌
         * func_info                公众号授权给开发者的权限集列表，ID为1到15时分别代表：消息管理权限 用户管理权限 帐号服务权限 网页服务权限 微信小店权限 微信多客服权限 群发与通知权限 微信卡券权限 微信扫一扫权限 微信连WIFI权限 素材管理权限 微信摇周边权限 微信门店权限 微信支付权限 自定义菜单权限 请注意：1）该字段的返回不会考虑公众号是否具备该权限集的权限（因为可能部分具备），请根据公众号的帐号类型和认证情况，来判断公众号的接口权限。
         */

        /*
         * 返回结果示例
         * {
         * "authorization_info": {
         * "authorizer_appid": "wxf8b4f85f3a794e77",
         * "authorizer_access_token": "QXjUqNqfYVH0yBE1iI_7vuN_9gQbpjfK7hYwJ3P7xOa88a89-Aga5x1NMYJyB8G2yKt1KCl0nPC3W9GJzw0Zzq_dBxc8pxIGUNi_bFes0qM",
         * "expires_in": 7200,
         * "authorizer_refresh_token": "dTo-YCXPL4llX-u1W1pPpnp8Hgm4wpJtlR6iV0doKdY",
         * "func_info": [
         * {
         * "funcscope_category": {
         * "id": 1
         * }
         * },
         * {
         * "funcscope_category": {
         * "id": 2
         * }
         * },
         * {
         * "funcscope_category": {
         * "id": 3
         * }
         * }
         * ]
         * }
         */
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=' . $token;
        $param = [
            'component_appid' => '',
            'authorization_code' => ''
        ];
        $callback = curlPost($url, $param);
        return $callback;
    }

    /**
     * 获取（刷新）授权公众号的接口调用凭据（令牌）
     * 该API用于在授权方令牌（authorizer_access_token）失效时，可用刷新令牌（authorizer_refresh_token）获取新的令牌。请注意，此处token是2小时刷新一次，开发者需要自行进行token的缓存，避免token的获取次数达到每日的限定额度。
     * @param $token
     * @return mixed
     */
    public static function authToken($token)
    {
        /*
         * 请求参数说明
         * component_appid          第三方平台appid
         * authorizer_appid         授权方appid
         * authorizer_refresh_token 授权方的刷新令牌，刷新令牌主要用于公众号第三方平台获取和刷新已授权用户的access_token，只会在授权时刻提供，请妥善保存。一旦丢失，只能让用户重新授权，才能再次拿到新的刷新令牌
         */

        /*
         * POST数据示例:
         * {
         * "component_appid":"appid_value",
         * "authorizer_appid":"auth_appid_value",
         * "authorizer_refresh_token":"refresh_token_value",
         * }
         */

        /*
         * 结果参数说明
         * authorizer_access_token  授权方令牌
         * expires_in               有效期，为2小时
         * authorizer_refresh_token 刷新令牌
         */

        /*
         * 返回结果示例
         * {
         * "authorizer_access_token": "aaUl5s6kAByLwgV0BhXNuIFFUqfrR8vTATsoSHukcIGqJgrc4KmMJ-JlKoC_-NKCLBvuU1cWPv4vDcLN8Z0pn5I45mpATruU0b51hzeT1f8",
         * "expires_in": 7200,
         * "authorizer_refresh_token": "BstnRqgTJBXb9N2aJq6L5hzfJwP406tpfahQeLNxX0w"
         * }
         */
        $url = 'https:// api.weixin.qq.com /cgi-bin/component/api_authorizer_token?component_access_token=' . $token;
        $param = [
            'component_appid' => '',
            'authorizer_appid' => '',
            'authorizer_refresh_token' => ''
        ];
        $callback = curlPost($url, $param);
        return $callback;
    }

    /**
     * 获取授权方的公众号帐号基本信息
     * 该API用于获取授权方的公众号基本信息，包括头像、昵称、帐号类型、认证类型、微信号、原始ID和二维码图片URL。
     * 需要特别记录授权方的帐号类型，在消息及事件推送时，对于不具备客服接口的公众号，需要在5秒内立即响应；而若有客服接口，则可以选择暂时不响应，而选择后续通过客服接口来发送消息触达粉丝。
     * @param $token
     * @return mixed
     */
    public static function info($token)
    {
        /*
         * 请求参数说明
         * component_appid  服务appid
         * authorizer_appid 授权方appid
         */

        /*
         * POST数据示例:
         * {
         * "component_appid":"appid_value" ,
         * "authorizer_appid": "auth_appid_value"
         * }
         */

        /*
         * 结果参数说明
         * authorizer_info    授权方昵称
         * head_img           授权方头像
         * service_type_info  授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号
         * verify_type_info   授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证
         * user_name          授权方公众号的原始ID
         * alias              授权方公众号所设置的微信号，可能为空
         * business_info      用以了解以下功能的开通状况（0代表未开通，1代表已开通）：open_store:是否开通微信门店功能 open_scan:是否开通微信扫商品功能 open_pay:是否开通微信支付功能 open_card:是否开通微信卡券功能 open_shake:是否开通微信摇一摇功能
         * qrcode_url         二维码图片的URL，开发者最好自行也进行保存
         * authorization_info 授权信息
         * appid              授权方appid
         * func_info          公众号授权给开发者的权限集列表，ID为1到15时分别代表：消息管理权限 用户管理权限 帐号服务权限 网页服务权限 微信小店权限 微信多客服权限 群发与通知权限 微信卡券权限 微信扫一扫权限 微信连WIFI权限 素材管理权限 微信摇周边权限 微信门店权限 微信支付权限 自定义菜单权限 请注意：1）该字段的返回不会考虑公众号是否具备该权限集的权限（因为可能部分具备），请根据公众号的帐号类型和认证情况，来判断公众号的接口权限。
         */

        /*
         * 返回结果实例
         * {
         * "authorizer_info": {
         * "nick_name": "微信SDK Demo Special",
         * "head_img": "http://wx.qlogo.cn/mmopen/GPyw0pGicibl5Eda4GmSSbTguhjg9LZjumHmVjybjiaQXnE9XrXEts6ny9Uv4Fk6hOScWRDibq1fI0WOkSaAjaecNTict3n6EjJaC/0",
         * "service_type_info": { "id": 2 },
         * "verify_type_info": { "id": 0 },
         * "user_name":"gh_eb5e3a772040",
         * "business_info": {"open_store": 0, "open_scan": 0, "open_pay": 0, "open_card": 0, "open_shake": 0},
         * "alias":"paytest01"
         * },
         * "qrcode_url":"URL",
         * "authorization_info": {
         * "appid": "wxf8b4f85f3a794e77",
         * "func_info": [
         * { "funcscope_category": { "id": 1 } },
         * { "funcscope_category": { "id": 2 } },
         * { "funcscope_category": { "id": 3 } }
         * ]
         * }
         * }
         */
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=' . $token;
        $param = [];
        $callback = curlPost($url, $param);
        return $callback;
    }

    /**
     * 获取授权方的选项设置信息
     * 该API用于获取授权方的公众号的选项设置信息，如：地理位置上报，语音识别开关，多客服开关。注意，获取各项选项设置信息，需要有授权方的授权，详见权限集说明。
     * @param $token
     * @return mixed
     */
    public static function get($token)
    {
        /*
         * 请求参数说明
         * component_appid  第三方平台appid
         * authorizer_appid 授权公众号appid
         * option_name      选项名称
         */

        /*
         * POST数据示例
         * {
         * "component_appid":"appid_value",
         * "authorizer_appid": " auth_appid_value ",
         * "option_name": "option_name_value"
         * }
         */

        /*
         * 结果参数说明
         * authorizer_appid 授权公众号appid
         * option_name      选项名称
         * option_value     选项值
         */

        /*
         * 返回结果示例
         * {
         * "authorizer_appid":"wx7bc5ba58cabd00f4",
         * "option_name":"voice_recognize",
         * "option_value":"1"
         * }
         */
        $url = 'https://api.weixin.qq.com/cgi-bin/component/ api_get_authorizer_option?component_access_token=' . $token;
        $param = [];
        $callback = curlPost($url, $param);
        return $callback;
    }

    /**
     * 设置授权方的选项信息
     * 该API用于设置授权方的公众号的选项信息，如：地理位置上报，语音识别开关，多客服开关。注意，设置各项选项设置信息，需要有授权方的授权，详见权限集说明。
     * @param $token
     * @return mixed
     */
    public static function set($token)
    {
        /*
         * 请求参数说明
         * component_appid  第三方平台appid
         * authorizer_appid 授权公众号appid
         * option_name      选项名称
         * option_value     设置的选项值
         */

        /*
         * POST数据示例
         * {
         * "component_appid":"appid_value",
         * "authorizer_appid": " auth_appid_value ",
         * "option_name": "option_name_value",
         * "option_value":"option_value_value"
         * }
         */

        /*
         * 结果参数说明
         * errcode 错误码
         * errmsg  错误信息
         */

        /*
         * 返回结果示例
         * {
         * "errcode":0,
         * "errmsg":"ok"
         * }
         */

        /*
         * 选项名和选项值表
         * option_name                     选项值说明
         * location_report  地理位置上报选项 0 无上报 1 进入会话时上报 2	每5s上报
         * voice_recognize  语音识别开关选项 0 关闭语音识别 1 开启语音识别
         * customer_service 多客服开关选项   0 关闭多客服 1 开启多客服
         */
        $url = 'https://api.weixin.qq.com/cgi-bin/component/ api_set_authorizer_option?component_access_token=' . $token;
        $param = [
            'component_appid'  => '',
            'authorizer_appid' => '',
            'option_name'      => '',
            'option_value'     => ''
        ];
        $callback = curlPost($url, $param);
        return $callback;
    }

    public static function curlPost($url, $param, $second = 30)
    {
        if (is_array($param)) {
            $param = http_build_query($param);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
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