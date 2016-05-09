<?php
//根号数理化
//$con = mysql_connect("w.rdc.sae.sina.com.cn","SAE_MYSQL_USER","SAE_MYSQL_PASS");
$con = mysql_connect("localhost","root","rootroot2@"); //本地
// 新浪云mysql
//$con = mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }  

mysql_select_db("wx_sjfx", $con); //本地
//mysql_select_db("app_fulindoor", $con);
mysql_query("SET NAMES utf8");
date_default_timezone_set("Asia/Shanghai");

?>
