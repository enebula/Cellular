<?php
/**
 * Cellular Faremwork
 * HTTP 提交输入安全检查
 * @copyright Cellular Team
 */

namespace core;


class Input {

    /**
    * 检查GET值
    * @param string $name 参数名
    * @return string|array 返回参数值或参数数组
    */
    public static function get($name = null)
    {
        if (null == $name) {
            if (count($_GET) > 0) {
                $result = array();
                foreach ($_GET as $key => $value) {
                    $value = self::cleanXSS($value);
                    $result[$key] = self::inject($value);
                }
                return $result;
            }
        } else {
            if (isset($_GET[$name])) {
                $value = self::cleanXSS($_GET[$name]);
                return self::inject($value);
            }
        }
        return false;
    }

    /**
    * 检查POST值
    * @param string $name 参数名
    * @return string|array 返回参数值或参数数组
    */
    public static function post($name = null, $strict = false)
    {
        if (null == $name) {
            if (count($_POST) > 0) {
                $result = array();
                foreach ($_POST as $key => $value) {
                    $value = self::cleanXSS($value);
                    $result[$key] = self::inject($value);
                }
                return $result;
            }
        } else {
            if (isset($_POST[$name])) {
                $value = self::cleanXSS($_POST[$name]);
                return self::inject($value);
            }
        }
        return false;
    }

    /**
    * 过滤XSS跨站脚本攻击
    */
    public static function cleanXSS($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) $value[$key] = self::cleanXSS($val);
            return $value;
        } else {
            //$value = strip_tags($value);
            return htmlspecialchars(trim($value), ENT_QUOTES);
        }
    }

    /**
    * 魔术引号
    */
    public static function inject($value)
    {
        if (!get_magic_quotes_gpc()) {
            if (is_array($value)) {
                foreach ($value as $key => $val) $value[$key] = self::inject($val);
                return $value;
            } else {
                return addslashes($value);
            }
        }
        return $value;
    }

}

?>
