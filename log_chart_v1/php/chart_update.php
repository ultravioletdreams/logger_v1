<?php
/********************************************************************************************************************************/
// Include log data class to get data from logger db
include('./simple_db.php');
include('./html_template.php');

// Check for request action
// last live
$action = isset($_REQUEST['ax']) ? $_REQUEST['ax'] : false;
// CHeck for interval used with last
$chart_interval = isset($_REQUEST['ix']) ? $_REQUEST['ix'] : '10 minutes';
// Skip every nth entry
$chart_skip     = isset($_REQUEST['sx']) ? $_REQUEST['sx'] : 100;

$chart_update_data = false;

// Switch action
switch($action)
{
	case 'live_table':
		$live_data = data_live_table();
		send_html_data($live_data);
	break;
	
	case 'live_chart':
		$live_data = data_live_chart();
		// Send the response
		send_json_data($live_data);
	break;
	
	case 'last':
		$chart_update_data = data_last($chart_interval,$chart_skip);
		// Send the response
		send_json_data($chart_update_data);
	break;
	
	case 'range':
		$df = $_REQUEST['df'];
		$dt = $_REQUEST['dt'];
		$chart_update_data = data_range($chart_interval,$chart_skip,$df,$dt);
		// Send the response
		send_json_data($chart_update_data);
	break;
	
	default:
	
	break;
}
/********************************************************************************************************************************/
// Get log entries - Date range
function data_range($interval,$skip,$date_from,$date_to)
{
	$ld = new simple_db();
	$ld->return_array = true;
	$chart_data_1 = $ld->query("SELECT arduino_t_stamp,temperature,humidity_linear FROM sensor_1 WHERE (arduino_t_stamp BETWEEN '" . $date_from . "' AND '" . $date_to . "') AND (entry_id % " . $skip . " = 0) ORDER BY arduino_t_stamp DESC");
	$chart_data_2 = $ld->query("SELECT arduino_t_stamp,temperature,humidity_linear FROM sensor_2 WHERE (arduino_t_stamp BETWEEN '" . $date_from . "' AND '" . $date_to . "') AND (entry_id % " . $skip . " = 0) ORDER BY arduino_t_stamp DESC");
	
	// *** DEV: more info in data
	$response_data = array();
	$response_data['title'] = 'This is the title.';
	
	// Add summary info for title and sub title
	$chart_1_count = count($chart_data_1);
	$chart_2_count = count($chart_data_2);
	$summary_1 = "Sensor: SHT15 - " . $chart_1_count . " Points. Every " . ($skip == 1 ? "single" : $skip) . " reading. From: " . $chart_data_1[$chart_1_count-1][0] . " To: " . $chart_data_1[0][0];
	$summary_2 = "Sensor: DH22  - " . $chart_2_count . " Points. Every " . ($skip == 1 ? "single" : $skip) . " reading. From: " . $chart_data_2[$chart_2_count-1][0] . " To: " . $chart_data_2[0][0];
	$response_data['summary_1'] = $summary_1;
	$response_data['summary_2'] = $summary_2;
	
	// Make timestamps into Date.UTC function calls for Highcharts
	parse_timestamps($chart_data_1);
	parse_timestamps($chart_data_2);
	
	$response_data['chart_1'] = $chart_data_1;
	$response_data['chart_2'] = $chart_data_2;
	
	// Return chart data
	return $response_data;
}
	
