<?php
session_start();
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/Yiban.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");

if(!isYibanLogin())
{
	header("location:index.php");
	//echo('<script>alert("你尚未登录");</script>');	
}
if(!(Yiban::RevokeAccessToken($_SESSION["access_token"])))
{
	echo('<script>alert("access token有误，注销失败");</script>');
}
$_SESSION["isLogin"]      = "false";
$_SESSION["access_token"] = Array();
$_SESSION["sex"]	  = Array();
$_SESSION["userID"]		  = Array();
setcookie("nickName");
header("location:index.php");
?>
