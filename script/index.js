var itemsPerPage = 10;
var currentPage = 1;
var maxPage;
function ShowMessage(type,message)
{
	var iconURL;
	var backgroundColor;
	if(type === "error")
	{
		iconURL = "./image/error.png";
		backgroundColor = "red";
	}
	else if(type === "success")
	{
		iconURL = "./image/success.png";
		backgroundColor = "yellowgreen";
	}
	else if(type === "warning")
	{
		iconURL = "./image/warning.png";
		backgroundColor = "yellow";
	}
	else
		return;
	var node = '<div class="message fixedPosition centerTextAlign autoMargin" style="display:none;z-index:auto;background-color:' + backgroundColor + '"><img src="' + iconURL + '" />' + message +'</div>';
	$message = $(node);
	$("#message").html($message);
	$message.fadeIn(2000,function(){
		$message.fadeOut(2000);
	});
	/*
	$("#message").click(function(){
		$(this).remove();
		$("#message").off("click");
	});
	*/
}

function GetMaxPage()
{
	$.ajax({
		url:"GetItemNum.php",
		async: false,
		dataType:"json",
		success:function(rtnData){
			if(rtnData["status"] === "success")
				maxPage = Math.ceil(rtnData["info"]["message"] / itemsPerPage);
			else
				ShowMessage(rtnData["status"],rtnData["info"]["message"]);
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			if(textStatus == "parseerror")
				ShowMessage("error","发生错误:返回数据格式有误,错误代码:3101");
			else if(textStatus == "timeout")
				ShowMessage("error","发生错误:与服务器失去联系,错误代码:3102");
			else 
				ShowMessage("error","发生错误:接受返回数据时发生错误,错误代码:3103");
		}

	});
}

function ConstructCommentNode(comment)
{
	if(comment.isAnonymous == 1)
		comment.NickName = "路人甲";
	var node = 
		'<hr/>' + 
		'<div id="loveComment_' + comment.CommentID + '">' +
		'<div id=userHead_comment class="userHead leftAlign littleMargin autoMargin">' + '<img src="' + comment.UserHead + '"/>' + '</div>' +

		'<div id="loveComment_text_' + comment.CommentID + '" class="inline">' +
		'<div id="loveComment_time_' + comment.CommentID + '" class="loveTime">评论时间：' + comment.SubmitTime + '</div>' +
		'<div id="loveComment_user_' + comment.CommentID + '" >' + comment.NickName + ':</div>' +
		'<div id=loveComment_words_' + comment.CommentID + '" class="loveWords">' +  comment.Context  + '</div>' +
		'</div>'+
		'</div>';
	return node;
}

function AddItemComment(itemID,comment)
{
	var node = ConstructCommentNode(comment);
	$("#loveComments_" + itemID).append(node);
}

function SubmitComment()
{
	$(document).delegate("[id^=commentArea_submit_]","click",function(){
		var itemID = $(this).attr("id").substr(19);
		var commentContext = $("#commentArea_context_" + itemID).val();
		if(commentContext == "")
		{
			ShowMessage("warning","评论不可为空");
			return;
		}
		$.ajax({
			url:"SubmitComment.php",
			type:"POST",
			async:false,
			dataType:"json",
			data:{itemID:itemID,context:commentContext},
			success:function(rtnData){
				if(rtnData["status"] != "success")
				{
					ShowMessage(rtnData["status"],rtnData["info"]["message"]);
				}
				else
				{
					ShowMessage("success","发表成功");
					var newComment = rtnData["info"]["message"][0];
					newComment.Context = ReturnToBr(newComment.Context);
					var node = ConstructCommentNode(newComment);
					$(node).prependTo("#loveComments_" + itemID).hide().slideDown("slow");
					$("#commentArea_context_" + itemID).val("");
					$("#commentNum_" + itemID).html(parseInt($("#commentNum_" + itemID).html())+1);
					//alert($("#loveOperation_comment_" + itemID).val("4"));
				}
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				if(textStatus == "parseerror")
					ShowMessage("error","发生错误:返回数据格式有误,错误代码:3101");
				else if(textStatus == "timeout")
					ShowMessage("error","发生错误:与服务器失去联系,错误代码:3102");
				else 
					ShowMessage("error","发生错误:接受返回数据时发生错误,错误代码:3103");
			}
		});
	});
}
function ReturnToBr(str) { 
	return str.replace(/\r?\n/g,"<br />"); 
} 
function SetItemComments(itemID)
{
	$("#loadingBar_comment_" +itemID).click(function(){
		SetItemComments(itemID);
	});

	$.ajax({
		url:"GetComments.php",
		type:"GET",
		async:false,
		dataType:"json",
		data:{itemID:itemID,page:$("#commentPage_" + itemID).val()},
		success:function(rtnData){
			if(rtnData["status"] != "success")
			{
				ShowMessage(rtnData["status"],rtnData["info"]["message"]);
				$("#loveComments_" + itemID).html(rtnData["info"]["message"]);
			}
			else
			{
				var comments = rtnData["info"]["message"];
				$.each(comments,function(index,item){
					item.Context = ReturnToBr(item.Context);
					AddItemComment(itemID,item);
				});
				$("#commentPage_" + itemID).val(parseInt($("#commentPage_" + itemID).val()) + 1);
			}
			$("#loadingBar_comment_" + itemID).html("点击加载下10条");
			if(parseInt($("#commentPage_" + itemID).val()) > Math.ceil(parseInt($("#commentNum_" + itemID).html()) / 10))
			{
				$("#loadingBar_comment_" + itemID).html("已经到底了哦");
				$("#loadingBar_comment_" + itemID).off("click");
			}
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			if(textStatus == "parseerror")
				ShowMessage("error","发生错误:返回数据格式有误,错误代码:3101");
			else if(textStatus == "timeout")
				ShowMessage("error","发生错误:与服务器失去联系,错误代码:3102");
			else 
				ShowMessage("error","发生错误:接受返回数据时发生错误,错误代码:3103");
		}
	});
}

