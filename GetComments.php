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

$columns_getComments = Array(); //="*"
$where_getComments	 = Array(); //="*"
$rtn = $dbc_getComments->SQLSelect($columns_getBack,"`Comment`",$where_getBack,1,-1,NULL,"ORDER BY SubmitTime DESC");
if($rtn["status"] === "success")
{
	foreach($rtn["info"]["message"] as $element)
	{
		if($element["isAnonymous"] == 1)
		{
			$element["UserID"] = "";
		}
	}
}
exit(json_encode($rtn));
?>
