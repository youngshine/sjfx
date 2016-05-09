<?php
//试卷分析－发送模版消息 token 微信上墙－现场大屏幕气氛 https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxe7253a6972bd2d4b&secret=c5c604c56402baac2c7ccd98b35ef2f2 

header("Content-type: text/html; charset=utf-8");
/*
define("ACCESS_TOKEN", "tcAMpvWOvLGdTOb3W0s3yjztugKXjWhg9Sp0OpCmCV3eoZKt6oZJVML6mLvbU2WbowpQXArrupm8P3eAGhHSjpXh8qdM-NIALzYnw0nAHHUFNGfABAYGZ");
*/

require_once "jssdk-token.php";

$corpid = "wxe7253a6972bd2d4b";
$corpsecret = "c5c604c56402baac2c7ccd98b35ef2f2";
$jssdk = new JSSDK($corpid, $corpsecret);
$access_token = $jssdk->getAccessToken();

define("ACCESS_TOKEN",$access_token );

//发送模版消息，参数wxid..
function httpPost($data,$access_token){
	$ch = curl_init();
	$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . ACCESS_TOKEN;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$tmpInfo = curl_exec($ch);
	if (curl_errno($ch)) {
	  return curl_error($ch);
	}

	curl_close($ch);
	return $tmpInfo;
}

$wxID = $_REQUEST['wxID'];
$content = addslashes($_REQUEST['content']);
$nj = $_REQUEST['nj'];
$km = $_REQUEST['km'];
$created = $_REQUEST['created'];
$updated = $_REQUEST['updated'];

// 测评结果模版 
$data = '{
       "touser":"' . $wxID . '",
       "template_id":"jGvwz2MMptj_w7CuN5A26oPt-r0GnhlQzXxBACqYA8I",
       "url":"http://www.xzpt.org/sjfx/script/weixinJS/oAuth2.php?menuitem=review",            
       "data":{
               "first": {
                   "value":"你于'. $created . '上传试卷已经分析完毕。",
                   "color":"#173177"
               },
               "keyword1": {
                   "value":"'.$nj.$km.'",
                   "color":"#173177"
               },
               "keyword2": {
                   "value":"' . $updated . '",
                   "color":"#173177"
               },
               "keyword3":{
                   "value": "'.$content.'\n如需进一步了解，请致电： 400-6680-118",
                   "color":"#173177"
               }
       }
   }';




echo httpPost($data);

?>

