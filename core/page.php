<?php
/**
 * Cellular Framework
 * 分页类
 * @copyright Cellular Team
 */

namespace core;
use Cellular;

class Page
{
    public function display($total, $page, $limit, $param = null)
    {
        $result = array();
        if ($total < 1) return $result;
        if (!$page) $page = 1;
        $result['total_page'] = ceil($total / $limit); //分页总数
        if ($result['total_page'] > 1) {
            $result['previous'] = $this->url($page > 1 ? $page - 1 : 1, $param); //上页链接
            $result['next'] = $this->url($page < $result['total_page'] ? $page + 1 : $result['total_page'], $param); //下页链接
            $result['first'] = $this->url(1, $param); //首页链接
            $result['last'] = $this->url($result['total_page'], $param); //尾页链接
        }
        //计算按步长分页
        $result['step'] = array();
        $step = 5;  //步长
        $start = $page - ($step - 1) / 2;
        if ($start < 1) {
            $start = 1;
            $end = ($step > $result['total_page']) ? $result['total_page'] : $step;
        } else {
            $end = $start + $step - 1;
            if ($end > $result['total_page']) {
                $offset = $start - ($end - $result['total_page']);
                $start = ($offset < 1) ? 1 : $offset;
                $end = $result['total_page'];
            }
        }
        $temp = array();
        for ($i = $start; $i <= $end; $i++) $temp[] = intval($i);
        foreach ($temp as $key => $value) $result['step'][$value] = $this->url($value, $param);
        return $result;
    }

    private function url($page, $param)
    {
        $controller = isset($param['controller']) ? $param['controller'] : null;
        $action = isset($param['action']) ? $param['action'] : null;
        $param['page'] = $page;
        return Cellular::getURL($controller, $action, $param);
    }
}
?>
