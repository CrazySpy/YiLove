<?php
include_once("config.php");
$dbc = mysqli_connect($db_ipAddr, $db_username, $db_password, $db_name, $db_port) or die("数据库配置有误，请查证");


$create_SchoolInfo = "CREATE TABLE `SchoolInfo` (" . 
	"`SchoolID` INT PRIMARY KEY NOT NULL," .
	"`SchoolName` VARCHAR(30) NOT NULL" . 
	")ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$create_UserInfo = "CREATE TABLE `UserInfo` (" . 
	"`UserID` INT PRIMARY KEY NOT NULL," .
	"`RealName` VARCHAR(30)," .
	"`UserName` VARCHAR(50) NOT NULL, " . 
	"`Sex` CHAR(1)," .
	"`UserHead` TEXT," .
	"`NickName` VARCHAR(50) NOT NULL," . 
	"`SchoolID` INT,FOREIGN KEY(`SchoolID`) REFERENCES `SchoolInfo`(`SchoolID`)" . 
	")ENGINE=InnoDB DEFAULT CHARSET=utf8;";


$create_Publish = "CREATE TABLE `Publish` (" .
	"`ItemID` INT(8) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT NOT NULL," .
	"`UserID` INT NOT NULL,FOREIGN KEY(`UserID`) REFERENCES `UserInfo`(`UserID`)," .
	"`Context` TEXT NOT NULL," .
	"`TargetName` TEXT NOT NULL," .
	"`isAnonymous` BOOLEAN DEFAULT 1 NOT NULL," .
	"`CommentCount` INT DEFAULT 0," .
	"`UpCount` INT DEFAULT 0," .
	"`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP" .
	")ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$create_Waiting = "CREATE TABLE `Waiting` (" .
	"`ItemID` INT(8) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT NOT NULL," .
	"`UserID` INT NOT NULL,FOREIGN KEY(`UserID`) REFERENCES `UserInfo`(`UserID`)," .
	"`Context` TEXT NOT NULL," .
	"`TargetName` TEXT NOT NULL," .
	"`isAnonymous` BOOLEAN DEFAULT 1 NOT NULL," .
	"`CommentCount` INT DEFAULT 0," .
	"`UpCount` INT DEFAULT 0," .
	"`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP" .
	")ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$create_Comment = "CREATE TABLE `Comment` (".
	"`CommentID` INT(8) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT NOT NULL,".
	"`UserID` INT NOT NULL,FOREIGN KEY(`UserID`) REFERENCES `UserInfo`(`UserID`),".
	"`ItemID` INT(8) UNSIGNED ZEROFILL NOT NULL,FOREIGN KEY(`ItemID`) REFERENCES `Publish`(`ItemID`),".
	"`Context` TEXT NOT NULL,".
	"`isAnonymous` BOOLEAN DEFAULT 1 NOT NULL," .
	"`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP" .
	")ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$create_Up = "CREATE TABLE `Up` (".
	"`UpID` INT(8) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT NOT NULL,".
	"`UserID` INT NOT NULL,FOREIGN KEY(`UserID`) REFERENCES `UserInfo`(`UserID`),".
	"`ItemID` INT(8) UNSIGNED ZEROFILL NOT NULL,FOREIGN KEY(`ItemID`) REFERENCES `Publish`(`ItemID`),".
	"`SubmitTime` DATETIME DEFAULT CURRENT_TIMESTAMP" .
	")ENGINE=InnoDB DEFAULT CHARSET=utf8;";

mysqli_query($dbc, $create_SchoolInfo);
mysqli_query($dbc, $create_UserInfo);
mysqli_query($dbc, $create_Publish);
mysqli_query($dbc, $create_Waiting);
mysqli_query($dbc, $create_Comment);
mysqli_query($dbc, $create_Up);

mysqli_close($dbc);
?>
