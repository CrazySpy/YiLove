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

$columns_getItems = Array();//="*"
$where_getItems = Array();//="*"

$rtn = $dbc_getItems->SQLSelect($columns_getItems,"`Publish`",$where_getItems,($page - 1) * $itemsPerPage,$itemsPerPage,null,"ORDER BY `SubmitTime` DESC",PDO::FETCH_ASSOC);
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
