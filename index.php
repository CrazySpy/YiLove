<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
session_start();
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
		<div id="headBar" class="headIntro fixedPosition autoMargin">
			<img id="logo" src="<?php echo($GLOBALS["logo"]); ?>" class="autoMargin noMargin leftAlign" />
			<div id="loginInfo" class="inline rightAlign littleMargin">
				尚未登录，请<a href="https://oauth.yiban.cn/code/html?client_id=<?php echo($GLOBALS['yiban_appid']);?>&redirect_uri=<?php echo($GLOBALS['yiban_callbackURL']);?>&state=STATE">登录</a>
			</div>
		</div>
		<div id="message"></div>
		<div name="block" style="top:0;left:0;width:100%;height:55px;" class="noMargin autoMargin"></div>
		<div id="submitArea" class="centerAligin littleBorderRadius">
			<div id="submitArea_targetName" class="leftAlign littleMargin">
				Dear<input id="submitArea_targetName_context" class="littleBorderRadius" name="targetName"/>
			</div>
			<div class="centerTextAlign"><textarea id="submitArea_context" class="wideTextArea littleBorderRadius littleMargin" name="context" row =100 col=100></textarea></div>
			<div id"submitArea_isAnonymous" class="rightTextAlign LittlePadding">
				From <div id="submitArea_isAnonymous_context" class="inline handCursor" name="isAnonymous">路人甲</div><br/>
				<div id="submitArea_isAnonymous_option" class="hide rightAlign">
					<div id="submitArea_isAnonymous_nickName" class="greyBox handCursor"></div>
					<div id="submitArea_isAnonymous_anonymous" class="greyBox handCursor">路人甲</div>
				</div>
			</div>
			<div id = "submitArea_submit" class="handCursor centerTextAlign autoMargin">发布</div>
			<br/>
        </div>
        <div id = "listArea">
        </div>
        <div id="loadingBar" class="loadingBar">
        </div>
    </body>
</html>
