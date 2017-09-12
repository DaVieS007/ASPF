<?php
class database_handler
{
	var $link;
	var $c_error;
	var $break_on_error;
	var $conn;
	
	/** DATABASE_HANDLER **/
	function __construct($host,$user,$passwd,$dbname)
	{
		$this->conn = array();
		$this->conn["host"] = $host;
		$this->conn["user"] = $user;
		$this->conn["passwd"] = $passwd;
		$this->conn["dbname"] = $dbname;

		$this->c_error = false;
		if(!$this->link = new mysqli($host,$user,$passwd,$dbname))
		{
			echo("Could not inititalize database handler");
			die();
		}
		
		if(mysqli_connect_errno())
		{
			$this->c_error = true;
		}		

		$break_on_error = false;
	}
	/** DATABASE_HANDLER **/

    function __destruct()
	{
        $this->link->close();
    }

	/** ESCAPE **/
	function escape($txt)
	{
		return $this->link->real_escape_string($txt);
	}
	
	/** QUERY **/
	function query($txt)
	{
		$try = true;

		while(true)
		{
			$res = $this->link->query($txt);
			if(!$res && $this->link->error != NULL)
			{
				if($try)
				{
					$this->link->close();
					$this->link = new mysqli($this->conn["host"],$this->conn["user"],$this->conn["passwd"],$this->conn["dbname"]);
					$try = false;
					continue;
				}

//				file_put_contents("sql.err", "SQL Error: (".$txt.")\n",FILE_APPEND);
				echo("SQL Error: ".$txt."\n");
				if($this->break_on_error)
				{
					echo("SQL-ERROR: ".$txt);
					die();
				}
				return false;
			}
			return $res;			
		}
	}
	/** QUERY **/
};

?>