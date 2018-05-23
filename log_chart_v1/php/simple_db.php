<?php
//********************************************************************************************************************************
// simple_db.php - Basic class for Postgres SQL queries
//********************************************************************************************************************************
class simple_db
{
	function __construct()
	{
		// Set return type
		$this->return_array = false;
	}
	
//********************************************************************************************************************************
// DB Connector
	
	function query($query_string)
	{
		$db_conn = pg_connect("host=localhost port=5432 dbname=logger user=pgadmin password=pgadmin123456");
		$result = pg_query($db_conn,$query_string);
		
		if($result)
		{
			$data = $this->db_rows($result);
		}
		else
		{
			$data = false;
		}
		
		pg_close($db_conn);
		
		return $data;
	}
	
	function db_rows($query_result)
	{
		$num_rows = pg_num_rows($query_result);
		$data = array();
		
		for($row_num=0;$row_num<$num_rows;$row_num++)
		{
			$data[] = $this->return_array ? pg_fetch_array($query_result,NULL,PGSQL_NUM) : pg_fetch_assoc($query_result);
		}	
		
		return $data;
	}
	
//********************************************************************************************************************************
// END OF CLASS
//********************************************************************************************************************************
}

//********************************************************************************************************************************
// END OF FILE
//********************************************************************************************************************************
?>