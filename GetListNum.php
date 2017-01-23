<?php
include_once("class/SQL.class.php");
$count = new SQL;
$result = $count->SQLCount("*", "publish",PDO::FETCH_NUM);
//var_dump($result);
echo($result);
?>