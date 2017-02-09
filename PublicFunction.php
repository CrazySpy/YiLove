<?php
function TranslateArr($arr,$FrontDelimiter = "'",$LastDelimiter = "',",$DeleteEndChar = 0)
{
	$obj = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
	$result = NULL;

	foreach($obj as $element) 
	{
		$result.=$FrontDelimiter.$element.$LastDelimiter;
	}
	if($DeleteEndChar)
	{
		$result = substr($result,0,strlen($result)-$DeleteEndChar); 
	}
	return $result;	
}

function ConstructReturnJSON($status = "success",$code = null,$message = null,$extra = null)
{
	if(is_array($message) || is_object($message))
	{
		$message = json_encode($message);
		$json = '{"status": "' . $status . '","info":{"code":"' . $code . '","message":' . $message;
	}
	else
	{
		$json = '{"status": "' . $status . '","info":{"code":"' . $code . '","message":"' . $message . '"';
	}
	if(!empty($extra))
	{
            if(is_array($extra) || is_object($extra))
            {
                $extra = json_encode($extra);
                $json .= '","extra": ' . $extra . '}}';
            }
            else
            {
                $json .= '","extra": "' . $extra . '"}}';
            }
	}
	else
	{
		$json .= '}}';
	}
	return json_decode($json,true);
}

function toSQLSafeString($string)
{
	$string = str_ireplace('"', '\"',$string);
	$string = str_ireplace("'", "\'",$string);
	$string = str_ireplace("\\", "\\\\",$string);
	$string = str_ireplace("/", "\/",$string);
	$string = str_ireplace(";", "\;",$string);
	$string = str_ireplace(".", "\.",$string);
	return $string;
}

function CreateUniqueID()
{
	$id = uniqid(true,true);
	$id = str_ireplace(".", "_", $id);
	return $id;
}

function isYibanLogin()
{
	if(!isset($_SESSION["isLogin"]) || $_SESSION["isLogin"] == "false")
	{
		return false;
	}
	return true;
}
?>
