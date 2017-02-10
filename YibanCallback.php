<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/Yiban.class.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/SQL.class.php");
session_start();
if(!isYibanLogin())
{
	$yiban = new Yiban($_GET["code"]);
	$_SESSION["isLogin"]      = "true";
	$_SESSION["access_token"] = toSQLSafeString($yiban->GetAccessToken());
	//		$_SESSION["realName"]	= toSQLSafeString($yiban->GetName());
	$_SESSION["userID"]		  = toSQLSafeString($yiban->GetUserID());
	$_SESSION["sex"]		  = toSQLSafeString($yiban->GetSex());
	setcookie("nickName",$yiban->GetNickName(),time() + 7*60*60*24);

	$dbc_setInfo = new SQL;
	
	$schoolInfo = Array(
		"SchoolID" => toSQLSafeString($yiban->GetSchoolID())
	);
	$count_isSchoolExist = $dbc_setInfo->SQLCount("SchoolInfo",$schoolInfo);
	if($dbc_setInfo->GetlastStatus() !== "success")
	{
		exit("错误代码:" . $count_isSchoolExist["info"]["code"]);
	}
	if($count_isSchoolExist["info"]["message"] == 0)
	{
		$schoolInfo = Array(
			"`SchoolID`"	=> toSQLSafeString($yiban->GetSchoolID()),
			"`SchoolName`"	=> toSQLSafeString($yiban->GetSchoolName())
		);
		$rtn = $dbc_setInfo->SQLInsert("SchoolInfo",$schoolInfo);
	}


	if($dbc_setInfo->GetLastStatus() === "success")
	{
		$userInfo = Array(
			"UserID"=>$_SESSION["userID"]
		);
		$count_isUserExist = $dbc_setInfo->SQLCount("UserInfo",$userInfo);
		if($dbc_setInfo->GetLastStatus() !== "success")
		{
			exit("发生错误:" . $count_isUserExist["info"]["code"]);
		}
		if($count_isUserExist["info"]["message"] == 0)
		{
			$userInfo = Array(
				"`UserID`"	=> $_SESSION["userID"],
				"`RealName`"	=> "unknown",
				"`UserName`"	=> toSQLSafeString($yiban->GetUserName()),
				"`Sex`"		=> toSQLSafeString($yiban->GetSex()),
				"`NickName`"	=> toSQLSafeString($yiban->GetNickName()),
				"`SchoolID`"	=> toSQLSafeString($yiban->GetSchoolID()),
			);
			$rtn = $dbc_setInfo->SQLInsert("UserInfo",$userInfo);

		}
		if($rtn["status"] !== "success")
		{
			exit("错误代码:" . $rtn["info"]["code"]);
		}
	}
	header("location:index.php");
}
else
{
	echo('<script>alert("您已登录");</script>');
	header("location:index.php");
}
?>
