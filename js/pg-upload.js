// 上传多图，暂时不下载???
App.controller('home', function (page) {
	var nj='',km='',
		localPhotos = [], // 选择或拍照的图片，未上传
		remotePhotos = [], // 上传服务器多图
		fileNames = []; //数据库图片文件名

	var $njkm = $(page).find('.njkm'),
		btnSubmit = $(page).find('.submit'),
		$list = $(page).find('.photos-wrapper'),
		$listItem = $(page).find('.photos-wrapper .imgContainer').remove()

	$njkm.parent().on('click',function(e){
		App.pick('select-njkm', {value:''}, function (data) {
			if(data){ 
				nj = data.nj; km = data.km;
				$njkm.text(nj +'•' + km);
			}
		});	
	})	
	
	// 添加，预览或删除，$转换为jquery对象方便操作
	$list.on('click',function(e){
		e.stopPropagation(); 
		console.log(e.target.className)
		var el = e.target.className
		
		if (el == 'imgPlus'){
			addImg()
		}else if(el == 'imgMinus'){
			// 删除图片，数组元素src
			var src = $(e.target).siblings()[0].src
			//alert(localPhotos.length)
			//e.target.parentNode.remove();
			$(e.target).parent().remove()
			
			//本地photos数组删除元素，通过src比对
			for(var i in localPhotos){	
				if(localPhotos[i] == src){	
					//reutrn i;//i就是下标
					//alert(localPhotos[i])
					localPhotos.splice(i, 1); 
					console.log(localPhotos)
					break; 
				}
			} 
		}else if(el == 'imgUpload'){ // class=imgUpload to preview
			wx.previewImage({
				current: e.target.src, 
				urls: [e.target.src]
			});
		}
	})
	function addImg(){			
		console.log('add multi img')
		/*
		var $node = $listItem.clone(true);
		$node.find('.imgUpload').attr('src','assets/img/nopic.jpg')
		$list.prepend($node);
		
		localPhotos.push('assets/img/nopic.jpg')
		console.log(localPhotos)
		return  */
		
		wx.chooseImage({
            count: 0, // 默认9
            sizeType: ['original', 'compressed'], 
            sourceType: ['album', 'camera'],
            success: function (res) {
                var localIds = res.localIds; 
				// 多选
				for (var i=0; i<localIds.length; i++)
				{
					var $node = $listItem.clone(true);
					$node.find('.imgUpload').attr('src',localIds[i])
					$node.find('.imgUpload').attr('id',localIds[i]); // localId
					$list.prepend($node); //append
					// 本地
					//localPhotos.push(localIds[i]) //mediaId
				}
            }
        });
	}
		
	// 提交保存按钮
	btnSubmit.on('click',function(e){		
		if(nj=='' || km==''){
			showPrompt('请选择年级科目')
			setTimeout(function () { hidePrompt() }, 1500);
			return;		
		}
		
		var imgs = $list.find('.imgUpload');
		if(imgs.length == 0){
		//if(localPhotos.length == 0 ){
			showPrompt('请添加试卷照片')
			setTimeout(function () { hidePrompt() }, 1500);
			return;		
		}
		localPhotos = []
		imgs.each(function(index){
			//localPhotos.push(imgs[index].src) //mediaId
			localPhotos.push(imgs[index].id)
		})
		
		App.dialog({
			title	     : '上传相片？', //'删除当前公告？',
			okButton     : '确定',
			cancelButton : '取消'
		}, function (choice) {
			if(choice){
				showPrompt('正在上传...');
				syncUpload(localPhotos); // 递归调用，不是循环
			}
		});
    });	
	// 1. 上传多图到微信服务器
	var syncUpload = function(localIds){
		var localId = localIds.pop(); //递归，逐步减少
		wx.uploadImage({
			localId: localId,
			isShowProgressTips: 0,
			success: function (res) {
				var serverId = res.serverId; // 返回图片的服务器端ID
				remotePhotos.push(res.serverId);
				//其他对serverId做处理的代码
				if(localIds.length > 0){
					syncUpload(localIds); //递归
				}else{
					syncDownload(remotePhotos)				
					/*
					// 3、保存数据库记录
					var obj = {
						"userId": myUserId,
						"photos": '', //fileNames.join(',') // 数组转字符串
						"mediaIds": remotePhotos.join(',') // 微信服务器保存3天的图片
					}
					createData(obj) */
				}
			}
		});
	};
	// 2. 下载刚才上传的到自己服务器，长久保存
	var syncDownload = function(serverIds){
		var serverId = serverIds.pop(); //递归，逐步减少
		var fileName = 'assets/img/exam/' + 
			new Date().getTime() + '_' + Math.floor(Math.random()*100) + '.jpg';
		fileNames.push(fileName); // photo多图的文件名
		var obj = {
			"mediaId": serverId,
			"fileName": fileName
		}
		$.ajax({
			url: 'script/weixinJS/wx_imgDown.php',
			data: obj, //必须符合json标准，才能执行success
			dataType: "json",//jsonp: 'callback',
			success: function(result){
				if(serverIds.length > 0){
					syncDownload(serverIds); //递归recursive
				}else{
					// 3、保存数据库记录
					var obj = {
						"userId": myUserId,
						"photos": fileNames.join(','), // 数组转字符串
						//"mediaIds": '' // remotePhotos.join(',') // 微信服务器保存3天的图片
						"nj": nj,
						"km": km
					}
					createData(obj)
				}
			},
		});
	};
	
	function createData(obj){		   
		//showPrompt('正在保存');
		$.ajax({
			url: dataUrl + 'createExam.php',
			dataType: "json",
			data: obj, //jsonp: 'callback',
			success: function(result){
				hidePrompt();
				
				App.dialog({
					title     : '上传试卷成功' ,
					text  : '分析报告争取在24小时内给出，请注意查看公众号模版消息提醒。'  ,
					okButton     : '关闭' ,
				}, function (choice) {
					if(choice){
						wx.closeWindow();
					}
				});
				
				/*
				//App.load('home'); 
				// 微信通知相关的收件人
				var userId = '@all', // all
					type = "上传试卷",
					msg = title,
					link = 'http://www.yiqizo.com/weixin/sjfx/exam-notify-zepto.php?id=' + 
						result.data.exam_id // 刚新增的公文id
				//wxNotify(userId,type,msg,link)  */
			},
			error: function(result){
				showPrompt('出错');
			}
		});
	} 

}); // exam upload ends


// 选择年级、科目
App.controller('select-njkm', function (page,request) {
	var me = this;
	
	// 多选中
	var btnOk = $(page).find('.ok'),
		nj = '', km = ''
	btnOk.bind('click', function () {		
		var objRet = {
			nj: nj,
			km: km
		}
		me.reply(objRet);
	})	

	$(page).find('.nj').find('li').bind('click', function (e){
		$('.nj li').css('background','#fff'); 
		$(this).css('background','#E0FFFF'); 
		//$('.nj li', this).hide();//css('color','blue'); 
		nj = $(this).text()
		console.log(nj)
		if(km != '') btnOk.show()
	})
	$(page).find('.km').find('li').bind('click', function (e){
		$('.km li').css('background','#fff'); 
		$(this).css('background','#E0FFFF'); 
		km = $(this).text()
		if(nj != '') btnOk.show()
	})
});
