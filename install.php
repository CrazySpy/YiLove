<?php
include_once("config.php");
$dbc = mysqli_connect($db_ipAddr, $db_username, $db_password, $db_name, $db_port) or exit("数据库配置有误，请查证");

$create_AnonymousInfo = "CREATE TABLE `anonymousInfo` (`id` VARCHAR(24) NOT NULL,`SubmitUser` VARCHAR(30) NOT NULL,`UserSchool` VARCHAR(30) NOT NULL);";
$create_Publish        = "CREATE TABLE `publish` (`id` VARCHAR(24) NOT NULL,`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,`SubmitUser` VARCHAR(30) NOT NULL,`UserSchool` VARCHAR(30) NOT NULL,`Context` TEXT NOT NULL,`TargetName` VARCHAR(30) NOT NULL,UserID INT NOT NULL,isAnonymous TINYINT DEFAULT 1);";
$create_Waiting        = "CREATE TABLE `waiting` (`id` VARCHAR(24) NOT NULL,`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,`SubmitUser` VARCHAR(30) NOT NULL,`UserSchool` VARCHAR(30) NOT NULL,`Context` TEXT NOT NULL,`TargetName` VARCHAR(30) NOT NULL,UserID INT NOT NULL,isAnonymous TINYINT DEFAULT 1);";
$addKey_AnonymousInfo = "Alter table `anonymousInfo` add primary key(`id`);";
$addKey_Publish = "Alter table `publish` add primary key(`id`);";
$addKey_Waiting = "Alter table `waiting` add primary key(`id`);";
mysqli_query($dbc, $create_AnonymousInfo);
mysqli_query($dbc, $create_Publish);
mysqli_query($dbc, $create_Waiting);
mysqli_query($dbc, $addKey_AnonymousInfo);
mysqli_query($dbc, $addKey_Publish);
mysqli_query($dbc, $addKey_Waiting);
mysqli_close($dbc);
?>
