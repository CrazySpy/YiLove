<?php
session_start();
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/class/Yiban.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");

if(!isYibanLogin())
{
	exit(json_encode(ConstructReturnJSON("error",3003,"未登录到易班")));
}

if(empty($_POST["context"]) || empty($_POST["targetName"]) || empty($_POST["isAnonymous"]))
{
	exit(json_encode(ConstructReturnJSON("error",3001,"有数据为非法空")));
}

$context            = toSQLSafeString($_POST["context"]);
$targetName         = toSQLSafeString($_POST["targetName"]);
//$isAnonymous        = toSQLSafeString($_POST["isAnonymous"]);
$userID				= $_SESSION["userID"];

$dbc_submitItem = new SQL();

$databaseName = "`Waiting`";
if($GLOBALS["needCheck"] === false)
{
	$databaseName = "`Publish`";
}
if(!isset($_POST["isAnonymous"]) || $_POST["isAnonymous"] != "off")
{$isAnonymous = 1;}
else
{$isAnonymous = 0;}

$submitItem = Array(
	"`UserID`" => $userID,
	"`Context`" => $context,
	"`TargetName`" => $targetName,
	"`isAnonymous`" => $isAnonymous,
);
$rtn = $dbc_submitItem->SQLInsert($databaseName,$submitItem);

if($dbc_submitItem->GetLastStatus() === "success")
{
	//Get the comment to rewrite
	$columns_getBack = Array(
		"Publish.ItemID",
		"Publish.UserID",
		"Publish.Context",
		"Publish.TargetName",
		"Publish.isAnonymous",
		"Publish.CommentCount",
		"Publish.UpCount",
		"Publish.SubmitTime",
		"UserInfo.UserName",
		"UserInfo.NickName"
	);
	$joinTables_getBack = Array(
		"`UserInfo`"
	);
	$on_getBack = Array(
		"Publish.UserID=UserInfo.UserID"
	);
	$where_getBack = Array(
		"Publish.UserID" => $userID
	);
	//	var_dump($itemID);
	//	$rtn = $dbc->SQLSelect($columns_GetItems,"`Publish`",$where_GetItems);
	$rtn = $dbc_submitItem->SQLInnerJoinSelect($columns_getBack,"`Publish`",$joinTables_getBack,$on_getBack,$where_getBack,0,1,"ORDER BY Publish.SubmitTime DESC");
	if($rtn["info"]["message"][0]["isAnonymous"] == 1)
	{
		$rtn["info"]["message"][0]["UserID"] = "";
		$rtn["info"]["message"][0]["UserName"] = "";
		$rtn["info"]["message"][0]["NickName"] = "";
	}
}
exit(json_encode($rtn));
?>
