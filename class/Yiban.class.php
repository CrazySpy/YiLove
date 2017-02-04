<?php
class Yiban
{
    private $access_token;
    private $returnInfo;
    private $exceptionMessage;
    private $exceptionCode;
    function __construct($at) 
    {
        $this->access_token = $at;
        $this->ConnectAuthServer();
    }
    
    private function curl_HTTPS($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $output = curl_exec($ch);
        curl_close($ch);
        return ($output);
    }               
    private function ConnectAuthServer()
    {
        $url = "https://openapi.yiban.cn/user/verify_me?access_token=$this->access_token";
        //return curl_HTTPS($url);
        $this->returnInfo = $this->curl_HTTPS($url);
        //if($this->returnInfo["status"] == "error")
        //        throw new Exception ($this->returnInfo["info"]["msgCN"],"yiban_" . $this->returnInfo["info"]["code"]);
    }
    
    public function GetExceptionCode()
    {
        return $this->exceptionCode;
    }
    
    public function GetExceptionMessage()
    {
        return $this->exceptionMessage;
    }
    
    public function CheckStatus()
    {
        $rtnJSON = json_decode($this->returnInfo,true);
        $status = $rtnJSON["status"];
        
        if($status != "success")
        {
            $this->exceptionMessage = $rtnJSON["info"]["msgCN"];
            $this->exceptionCode = "1" . $rtnJSON["info"]["code"][1] . $rtnJSON["info"]["code"][2] . $rtnJSON["info"]["code"][3];
        }
        return $status;
    }
    public function GetUserID()
    {
        $ID = json_decode($this->returnInfo,true)["info"]["yb_userid"];
        return $ID;
    }
    public function GetName()
    {
        $name = json_decode($this->returnInfo,true)["info"]["yb_realname"];
        return $name;
    }
    public function GetSchool()
    {
        $school = json_decode($this->returnInfo,true)["info"]["yb_schoolname"];
        return $school;
    }
    
};

