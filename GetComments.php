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
$dbc_GetComments = new SQL;
$columns = Array("CommentID","ItemID","Context","SubmitUser","SubmitTime");
exit(json_encode($dbc_GetComments->SQLSelect($columns,"comment","ItemID",$itemID,1,-1,"ORDER BY `SubmitTime` DESC",PDO::FETCH_ASSOC)));
?>
