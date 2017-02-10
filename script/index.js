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
	else
		return;
	var node = '<div class="message fixedPosition" style="display:none;z-index:auto;background-color:' + backgroundColor + '"><img src="' + iconURL + '" />' + message +'</div>';
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
				ShowMessage("error","发生错误:" + rtnData["info"]["message"] + ",错误代码：" + rtnData["info"]["code"]);
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
	if(comment.NickName == "")
		comment.NickName = "路人甲";
	var node = 
		'<div id="loveComment_' + comment.CommentID + '">' +
		'<div id="loveComment_time_' + comment.CommentID + '">评论时间：' + comment.SubmitTime + '</div>' +
		'<div id=loveComment_words_' + comment.CommentID + '">' +  comment.Context  + '</div>' +
		'<div id="loveComment_user_' + comment.CommentID + '">' + comment.NickName + '</div>' +
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
			ShowMessage("error","评论不可为空");
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
					ShowMessage("error","发生错误:" + rtnData["info"]["message"] + ",错误代码：" + rtnData["info"]["code"]);
				}
				else
				{
					ShowMessage("success","发表成功");
					var newComment = rtnData["info"]["message"][0];
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

function SetItemComments()
{
	$(document).delegate("[id^=loveOperation_comment_]","click",function(){
		var itemID = $(this).attr("id").substr(22);
		$("#commentArea_" + itemID).slideToggle("slow");

		if($("#loveComments_" + itemID).html() != "")
			return;

		$.ajax({
			url:"GetComments.php",
			type:"GET",
			async:false,
			dataType:"json",
			data:"itemID=" + itemID,
			success:function(rtnData){
				if(rtnData["status"] != "success")
				{
					ShowMessage("error","获取数据时发生错误");
					$("#loveComments_" + itemID).html("获取数据时发生错误");
				}
				var comments = rtnData["info"]["message"];
				$.each(comments,function(index,item){
					AddItemComment(itemID,item);
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
	});
}

function ConstructItemNode(item)
{
	if(item.NickName == "")
		item.NickName = "路人甲";
	var node = 
		'<div id="loveItem_' + item.ItemID + '">' +
		'<div id="loveTime_' + item.ItemID + '" class="loveTime">表白时间：' + item.SubmitTime + '</div>' +
		'<div id="loveTarget_' + item.ItemID + '" class="loveTarget">Dear ' + item.TargetName + '</div>' +
		'<div id="loveWords_' + item.ItemID + '" class="loveWords">' + item.Context + '</div>' +
		'<div id="loveUser_' + item.ItemID + '" class="loveUser">From ' + item.NickName + '</div>' +
		'<div id="loveOperation_' + item.ItemID + '">' + 
		'<div id="loveOperation_up_' + item.ItemID + '" class="inline handCursor littleMargin">'+ "赞(" + '<div id="upNum_' + item.ItemID + '" class="inline">' + item.UpCount + '</div>)</div>'+
		'<div id="loveOperation_comment_' + item.ItemID + '"  class="inline handCursor littleMargin">' + '评论(' + '<div id="commentNum_' + item.ItemID + '" class="inline">' + item.CommentCount + '</div>)</div>' +
		'</div>' +
		'<div id="commentArea_' + item.ItemID + '" style="display:none;">' + 
		'<textarea id="commentArea_context_' + item.ItemID + '" name="context" class="loveComment wideTextArea littleMargin autoMargin littleBorderRadius" row =100 col=100></textarea>' +
		'<div id="commentArea_submit_' + item.ItemID + '"  class="loveComment_Submit handCursor centerAligin autoMargin littleMargin">提交</div>' +
		'<div id="loveComments_' + item.ItemID +'">' + '</div>' +
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
			}
			var items = rtnData["info"]["message"];
			$.each(items,function(index,item){
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
		if($("#submitArea_context").val()==="")
		{
			ShowMessage("error","表白内容不可为空");
			return;
		}
		if($("#submitArea_targetName").val()==="")
		{
			ShowMessage("error","表白对象不可为空");
			return;
		}
		if(!isSending)
			isSending = true;
		else
		{
			ShowMessage("error","请勿重复请求");
			return;
		}

		$(this).html("正在发布..");
		var isAnonymous;
		if($("#submitArea_isAnonymous").is(":checked"))
			isAnonymous = "on";
		else
			isAnonymous = "off";
		$.ajax({
			url:"submit.php",
			type:"POST",
			dataType:"json",
			data:{context:$("#submitArea_context").val(),targetName:$("#submitArea_targetName").val(),isAnonymous:isAnonymous},
			async:false,
			success:function(rtnData){
				//				rtnData = $.parseJSON(rtnData);
				if(rtnData["status"] !== "success")
					ShowMessage("error","发生错误:" + rtnData["info"]["message"] + ",错误代码：" + rtnData["info"]["code"]);
				else
				{
					ShowMessage("success","发表成功");
					var item = rtnData["info"]["message"][0];
					var node = ConstructItemNode(item);
					$(node).prependTo("#listArea").hide().slideDown("slow");
					$("#submitArea_context").val("");
					$("#submitArea_targetName").val("");
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
					ShowMessage("error","发生错误:" + rtnData["info"]["message"] + ",错误代码：" + rtnData["info"]["code"]);
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
			$("#loginInfo").html("欢迎您," + GetCookie("nickName") + '  ' + '<a href="logout.php">退出登录</a>');
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
	SetItemComments();
	SubmitComment();
	SubmitUp();
	SetLoginInfo();
});
