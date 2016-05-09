App.controller('home', function (page,request) {
	//var myOpenID = location.search.substr(1);
	//alert('id:  '+myUserId)
	// 多极选择返回学校机器所在省市县
	$(page).on('appShow', function () {
		console.log('the user can see it!');
		//if(request.school){ //有选择到学校
		if(request.district){ //有选择到学校
			//editSchool.text(request.school);
			//editSchool.text(request.city+request.district+'•'+request.school);
			$county.text(request.province+request.city+request.district)
			/*
			var obj = {
				'fieldName': 'school',
				'fieldValue': request.school,
				'wxID': myUserId
			} 
			updateData(obj) */
			// 省市县信息
			var obj1 = {
				'fieldName': 'province',
				'fieldValue': request.province,
				'wxID': myUserId
			} 
			updateData(obj1)
			var obj2 = {
				'fieldName': 'city',
				'fieldValue': request.city,
				'wxID': myUserId
			} 
			updateData(obj2)
			var obj3 = {
				'fieldName': 'district',
				'fieldValue': request.district,
				'wxID': myUserId
			} 
			updateData(obj3)
		}
	});
	
	var phone,membername,school,grade,province,city,district;
	
	var editPhone = $(page).find('.phone'),
		editName = $(page).find('.name'),
		$county = $(page).find('.county'),
		editGrade = $(page).find('.grade')
		//editDistrict = $(page).find('.district')
	
	var params = {
		"wxID": myUserId
	}
	readData(params);
	
	function readData(obj){
		showPrompt('加载中...');	
		$.ajax({
			url: dataUrl + 'readMember.php?data=' + JSON.stringify(obj),
			dataType: "jsonp",
			jsonp: 'callback',
			success: function(result){
				hidePrompt()
				console.log(result.data[0])
				populateData(result.data[0])
				hidePrompt()
			},
			error: function(xhr, type){
				console.log('fail')
				hidePrompt()
			}
		});
	}	
	function populateData(item){
		if(item.checked == 1){
			editPhone.text(item.phone + ' 已验证');
			//editPhone.attr('disabled','disabled')
			//editPhone.attr('readOnly','true')
		}else{
			editPhone.text(item.phone);
		}
		
		
		editName.text(item.member_name);
		editGrade.text(item.grade);
		//editSchool.text(item.city+item.district+'•'+item.school);
		$county.text(item.province+item.city+item.district)
		phone = item.phone;
		membername = item.member_name
		grade = item.grade
		school = item.school; //通过学校，获取所在省、市、县
	}	

	//保存
	function updateData(obj){		
		$.ajax({
		    url: dataUrl + 'updateMember.php?data=' + JSON.stringify(obj),
		    //data: { name: 'Super Volcano Lair' },
			dataType: "jsonp",
			jsonp: 'callback',
		    success: function(result){
			  //btnSubmit.text('保存成功')	
		  	  //WeixinJSBridge.invoke('closeWindow',{},function(res){});
			  //App.load('home');
		    },
		});		
	}

	// 手机绑定验证
	editPhone.on('click', function () {
		if($(this).text().indexOf('已验证')>0)
			return false
			
		//App.load('input-phone-confirm',{value: phone, title: '绑定手机'})
		App.pick('input-phone-confirm', {value: phone, title: '手机验证'}, function (data) {
			if(data){ 
				phone = data.value
				console.log(data)
				editPhone.text(phone + ' 已验证');
				//editPhone.attr('disabled','disabled'); // 不能修改
				//editPhone.attr('readOnly',true)
				
				var obj = {
					'fieldName': 'phone',
					'fieldValue': data.value,
					'wxID': myUserId
				}
				updateData(obj)
				var obj2 = {
					'fieldName': 'checked',
					'fieldValue': 1,
					'wxID': myUserId
				}
				updateData(obj2); //绑定
				
				// 绑定成功，接着选择省市县学校
				//editSchool.click()
				App.load('select-province');
			}
		});
	})
	
	editName.on('click', function () {
		App.pick('input-text', {value: membername, title: '姓名'}, function (data) {
			if(data){ 
				membername = data.value
				editName.text(membername);
				var obj = {
					'fieldName': 'member_name',
					'fieldValue': data.value,
					'wxID': myUserId
				}
				updateData(obj)
				console.log(obj)
			}
		});
	})
	editGrade.on('click', function () {
		App.pick('select-options', {value: '', title: '年级'}, function (data) {
			if(data){ 
				grade = data.value
				editGrade.text(grade);
				var obj = {
					'fieldName': 'grade',
					'fieldValue': data.value,
					'wxID': myUserId
				}
				updateData(obj)
			}
		});
	})
	
	$county.on('click', function () {
		App.load('select-province');
	}) 
});



