<?php
session_start();
//require_once($_SERVER["DOCUMENT_ROOT"] . "/class/Yiban.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");

if(!isYibanLogin())
{
	exit(json_encode(ConstructReturnJSON("error",3003,"未登录到易班")));
}

if(empty($_POST["itemID"]))
{
	exit(json_encode(ConstructReturnJSON("error","3001","有数据为非法空")));
}

$itemID = toSQLSafeString($_POST["itemID"]);

$dbc_submitUp = new SQL;

//Check whether the ItemID is available
function CheckItemID($dbc,$itemID)
{
	$checkItemID = Array(
		"ItemID" => $itemID
	);
	$rtn = $dbc->SQLCount("Publish",$CheckItemID);	
	if($dbc->GetLastStatus() === "success")
	{
		return $rtn["info"]["message"];
	}
	else
	{
		exit(json_encode($rtn));
	}
}

//Check whether the user has upped it
function isUpped($dbc,$itemID,$userID)
{
	$isUpped = Array(
		"ItemID" => $itemID,
		"UserID" => $userID
	);
	$rtn	 = $dbc->SQLCount("Up",$isUpped);
	if($dbc->GetLastStatus() === "success")
	{
		return $rtn["info"]["message"];
	}
	else
	{
		exit(json_encode($rtn));
	}
}

if(!CheckItemID($dbc_submitUp,$itemID))
{
	$rtn = ConstructReturnJSON("error",3002,"表白内容不存在或暂未发表");
}

if(isUpped($dbc_submitUp,$itemID,$_SESSION["userID"]))
{
	$rtn = ConstructReturnJSON("error",3004,"你已经赞过这条表白");
}
if(!isset($rtn))
{
	$up = Array(
		"UserID" => $_SESSION["userID"],
		"ItemID" => $itemID
	);
	$rtn	 = $dbc_submitUp->SQLInsert("Up",$up);
	if($dbc_submitUp->GetLastStatus() === "success")
	{
		$data_upPlus = Array(
			"UpCount" => "`UpCount`+1"
		);
		$where_upPlus = Array(
			"ItemID" => $itemID
		);
		$rtn = $dbc_submitUp->SQLUpdate("Publish",$data_upPlus,$where_upPlus);
	}
}
exit(json_encode($rtn));
?>
