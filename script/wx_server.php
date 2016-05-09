<?php
/*
    试卷分析公益平台，根号教育2015.11 微信服务器
    Copyright 2014 All Rights Reserved
*/

require_once('db/database_connection.php'); 

define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();

if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "event":
                	$result = $this->receiveEvent($postObj);
                    break;
                case "text":
                	$result = $this->receiveText($postObj);
                    break;
                case "voice":
                	//$result = $this->receiveVoice($postObj);
                	$result = $this->receiveText($postObj);
                    break;
            }
            $this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                // 关注者绑定到后台数据库，如果取消又重新关注？openID一样
            	$query = "INSERT INTO member(wxID) VALUES('$object->FromUserName')";
            	$result = mysql_query($query);
            
                // 取消关注后，重新关注的
                $query = "UPDATE member set current=1 WHERE wxID = '$object->FromUserName'";
            	$result = mysql_query($query);
            
            	$content =  
                 "提分可以更容易！\n" .   
				 "1：考试没满分，为什么？\n" . 
                 "2：学习没效率，为什么？\n" .
				 "3：上课听不懂，为什么？\n" . 
                 "4：作业不会做，为什么？\n" .
				 "试卷分析，一针见血！\n" . 
                 "两步告诉您：用户注册——上传试卷\n" .
				 "点我做公益，我任性！";
			  
                $result = $this->transmitText($object, $content);
                return $result;
                break;
            case "unsubscribe":
                //$query = "DELETE FROM member WHERE wxID = '$object->FromUserName'";
            	$query = "UPDATE member set current=0 WHERE wxID = '$object->FromUserName'";
            	$result = mysql_query($query);
            
            	$content = "Bye 欢迎再来（已经从数据库Member表禁用current=0，不删除，）";
            	$result = $this->transmitText($object, $content);
        		return $result;
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "help":
                        $content = "帮助：\n\n" . 
                            "1、上传试卷 \n" .
                            "点击＋加号图标，单选或多选试卷图片或拍照，点击提交上传。\n\n" .
                            "2、分析报告 \n" .
                            "试卷分析报告状态：分析中和查看结果。\n\n" .
                            "3、我 \n" .
                            "请填写手机号码 \n" .
                            "选择所在县区" ;
                         
                    	$result = $this->transmitText($object, $content);
        				return $result;
                        break;
      
                }
                break;

            default:
                break; 
        }

    }

    private function receiveText($object)
    {
        /*
        $content = array();
        $a = array(
            "Title"=>"根号教育",  
            "Description"=>"", 
            "PicUrl"=>"http://ysmall.sinaapp.com/assets/title.jpg", 
            //"Url" =>"http://www.yiqizo.com/weixin/whlj/index.html?$object->FromUserName" //带入当前用户
            //"Url" =>"http://www.yiqizo.com/weixin/whlj/index.html?1"
            "Url" =>"index.html?$object->FromUserName"
        ); 
        $c = array(
            "Title"=>"会员中心",  
            "Description"=>"", 
            "PicUrl"=>"http://ysmall.sinaapp.com/assets/contact.jpg", 
            "Url" =>"member.html?$object->FromUserName"
        );

        $content = array($a,$b,$c);

        $result = $this->transmitNews($object, $content);
		*/
        $content = "请选择下方导航菜单进行相应操作。\n免费服务电话：\n400-6680-118";
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[transfer_customer_service]]></MsgType>
            </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>
            ";
        
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <Content><![CDATA[]]></Content>
            <ArticleCount>%s</ArticleCount>
            <Articles>
            $item_str</Articles>
            </xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    private function logger($log_content)
    {
      
    }
}
?>
