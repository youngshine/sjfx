// 结果chart 等等
App.controller('more', function (page,request) {
	var me = this;
	console.log(request)
	
	// 知识点错误个次，做总结
	var wrongs = request.tm_id_list.split(',')
	
	var obj = {
		mb_id: request.mb_id
	} 
	showPrompt('加载中...');		
	$.ajax({
    	url: dataUrl + 'readExammbtmList.php',
		data: obj,
		dataType: "json",
		//jsonp: 'callback',
        success: function(result){
			hidePrompt()
			console.log(result)
			var zsd = 0, zsdWrongs = 0; //出现次数累计
			for(var i=0;i<result.length;i++){
				var tm_id = result[i].tm_id,
					zsdList = result[i].zsd.split(',')
				zsdList.forEach(function(item){
					zsd += 1
					if(wrongs.indexOf(tm_id)>=0){
						zsdWrongs += 1
					}
					
				})
			}
			var pct = 100*zsdWrongs/zsd,
				result = '';
			console.log(pct)
			if(pct>=40){
				result = '<strong>通过互联网＋教育平台大数据分析：</strong><br>此次考试，您的成绩位居中下水平。<br>请上课专心听讲，多做基础练习，从基本知识点抓起！'
			}else if(pct>20 && pct<40){
				result = '<strong>通过互联网＋教育平台大数据分析：</strong><br>此次考试，您的成绩位居中等水平。<br>在掌握基础知识点的情况下，融会贯通各知识点的关系。克服马虎大意，养成做题后检查的好习惯！'
			}else{
				result = '<strong>通过互联网＋教育平台大数据分析：</strong><br>此次考试，您的成绩位居上等水平。<br>基础知识点基本已经掌握，请把时间花在提高及易错题型上。再接再厉！'
			}
			result += '<br>如需进一步了解分析报告及高效的解决方案，请致电：400-6680-118'
			$(page).find('.summary').html(result)
        },
		error: function(xhr, type){
			showPrompt('出错');	
		}
    });
			
	var //btnFeatures = $(page).find('.btnFeatures'),
		btnChartZsd = $(page).find('.btnChartZsd'),
		btnChartWrongs = $(page).find('.btnChartWrongs'),
		btnWrongs = $(page).find('.btnWrongs')


	/*
	btnFeatures.bind('click', function () {
		App.load('features')
	}) */
	
	btnChartZsd.bind('click', function () {		
		var obj = {
			mb_id: request.mb_id
		}
		showPrompt('加载中...');		
		$.ajax({
	    	url: dataUrl + 'readExammbtmList.php',
			data: obj,
			dataType: "json",
			//jsonp: 'callback',
			success: function(result){
				hidePrompt()
				console.log(result)
				var arr = []; // associated array for chart (label,value)
				// 遍历题目
				for(var i=0;i<result.length;i++){
					var zsdList = result[i].zsd.split(',')
					zsdList.forEach(function(item){
						//var ele = $.inArray(item, arr);
						var isExistInArr = false
						for (key in arr) {
							if( item == arr[key].label ){
								isExistInArr = true
								arr[key].value += 1 // 已经存在，次数累加 1
							}				
						}
						if( !isExistInArr ){ // 不存在
							arr.push({
								color: getRandomColor(), //randomColor(),
								//highlight: randomColor(),
								label: item,
								value: 1
							})
						}
						function randomColor(){
							var colorStr = Math.floor(Math.random()*0xFFFFFF).toString(16).toUpperCase();
							return "#"+"000000".substring(0,6-colorStr)+colorStr;
						}
						function getRandomColor() { 
							var c = '#'; 
							var cArray = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F']; 
							for(var i = 0; i < 6;i++) { 
								var cIndex = Math.round(Math.random()*15); 
								c += cArray[cIndex]; 
							} 
							return c; 
						} 	
					})
				}
				console.log(arr)
				//chartPie()
				App.load('chartZsd',arr);
			}
		});		
	})	
	
	btnChartWrongs.bind('click', function () {		
		var obj = {
			wrongs: request.tm_id_list
		}
		showPrompt('加载中...');			
		$.ajax({
	    	url: dataUrl + 'readExammbtmWrongs.php',
			/* 
            params:{
                data: JSON.stringify(objWrongs)
            },
			 */
			data: obj,
			dataType: "json",
			//jsonp: 'callback',
			success: function(result){
				console.log(result)
				hidePrompt()
				var arrLabels = [],
					arrData = []; 
				// 遍历题目
				for(var i=0;i<result.length;i++){
					var zsdList = result[i].zsd.split(',')
					zsdList.forEach(function(item){
						var ele = $.inArray(item, arrLabels); // 单个数组
						if(ele < 0 ){ // 不存在，添加
							arrLabels.push(item);
							arrData.push(1)
						}else{
							arrData[ele] += 1 // 次数累加
						}	
					})
				}
				
				var data = {
					labels : arrLabels,
					datasets : [{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,1)",
						data : arrData
					}]
				}
				console.log(data)
				//chartPie()
				App.load('chartWrongs',data);
			}
		});	
	})	
	
	btnWrongs.bind('click', function () {		
		var obj = {
			wrongs: request.tm_id_list
		}
		showPrompt('加载中...');			
		$.ajax({
	    	url: dataUrl + 'readExammbtmWrongs.php',
			data: obj,
			dataType: "json",
			//jsonp: 'callback',
			success: function(result){
				console.log(result)
				hidePrompt()
				var arr = []; 
				// 遍历题目
				for(var i=0;i<result.length;i++){
					arr.push({
						photo: result[i].photo,
						zsd: result[i].zsd
					})
				}
				console.log(arr)
				//chartPie()
				App.load('wrongs',arr);
			}
		});	
	})				
}); 