/********************************************************************************************************************************/
// Get log entries for a period
function data_last($interval,$skip)
{
	$ld = new simple_db();
	$ld->return_array = true;
	$chart_data_1 = $ld->query("SELECT arduino_t_stamp,temperature,humidity_linear FROM sensor_1 WHERE (arduino_t_stamp > (CURRENT_TIMESTAMP  - INTERVAL '" . $interval . "')) AND (entry_id % " . $skip . " = 0) ORDER BY arduino_t_stamp DESC");
	$chart_data_2 = $ld->query("SELECT arduino_t_stamp,temperature,humidity_linear FROM sensor_2 WHERE (arduino_t_stamp > (CURRENT_TIMESTAMP  - INTERVAL '" . $interval . "')) AND (entry_id % " . $skip . " = 0) ORDER BY arduino_t_stamp DESC");
	
	// *** DEV: more info in data
	$response_data = array();
	$response_data['title'] = 'This is the title.';
	
	// Add summary info for title and sub title
	$chart_1_count = count($chart_data_1);
	$chart_2_count = count($chart_data_2);
	$summary_1 = "Sensor: SHT15 - " . $chart_1_count . " Points. Every " . ($skip == 1 ? "single" : $skip) . " reading. From: " . $chart_data_1[$chart_1_count-1][0] . " To: " . $chart_data_1[0][0];
	$summary_2 = "Sensor: DH22  - " . $chart_2_count . " Points. Every " . ($skip == 1 ? "single" : $skip) . " reading. From: " . $chart_data_2[$chart_2_count-1][0] . " To: " . $chart_data_2[0][0];
	$response_data['summary_1'] = $summary_1;
	$response_data['summary_2'] = $summary_2;
	
	// Make timestamps into Date.UTC function calls for Highcharts
	parse_timestamps($chart_data_1);
	parse_timestamps($chart_data_2);
	
	$response_data['chart_1'] = $chart_data_1;
	$response_data['chart_2'] = $chart_data_2;
	
	// Return chart data
	return $response_data;
}

/********************************************************************************************************************************/
// Highcharts is making dates and times hard :-(
function parse_timestamps(&$chart_data,$row_number=0)
{	
	foreach($chart_data as $row_num => $row)
	{
		$date_parts = date_parse($row[$row_number]);
		$tmp_t_stamp = 'Date.UTC(' . $date_parts['year'] . ',' . ($date_parts['month'] - 1) . ',' . $date_parts['day'] . ',' . $date_parts['hour'] . ',' . $date_parts['minute'] . ',' . $date_parts['second'] . ')';
		$chart_data[$row_num][$row_number] = $tmp_t_stamp;
	}
	
	// Return highcharts date format
	return $chart_data;
}
/********************************************************************************************************************************/
// Get live data return as html for table view
function data_live_chart()
{
	$ld = new simple_db();
	
	// Template data
	$series_1 = $ld->query("SELECT entry_id,temperature,arduino_t_stamp,server_t_stamp FROM sensor_1 ORDER BY server_t_stamp DESC LIMIT 1");
	$series_2 = $ld->query("SELECT entry_id,temperature,arduino_t_stamp,server_t_stamp FROM sensor_2 ORDER BY server_t_stamp DESC LIMIT 1");
	
	parse_timestamps($series_1,'server_t_stamp');
	parse_timestamps($series_2,'server_t_stamp');
	
	$response_data = Array();
	$response_data['series_1'] = $series_1[0];
	$response_data['series_2'] = $series_2[0];


	// Return first row only
	return $response_data;
}

/********************************************************************************************************************************/
// Get live data return as json for plotting on chart
function data_live_table()
{
	$ld = new simple_db();
	
	// Template data
	$template_data['sensor_1'] = $ld->query("SELECT entry_id,temperature,server_t_stamp,arduino_t_stamp FROM sensor_1 ORDER BY server_t_stamp DESC LIMIT 1");
	$template_data['sensor_2'] = $ld->query("SELECT entry_id,temperature,server_t_stamp,arduino_t_stamp FROM sensor_2 ORDER BY server_t_stamp DESC LIMIT 1");
	
	// Inject current server time
	$template_data['current_server_time'] = date("Y-m-d H:i:s",time());
	
	// Render data into HTML template
	$html = render_template('../html/live_data_template.html',$template_data);


	// Return first row only
	return $html;
}

/********************************************************************************************************************************/
// Send data as JSON response
function send_json_data($response_data)
{
	// Set the JSON header
	header("Content-type: text/json");
	
	// Send response data
	echo json_encode($response_data,JSON_FORCE_OBJECT);
}

/********************************************************************************************************************************/
// Send data as HTML response
function send_html_data($response_data)
{
	// Set the JSON header
	header("Content-type: text/html");
	
	// Send response data
	echo $response_data;
}

/********************************************************************************************************************************/
// END OF FILE
/********************************************************************************************************************************/
?>