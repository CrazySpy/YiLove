<?php
session_start();
//require_once($_SERVER["DOCUMENT_ROOT"] . "/class/Yiban.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");

if(!isYibanLogin())
{
	exit(json_encode(ConstructReturnJSON("error",3003,"未登录到易班")));
}

//$yiban = new Yiban($_SESSION["access_token"]);
//$rtn = $yiban->GetReturnJSON();
//if($rtn["status"] != "success")
//{
//	exit(json_encode($rtn));
//}
//$submitUser = $rtn["info"]["message"];

if(empty($_POST["context"]) || empty($_POST["itemID"]))
{
	exit(json_encode(ConstructReturnJSON("error","3001","有数据为非法空")));
}

$comment = toSQLSafeString($_POST["context"]);
$itemID = toSQLSafeString($_POST["itemID"]);

$dbc_SubmitComment = new SQL;
$rtn = $dbc_SubmitComment->SQLSelect("ItemID","publish","ItemID",$itemID);
if($dbc_SubmitComment->GetLastStatus() === "success")
{
	if($rtn["info"]["message"][0]["ItemID"] === $itemID)
	{
		$commentID = CreateUniqueID();
		$columns = Array("CommentID","ItemID","Context","SubmitUser","UserID");
		$data    = Array($commentID,$itemID,$comment,$_SESSION["realName"],$_SESSION["userID"]);
		$rtn = $dbc_SubmitComment->SQLInsert("comment",$columns,$data);
		if($dbc_SubmitComment->GetLastStatus() === "success")
		{
			$rtn = $dbc_SubmitComment->SQLUpdate("publish","CommentNum","`CommentNum`+1","ItemID",$itemID);
			if($dbc_SubmitComment->GetLastStatus() === "success")
			{
				$columns = Array("CommentID","ItemID","Context","SubmitUser","SubmitTime");
				$rtn = $dbc_SubmitComment->SQLSelect($columns,"comment","CommentID",$commentID);
			}
		}
	}
	else
	{
		$rtn = ConstructReturnJSON("error","3002","表白内容不存在或暂未发表");
	}
}

exit(json_encode($rtn));
?>
