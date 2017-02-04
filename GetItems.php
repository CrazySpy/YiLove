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
$columns = Array("ItemID","SubmitTime","SubmitUser","TargetName","Context","UpNum","CommentNum");
$rtn = $dbc_getItems->SQLSelect($columns, "publish",1,1,($page - 1) * $itemsPerPage,$itemsPerPage,"ORDER BY `SubmitTime` DESC",PDO::FETCH_ASSOC);
exit(json_encode($rtn));
?>