/**
类目列表－－公用类，对应app.pick
*/
App.controller('select-province', function (page,request) {
	var $list = $(page).find('.app-list'),
		$listItem = $(page).find('.app-list li').remove()
		 // 省份关闭，其它地方有调用，不采用返回data-back
	
		$(page).find('.cancel').on("click",function(){
			App.back('home')
		})
		
		$.ajax({ //底层方法； 
			// 阿里云api，重启后必须手工启动
			//url: 'http://121.40.52.125:8443/k12-schools',
			url: 'script/xml-county.php',
			//dataType: 'json',
			success: function (data, status) { 
				console.log(data)
				var objData = JSON.parse(data);
				//console.log(objData.province)
				console.log(objData)
				populateData(objData)
				handleData($list,objData)
			}, 
			error: function(obj,info,errObj){ 
				alert("数据api错误：" + info); 
			} 
		}); 
	
	// 列表
	function populateData(items){
		items.forEach(function (item) {
			var $node = $listItem.clone(true);
			$node.text(item)
			$list.append($node);
		});	
	}
	function handleData(list,items){
		$list.find('li').on({
			click: function (e) {
				var province = $(this).text()		
				App.load('select-city', {province:province});
			},
		})
	}
});

App.controller('select-city', function (page,request) {
	var $list = $(page).find('.app-list'),
		$listItem = $(page).find('.app-list li').remove()
	
		console.log(request.province)
		// 标题也是传入
		$(page).find('.app-title').html(request.province)
	
		$.ajax({ //底层方法； 
			//url: 'http://121.40.52.125:8443/k12-schools?province=' + request.province,
			url: 'script/xml-county.php?state=' + request.province,
			success: function (data, status) { 
				//console.log(data)
				var objData = JSON.parse(data);
				//console.log(objData.cities)
				console.log(objData)
				populateData(objData)
				handleData($list,objData)
			}, 
			error: function(obj,info,errObj){ 
				alert("$.ajax()中发生错误：" + info); 
			} 
		}); 
	
	// 列表
	function populateData(items){
		items.forEach(function (item) {
			var $node = $listItem.clone(true);
			$node.text(item)
			$list.append($node);
		});
	}
	function handleData(list,items){
		$list.find('li').on({
			click: function (e) {
				var city = $(this).text()
				App.load('select-district', {province:request.province,city:city});
			},
		})
	}
});

App.controller('select-district', function (page,request) {
	var $list = $(page).find('.app-list'),
		$listItem = $(page).find('.app-list li').remove()
	
		console.log(request.city)
		// 标题也是传入
		$(page).find('.app-title').html(request.province+request.city)
	
		$.ajax({ //底层方法； 
			url: 'script/xml-county.php?state=' + request.province + '&city=' + request.city,
			success: function (data, status) { 
				//console.log(data)
				var objData = JSON.parse(data);
				console.log(objData.district)
				console.log(objData)
				populateData(objData)
				handleData($list,objData)
			}
		}); 
	
	// 列表
	function populateData(items){
		items.forEach(function (item) {
			var $node = $listItem.clone(true);
			$node.text(item)
			$list.append($node);
		});
	}
	function handleData(list,items){
		$list.find('li').on({
			click: function (e) {
				var district = $(this).text()		
				//App.load('select-school', {province:request.province,city:request.city,district:district});
				var obj = {
					//'school': $(this).text(),
					'district': district,
					'city': request.city,
					'province': request.province
				}
				App.load('home',obj)
			},
		})
	}
});

