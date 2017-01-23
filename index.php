<?php
require_once("config.php");
require_once("PublicFunction.php");
session_start();
if(isset($_GET["access_token"]))
{
    $_SESSION["access_token"] = toSQLSafeString($_GET["access_token"]);
}
?>
<html>
    <head>
        <meta content="text/html; charset=utf-8" />
        <title><?php echo($GLOBALS["title"]); ?></title>
        <script src="https://apps.bdimg.com/libs/jquery/1.6.4/jquery.min.js"></script>
        <script src="script/index.js"></script>
        <link rel="stylesheet" type="text/css" href="style/style.css" />
    </head>
    <body>
        <h1>上海立信会计金融学院易班表白墙</h1>
        <div id="submitArea">
            表白内容:<br/><textarea id="submitArea_context" name="context" row =100 col=100></textarea><br/>
            表白对象:<input id="submitArea_targetName" name="targetName"/>
            是否匿名发布（隐藏您的信息）<input id="submitArea_isAnonymous" name="isAnonymous" type="checkbox" checked="checked" /><br/>
            <div id = "submitArea_submit">发布</div>
        </div>
        <div id = "listArea">
        </div>
        <div id="loadingBar">
        </div>
    </body>
</html>