// pie chart
App.controller('chartZsd', function (page,request) {
	var me = this;	
	/*
	var pieData = [{
		value: 300,
		color:"#F7464A",
		highlight: "#FF5A5E",
		label: "Red"
	},{
		value: 50,
		color: "#46BFBD",
		highlight: "#5AD3D1",
		label: "Green"
	},{
		value: 100,
		color: "#FDB45C",
		highlight: "#FFC870",
		label: "Yellow"
	}]; */

	var pieData = request
	console.log(pieData)
	
	
	var canvas = $(page).find('#chart-area');
	//console.log(canvas.get(0)); 
	var ctx = canvas.get(0).getContext("2d");
	//var ctx = document.getElementById("chart-area").getContext("2d");
	//console.log(document.getElementById("chart-area"))
	//var ctx = $("#chart-area").get(0).getContext("2d");
	$(page).on('appShow',function(){
		window.myPie = new Chart(ctx).Pie(pieData);
	})	
	
	var $list     = $(page).find('.app-list'),
		$listItem = $(page).find('.app-list li').remove()
	var total = 0
	// 数组循环
	for (key in pieData) {
		total += pieData[key].value	// parseInt		
	}
	
	pieData.forEach(function (item) {
		var $node = $listItem.clone(true);
		//$node.html(item.archive_title );
		$node.find('.title').text(item.label );
		var pct = Math.round(100*item.value/total)
		$node.find('.value').text(pct + '%' );
		$node.find('.value').css('background',item.color)
		$node.find('.value').css('color',"#fff")
		$list.append($node);
	});	
}); 
// bar chart
App.controller('chartWrongs', function (page,request) {
	var me = this;	

	/*
	var data = {
		labels : ["January","February","March","April","May","June","July"],
		datasets : [{
			fillColor : "rgba(220,220,220,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			data : [65,59,90,81,56,55,40]
		},{
			fillColor : "rgba(151,187,205,0.5)",
			strokeColor : "rgba(151,187,205,1)",
			data : [28,48,40,19,96,27,100]
		}]
	} */

	var barData = request
	console.log(barData)
	
	var canvas = $(page).find('#chart-area');
	var ctx = canvas.get(0).getContext("2d");
	//var ctx = document.getElementById("chart-area").getContext("2d");
	//console.log(document.getElementById("chart-area"))
	//var ctx = $("#chart-area").get(0).getContext("2d");
	$(page).on('appShow',function(){
		window.myPie = new Chart(ctx).Bar(barData);
	})		
	
	var strWeak = '',
		strLearn = ''
	console.log(barData.datasets[0].data)

	for(var i=0;i<barData.labels.length;i++){
		if(barData.datasets[0].data[i]>=2){
			strWeak += barData.labels[i] + '；'
		}
		if(barData.datasets[0].data[i]>=3){
			strLearn += barData.labels[i] + '；'
		}
	}	
	strWeak = strWeak=='' ? '无' : strWeak;
	strLearn = strLearn=='' ? '无' : strLearn; 
	$(page).find('.weak').html(strWeak)
	$(page).find('.learn').html(strLearn)
}); 

App.controller('wrongs', function (page,request) {
	var me = this;	
	
	var content = $(page).find('.app-content')
	request.forEach(function(item){
		var img = '<img width=250 src=../sjfxADM/' + item.photo + ' />',
			html = '<p style="color:green;">涉及知识点：</p>' +  item.zsd.replace(/\,/g,"；") + '</div>'
		content.append('<div class="app-section">'+img+html+'</div>')
	})	
	
	$(page).find('img').bind({
		click: function(e){
			WeixinJSBridge.invoke('imagePreview', {  
				'current' : e.target.src,  
				'urls' : [e.target.src]  
			});
		}
	})
}); 

/* var img = new Image();  
img.src = "move1.png";  
img.onload = function(){    
    ctx.drawImage(img,0,0,20,20);    
} */
