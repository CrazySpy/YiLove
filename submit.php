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
$isAnonymous        = toSQLSafeString($_POST["isAnonymous"]);
$Yiban_access_token = $_SESSION["access_token"];

//$yiban = new Yiban($Yiban_access_token);
//$rtn = $yiban->GetReturnJSON();
//if($rtn["status"] !== "success")
//{
//	exit(json_encode($rtn));
//}


$sql = new SQL();

function GetSubmitData($sql,$itemID)
{
	$select_columns = Array("ItemID","SubmitTime","SubmitUser","TargetName","Context","UpNum","CommentNum");
	//	var_dump($itemID);
	return $sql->SQLSelect($select_columns,"publish","ItemID",$itemID);
}


$databaseName = "waiting";
if($GLOBALS["needCheck"] == false)
{
	$databaseName = "publish";
}

//$submitUser = $yiban->GetName();
//$userSchool = $yiban->GetSchool();
//$userID     = $yiban->GetUserID();
$submitUser = $_SESSION["realName"];
$userSchool = $_SESSION["userSchool"];
$userID		= $_SESSION["userID"];


$itemID = CreateUniqueID();

if($isAnonymous != "on")
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
exit(json_encode($rtn));
?>
