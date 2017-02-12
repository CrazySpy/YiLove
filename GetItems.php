<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");

if(isset($_GET["lpp"]) && isset($_GET["page"]))
{
	$itemsPerPage = toSQLSafeString($_GET["lpp"]);
	$page		  = toSQLSafeString($_GET["page"]);
}
else
{
	exit(ConstructReturnJSON("error","3001","有数据为非法空"));
}

$dbc_getItems = new SQL;

$columns_getItems = Array(
	"Publish.ItemID",
	"Publish.UserID",
	"Publish.Context",
	"Publish.TargetName",
	"Publish.isAnonymous",
	"Publish.CommentCount",
	"Publish.UpCount",
	"Publish.SubmitTime",
	"UserInfo.UserName",
	"UserInfo.NickName",
	"UserInfo.UserHead"
);
$joinTables_getItems = Array(
	"`UserInfo`"
);
$on_getItems = Array(
	"Publish.UserID=UserInfo.UserID"
);
$where_getItems = Array();//="*"

$rtn = $dbc_getItems->SQLInnerJoinSelect($columns_getItems,"`Publish`",$joinTables_getItems,$on_getItems,$where_getItems,($page - 1) * $itemsPerPage,$itemsPerPage,"ORDER BY Publish.SubmitTime DESC");

if($rtn["status"] === "success")
{
	foreach($rtn["info"]["message"] as $element => $v)
	{
		if($v["isAnonymous"] == 1)
		{
			$rtn["info"]["message"][$element]["UserID"] = "";
			$rtn["info"]["message"][$element]["UserName"] = "";
			$rtn["info"]["message"][$element]["NickName"] = "";
			$rtn["info"]["message"][$element]["UserHead"] = "../image/anonymous.png";
		}
	}	
}
exit(json_encode($rtn));
?>
