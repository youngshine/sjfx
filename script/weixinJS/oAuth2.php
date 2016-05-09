<?php 
// 502 bad gateway nginx 1.10
if(isset($_SESSION['user'])){ 
	print_r($_SESSION['user']);
	exit;
}

$url = 'http://www.xzpt.org/wx_sjfx/'; //redirect
$menuitem = $_GET["menuitem"];
switch($menuitem){
	case "upload":
		$url = $url . "script/weixinJS/callback.php";
		break;
	case "review":
		$url = $url . "review.php";
		break;
	case "member":
		$url = $url . "member.php";
		break;
}

$APPID = 'wxe7253a6972bd2d4b';
//$REDIRECT_URI = 'http://www.yiqizo.com/weixin/sjfx/script/weixinJS/callback.php';
$REDIRECT_URI = $url; // 获取用户授权的网页
$scope = 'snsapi_base'; // silent
$state = '9';
//$scope='snsapi_userinfo';//需要授权
$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $APPID . '&redirect_uri=' . urlencode($REDIRECT_URI) . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';

header("Location:".$url);

?>
