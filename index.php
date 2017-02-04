<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/class/Yiban.class.php");
session_start();
if(isset($_GET["access_token"]) && !isYibanLogin())
{
	$_SESSION["access_token"] = toSQLSafeString($_GET["access_token"]);
	$yiban = new Yiban($_SESSION["access_token"]);
	if($yiban->GetReturnJSON()["status"] === "success")
	{
		$_SESSION["isLogin"]	= "true";
		$_SESSION["realName"]	= toSQLSafeString($yiban->GetName());
		$_SESSION["userID"]		= toSQLSafeString($yiban->GetUserID());
		$_SESSION["userSchool"] = toSQLSafeString($yiban->GetSchool());
	}
	else
	{
		exit(json_encode($yiban->GetReturnJSON()));
	}
}
?>
<html>
    <head>
        <meta content="text/html; charset=utf-8" />
        <title><?php echo($GLOBALS["title"]); ?></title>
        <script src="script/jquery-1.11.3.js"></script>
        <script src="script/index.js"></script>
        <link rel="stylesheet" type="text/css" href="style/style.css" />
    </head>
	<body>
		<div id="message"></div>
        <h1>上海立信会计金融学院易班表白墙</h1>
        <div id="submitArea" class="centerAligin littleBorderRadius">
            <textarea id="submitArea_context" class="wideTextArea littleBorderRadius littleMargin" name="context" row =100 col=100></textarea><br/>
            <input id="submitArea_targetName" name="targetName"/>
            <input id="submitArea_isAnonymous" name="isAnonymous" type="checkbox" checked="checked" />
            <div id = "submitArea_submit" class="handCursor centerAligin noMargin autoMargin">发布</div>
        </div>
        <div id = "listArea">
        </div>
        <div id="loadingBar">
        </div>
    </body>
</html>
