<?php

	/*  上传图片后，插入数据库，图片先不下载？？加快用户端速度  */

	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	header('Access-Control-Allow-Origin: *'); // 跨域问题
	//header('Access-Control-Allow-Headers: X-Requested-With');

	require_once('db/database_connection.php');
	
	$km = $_REQUEST['km'];
    $nj = $_REQUEST['nj'];
	$photos = addslashes($_REQUEST['photos']); //下载到服务器的文件名，永久
    //$mediaIds = addslashes($_REQUEST['mediaIds']); //微信服务器图片，保存3天
	$wxID = addslashes($_REQUEST['userId']);

    $query = "INSERT INTO exam(km,nj,photos,wxId) 
    	VALUES('$km','$nj','$photos','$wxID')";

    $result = mysql_query($query) 
        or die("Invalid query: createExam" . mysql_error());

        
    // 返回最新添加数据记录的id order by 时间字段 desc limit 1
	/*
    $query = "SELECT LAST_INSERT_ID() limit 1"; 
    $result = mysql_query($query) 
        or die("Invalid query: createExam select last_id" . mysql_error());
    $row = mysql_fetch_row($result); 
    
    $res->data = array("exam_id" => $row[0]);  
    $res->success = true;
    $res->message = "上传试卷sjfx成功";
    */
	
    $ID = mysql_insert_id(); // 最新插入记录id

	echo json_encode(array(
		"success" => true,
		"message" => "上传试卷sjfx成功",
		"ID"	  => $ID
	));
//	}

?>
