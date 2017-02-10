<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");

if(isset($_GET["itemID"]))
{
	$itemID = toSQLSafeString($_GET["itemID"]);
}
else
{
	exit(json_encode(ConstructReturnJSON("error","3001","有数据为非法空")));
}
$dbc_getComments = new SQL;

$columns_getComments = Array(
	"Comment.ItemID",
	"Comment.UserID",
	"Comment.Context",
	"Comment.isAnonymous",
	"Comment.SubmitTime",
	"UserInfo.UserName",
	"UserInfo.NickName"
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
$rtn = $dbc_getComments->SQLInnerJoinSelect($columns_getComments,"`Comment`",$joinTables_getComments,$on_getComments,$where_getComments,1,-1,"ORDER BY Comment.SubmitTime DESC");
if($rtn["status"] === "success")
{
	foreach($rtn["info"]["message"] as $element => $v)
	{
		if($v["isAnonymous"] == 1)
		{
			$rtn["info"]["message"][$element]["UserID"] = "";
			$rtn["info"]["message"][$element]["UserName"] = "";
			$rtn["info"]["message"][$element]["NickName"] = "";
		}
	}}
		exit(json_encode($rtn));
?>
