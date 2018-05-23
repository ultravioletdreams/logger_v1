<?php
//********************************************************************************************************************************
// logger.php - Class to wrap logger functions
//********************************************************************************************************************************
class logger 
{
	function __construct()
	{
		// echo 'Constructor!';
	}
	
	// Create log entry in database
	function create_entry_sensor_1($temperature,$h_true,$h_linear,$h_abs,$dew_point,$arduino_stamp,$server_stamp)
	{	
		$query_string = "INSERT INTO sensor_1 "
		              . "(temperature,humidity_true,humidity_linear,humidity_absolute,dew_point,arduino_t_stamp,server_t_stamp) "
                      . "VALUES ("
					  . "'$temperature',"
					  . "'$h_true',"
					  . "'$h_linear',"
					  . "'$h_abs',"
					  . "'$dew_point',"
					  . "'$arduino_stamp',"
					  . "'$server_stamp'"
					  . ") RETURNING entry_id;";
					  
		return $this->db_query($query_string);
		
	}
	
	// Create log entry in database
	function create_entry_sensor_2($temperature,$h_linear,$arduino_stamp,$server_stamp)
	{	
		$query_string = "INSERT INTO sensor_2 "
		              . "(temperature,humidity_linear,arduino_t_stamp,server_t_stamp) "
                      . "VALUES ("
					  . "'$temperature',"
					  . "'$h_linear',"
					  . "'$arduino_stamp',"
					  . "'$server_stamp'"
					  . ") RETURNING entry_id;";
					  
		return $this->db_query($query_string);
		
	}
	
	function list_all($sensor_table_name='sensor_1')
	{
		$query_string = "SELECT * FROM $sensor_table_name ORDER BY entry_id DESC LIMIT 100";
		return $this->db_query($query_string);
	}
	
	function db_query($query_string)
	{
		$db_conn = pg_connect("host=localhost port=5432 dbname=logger user=pgadmin password=pgadmin123456");
		$result = pg_query($db_conn,$query_string);
		echo pg_last_error($db_conn);
		
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
			$data[$row_num] = pg_fetch_assoc($query_result);
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