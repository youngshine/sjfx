<?php
//多图下载
require_once "jssdk-token.php";

$mediaId = $_REQUEST['mediaId'];
$fileName = $_REQUEST['fileName'];
//$fileName = '../../assets/img/baoxiao/' . $fileName;
$fileName = '../../' . $fileName; // 相对路径，公用
// 试卷分析
$corpid = "wxe7253a6972bd2d4b";
$corpsecret = "c5c604c56402baac2c7ccd98b35ef2f2";
$jssdk = new JSSDK($corpid, $corpsecret); 
$access_token = $jssdk->getAccessToken();
/*
$url = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaId";
*/
$url =
"http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaId";

$result = downImgFromWx($url);
//var_dump($result);
$fileContent = $result['body'];
//$fileName = '../../assets/img/baoxiao/' . date('YmdHis') . "_" . rand(100,999) . '.jpg';

// 采用文件流保存到服务器
file_put_contents( $fileName, $fileContent );

//echo $fileName; // 前段才能取得返回值json_decode

function downImgFromWx($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOBODY, 0);	//只取body头
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$package = curl_exec($ch);
	$httpinfo = curl_getinfo($ch);
	curl_close($ch);
	$imageAll = array_merge(array('header'=>$httpinfo),array('body'=>$package));
	return $imageAll;
}

?>