function ConstructItemNode(item)
{
	if(item.isAnonymous == 1)
		item.NickName = "路人甲";
	var node = 
		'<div id="loveItem_' + item.ItemID + '">' +
		'<div id="loveTime_' + item.ItemID + '" class="loveTime">表白时间：' + item.SubmitTime + '</div>' +
		'<div id="loveTarget_' + item.ItemID + '" class="loveTarget">Dear ' + item.TargetName + '</div>' +
		'<div id="loveWords_' + item.ItemID + '" class="loveWords">' + item.Context + '</div>' +
		'<div id="loveUser_' + item.ItemID + '" class="loveUser">From ' + item.NickName + '</div>' +
		'<div id="loveOperation_' + item.ItemID + '" class="centerTextAlign">' + 
		'<div id="loveOperation_up_' + item.ItemID + '" class="inline handCursor littleMargin">'+ "赞(" + '<div id="upNum_' + item.ItemID + '" class="inline">' + item.UpCount + '</div>)</div>'+
		'<div id="loveOperation_comment_' + item.ItemID + '"  class="inline handCursor littleMargin">' + '评论(' + '<div id="commentNum_' + item.ItemID + '" class="inline">' + item.CommentCount + '</div>)</div>' +
		'</div>' +
		'<div id="commentArea_' + item.ItemID + '" class="hide">' + 
		'<div class="centerTextAlign"><textarea id="commentArea_context_' + item.ItemID + '" name="context" class="loveCommentBox wideTextArea littleBorderRadius" row =100 col=100></textarea></div>' +
		'<div id="commentArea_submit_' + item.ItemID + '"  class="loveComment_Submit handCursor centerAligin autoMargin littleMargin">提交</div>' +
		'<div id="loveComments_' + item.ItemID +'">' + '</div>' +
		'<div id="loadingBar_comment_' + item.ItemID + '" class="loadingBar">点击加载下10条</div>'+
		'<input id="commentPage_' + item.ItemID + '"  value="1" class="hide"/>' +
		'</div>' +
		'</div>';
	return node;
}

function AddListItem(item)
{
	var node = ConstructItemNode(item);
	$("#listArea").append(node);
}

function SetList(page)
{       
	if(page <= maxPage)
		window.currentPage = page;
	else
		return false;
	$.ajax({
		url:"GetItems.php",
		type:"GET",
		dataType:"json",
		data:{page:page,lpp:itemsPerPage},
		async:false,
		success:function(rtnData)
		{
			if(rtnData["status"] !== "success")
			{
				ShowMessage("error","获取数据时发生错误");
				$("#listArea").html("获取数据时发生错误");
				return;
			}
			var items = rtnData["info"]["message"];
			$.each(items,function(index,item){
				item.Context = ReturnToBr(item.Context);
				AddListItem(item);
			});
		},
		error:function(XMLHttpRequest, textStatus, errorThrown){
			if(textStatus == "parseerror")
				ShowMessage("error","发生错误:返回数据格式有误,错误代码:3101");
			else if(textStatus == "timeout")
				ShowMessage("error","发生错误:与服务器失去联系,错误代码:3102");
			else 
				ShowMessage("error","发生错误:接受返回数据时发生错误,错误代码:3103");
		}

	});
}

