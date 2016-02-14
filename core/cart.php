<?php
/**
 * Cellular Framework
 * Cart 购物车类
 * @copyright Cellular Team
 */
namespace core;

class Cart extends Base {

  private $cookie = null;

  public function __construct()
  {

  }

	function __set($name, $value) {
		$this->$name = $value;
	}

	/**
	 * 查找
	 */
	function search($id) {
		if (isset($_COOKIE[$this->cookie])) {
			$cart = unserialize($_COOKIE[$this->cookie]);
			if (isset($cart[$id])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 添加
	 * id:编号
	 * number:数量
	 */
	function add($id, $number) {
		if ($this->search($id)) {
			$cart = unserialize($_COOKIE[$this->cookie]);
			$cart[$id] += $number;
			setCookie($this->cookie, serialize($cart), time() + 3600 * 24 * 365);
		} else {
			if (isset($_COOKIE[$this->cookie])) {
				$cart = unserialize($_COOKIE[$this->cookie]);
			}
			$cart[$id] = $number;
			setCookie($this->cookie, serialize($cart), time() + 3600 * 24 * 365);
		}
	}

	/**
	 * 更新
	 */
	function update($id, $number) {
		if ($this->search($id)) {
			$cart = unserialize($_COOKIE[$this->cookie]);
			$cart[$id] = $number;
			setCookie($this->cookie, serialize($cart), time() + 3600 * 24 * 365);
		}
	}

	/**
	 * 移除
	 * id:编号
	 */
	function remove($id) {
		if (isset($_COOKIE[$this->cookie])) {
			$cart = unserialize($_COOKIE[$this->cookie]);
			if (isset($cart[$id])){
				unset($cart[$id]);
			}
			if (count($cart) > 0) {
				setCookie($this->cookie, serialize($cart), time() + 3600 * 24 * 365);
			} else {
				$this->clear();
			}
		}
	}

	/**
	 * 清空
	 */
	function clear() {
		setCookie($this->cookie, null, time() - 1);
	}
  
	/**
	 * 获取
	 */
	function get() {
		if (isset($_COOKIE[$this->cookie])) {
			return unserialize($_COOKIE[$this->cookie]);
		}
		return null;
	}

}
?>
