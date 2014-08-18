<?php
//LinePHP库目录,网站发布时一定要更改
$t = microtime(true);
define('LP_LIBRARY','lib');
require_once(dirname(__FILE__).'/'.LP_LIBRARY.'/line/core/Bootstrap.php');
Bootstrap::start();
echo '<br/>',microtime(true)-$t;