App.controller('select-school', function (page,request) {
	var me = this,
		$list = $(page).find('.app-list'),
		$listItem = $(page).find('.app-list li').remove()
	
	var tabPrimary = $(page).find('.primary'),
		tabSecondary = $(page).find('.secondary'),
		records = []
	

		// 标题也是传入
		$(page).find('.app-title').html(request.province+request.city+request.district)
	
		$.ajax({ //底层方法； 
			url: 'http://121.40.52.125:8443/k12-schools?province=' + request.province + 
				'&city=' + request.city + '&district=' + request.district,
			success: function (data, status) { 
				//console.log(data)
				var objData = JSON.parse(data);
				console.log(objData.schools)
				populateData(objData.schools.primary); //初始默认小学
				handleData($list,objData.schools);
				records = objData.schools;
			}
		}); 
	
	// 列表
	function populateData(items){
		items.forEach(function (item) {
			var $node = $listItem.clone(true);
			$node.text(item)
			$list.append($node);
		});
	}
	function handleData(list,items){
		$list.find('li').on({
			click: function (e) {
				//show: pick or return show to refresh
				//$(this).text()
				var obj = {
					'school': $(this).text(),
					'district': request.district,
					'city': request.city,
					'province': request.province
				}
				//App.load('member',{'school':$(this).text()})
				App.load('home',obj)
			},
		})

	}
	
	tabPrimary.on('click',function(e){
		$(this).css('background','#fff');
		tabSecondary.css('background','#eee')
		$list.empty()
		populateData(records.primary)	
		handleData($list,records.primary);
	})
	tabSecondary.on('click',function(e){
		$(this).css('background','#fff');
		tabPrimary.css('background','#eee')
		$list.empty()
		populateData(records.middle)	
		handleData($list,records.middle);
	})
});

App.controller('input-phone-confirm', function (page,request) {
	var me = this;
	console.log(request)
	var btnOk = $(page).find('.ok'),
		btnGetCode = $(page).find('.getCode'),
		editPhone = $(page).find('.app-input[name=phone]'),
		editCode = $(page).find('.app-input[name=code]'),
		editRandomCode = $(page).find('.app-input[name=randomCode]'),
		phone 
	
	editPhone.val(request.value) // 手机号
	
	btnGetCode.on('click',function(e){
		if (btnGetCode.text() != '获取验证码')
			return
			
		$(page).find('.app-input').blur(); // 关闭软键盘
		
		if( editPhone.val().trim().length != 11 ||isNaN(editPhone.val()) ){
			//alert('输入11位手机号码')
			showPrompt('手机号不正确')
			setTimeout(function () { hidePrompt() }, 1500);
			btnOk.css('display','none')
			return;	
		}
	 	/*
	 	   存在的话，这里生成随机码4位 Math.floor(Math.random() * (max - min + 1)) + min;
	 	    服务端发送随机码到手机短信或邮箱。
	 	   用户收到随机码，输入的和客户端生成的一致，则点下一步到密码重置页面		*/
	 	var randomCode = Math.floor(Math.random() * (9999 - 1000 + 1)) + 1000;
		
			console.log('randomcode: '+randomCode)
		 	editRandomCode.val(randomCode);

			phone = editPhone.val().trim(); // 获得手机号码，避免修改
			
			btnOk.css('display','block')
		
		// 发送验证码到 手机号
		var obj = {
			phone: phone,
			code: randomCode
		}	
		$.ajax({  
			url: 'script/SUBMAIL/demo/message_xsend.php',
			data: obj,
			success: function (data, status) { 
				
			}
		});
		
		editPhone.attr('disabled','disabled')
		//editPhone.attr('readOnly',true)
		
		//验证码发送成功30秒内不能再次发送
		var sec = 60;
		function count(){
			sec = sec - 1;
			if(sec>0){
				btnGetCode.text(sec + '秒后重获验证');
				btnGetCode.css('background','gray')
				s = setTimeout(function(){count();},1000);
			}else{
				btnGetCode.text('获取验证码');
				btnGetCode.css('background','#32CD32')
				editPhone.removeAttr("disabled")
			}
		}
		count();
		
	})

	btnOk.on('click', function (e){
		$(page).find('.app-input').blur(); // 关闭软键盘
		
		if( editRandomCode.val() != editCode.val() ){
			//alert('验证码错误')
			showPrompt('验证码错误')
			setTimeout(function () { hidePrompt() }, 1500);
			return;	
		}
		
		showPrompt('手机验证成功')
		setTimeout(function () { hidePrompt() }, 3000);
		
		var obj = {
			"value": phone //input.val().trim()
		}
		me.reply(obj); // app.pick
	})
});	