var isSending = false;
function Submit()
{
	$("#submitArea_submit").click(function(){
		if($("#submitArea_context").val()=="")
		{
			ShowMessage("warning","表白内容不可为空");
			return;
		}
		if($("#submitArea_targetName_context").val()=="")
		{
			ShowMessage("warning","表白对象不可为空");
			return;
		}
		if(!isSending)
			isSending = true;
		else
		{
			ShowMessage("warning","请勿重复请求");
			return;
		}

		$(this).html("正在发布..");
		var isAnonymous;
		if($("#submitArea_isAnonymous_context").html()=="路人甲")
			isAnonymous = "on";
		else
			isAnonymous = "off";
		$.ajax({
			url:"submit.php",
			type:"POST",
			dataType:"json",
			data:{context:$("#submitArea_context").val(),targetName:$("#submitArea_targetName_context").val(),isAnonymous:isAnonymous},
			async:false,
			success:function(rtnData){
				//				rtnData = $.parseJSON(rtnData);
				if(rtnData["status"] !== "success")
					ShowMessage(rtnData["status"],rtnData["info"]["message"]);
				else
				{
					ShowMessage("success","发表成功");
					var item = rtnData["info"]["message"][0];
					item.Context = ReturnToBr(item.Context);
					var node = ConstructItemNode(item);
					$(node).prependTo("#listArea").hide().slideDown("slow");
					$("#submitArea_context").val("");
					$("#submitArea_targetName_context").val("");
				}
				isSending = false;
				$("#submitArea_submit").html("发布");

			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				if(textStatus == "parseerror")
					ShowMessage("error","发生错误:返回数据格式有误,错误代码:3101");
				else if(textStatus == "timeout")
					ShowMessage("error","发生错误:与服务器失去联系,错误代码:3102");
				else 
					ShowMessage("error","发生错误:接受返回数据时发生错误,错误代码:3103");

				isSending = false;
				$("#submitArea_submit").html("发布");
			}
		});
	});
}

function SubmitUp()
{
	$(document).delegate("[id^=loveOperation_up_]","click",function(){
		var itemID = $(this).attr("id").substr(17);
		$.ajax({
			url:"SubmitUp.php",
			type:"POST",
			dataType:"json",
			async:false,
			data:{itemID:itemID},
			success:function(rtnData){
				if(rtnData["status"] !== "success")
					ShowMessage(rtnData["status"],rtnData["info"]["message"]);
				else
				{
					ShowMessage("success","点赞成功");
					$("#upNum_" + itemID).html(parseInt($("#upNum_" + itemID).html()) + 1);
				}
			},
			error:function(XMLHttpRequest, textStatus, errorThrown){
				if(textStatus == "parseerror")
					ShowMessage("error","发生错误:返回数据格式有误,错误代码:3101");
				else if(textStatus == "timeout")
					ShowMessage("error","发生错误:与服务器失去联系,错误代码:3102");
				else 
					ShowMessage("error","发生错误:接受返回数据时发生错误,错误代码:3103");
			}

		});
	});
}
function isCookieAvailable()
{
	if(window.navigator.cookieEnabled)  
		return true;  
	else
		return false;
}

function GetCookie(cookieName)
{
	var arr = document.cookie.match(new RegExp("(^| )"+cookieName+"=([^;]*)(;|$)"));
	if(arr != null)
		return unescape(arr[2]); 
	else
		return null;
}

function SetLoginInfo()
{
	if(isCookieAvailable())
	{
		if(GetCookie("nickName") != null)
		{
			$("#loginInfo").html("欢迎您," + decodeURIComponent(GetCookie("nickName")) + '  ' + '<a href="logout.php">退出登录</a>');
		}
	}
	else
	{
		ShowMessage("error","您的浏览器未开启cookie，这可能会影响体验！");
	}
}


$(document).ready(function(){
	GetMaxPage();
	SetList(currentPage);
	$("#loadingBar").append("<div>拉到底加载哦</div>");
	$(function(){
		$(window).scroll(function() {
			if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
				$("#loadingBar").html('<div><img src="image/loading.gif" />正在加载...</div>');
				if(SetList(currentPage + 1))
					$("#loadingBar").html("<div>拉到底加载哦</div>");
				else
					$("#loadingBar").html("<div>已经到底了哦</div>");
			}
		});
	});
	Submit();
	SubmitComment();
	SubmitUp();
	SetLoginInfo();


	if(GetCookie("nickName") != null)
		$("#submitArea_isAnonymous_nickName").append(decodeURIComponent(GetCookie("nickName")));
	$("#submitArea_isAnonymous_context").click(function(){
		$("#submitArea_isAnonymous_option").slideToggle("slow");
	});
	$("#submitArea_isAnonymous_nickName").click(function(){
		$("#submitArea_isAnonymous_context").html(decodeURIComponent(GetCookie("nickName")));
		$("#submitArea_isAnonymous_option").slideToggle("slow");
	});
	$("#submitArea_isAnonymous_anonymous").click(function(){
		$("#submitArea_isAnonymous_context").html("路人甲");
		$("#submitArea_isAnonymous_option").slideToggle("slow");
	});

	$(document).delegate("[id^=loveOperation_comment_]","click",function(){
		var itemID = $(this).attr("id").substr(22);
		$("#commentArea_" + itemID).slideToggle("slow");
		if(parseInt($("#commentPage_" + itemID).val()) == 1)
		{
			$("#loadingBar_comment_" + itemID).html('<div><img src="image/loading.gif" />正在加载..</div>');
			SetItemComments(itemID);
		}
	});

});