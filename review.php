<?php
	//http://www.jb51.net/callback.php
	$code = $_GET["code"]; 
	$appid = "wxe7253a6972bd2d4b"; 
	$secret = "c5c604c56402baac2c7ccd98b35ef2f2"; 

//通过code换取的是一个特殊的网页授权access_token,与基础支持中的access_token（该access_token用于调用其他接口）不同	
	$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url); 
	curl_setopt($ch,CURLOPT_HEADER,0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
	$res = curl_exec($ch); 
	curl_close($ch); 
	$json_obj = json_decode($res,true); //获得access_token & openid
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
	<meta charset="utf-8" />
	<meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">	  
	<link rel="stylesheet" href="//cdn.kik.com/app/2.0.1/app.min.css">
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
		
		.list .listItem {
			background: #fff;
			padding: 10px 15px;
			border-bottom: 1px solid #ddd;
		}
		.list .listItem .title {
			 display:block;
			 text-overflow:ellipsis;
			 overflow:hidden;
			 white-space:nowrap;
		}
		.list .listItem .subtitle{
			 color: #888;
			 font-size:0.9em;
		}
	</style> 
  </head>

  <body>
	  
    <div class="app-page" data-page="home">
      <div class="app-topbar">
          <div class="app-title">试卷分析结果</div>
      </div>
      <div class="app-content">
  		<!-- 
		<div style="margin-bottom:10px;">
			<input class="app-input" type="search" placeholder="search..." readOnly=true>
		</div>	
		-->
	    <div class="list">
			<div class="listItem">		
				<div class="title" style="line-height:30px;">
					
					<span class="examId" style="display:none;"></span>				
					<span class="time"></span>｜<span class="njkm"></span>
					
					<span class="removeItem" style="float:right;color:pink;display:none;">删除</span>
					<span class="showItem" style="float:right;color:green;display:none;">查看</span>
					
				</div>
				<div class="photos"></div>

			</div>
	    </div>
		
	  </div>	  
    </div>
	
    <div class="app-page" data-page="more">
      <div class="app-topbar">      
        <div class="app-title">分析结果</div>
		<div class="app-button left" data-back data-autotitle></div>
      </div>

      <div class="app-content">
		
		<div class="app-section">
			<div class="summary"></div>
		</div>

		<div class="app-section">
			<div class="app-button red btnWrongs">错题分析</div>
			<div class="app-button orange btnChartZsd">知识点权重图表</div>
			<div class="app-button green btnChartWrongs">出错知识点及建议</div>
		</div>
	  </div>	
    </div>
	
    <div class="app-page" data-page="wrongs">
      <div class="app-topbar">      
        <div class="app-title">错题分析</div>
		<div class="app-button left" data-back data-autotitle></div>
      </div>
      <div class="app-content">
		  <!-- -->
	  </div>	
    </div>
	
    <div class="app-page" data-page="chartZsd">
      <div class="app-topbar">      
        <div class="app-title">知识点权重图表</div>
		<div class="app-button left" data-back data-autotitle></div>
      </div>
      <div class="app-content">
  		<div id="canvas-holder" style="text-align:center;margin:10px;">
  			<canvas id="chart-area" width="300" height="300" />
  		</div>
		<!-- legend -->
		<ul class="app-list">
			<li>						
				<span class="value" style="width:30px;"></span>
				<span class="title"></span>
				
			</li>
		</ul>
	  </div>	
    </div>
    <div class="app-page" data-page="chartWrongs">
      <div class="app-topbar">      
        <div class="app-title">错题分析图</div>
		<div class="app-button left" data-back data-autotitle></div>
      </div>
      <div class="app-content">
  		<div id="canvas-holder" style="text-align:center;margin:10px;">
  			<canvas id="chart-area" width="300" height="300" />
  		</div>
		<!-- legend -->
		<div class="app-section">
			<div style="color:green;">薄弱知识点：</div>
			<div class="weak"></div>
		</div>
		<div class="app-section">
			<div style="color:green;">建议复读知识点：</div>
			<div class="learn"></div>
		</div>
	  </div>	
    </div>

    <script src="src/zepto.js"></script>
    <script src="src/app.min.js"></script>
	
	<script src="js/main.js"></script>		
	<script src="js/pg-review.js"></script>
	<script src="js/pg-review-more.js"></script>
	
	<script src="js/Chart.js"></script>	
	
	<script>
		myUserId =  '<?php echo $openid; ?>'
		//alert(myUserId)
		App.load('home');
		
		/* android长按，滑动失灵？但是无法list下拉
		document.addEventListener('touchmove', function (event) {
		   event.preventDefault();
		}, false); */
	</script>
  </body>
</html>
