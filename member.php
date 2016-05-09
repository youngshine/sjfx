<?php
	//http://www.jb51.net/callback.php
	$code = $_GET["code"]; 
	$appid = "wxe7253a6972bd2d4b"; //试卷分析code直接获得id，不需要access_token，企业号则需要
	$secret = "c5c604c56402baac2c7ccd98b35ef2f2"; 
	
	$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url); 
	curl_setopt($ch,CURLOPT_HEADER,0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
	$res = curl_exec($ch); 
	curl_close($ch); 
	$json_obj = json_decode($res,true); 
	//根据openid和access_token查询用户信息 
	//$access_token = $json_obj['access_token']; 
	$openid = $json_obj['openid']; 
	//$openid = 'o2wBFuPB9cVcAa2Xf4JnL2Hhu1og';
	//echo $code;
	//echo $openid;
?>

<!DOCTYPE html>
<html>
	<head>
	<title>试卷分析公益平台</title>
	<meta name="format-detection" content="telephone=no" />
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
	<meta name="kik-transparent-statusbar" content="true" charset="utf-8">	
	<link rel="stylesheet" href="//cdn.kik.com/app/2.0.1/app.min.css">
	<!-- 
	<link rel="stylesheet" href="assets/css/docs-exam.css"> -->
	<style>
		.app-topbar {background:#4285f4;}
		
		/*  msg box */
		#m {
			filter: alpha(opacity=30);
			-moz-opacity: 0.3; 
			opacity: 0.3;
			position:absolute;
			z-index:10000;
			background-color:none;
		}
		#lo {
			position:absolute;
			width:150px;
			height:90px;
			line-height:90px;
			background-color:#000;
			color:#fff;
			text-align:center;
			z-index:9999;
	
			filter: alpha(opacity=70);
			-moz-opacity: 0.7; 
			opacity: 0.7;
			
			border-radius:10px;
			-moz-border-radius:10px; /* Old Firefox */
		}
		/* ends - msg box */
	</style>
	</head>
	<body>		
		<div class="app-page" data-page="home">
			<div class="app-topbar">
				<div class="app-title"></span>账号资料</div>
			</div>

			<div class="app-content">
			
				<div style="margin:15px;">	
					<div style="margin-bottom:10px;">
						<div class="label" style="color:#888;">绑定手机</div>
						<div class="phone" style="background:#fff;height:40px;line-height:45px;padding-left:5px;"></div>
					</div>
					<div style="margin-bottom:10px;">
						<div class="label" style="color:#888;">姓名</div>
						<div class="name" style="background:#fff;height:40px;line-height:45px;padding-left:5px;"></div>
					</div>	

					<div style="margin-bottom:10px;">
						<div class="label" style="color:#888;">所在地区</div>
						<div class="county" style="background:#fff;height:40px;line-height:45px;padding-left:5px;display:block;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;"></div>
					</div>	
					<div style="margin-bottom:10px;">
						<div class="label" style="color:#888;">年级</div>
						<div class="grade" style="background:#fff;height:40px;line-height:45px;padding-left:5px;"></div>
					</div>
				</div>	
				
				<div class="co" style="text-align:center;color:green;margin:15px;">
					服务电话：400-6680-118
				</div>
			</div>		
		</div>
		
		<div class="app-page" data-page="input-phone-confirm">
			<div class="app-topbar">
				<div class="app-button left" data-back data-autotitle></div>
				<div class="app-title">手机验证</div>
			</div>	
			<div class="app-content">
				<form style="margin:15px;">
					<input class="app-input" type="tel" name="phone" placeholder="手机号" maxlength="11">
					
					<div style="border-top:10px solid #eee;border-bottom:1px solid #eee;">
						<div style="float:left;width:50%;background:#fff;margin-right:10px;">
							<input class="app-input" type="tel" name="code" placeholder="验证码" maxlength="4">
						</div>	
						<div class="app-button green getCode">获取验证码</div>	
						<input class="app-input" name="randomCode" placeholder="生成的" style="display:none;">
					</div>	
					
					<div class="form-button" style="clear:both;margin-top:15px;">
						<div class="app-button green ok" style="display:none;">提交</div>	
					</div>
					
				</form>
			</div>	  
		</div>
		
		<!-- 数字、文本及多行文本输入，公用-->
		<div class="app-page" data-page="input-text">
			<div class="app-topbar">
				<div class="app-button left" data-back data-autotitle></div>
				<div class="app-title"> </div>
				<div class="app-button right ok" style="color:#fff;">保存</div>
			</div>
			<div class="app-content">
				<form style="margin:15px;">
					<input class="app-input">	
				</form>
			</div>	  
		</div>
	    <div class="app-page" data-page="select-options">
	      <div class="app-topbar">
	        <div class="app-button left" data-back data-autotitle></div>
	        <div class="app-title">年级</div>
	      </div>
	      <div class="app-content">
			  <ul class="app-list" style="margin:10px;">
				<li>一年级</li>
				<li>二年级</li>
				<li>三年级</li>
				<li>四年级</li>
				<li>五年级</li>
				<li>六年级</li>
				<li>七年级</li>
				<li>八年级</li>
				<li>九年级</li>
			  </ul>	  		 
	      </div>
	    </div>
		
	    <div class="app-page" data-page="select-province">
	      <div class="app-topbar">
			  <!-- 因为在验证号码成功后调用, 不能返回
			  <div class="app-button left" data-back data-autotitle></div>
				  -->
		      <div class="app-title">选择省份</div>
			  <div class="app-button right cancel">╳</div>
	      </div>
	      <div class="app-content">
			 <ul class="app-list">
				<li></li>
			 </ul>
	 	  </div>
	    </div>	
	    <div class="app-page" data-page="select-city">
	      <div class="app-topbar">
			  <div class="app-button left" data-back data-autotitle></div>
	        <div class="app-title">选择市</div>
	      </div>
	      <div class="app-content">
			 <ul class="app-list">
				<li></li>
			 </ul>
	 	  </div>
	    </div>
	    <div class="app-page" data-page="select-district">
	      <div class="app-topbar">
			  <div class="app-button left" data-back data-autotitle></div>
	        <div class="app-title">选择县区</div>
	      </div>
	      <div class="app-content">
			 <ul class="app-list">
				<li></li>
			 </ul>
	 	  </div>
	    </div>
	    <div class="app-page" data-page="select-school">
	      <div class="app-topbar">
			  <div class="app-button left" data-back data-autotitle></div>
	        <div class="app-title">选择学校</div>
	      </div>
		  
	      <div class="app-content">
			<div style="border-top:1px solid #eee;border-bottom:1px solid #eee;">
				<div class="app-button primary" style="float:left;width:50%;background:#fff;">小学</div>	
				<div class="app-button secondary" style="background:#eee;">中学</div>	
			</div>
			 
			 <ul class="app-list">
				<li></li>
			 </ul>
	 	  </div>
	    </div>

		<!-- Not necessary, but will make our lives a little easier 
		<script src="src/zepto.min.js"          ></script>
		-->
	    <script src="src/zepto.js"></script>
	    <script src="src/app.min.js"></script>

			
		<script src="js/main.js"></script>
			
		<script src="js/pg-member.js"></script>
		<script src="js/input.js"></script>
		<script src="js/select-options.js"></script>
		<!-- 
		<script src="js/select-province.js"></script>
		<script src="js/select-city.js"></script>
		-->
		<script>
			// 用户id，全局变量
			myUserId =  '<?php echo $openid;?>';

			App.load('home')
		</script>

		<!-- some kik goodness for demos 
		<script src="//cdn.kik.com/kik/1.0.9/kik.js"></script>  -->
	</body>
</html>
