<?php
session_start();
require_once("PublicFunction.php");
require_once("class/SQL.class.php");
require_once("class/Yiban.class.php");
require_once("config.php");
require_once("PublicFunction.php");
$context            = toSQLSafeString($_POST["context"]);
$targetName         = toSQLSafeString($_POST["targetName"]);
$isAnonymous        = toSQLSafeString($_POST["isAnonymous"]);
$Yiban_access_token = $_SESSION["access_token"];

if(!empty($Yiban_access_token))
{
	$yiban = new Yiban($Yiban_access_token);

	if($yiban->CheckStatus() == "success")
	{
                $sql = new SQL();
                $databaseName = "waiting";
                if($GLOBALS["needCheck"] == false)
                {
                    $databaseName = "publish";
                }
                
		$SubmitUser = $yiban->GetName();
		$UserSchool = $yiban->GetSchool();
		$UserID     = $yiban->GetUserID();
		
		$id = uniqid(true,true);
                
                $a = 1;
		if($isAnonymous != "on")
		{
			$a=0;
			$column = array("id","SubmitUser","UserSchool","Context","TargetName","UserID","isAnonymous");
			$data   = array("'$id'","'$SubmitUser'","'$UserSchool'","'$context'","'$targetName'","$UserID",$a);
			echo($sql -> SQLInsert($databaseName,$column,$data));
		}
		else
		{
			$column = array("id","SubmitUser","UserSchool","Context","TargetName","UserID","isAnonymous");
			$data   = array("'$id'","'匿名'","'隐藏学校'","'$context'","'$targetName'","$UserID",$a);
			$column2 = array("id","SubmitUser","UserSchool");
			$data2 = array("'$id'","'$SubmitUser'","'$UserSchool'");

			$rtn1 = $sql -> SQLInsert($databaseName,$column,$data);
			if($sql->GetLastStatus() == "success")
			{
				$rtn2 = $sql -> SQLInsert("anonymousInfo",$column2,$data2);
				if($sql->GetLastStatus() == "success")
				{
					echo(ConstructReturnJSON("success"));
				}
				else
				{
					echo($rtn2);
				}
			}
			else
			{
				echo($rtn1);
			}
		}
	}
	else 
	{
		try
		{
			throw new Exception($yiban->GetExceptionMessage(),$yiban->GetExceptionCode());
		}
		catch (Exception $ex)
		{
			echo(ConstructReturnJSON("error", $ex->getCode(), $ex->getMessage()));
		}
	}
}
else
{
    echo(ConstructReturnJSON("error",3001,"未登录到易班"));
}

?>
