<?php
//********************************************************************************************************************************
// Temperature and humidity logger
//********************************************************************************************************************************
// Logger class
include './logger.php';

// Template function
include './html_template.php';

// Get action parameter and error if no action is specified
$action = isset($_REQUEST['ax']) ? $_REQUEST['ax'] : false;
if($action === false) 
{
	header("HTTP/1.0 400 Not Found / Bad Action");
	die();
}

// Execute action
switch($action)
{	
	case 'test':
		echo date("Y-m-d H:i:s");
		die();
	break;
	
	case 'log':
		action_input();
	break;
	
	case 'output':
		action_output();
	break;
	
	default:
		echo "Action paramater \"$action\" is not recognised.";
	break;
}

// Input sensor log entry.
function action_input()
{
	// Initialise logger object
	$log = new logger(); 
	
	// Generate timestamp from server time
	$server_timestamp = date("Y-m-d H:i:s");
		
	// Add a new entry
	switch($_REQUEST['sensor_id'])
	{
		case 'sensor_1':
			$entry_result = $log->create_entry_sensor_1($_REQUEST['t1'],$_REQUEST['h1'],$_REQUEST['h2'],$_REQUEST['h3'],$_REQUEST['dp'],$_REQUEST['t_stamp'],$server_timestamp);
		break;
		
		case 'sensor_2':
			$entry_result = $log->create_entry_sensor_2($_REQUEST['t1'],$_REQUEST['h1'],$_REQUEST['t_stamp'],$server_timestamp);
		break;
	}

	
	print_r($entry_result);
}

// Output list of sensor log entries.
function action_output()
{
	// Initialise logger object
	$log = new logger(); 
	
	// Get entries from db
	$log_entries = $log->list_all();
	// Render template from entries
	$html = render_template('./table_view.html',$log_entries);
	// Print list
	echo $html;
}

//********************************************************************************************************************************
// END OF FILE
//********************************************************************************************************************************
?>