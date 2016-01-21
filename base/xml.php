<?php
/**
 *
 * Cellular Framework
 * XML封装
 *
 * @author mark weixuan.1987@hotmail.com
 * @version 1.0 2015-12-24
 *
 */

namespace base;

class Xml {

    private $char;
    private $chars;
    private $doc;
    private $di;

    public function __construct()
    {
        $this->chars = array('utf8' => 'UTF-8', 'gbk' => 'GBK');
        $this->char = $this->chars['utf8'];
        $this->di = array();
        if (class_exists('DOMDocument')) $this->di['doc'] = '\DOMDocument';
        if (class_exists('SimpleXMLElement')) $this->di['simpleXML'] = '\SimpleXMLElement';
    }

    //设置编码格式
    public function charset($name)
    {
        if (isset($this->chars[$name])) {
            $this->char = $this->chars[$name];
        }
    }

    private function creatNode($key, $value, $root)
    {
        //检测标签名格式是否安全
        if (preg_match("/^[A-Za-z]+[A-Za-z0-9_]*$/", $key)) {
            $node = $this->doc->createElement($key);
            $root = $root->appendChild($node);
            if (is_array($value)) {
                foreach ($value as $k => $val) {
                    $this->creatNode($k, $val, $root);
                }
            } else {
                $text = $this->doc->createTextNode($value);
            $   root->appendChild($text);
            }
        }
    }

    /**
    * json转为XML
    * @param array | string $json json数组格式
    * @param boolean $arr $json参数是否为数组
    * @return XML
    */
    public function jsonToXml($json, $arr = true)
    {
        if (isset($this->di['doc'])) {
            try {
                $this->doc = new $this->di['doc']('1.0', $this->char);
                //$this->doc->formatOutput = true;
                if (false === $arr) $json = json_decode($json, true);
                foreach ($json as $key => $value) {
                    $this->creatNode($key, $value, $this->doc);
                }
                return $this->doc->saveXML();
            } catch (Exception $e) {
                echo 'Exception:'.$e->getMessage();
            }
        } else {
            echo 'The "Xml" class "DOMDocument dependency" is missing';
        }
        return false;
    }

    /**
    * XML解析为数组对象
    * @param string $xml xml文本字符串
    * @return object
    */
    public function xmlToObject($xml)
    {
        if ($xml) {
            if (isset($this->di['simpleXML'])) {
                try {
                    $xml = new $this->di['simpleXML']($xml);
                    if (false === $xml) return false;
                    return $xml;
                } catch (Exception $e) {
                    echo 'Exception:'.$e->getMessage();
                }
            } else {
                echo 'The "Xml" class "SimpleXMLElement dependency" is missing';
            }
        }
        return false;
    }

}

?>
