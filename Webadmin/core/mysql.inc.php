<?php
class database_handler
{
	var $link;
	
	/** DATABASE_HANDLER **/
	function __construct($host,$user,$passwd,$dbname)
	{
		if(!$this->link = new mysqli($host,$user,$passwd,$dbname))
		{
			echo("Could not inititalize database handler");
			die();
		}
		
		if(mysqli_connect_errno())
		{
			echo("SQL Error: ".mysqli_connect_error());
			die();
		}		
	}
	/** DATABASE_HANDLER **/
	
	/** ESCAPE **/
	function escape($txt)
	{
		return $this->link->real_escape_string($txt);
	}
	
	/** QUERY **/
	function query($txt)
	{
		$res = $this->link->query($txt);
		if(!$res && $this->link->error != NULL)
		{
			echo("SQL Error: (".$txt.")\n");
			file_put_contents("sql.err", "SQL Error: (".$txt.")\n",FILE_APPEND);
			die();
		}
		return $res;
	}
	/** QUERY **/
};

?>