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

function GetSubmitData($dbc,$itemID)
{
	$columns_GetItems = Array("`ItemID`",`UserID`,"Context","TargetName","`SubmitTime`","`SubmitUser`","`UpCount`","`CommentCount`");
	$where_GetItems = Array(
		"`ItemID`" => $itemID
	);
	//	var_dump($itemID);
	$rtn = $dbc->SQLSelect($columns_GetItems,"`publish`",$where_GetItems);
	if($rtn["info"]["message"]["isAnonymous"] == 1)
	{
		$rtn["info"]["message"]["UserID"] = ""; 
	}
	return $rtn;
}

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
	$columns_getBack = Array(); //="*"
	$where_getBack = Array(
		"`UserID`" => $userID
	);
	$rtn = $dbc_submitItem->SQLSelect($columns_getBack,"`Publish`",$where_getBack,0,1,null,"ORDER BY SubmitTime DESC");
	if($dbc_submitItem->GetLastStatus() === "success")
	{
		//Hide the user information if the `isAnonymoue` == 1
		if($rtn["info"]["message"]["isAnonymous"] == 1)
		{
			$rtn["info"]["message"]["UserID"] = "";
		}
	}

}
/*
if($isAnonymous === "on")
{
	$columns = array("ItemID","SubmitUser","UserSchool","Context","TargetName","UserID","isAnonymous");
	$data    = array($itemID,$submitUser,$userSchool,$context,$targetName,$userID,0);
	$rtn	 = $sql -> SQLInsert($databaseName,$columns,$data);
	if($sql->GetLastStatus() === "success")
	{
		$rtn = GetSubmitData($sql,$itemID);
	}
}
else
{
	$columns1 = array("ItemID","SubmitUser","UserSchool","Context","TargetName","UserID","isAnonymous");
	$data1   = array($itemID,"匿名","隐藏学校",$context,$targetName,$userID,1);
	$columns2 = array("ItemID","SubmitUser","UserSchool");
	$data2 = array($itemID,$submitUser,$userSchool);

	$rtn = $sql -> SQLInsert($databaseName,$columns1,$data1);

	if($sql->GetLastStatus() === "success")
	{
		$rtn = $sql -> SQLInsert("anonymousInfo",$columns2,$data2);
		if($sql->GetLastStatus() === "success")
		{
			$rtn = GetSubmitData($sql,$itemID);
		}
	}
}
 */
exit(json_encode($rtn));
?>
