<?php
include_once("config.php");
$dbc = mysqli_connect($db_ipAddr, $db_username, $db_password, $db_name, $db_port) or die("数据库配置有误，请查证");

$create_AnonymousInfo = "CREATE TABLE `anonymousInfo` (`ItemID` VARCHAR(24) PRIMARY KEY NOT NULL,`SubmitUser` VARCHAR(30) NOT NULL,`UserSchool` VARCHAR(30) NOT NULL);";
$create_Publish        = "CREATE TABLE `publish` (`ItemID` VARCHAR(24) PRIMARY KEY NOT NULL,`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,`SubmitUser` VARCHAR(30) NOT NULL,`UserSchool` VARCHAR(30) NOT NULL,`Context` TEXT NOT NULL,`TargetName` VARCHAR(30) NOT NULL,UserID INT NOT NULL,`UpNum` INT NOT NULL DEFAULT 0,`CommentNum` INT NOT NULL DEFAULT 0,`isAnonymous` TINYINT DEFAULT 1);";
$create_Waiting        = "CREATE TABLE `waiting` (`ItemID` VARCHAR(24) PRIMARY KEY NOT NULL,`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,`SubmitUser` VARCHAR(30) NOT NULL,`UserSchool` VARCHAR(30) NOT NULL,`Context` TEXT NOT NULL,`TargetName` VARCHAR(30) NOT NULL,UserID INT NOT NULL,`isAnonymous` TINYINT DEFAULT 1);";
$create_Comment		  = "CREATE TABLE `comment` (`CommentID` VARCHAR(24) PRIMARY KEY NOT NULL,`ItemID` VARCHAR(24) NOT NULL,`Context` TEXT NOT NULL,`SubmitUser` VARCHAR(30),`UserID` INT NOT NULL,`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,isAnonymous TINYINT DEFAULT 0)";
$create_Up			  = "CREATE TABLE `up` (`UpID` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,`UserID` INT NOT NULL,`ItemID` VARCHAR(24) NOT NULL,`UpTime` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL)";


//$addKey_AnonymousInfo = "Alter table `anonymousInfo` add primary key(`id`);";
//$addKey_Publish = "Alter table `publish` add primary key(`id`);";
//$addKey_Waiting = "Alter table `waiting` add primary key(`id`);";
//$addKey_Comment = "Alter table `comment` add primary key(`id`);";

mysqli_query($dbc, $create_AnonymousInfo);
mysqli_query($dbc, $create_Publish);
mysqli_query($dbc, $create_Waiting);
mysqli_query($dbc, $create_Comment);
mysqli_query($dbc, $create_Up);

//mysqli_query($dbc, $addKey_AnonymousInfo);
//mysqli_query($dbc, $addKey_Publish);
//mysqli_query($dbc, $addKey_Waiting);
//mysqli_query($dbc, $addKey_Comment);
mysqli_close($dbc);
?>
