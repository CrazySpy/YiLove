<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
$dbc_getItemCount = new SQL;

$where_getItemCount = Array();//="*"
$rtn = $dbc_getItemCount->SQLCount("`Publish`",$where_getItemCount);
//var_dump($result);
exit(json_encode($rtn));
?>
