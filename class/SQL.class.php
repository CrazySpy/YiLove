 <?php
 require_once("PublicFunction.php");
 require_once("config.php");
class SQL
{
    public $PDO;
    private $ipAddr,$username,$password,$port,$database;

    function __construct()
    {
    	$this -> ipAddr   = $GLOBALS["db_ipAddr"];
        $this -> port     = $GLOBALS["db_port"];
	$this -> username = $GLOBALS["db_username"];
	$this -> password = $GLOBALS["db_password"];
	$this -> database = $GLOBALS["db_name"];

	$dsn = "mysql:dbname=$this->database;host=$this->ipAddr;port=$this->port;";
	$this -> PDO = new PDO($dsn,$this -> username,$this -> password);
    }

    function __destruct()
    {
	unset($this->PDO);
    }

    private function GetErrorInfo($obj_operate)
    {
        $error = $obj_operate->errorinfo();
        try
        {
            if ($error[0] != "00000") 
            {
                throw new Exception($error[2], "2".$error[0]);
            }
        } 
        catch (Exception $ex)
        {
            return(ConstructReturnJSON("error", $ex->getCode(), $ex->getMessage()/*"发生了数据库错误"*/));
        }
        return ConstructReturnJSON("success");
    }
    
    private $lastStatus;
    public function GetLastStatus()
    {
        return $this->lastStatus;
    }
    
    public function SQLSelect($ColumnName,$TableName,$where_ColumnName = 1,$where_data = 1,$StartRow = 0,$RowNum = -1,$extra=null,$fetchMode = PDO::FETCH_BOTH)
    {
        $cmd = "SELECT $ColumnName FROM `$TableName` WHERE $where_ColumnName = :where_data $extra LIMIT $StartRow,$RowNum";
        $PDO_select = $this->PDO->prepare($cmd);
        $PDO_select->bindValue(":where_data",$where_data);
        
        $PDO_select->execute();
        
        $errorInfo = $this->GetErrorInfo($PDO_select);
        //$PDO_select->debugDumpParams();
        $errorInfo_jsonArray = json_decode($errorInfo,true);
        $this->lastStatus = $errorInfo_jsonArray["status"];
        if($errorInfo_jsonArray["status"] == "success")
        {
            return ConstructReturnJSON("success",null, $PDO_select -> fetchAll($fetchMode));
        }
        else
        {
            return $this->GetErrorInfo($PDO_select);
        }
    }

    public function SQLInsert($TableName,$Arr_ColumnName,$Arr_InsertData)
    {   
        $cmd = "INSERT INTO `$TableName` (" . TranslateArr($Arr_ColumnName,"",",",1) . ") VALUES (".TranslateArr($Arr_InsertData,"",",",1) . ")";
        $PDO_insert = $this->PDO->prepare($cmd);
        //$PDO_insert->bindParam(":InsertData",TranslateArr($Arr_InsertData));
        $PDO_insert->execute();
        //$PDO_insert->debugDumpParams();
        $rtn = $this->GetErrorInfo($PDO_insert);
        $this->lastStatus = (json_decode($rtn,true)["status"]);
        return $rtn;
    }
    
    public function SQLCount($columnName,$tableName,$fetchMode = PDO::FETCH_NUM)
    {
        $cmd = "SELECT COUNT($columnName) FROM $tableName";
        $PDO_count = $this-> PDO -> prepare($cmd);
        $PDO_count -> execute();
        
        $errorInfo = $this->GetErrorInfo($PDO_count);
        $errorInfo_jsonArray = json_decode($errorInfo,true);
        $this->lastStatus = $errorInfo_jsonArray["status"];
        if($errorInfo_jsonArray["status"] == "success")
        {
            return ConstructReturnJSON("success",null, json_decode($PDO_count -> fetchAll($fetchMode)[0][0]));
        }
        else
        {
            return $this->GetErrorInfo($PDO_count);
        }
    }
}
 ?>
