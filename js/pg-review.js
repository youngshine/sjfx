App.controller('home', function (page) {	
	// 如果图片仍在微信服务器 photos=''，下载到自己服务器 syncDownload()
	
	var $list = $(page).find('.list'),
		$listItem = $(page).find('.list .listItem').remove()
		//search = $(page).find('input[type=search]');	
	/*	
	search.on('click', function () {
		selectOptions(records)
	});	*/
	// 当前员工,oAuth2带入用户参数 html?code
	//var records = []; // 全局表量，列表

	var params = {
		"userId": myUserId
	}
	readData(function(data){
		//records = data;
		populateData(data)	
		handleData( $list, data )
	}, params );

	
	function readData(callback, obj){
		showPrompt('加载中...');		
		$.ajax({
	    	//url: dataUrl + 'readExamList.php?data=' + JSON.stringify(obj),
			url: dataUrl + 'readExamList.php',
			dataType: "json", //jsonp: 'callback',
			data: obj,
			success: function(result){
				hidePrompt()
				console.log(result)
			    //populateData(result.data)
				callback(result)
			},
			error: function(xhr, type){
				showPrompt('出错');	
			}
		});
	}

	function populateData(items){
		if($list.children().length != 0){
			$list.empty(); //清除旧的列表项 if any
		}
		
		var checked = ''; // 分析状态
		items.forEach(function (item) {
			if(item.checked != checked){
				checked = item.checked;
				var checkedStatus = item.checked==0?'待分析':'已分析'
				$list.append('<div style="padding:5px 15px;background:#d8e4e5;color:#888;">' + 
					checkedStatus + '</div>');
			}
			var $node = $listItem.clone(true);
			$node.find('.examId').text(item.exam_id); // hidden
			$node.find('.njkm').text(item.nj+item.km);
			$node.find('.time').text(item.created.substr(2,8));
	
			if( item.checked == 1 ) { // 已分析的
				//$node.css('background','#fff9f9')
				$node.find('.removeItem').css('display','none')
				$node.find('.showItem').css('display','block')
			}else{
				$node.find('.removeItem').show()
				$node.find('.showItem').hide()
			}
			
			var $photos = $node.find('.photos')
			// 图片列表：尚未下载到自己服务器的 mediaIds
			var photos = item.photos.split(',');
			/*
			if(photos==''){
				photos = item.mediaIds.split(','); 
			} */
			$.each(photos, function(i,photo){      
				console.log(photo)
				var $img = '<img src='+photo+' style="width:50px;height:50px;padding:2px;" />'
				$photos.append($img)
			});	
		
			$list.append($node);
		});
	}	
	function handleData(list, items){
		list.find('.listItem').find('.showItem').bind({
			click: function(e){
				console.log($(this).siblings(".examId").text())
				toShowDetail( $(this).siblings(".examId").text(), items )
			}
		})
		list.find('.listItem').find('.removeItem').bind({
			click: function(e){
				//console.log($(this).siblings(".examId").text())
				//toDelete( $(this).siblings(".examId").text(), items )
				var selected = $(this).parent().parent()
				console.log(selected.find('.examId').text())
				App.dialog({
					title	     : '删除试卷？', //'删除当前公告？',
					okButton     : '确定',
					cancelButton : '取消'
				}, function (choice) {
					if(choice){
						deleteData( selected.find('.examId').text(), selected )
					}
				});
			}
		})
			
		list.find('.listItem').find('img').bind({
			click: function(e){
				//console.log(e.target)
				var imgs = $(this).parent().children();
				var urls = [];
				imgs.forEach(function (img) {
					urls.push(img.src)
				})
				console.log(urls);
				WeixinJSBridge.invoke('imagePreview', {  
					'current' : e.target.src,  
					'urls' : urls  
				});
			}
		})
	}

	function toShowDetail(id,items){
		// 利用下标查找数组元素
		var selected = []
		for(var i in items){
			if(items[i].exam_id == id ){
				selected = items[i] 
				break; 
			}
		}  
		console.log(selected)
		/*
		var item = items.filter(function(ele,pos){
		    return ele.proj_id == id ;
		}); */
		App.load('more', selected);
	}
	
	// 删除
	function deleteData(ID, selected){
		showPrompt('正在删除...'); console.log(ID)
		$.ajax({
			url: dataUrl + 'deleteExam.php',
			data: {"examID":ID},
			dataType: "json", // 返回的也是 json
			success: function(result){
				if(result.success){
					hidePrompt();
					//显示服务端出错信息,有跟贴子表记录，不能删除
					selected.remove()
				}else{
					showPrompt(result.message)
					setTimeout(function() { hidePrompt() }, 3000);
				}						
			},
		});
	}	
	/*
	function wxPreviewImg(current,photos){
		var url = location.href;
		url =  url.substring(0,url.lastIndexOf("/")+1); //.replace("//","/"); 
		console.log(url); //当前
		var pic_list = []
		$.each(photos, function(i,photo){      
			pic_list.push(url + photo)
		});
		console.log(pic_list)
		WeixinJSBridge.invoke('imagePreview', {  
			'current' : current,  
			'urls' : pic_list  
		});
	} */
	
	/* 图片完全加载...
	var img = new Image();  
	img.src = "move1.png";  
	img.onload = function(){    
	    ctx.drawImage(img,0,0,20,20);    
	} */
}); //试卷分析




