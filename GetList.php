<?php
include_once("class/SQL.class.php");
$itemsPerPage = $_GET["lpp"];
$page = $_GET["page"];
$sql = new SQL();
$list = $sql->SQLSelect("`id`,`SubmitTime`,`SubmitUser`,`TargetName`,`Context`", "publish",1,1,($page - 1) * $itemsPerPage,$itemsPerPage,"ORDER BY SubmitTime DESC",PDO::FETCH_ASSOC);
echo($list);
?>



