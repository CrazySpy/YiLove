<?php
class Yiban
{
	private $access_token;
	private $userInfo;
	private $code;
	public  $lastStatus;

	function __construct($code) 
	{
		$this->code			= $code;
		$this->access_token = $this->CreateAccessToken();
		if(!empty($this->access_token))
		{
			$this->userInfo = Array();
			$this->userInfo = array_merge($this->userInfo,$this->GetUserInfo());
		}
	}

	private static function curl_HTTPS($url,$method="GET",$post_data=Array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_TIMEOUT, $GLOBALS["yiban_timeout"]);
		// https请求 不验证证书和hosts
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		if($method == "POST")
		{
			// post数据
			curl_setopt($ch, CURLOPT_POST, 1);
			// post的变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		$output = curl_exec($ch);
		curl_close($ch);
		return ($output);
	}

	private function CreateAccessToken()
	{
		$url = "https://openapi.yiban.cn/oauth/access_token";
		$postData = Array("client_id" => $GLOBALS["yiban_appid"],"client_secret" => $GLOBALS["yiban_appSecret"],"code" => $this->code,"redirect_uri" => $GLOBALS["yiban_callbackURL"]);
		$rtn =  json_decode($this->curl_HTTPS($url,"POST",$postData),true);
		if(isset($rtn["access_token"]))
		{
			return $rtn["access_token"];
		}
		else
		{
			exit($rtn["info"]["msgCN"]);
		}
	}

	private function GetUserInfo()
	{
		//权限限制，暂时先这样
		//$url = "https://openapi.yiban.cn/user/verify_me?access_token=$this->access_token";
		$url = "https://openapi.yiban.cn/user/me?access_token=$this->access_token";	
		$rtn = json_decode($this->curl_HTTPS($url),true);
		if(isset($rtn["status"]) && $rtn["status"] === "error")
		{
			exit($rtn["info"]["message"]);
		}
		else
		{
			return $rtn;
		}
		//if($this->returnInfo["status"] == "error")
		//        throw new Exception ($this->returnInfo["info"]["msgCN"],"yiban_" . $this->returnInfo["info"]["code"]);
	}
	/*
	public function GetReturnJSON()
	{
		$rtnJSON = json_decode($this->userInfo,true);
		$status = $rtnJSON["status"];

		if($status != "success")
		{
			$errorCode =  "1" . $rtnJSON["info"]["code"][1] . $rtnJSON["info"]["code"][2] . $rtnJSON["info"]["code"][3];
			$errorMessage = $rtnJSON["info"]["msgCN"];
			return ConstructReturnJSON("error",$errorCode,$errorMessage);
		}
		else
		{
			return ConstructReturnJSON("success");
		}
	}
	 */
	/*
	public function GetExceptionCode()
	{
		return $this->exceptionCode;
	}

	public function GetExceptionMessage()
	{
		return $this->exceptionMessage;
	}
	 */
	public function GetLastStatus()
	{
		return $this->lastStatus;
	}

	public static function RevokeAccessToken($access_token)
	{
		$url = "https://openapi.yiban.cn/oauth/revoke_token";
		$postData = Array("client_id" => $GLOBALS["yiban_appid"],"access_token" => $access_token);
		$rtn = json_decode(Yiban::curl_HTTPS($url,"POST",$postData),true);
		if($rtn["status"] == "200")
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function GetAccessToken()
	{
		return $this->access_token;
	}

	public function GetInfo($infoName)
	{
		if(!isset($this->userInfo["info"][$infoName]))
		{
			return ConstructReturnJSON("error",3006,"返回数据有误");
		}
		return $this->userInfo["info"][$infoName];
	}
	public function GetUserID()
	{
		return $this->GetInfo("yb_userid");
	}
	public function GetName()
	{
		return $this->GetInfo("yb_realname");
	}
	public function GetNickName()
	{
		return $this->GetInfo("yb_usernick");
	}
	public function GetSchoolID()
	{
		return $this->GetInfo("yb_schoolid");
	}
	public function GetSchoolName()
	{
		return $this->GetInfo("yb_schoolname");
	}
	public function GetSex()
	{
		return $this->GetInfo("yb_sex");
	}
	public function GetUserName()
	{
		return $this->GetInfo("yb_username");
	}


};
?>
