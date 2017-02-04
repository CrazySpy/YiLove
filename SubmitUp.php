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
	$columns = Array("ItemID");
	$data	 = Array($itemID);
	return $dbc->SQLCount("publish",$columns,$data)["info"]["message"];
}

//Check whether the user has upped it
function isUpped($dbc,$itemID,$userID)
{
	$columns = Array("ItemID","UserID");
	$data	 = Array($itemID,$userID);
	return $dbc->SQLCount("Up",$columns,$data)["info"]["message"];
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
	$columns = Array("UserID","ItemID");
	$data	 = Array($_SESSION["userID"],$itemID);
	$rtn	 = $dbc_submitUp->SQLInsert("up",$columns,$data);
	if($dbc_submitUp->GetLastStatus() === "success")
	{
		$rtn = $dbc_submitUp->SQLUpdate("publish","UpNum","`UpNum`+1","ItemID",$itemID);
	}
}
exit(json_encode($rtn));
?>
