<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/PublicFunction.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");
class SQL
{
	public $PDO;
	private $ipAddr,$username,$password,$port,$database;

	function __construct()
	{
		//new PDO;
		$this -> ipAddr   = $GLOBALS["db_ipAddr"];
		$this -> port     = $GLOBALS["db_port"];
		$this -> username = $GLOBALS["db_username"];
		$this -> password = $GLOBALS["db_password"];
		$this -> database = $GLOBALS["db_name"];

		$dsn = "mysql:dbname=$this->database;host=$this->ipAddr;port=$this->port;";
		$this->PDO = new PDO($dsn,$this->username,$this->password);
	}

	function __destruct()
	{
		unset($this->PDO);
	}

	private function GetSQLStatus($obj_operate)
	{
		$error = $obj_operate->errorinfo();
		if ($error[0] != "00000") 
		{
			return ConstructReturnJSON("error","2".$error[1],/*$error[2]*/"发生了数据库错误");
		}
		return ConstructReturnJSON("success");
	}

	private $lastStatus;
	public function GetLastStatus()
	{
		return $this->lastStatus;
	}
/*
	public function SQLSelect($columnName,$tableName,$where_columnName = 1,$where_data = 1,$startRow = 1,$rowNum = -1,$extra=null,$fetchMode = PDO::FETCH_ASSOC)
	{
		if(is_array($columnName))
		{
			$columnName = TranslateArr($columnName,"`","`,",1);
		}

		$cmd = "SELECT $columnName FROM `$tableName` WHERE";

		$index = 0;
		//		if(is_array($where_columnName) && is_array($where_data))
		//		{
		for($index = 0;$index<count($where_columnName);++$index)
		{
			if($index !== 0)
			{
				$cmd .= " AND";
			}
			if(is_array($where_columnName) && is_array($where_data))
			{
				$cmd .= " $where_columnName[$index]";
			}
			else
			{
				$cmd .= " $where_columnName";
			}
			$cmd .= "= :where_data$index";
		}
		//		}
		//		else
		//		{
		//			$cmd .= " $where_columnName = :where_data0";
		//		}
		$cmd .= " $extra";
		if($startRow + $rowNum > 0)
		{
			$cmd .= " LIMIT $startRow,$rowNum";
		}
		$PDO_select = $this->PDO->prepare($cmd);
		if(is_array($where_columnName) && is_array($where_data))
		{
			for($i = 0;$i<$index;++$i)
			{
				$PDO_select->bindValue(":where_data$i",$where_data[$i]);
			}
		}
		else
		{
			$PDO_select->bindValue(":where_data0",$where_data);
		}
		$PDO_select->execute();

		$this->lastStatus = $this->GetReturnJSON($PDO_select)["status"];
		//$PDO_select->debugDumpParams();
		$status_jsonArray = $this->GetReturnJSON($PDO_select);
		if($status_jsonArray["status"] == "success")
		{
			return ConstructReturnJSON("success",null, $PDO_select -> fetchAll($fetchMode));
		}
		else
		{
			return $this->GetReturnJSON($PDO_select);
		}
	}
 */

	public function SQLSelect($columnNames,$tableName,$whereData,$startRow=1,$rowCount=-1,$extra=null,$order=null,$fetchMode = PDO::FETCH_ASSOC)
	{
		$count_columns = count($columnNames);
		$count_whereData = count($whereData);

		$columns = "*";
		if($count_columns != 0)
		{
			$columns = "";
			for($i = 0;$i<$count_columns;++$i)
			{
				if($i != 0)
				{
					$columns .= ",";
				}
				$columns .= $columnNames[$i];
			}
		}

		$where = "1";
		$keys;
		if($count_whereData != 0)
		{
			$where = "";

			$keys = array_keys($whereData);
			for($i = 0;$i<$count_whereData;++$i)
			{
				if($i != 0)
				{
					$where .= " AND ";
				}
				$where .= $keys[$i] . "=:whereData$i";
			}
		}

		$limit = "";
		if($startRow + $rowCount != 0)
		{
			$limit = "LIMIT $startRow,$rowCount"; 
		}

		$cmd = "SELECT $columns FROM $tableName $extra WHERE $where $order $limit;";

		$PDO_select = $this->PDO->prepare($cmd);

		for($i = 0;$i<$count_whereData;++$i)
		{
			$PDO_select->bindValue(":whereData$i",$whereData[$keys[$i]]);
		}

		$PDO_select->execute();

		$this->lastStatus = $this->GetSQLStatus($PDO_select)["status"];
		//$PDO_select->debugDumpParams();
		if($this->lastStatus  === "success")
		{
			return ConstructReturnJSON("success",null,$PDO_select->fetchAll($fetchMode));
		}
		else
		{
			return $this->GetSQLStatus($PDO_select);
		}
	}

