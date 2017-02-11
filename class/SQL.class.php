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
		$this->PDO = new PDO($dsn,$this->username,$this->password,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
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
		/*
		foreach($keys_setData as $key)
		{
			if($setData[$key][0] === "`")
			{
				$set .= $key . "=" . $setData[$key] . ",";
			}
			else
			{
				$set .= $key . "='" . $setData[$key] . "',";
			}
			$set = implode(",",$set);
		}
		 */
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

	public function SQLInnerJoinSelect($columnsName,$tableName,$joinTables,$onData,$whereData,$startRow = 1,$rowCount = -1,$order = null,$fetchMode = PDO::FETCH_ASSOC)
	{
		$count_onData	  = count($onData);
		$count_joinTables = count($joinTables);

		//join more than 1 table
		if($count_joinTables > 1)
		{
			for($i = 1;$i<$count_joinTables;++$i)
			{
				$tableName = "(" . $tableName;
			}
		}
		for($i = 0;$i<$count_joinTables;++$i)
		{
			if($i != 0)
			{
				$join .= ")";
			}
			$join .= "INNER JOIN " . $joinTables[$i];
		}

		for($i = 0;$i<$count_onData;++$i)
		{
			if($i != 0)
			{
				$on .= ",";
			}
			$on .= $onData[$i];
		}
		$cmd_innerJoin = "$join ON ($on)";
		$rtn = $this->SQLSelect($columnsName,$tableName,$whereData,$startRow,$rowCount,$cmd_innerJoin,$order);

		if($this->lastStatus === "success")
		{
			return ConstructReturnJSON("success",null,$rtn["info"]["message"]);
		}
		else
		{
			return $rtn;
		}	
	}
}
?>
