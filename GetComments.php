<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");


if(isset($_GET["itemID"]) && isset($_GET["page"]))
{
	$itemID = toSQLSafeString($_GET["itemID"]);
	$page	= toSQLSafeString($_GET["page"]);
}
else
{
	exit(json_encode(ConstructReturnJSON("error","3001","有数据为非法空")));
}
$dbc_getComments = new SQL;

$columns_getComments = Array(
	"Comment.ItemID",
	"Comment.UserID",
	"Comment.CommentID",
	"Comment.Context",
	"Comment.isAnonymous",
	"Comment.SubmitTime",
	"UserInfo.UserName",
	"UserInfo.NickName",
	"UserInfo.UserHead"
);
$joinTables_getComments = Array(
	"`UserInfo`"
);
$on_getComments = Array(
	"Comment.UserID = UserInfo.UserID"
);

$where_getComments	 = Array(
	"Comment.ItemID" => $itemID
);
$rtn = $dbc_getComments->SQLInnerJoinSelect($columns_getComments,"`Comment`",$joinTables_getComments,$on_getComments,$where_getComments,($page - 1) * $commentsPerPage,$commentsPerPage,"ORDER BY Comment.SubmitTime DESC");
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
	}}
		exit(json_encode($rtn));
?>
