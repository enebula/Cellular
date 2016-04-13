<?php
/**
 * Cellular Framework
 * 微信消息推送处理接口
 * @copyright Cellular Team
 */

namespace ext\wechat;
class Push
{
    const MSG_TEXT = 'text';             # 1 test       文本消息
    const MSG_IMAGE = 'image';           # 2 image		图片消息
    const MSG_VOICE = 'voice';           # 3 voice		语音消息
    const MSG_VIDEO = 'video';           # 4 video		视频消息
    const MSG_SHORTVIDEO = 'shortvideo'; # 5 shortvideo	小视频消息
    const MSG_LOCATION = 'location';     # 6 location	地理位置消息
    const MSG_LINK = 'link';             # 7 link		链接消息
    const MSG_EVENT = 'event';           # 8 event		事件消息
    const MSG_MUSIC = 'music';
    const MSG_NEWS = 'news';
    private $token;
    private $_msg;
    private $_funcflag = false;
    private $_receive;
    public $debug = false;
    private $_logcallback;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * 验证服务器地址的有效性
     * @return bool
     */
    private function signature()
    {
        if (!isset($_GET['signature']) || !isset($_GET['timestamp']) || !isset($_GET['nonce'])) die();
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证函数
     * @param bool $return
     * @return bool|string
     */
    public function valid($return = false)
    {
        $echoStr = isset($_GET["echostr"]) ? $_GET["echostr"] : '';
        if ($return) {
            if ($echoStr) {
                if ($this->signature())
                    return $echoStr;
                else
                    return false;
            } else
                return $this->signature();
        } else {
            if ($echoStr) {
                if ($this->signature())
                    die($echoStr);
                else
                    die('no access');
            } else {
                if ($this->signature())
                    return true;
                else
                    die('no access');
            }
        }
        return false;
    }

    /**
     * 发送消息
     * @param string $msg
     * @param bool $append
     * @return array|string
     */
    public function message($msg = '', $append = false)
    {
        if (is_null($msg)) {
            $this->_msg = array();
        } elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg, $msg);
            else
                $this->_msg = $msg;
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }

    public function setFuncFlag($flag)
    {
        $this->_funcflag = $flag;
        return $this;
    }

    private function log($log)
    {
        if ($this->debug && function_exists($this->_logcallback)) {
            if (is_array($log)) $log = print_r($log, true);
            return call_user_func($this->_logcallback, $log);
        }
    }

    /**
     * 获取微信服务器发来的信息
     * @return $this
     */
    public function receive()
    {
        $content = file_get_contents("php://input");
        $this->log($content);
        if (!empty($content)) {
            $this->_receive = (array)simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        return $this;
    }

    /**
     * 获取消息发送者
     * @return bool
     */
    public function getForm()
    {
        if ($this->_receive)
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    /**
     * 获取消息接收者
     * @return bool
     */
    public function getTo()
    {
        if ($this->_receive)
            return $this->_receive['ToUserName'];
        else
            return false;
    }

    /**
     * 获取接收消息的类型
     * @return bool
     */
    public function getType()
    {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }

    /**
     * 获取消息 ID
     * @return bool
     */
    public function getID()
    {
        if (isset($this->_receive['MsgId']))
            return $this->_receive['MsgId'];
        else
            return false;
    }

    /**
     * 获取消息发送时间
     * @return bool
     */
    public function getTime()
    {
        if (isset($this->_receive['CreateTime']))
            return $this->_receive['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     * @return bool
     */
    public function getContent()
    {
        if (isset($this->_receive['Content']))
            return $this->_receive['Content'];
        else
            return false;
    }

    /**
     * 获取接收消息图片
     * @return bool|string
     */
    public function getPicture()
    {
        if (isset($this->_receive['PicUrl']))
            return (string)$this->_receive['PicUrl'];
        else
            return false;
    }

    /**
     * 获取接收消息链接
     * @return array|bool
     */
    public function getLink()
    {
        if (isset($this->_receive['Url'])) {
            return array(
                'url' => $this->_receive['Url'],
                'title' => $this->_receive['Title'],
                'description' => $this->_receive['Description']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     * @return array|bool
     */
    public function getGeo()
    {
        if (isset($this->_receive['Location_X'])) {
            return array(
                'x' => $this->_receive['Location_X'],
                'y' => $this->_receive['Location_Y'],
                'scale' => $this->_receive['Scale'],
                'label' => $this->_receive['Label']
            );
        } else
            return false;
    }

    /**
     * 获取接收事件推送
     * @return array|bool
     */
    public function getEvent()
    {
        if (isset($this->_receive['Event'])) {
            return array(
                'event' => $this->_receive['Event'],
                'key' => $this->_receive['EventKey'],
            );
        } else
            return false;
    }

    public static function xmlSafeStr($str)
    {
        return '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str) . ']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data)
    {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
            list($key,) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id 数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml .= "<{$root}{$attr}>";
        $xml .= self::data_to_xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }

    /**
     * 设置回复消息
     * Example: $obj->text('hello')->reply();
     * @param string $text
     * @return $this
     */
    public function text($text = '')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getForm(),
            'FromUserName' => $this->getTo(),
            'MsgType' => self::MSG_TEXT,
            'Content' => $text,
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
        );
        $this->message($msg);
        return $this;
    }

    /**
     * 设置回复音乐
     * @param $title
     * @param $desc
     * @param $musicurl
     * @param string $hgmusicurl
     * @return $this
     */
    public function music($title, $desc, $musicurl, $hgmusicurl = '')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getForm(),
            'FromUserName' => $this->getTo(),
            'CreateTime' => time(),
            'MsgType' => self::MSG_MUSIC,
            'Music' => array(
                'Title' => $title,
                'Description' => $desc,
                'MusicUrl' => $musicurl,
                'HQMusicUrl' => $hgmusicurl
            ),
            'FuncFlag' => $FuncFlag
        );
        $this->message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *    [0]=>array(
     *        'Title'=>'msg title',
     *        'Description'=>'summary text',
     *        'PicUrl'=>'http://www.domain.com/1.jpg',
     *        'Url'=>'http://www.domain.com/1.html'
     *    ),
     *    [1]=>....
     *  )
     * @return $this
     */
    public function news($newsData = array())
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $count = count($newsData);

        $msg = array(
            'ToUserName' => $this->getForm(),
            'FromUserName' => $this->getTo(),
            'MsgType' => self::MSGTYPE_NEWS,
            'CreateTime' => time(),
            'ArticleCount' => $count,
            'Articles' => $newsData,
            'FuncFlag' => $FuncFlag
        );
        $this->message($msg);
        return $this;
    }

    /**
     * 回复微信服务器, 此函数支持链式操作
     * Example: $this->text('msg tips')->reply();
     * @param array $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     * @return string
     */
    public function reply($msg = array(), $return = false)
    {
        if (empty($msg))
            $msg = $this->_msg;
        $xmldata = $this->xml_encode($msg);
        $this->log($xmldata);
        if ($return)
            return $xmldata;
        else
            echo $xmldata;
    }
}