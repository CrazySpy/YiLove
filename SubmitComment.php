<?php
session_start();
//require_once($_SERVER["DOCUMENT_ROOT"] . "/class/Yiban.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");

if(!isYibanLogin())
{
	exit(json_encode(ConstructReturnJSON("error",3003,"未登录到易班")));
}

if(empty($_POST["context"]) || empty($_POST["itemID"]))
{
	exit(json_encode(ConstructReturnJSON("error","3001","有数据为非法空")));
}

$comment	 = toSQLSafeString($_POST["context"]);
$itemID		 = toSQLSafeString($_POST["itemID"]);
$userID		 = $_SESSION["userID"];

if(!isset($_POST["isAnonymous"]) || $_POST["isAnonymous"] != "off")
{$isAnonymous = 1;}
else
{$isAnonymous = 0;}

//The anonymous comment function is not available
$isAnonymous = 0;

$dbc_submitComment = new SQL;

//Check whether the item is available(in table `Publish`)
$columns_isItemAvailable = Array();
$where_isItemAvailable = Array(
	"`ItemID`" => $itemID
);
$rtn = $dbc_submitComment->SQLSelect($columns_isItemAvailable ,"`Publish`",$where_isItemAvailable);

if($dbc_submitComment->GetLastStatus() === "success")
{
	if($rtn["info"]["message"][0]["ItemID"] === $itemID)
	{
		//insert comment into table `Comment`
		$submitComment = Array(
			"`ItemID`"		=> $itemID,
			"`Context`"		=> $comment,
			"`UserID`"		=> $userID,
			"`isAnonymous`" => $isAnonymous
		);
		$rtn = $dbc_submitComment->SQLInsert("`Comment`",$submitComment);
		if($dbc_submitComment->GetLastStatus() === "success")
		{
			//The comment count in `Publish` table +1
			$data_commentPlus = Array(
				"`CommentCount`" => "`CommentCount`+1"
			);
			$where_commentPlus = Array(
				"`ItemID`" => $itemID
			);
			$rtn = $dbc_submitComment->SQLUpdate("`Publish`",$data_commentPlus,$where_commentPlus);
			if($dbc_submitComment->GetLastStatus() === "success")
			{
				//Get the comment back for rewrite
				$columns_getBack = Array(
					"Comment.ItemID",
					"Comment.UserID",
					"Comment.Context",
					"Comment.isAnonymous",
					"Comment.SubmitTime",
					"UserInfo.UserName",
					"UserInfo.NickName"
				); 
				$joinTable_getBack = Array(
					"`UserInfo`"
				);
				$on_getBack = Array(
					"Comment.UserID = UserInfo.UserID"
				);
				$where_getBack = Array(
					"Comment.ItemID" => $itemID,
					"Comment.UserID" => $userID
				);
				$rtn = $dbc_submitComment->SQLInnerJoinSelect($columns_getBack,"`Comment`",$joinTable_getBack,$on_getBack,$where_getBack,0,1,"ORDER BY Comment.SubmitTime DESC");
				if($dbc_submitComment->GetLastStatus() === "success")
				{
					//Hide the user information if the `isAnonymoue` == 1
					if($rtn["info"]["message"][0]["isAnonymous"] == 1)
					{
						$rtn["info"]["message"][0]["UserID"] = "";
						$rtn["info"]["message"][0]["UserName"] = "";
						$rtn["info"]["message"][0]["NickName"] = "";
					}
				}
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
