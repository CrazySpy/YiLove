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
				"`CommentsCount`" => "`CommentsCount`+1"
			);
			$where_commentPlus = Array(
				"`ItemID`" => $itemID
			);
			$rtn = $dbc_submitComment->SQLUpdate("`Publish`",$data_commentPlus,$where_commentPlus);
			if($dbc_submitComment->GetLastStatus() === "success")
			{
				//Get the comment back for rewrite
				$columns_getBack = Array(); //="*"
				$where_getBack = Array(
					"`ItemID`" => $itemID,
					"`UserID`" => $userID
				);
				$rtn = $dbc_submitComment->SQLSelect($columns_getBack,"`Comment`",$where_getBack,0,1,NULL,"ORDER BY SubmitTime DESC");
				if($dbc_submitComment->GetLastStatus() === "success")
				{
					//Hide the user information if the `isAnonymoue` == 1
					if($rtn["info"]["message"]["isAnonymous"] == 1)
					{
						$rtn["info"]["message"]["UserID"] = "";
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
