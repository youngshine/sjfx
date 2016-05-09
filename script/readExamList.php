<?php
	// 我的试卷
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	header('Access-Control-Allow-Origin: *'); // 跨域问题
	//header('Access-Control-Allow-Headers: X-Requested-With');

	require_once('db/database_connection.php');

	$wxID = addslashes($_REQUEST['userId']);

    $query = "SELECT *  from exam 
    	where wxID = '$wxID' and current=1   
        order by checked Desc,updated Desc, created Desc ";

    $result = mysql_query($query) 
        or die("Invalid query: readExamList " . mysql_error());
	
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
