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
    var node = '<div id="message" style="display:none;z-index:auto;background-color:' + backgroundColor + '"><img src="' + iconURL + '" />' + message +'</div>';
    $message = $(node).prependTo("body");
    $message.fadeIn(2000,function(){
        $message.fadeOut(2000,function(){
            $message.remove();
        });
    });
}

function GetMaxPage()
{
	$.ajax({
		url:"GetListNum.php",
		async: false,
		success:function(data){
			json = data;
		}
	});
	if($.parseJSON(json)["status"] === "success")
		maxPage = Math.ceil($.parseJSON(json)["info"]["message"] / itemsPerPage);
	else
		ShowMessage("error","获取表白列表时发生错误！");
}

function SetList(page)
{       /*
	if(page < 1)
	{
		ShowMessage("error","已经是第一页了！");
		return false;
	}
	else if(page > maxPage)
	{
                if(maxPage != 0)
                    ShowMessage("error","已经是最后一页了！");
		return false;
	}
        */
	//$("#list").html("正在加载...");
        if(page <= maxPage)
            window.currentPage = page;
        else
            return false;
	$.ajax({
		url:"GetList.php",
		type:"GET",
		data:"page=" + page + "&lpp=" + itemsPerPage,
		async:false,
		success:function(rtnData)
		{
			if($.parseJSON(rtnData)["status"] !== "success")
                        {
                                ShowMessage("error","获取数据时发生错误");
                                $("#listArea").html("获取数据时发生错误");
				return false;
                        }
			rtnData_json = $.parseJSON(rtnData)["info"]["message"];
			$.each(rtnData_json,function(index,item){
				var node = 
					'<div id="loveItem_' + item.id + '">' +
                                            '<div id="loveTime_' + item.id + '" class="loveTime">' + item.SubmitTime + '</div>' +
                                            '<div id="loveTarget_' + item.id + '" class="loveTarget">Dear ' + item.TargetName + '</div>' +
                                            '<div id="loveWords_' + item.id + '" class="loveWords">' + item.Context + '</div>' +
                                            '<div id="loveUser_' + item.id + '" class="loveUser">From ' + item.SubmitUser + '</div>' +
                                            '<hr/>' +
                                            '<div id="loveItem_OthersOperation_' + item.id + '">' + 
                                                '<div id="loveItem_OthersOperation_up_' + item.id + '" class="loveItem_OthersOperation">'+ "赞</div>"+
                                                '<div id="loveItem_OthersOperation_comment_' + item.id + '" class="loveItem_OthersOperation">'+ "评论</div>"
                                            '</div>' +
					'</div>';
//				if(index==0)
//					$("#list").empty();
				$("#listArea").append(node); 
			});
		}
	});
	return true;
}

/*
function SetControlBar()
{
        $("#controlBar").undelegate("click","[div[id^=toPage_]]");
        $("#controlBar").empty();
        if(currentPage>1)
            $("#controlBar").append('<div id="lastPage">上一页</div>');
        
	if(currentPage<=5)
	{
		for(var i=1;i<=11 && i<=maxPage;++i)
		{
			var node ='<div id="toPage_' + i + '" >' + i + '</div>';
			$("#controlBar").append(node);
		}
	}
	else if(currentPage>maxPage-5)
	{
		for(var i=maxPage - 11;i<=maxPage;++i)
		{
			var node ='<div id="toPage_' + i + '" >' + i + '</div>';
			$("#controlBar").append(node);
		}
	}
	else
	{
		for(var i=currentPage - 5;i<=current + 5;++i)
		{
			var node ='<div id="toPage_' + i + '" >' + i + '</div>';
			$("#controlBar").append(node);
		}
	}
	$("div[id^=toPage_]").each(function(index,item){
		$("#controlBar").delegate("#" + $(item).attr("id"),"click",function(){
                    if($(item).attr("id") !== "toPage_"+currentPage)
                    {
			SetList(index + 1);
                        $("html,body").animate({scrollTop:$("#board").offset().top},1000);
                        SetControlBar();
                    }
		});
	});
        
        if(currentPage < maxPage)
            $("#controlBar").append('<div id="nextPage">下一页</div>');
}
*/

var isSending = false;
$(document).ready(function(){
	GetMaxPage();
	SetList(currentPage);
        /*
	SetControlBar();
        
        $("#controlBar").delegate("#lastPage","click",function(){
		SetList(currentPage-1);
                $("html,body").animate({scrollTop:$("#board").offset().top},1000);
                SetControlBar();
        });
        
        $("#controlBar").delegate("#nextPage","click",function(){
		SetList(currentPage+1);
                $("html,body").animate({scrollTop:$("#board").offset().top},1000);
                SetControlBar();
        });  
        */
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
			data:{context:$("#submitArea_context").val(),targetName:$("#submitArea_targetName").val(),isAnonymous:isAnonymous},
			success:function(rtnData){
				if($.parseJSON(rtnData)["status"] !== "success")
					ShowMessage("error","发生错误:" + $.parseJSON(rtnData)["info"]["message"] + ",错误代码：" + $.parseJSON(rtnData)["info"]["code"]);
				else
				{
					ShowMessage("success","发表成功");
					location.reload();
				}
			}
		});
                isSending = false;
                $(this).html = "发布";
	});
});
