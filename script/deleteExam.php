<?php
/* 
 * 删除
 */

	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	header('Access-Control-Allow-Origin: *'); // 跨域问题
	//header('Access-Control-Allow-Headers: X-Requested-With');

	require_once('db/database_connection.php');


    $examID = $_REQUEST['examID'];
    
    //删除试卷图片吗？
    //$query = "delete from exam where exam_id = $examID ";
    // 不真正删除，作废
	$query = "Update exam set current=0 where exam_id = $examID ";
    $result = mysql_query($query) 
        or die("Invalid query: deleteExam" . mysql_error());
    
    echo json_encode(array(
        "success" => true,
        "message" => "删除试卷exam成功"
    ));
  
?>
