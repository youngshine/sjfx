<?php
//试卷分析 token https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxe7253a6972bd2d4b&secret=c5c604c56402baac2c7ccd98b35ef2f2 

header("Content-type: text/html; charset=utf-8");
define("ACCESS_TOKEN", "-zh93xxSTrMOTJrr-QtkJE_doXvpNAFwAZ7EWkbzB7ar5i_6_9NNcTXwT6K7hUiIaZh0h06_L52Pp6W7iRfnPlvR-kWOLVkxvxUPADytiKcBMBcADAQBQ");

//创建菜单
function httpPost($data){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".ACCESS_TOKEN);
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
/*
//获取菜单
function getMenu(){
return file_get_contents("https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".ACCESS_TOKEN);
}

//删除菜单
function deleteMenu(){
return file_get_contents("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".ACCESS_TOKEN);
}

*/
/*
                {
                    "type": "view", 
                    "name": "调试", 
                    "url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe7253a6972bd2d4b&redirect_uri=http%3a%2f%2fwww.yiqizo.com%2fweixin%2fsjfx%2fscript%2fweixinJS%2foAuth2-exam-upload.php&response_type=code&scope=snsapi_base&state=123#wechat_redirect"
                }

            "sub_button": [
                {
                    "type": "view", 
                    "name": "账号绑定", 
                    "url": "http://www.yiqizo.com/abc/script/weixinJS/oAuth2-member.php"
                }
            ]
*/



$data = '{
    "button": [
        {
            "name": "上传试卷", 
            "type": "view", 
			"url": "http://www.xzpt.org/wx_sjfx/script/weixinJS/oAuth2.php?menuitem=upload"
        }, 	
        {
            "name": "分析报告", 
			"type": "view",
            "url": "http://www.xzpt.org/wx_sjfx/script/weixinJS/oAuth2.php?menuitem=review"
        },         
        {
            "name": "账号", 
            "sub_button": [
                {
                    "type": "view", 
                    "name": "注册", 
                    "url": "http://www.xzpt.org/wx_sjfx/script/weixinJS/oAuth2.php?menuitem=member"
                }, 
                {
                    "type": "view", 
                    "name": "帮助", 
                    "url": "http://www.xzpt.org/wx_sjfx/help.html"
                }
            ]
        }
    ]
}';




echo httpPost($data);

?>

