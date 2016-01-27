<h1>Cellular</h1>
<p>Cellular是一个超轻量级的WEB开发框架，完全使用PHP语言编写，框架由一个核心文件和一些基本类库组成。</p>

<h3>基本功能</h3>
<ul>
  <li>支持MVC开发模式</li>
  <li>支持自动加载类</li>
  <li>实现单一入口</li>
</ul>

<h3>引用Cellular框架</h3>
<p><p>
<pre>
# 引入框核心文件
include('/Cellular/init.php');

# 设置调试状态
# development: 开发模式(显示错误信息)
# production: 生产模式(不显示错误信息)
Cellular::debug('development');

#启动应用
# path: 应用程序目录
# application: 应用程序名称(应程序文件夹名称)，此项可以为空，如果为空URL的第一层目录默认为应用程序名称
Cellular::application('/path', 'application');
</pre>
