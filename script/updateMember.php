<?php
/*log
*14-6-20 会员资料补充
endlog */
require_once 'db/response.php';
require_once 'db/request.php';
require_once('db/database_connection.php');



$req = new Request(array());
$res = new Response();

if(isset($req->params))
{
    $arr = $req->params;
	//print_r($arr);
    
    $wxID = $arr->wxID;
    $fieldName = $arr->fieldName;
    $fieldValue = addslashes($arr->fieldValue);
    


	$query = "update member set $fieldName  = '$fieldValue' 
		where wxID = '$wxID' "; //openID = '$dataobj->openID'";
	//echo $query;

	$result = mysql_query($query) 
        or die("Invalid query: updateMember" . mysql_error());
	$res->success = true;
	$res->message = "修改会员信息member成功";
	$res->data = array();
}
else
{
    $res->success = false;
    $res->message = "参数错误";
	$res->data = array();
}

echo $_GET['callback']."(".$res->to_json().")";
?>