	public function SQLInsert($tableName,$insertData)
	{
		$count_insertData = count($insertData);

		$keys = array_keys($insertData);
		for($i = 0;$i<$count_insertData;++$i)
		{
			if($i != 0)
			{
				$columns .= ",";
			}
			$columns .= $keys[$i]; 		
		}

		for($i = 0;$i<$count_insertData;++$i)
		{
			if($i != 0)
			{
				$data .= ",";
			}
			$data .= ":insertData$i";
		}
		$cmd = "INSERT INTO $tableName ($columns) VALUES ($data);";
		$PDO_insert = $this->PDO->prepare($cmd);

		for($i = 0;$i<$count_insertData;++$i)
		{
			$PDO_insert->bindValue(":insertData$i",$insertData[$keys[$i]]);
		}
		$PDO_insert->execute();
		//$PDO_insert->debugDumpParams();

		$this->lastStatus = $this->GetSQLStatus($PDO_insert)["status"];
		if($this->lastStatus === "success")
		{
			return ConstructReturnJSON("success");
		}
		else
		{
			return $this->GetSQLStatus($PDO_insert);
		}
	}

	public function SQLCount($tableName,$whereData,$startRow = 1,$rowCount = -1,$extra=null)
	{
		$rtn = $this->SQLSelect(Array("COUNT(*)"),$tableName,$whereData,$startRow,$rowCount,$extra,null,PDO::FETCH_NUM);

		if($this->lastStatus === "success")
		{
			return ConstructReturnJSON("success",null,$rtn["info"]["message"][0][0]);
		}
		else
		{
			return $rtn;
		}		 
	}

	public function SQLUpdate($tableName,$setData,$whereData)
	{
		$count_setData = count($setData);
		$count_whereData = count($whereData);

		$keys_setData = array_keys($setData);
		for($i = 0;$i < $count_setData;++$i)
		{
			if($i != 0)
			{
				$set .= ",";
			}
			if($setData[$keys_setData[$i]][0] === "`")
			{
				$set .= $keys_setData[$i] . "=" . $setData[$keys_setData[$i]];
			}
			else
			{
				$set .= $keys_setData[$i] . "='" . $setData[$keys_setData[$i]] . "'";
			}
		}

		$keys_whereData = array_keys($whereData);
		$where = 1;
		for($i = 0;$i < $count_whereData;++$i)
		{
			$where = "";
			if($i != 0)
			{
				$where .= " AND ";
			}
			if($whereData[$keys_whereData[$i]][0] === "`")
			{
				$where .= $keys_whereData[$i] . "=" . $whereData[$keys_whereData[$i]];
			}
			else
			{
				$where .= $keys_whereData[$i] . "='" . $whereData[$keys_whereData[$i]] . "'";
			}
		}

		$cmd = "UPDATE $tableName SET $set WHERE $where";
		
		$PDO_update = $this->PDO->prepare($cmd);
		$PDO_update->execute();

		$this->lastStatus = $this->GetSQLStatus($PDO_update)["status"];
		if($this->lastStatus === "success")
		{
			return ConstructReturnJSON("success");
		}
		else
		{
			return $this->GetSQLStatus($PDO_update);
		}
	}

