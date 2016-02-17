<?php
/**
 * Cellular Framework
 * Page 分页类
 * @copyright Cellular Team
 */
namespace core;

class Page
{
    function display($total, $page, $limit, $param = null)
    {
        $result = array();
        if ($total < 1) return $result;
        if (!$page) $page = 1;
        $result['current'] = $page; //当前页
        $result['total'] = $total; //总记录数
        $result['limit'] = $limit; //显示记录数
        $result['total_page'] = ceil($total / $limit); //分页总数
        if ($result['total_page'] > 1)
        {
            $result['previous'] = $this->redirect($page - 1, $param); //上页链接
            $result['next'] = $this->redirect($page + 1, $param); //下页链接
            $result['first'] = $this->redirect(1, $param); //首页链接
            $result['last'] = $this->redirect($result['total_page'], $param); //尾页链接
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
        for ($i=$start; $i<=$end; $i++) $temp[] = intval($i);
        foreach ($temp as $key => $value) $result['step'][$value] = $this->redirect($value, $param);
        return $result;
    }

    private function redirect($page, $param = null)
    {
        $param['page'] = $page;
        $url = null;
        foreach ($param as $key => $val) {
            $url .= '&' . $key . '=' . $val;
        }
        return '?' . substr($url, 1);
    }
}
?>
