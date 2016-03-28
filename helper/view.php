<?php
/**
 * 生成站内URL访问地址
 * @param null $controller
 * @param null $action
 * @param null $param
 */
function URL($controller = null, $action = null, $param = null)
{
    return Cellular::getURL($controller, $action, $param);
}
?>