	public function SQLInnerJoinSelect()
	{
	}
}
	/*
	public function SQLInsert($tableName,$columnName,$insertData)
	{
		if(is_array($columnName))
		{
			$columnName = TranslateArr($columnName,"`","`,",1);
		}
		if(is_array($insertData))
		{
			$insertData = TranslateArr($insertData,"'","',",1);
		}

		$cmd = "INSERT INTO `$tableName` ($columnName) VALUES ($insertData)";
		$PDO_insert = $this->PDO->prepare($cmd);
		//$PDO_insert->bindParam(":InsertData",TranslateArr($Arr_InsertData));
		$PDO_insert->execute();
		//$PDO_insert->debugDumpParams();
		$rtn = $this->GetReturnJSON($PDO_insert);
		$this->lastStatus = $rtn["status"];
		return $rtn;
	}

	public function SQLCount($tableName,$where_column = 1,$where_data = 1,$startRow = 1,$rowNum = -1,$extra=null)
	{
		$rtn = $this->SQLSelect("COUNT(*)",$tableName,$where_column,$where_data,$startRow,$rowNum,$extra,PDO::FETCH_NUM);

		if($rtn["status"] === "success")
		{
			return ConstructReturnJSON("success",null, json_decode($rtn["info"]["message"][0][0]));
		}
		else
		{
			return $rtn;
		}
		/*
		if(is_array($columnName))
		{
			$columnName = TranslateArr($columnName,"`","`,",1);
			$cmd = "SELECT COUNT($columnName) FROM $tableName";
		}
		else
		{
			$cmd = "SELECT COUNT($columnName) FROM `$tableName`";
		}

		$PDO_count = $this-> PDO -> prepare($cmd);
		$PDO_count -> execute();

		$status_jsonArray = $this->GetReturnJSON($PDO_count);
		$this->lastStatus = $status_jsonArray["status"];
		if($status_jsonArray["status"] == "success")
		{
			return ConstructReturnJSON("success",null, json_decode($PDO_count -> fetchAll($fetchMode)[0][0]));
		}
		else
		{
			return $this->GetReturnJSON($PDO_count);
		}

	}

	public function SQLUpdate($tableName,$columnName,$newData,$where_columnName,$where_data)
	{
		$cmd = "UPDATE `$tableName` SET";
		if(is_array($columnName) && is_array($newData))
		{
			for($index = 0;$index<count($columnName);++$index)
			{
				if($index !== 0)
				{
					$cmd .= ",";
				}
				$cmd .= " `$columnName[$index]` = $newData[$index]";
			}
		}
		else
		{
			$cmd .= " `$columnName` = $newData";
		}

		$cmd .= " WHERE";

		$index = 0;
		//		if(is_array($where_columnName) && is_array($where_data))
		//		{
		for(;$index<count($where_columnName);++$index)
		{
			if($index !== 0)
			{
				$cmd .= " AND";
			}
			if(is_array($where_columnName) && is_array($where_data))
			{
				$cmd .= " `$where_columnName[$index]`";
			}
			else
			{
				$cmd .= " `$where_columnName`";
			}
			$cmd .= " = :where_data$index";
		}
		//		}
		//		else
		//		{
		//			$cmd .= " $where_columnName = :where_data0";
		//		}
		$PDO_update = $this->PDO->prepare($cmd);

		if(is_array($where_columnName) && is_array($where_data))
		{
			for($i = 0;$i<$index;++$i)
			{
				$PDO_update->bindValue(":where_data$i",$where_data[$i]);
			}
		}
		else
		{
			$PDO_update->bindValue(":where_data0",$where_data);
		}

		$PDO_update->execute();
		$status_jsonArray = $this->GetReturnJSON($PDO_update);
		$this->lastStatus = $status_jsonArray["status"];

		//$PDO_update->debugDumpParams();

		if($status_jsonArray["status"] == "success")
		{
			return ConstructReturnJSON("success");
		}
		else
		{
			return $this->GetReturnJSON($PDO_update);
		}
	}
}
	 */
?>
