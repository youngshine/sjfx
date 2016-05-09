<?php
	//试卷分析公众号，json文件路径
	require_once "jssdk-sig.php";

	$url = $_REQUEST['url']; // 前台传入，因ajax调用后台文件
	$corpid = "wxe7253a6972bd2d4b";
	$corpsecret = "c5c604c56402baac2c7ccd98b35ef2f2";
	
	$jssdk = new JSSDK($corpid, $corpsecret, $url);
	
	$signPackage = $jssdk->getSignPackage();
	
	echo json_encode($signPackage);
?>

