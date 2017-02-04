<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
$count = new SQL;
$result = $count->SQLCount("publish",1,1);
//var_dump($result);
exit(json_encode($result));
?>
