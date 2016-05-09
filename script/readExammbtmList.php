<?php
// 我的试卷：分析结果
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Origin: *'); // 跨域问题
//header('Access-Control-Allow-Headers: X-Requested-With');

require_once('db/database_connection.php');


    $mbID = addslashes($_REQUEST['mb_id']);

    $query = "SELECT * from exam_mb_tm WHERE mb_id = $mbID ";

    $result = mysql_query($query) 
        or die("Invalid query: readExammbtmList " . mysql_error());
	
	//$res->total = mysql_num_rows($result);

	$query_array = array();
	$i = 0;
	//Iterate all Select
	while($row = mysql_fetch_array($result))
	{
		array_push($query_array,$row);
		$i++;
	}
		
	echo json_encode($query_array);

?>
