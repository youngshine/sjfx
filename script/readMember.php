<?php
require_once 'db/response.php';
require_once 'db/request.php';
require_once('db/database_connection.php');

$req = new Request(array());
$res = new Response();
if(isset($req->params))
{
	$arr = $req->params;
    $query = "SELECT * from member where wxID = '$arr->wxID' limit 1";
    //$query = "select * from member where member_id=3";
    $result = mysql_query($query) or 
        die("Invalid query: readMember" . mysql_error());
	$row = mysql_fetch_array($result) or 
        die("Invalid query: readMember2" . mysql_error());
	//print_r($row);
	$query_array = array();
	$query_array[0] = $row;
		
	$res->success = true;
	$res->message = "读取会员信息member成功";
	$res->data = $query_array;
}else{
    $res->success = false;
    $res->message = "参数错误";
    $res->data = array();
}
echo $_GET['callback']."(".$res->to_json().")";
?>
