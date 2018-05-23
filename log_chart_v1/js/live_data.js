/*--------------------------------------------------------------------------------------------------------------------------------*/
// live_data.js - Get live data from the db
/*--------------------------------------------------------------------------------------------------------------------------------*/
var live_data = true;
var countdown = 5;
var stale_count = 0;
var last_entry_id = 0;
var t1 = 0;
var t2 = 0;
var t3 = 0;

var last_series_1 = 0;
var last_series_2 = 0;

// Start execution when document is ready
$(document).ready(function(){ 

	$("#toggle_live").on('click',toggle_live);
	console.log("live_data - Start");
	// Toggle live on at start
	toggle_live();
});

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Update chart with live data
function update_chart_live()
{
	var get_request = $.get("chart_update.php",{ 'ver': new Date().getTime() , 'ax' : 'live_chart' });
	get_request.done(function(data){
		
		console.log(data.series_1.entry_id);
		// Check last entry id's
		if(last_series_1 != data.series_1.entry_id)
		{
			last_series_1 = data.series_1.entry_id;
			// Update series 1
			add_chart_point_live(chart_obj,0,data.series_1.arduino_t_stamp,data.series_1.temperature);
		}
		
		if(last_series_2 != data.series_2.entry_id)
		{
			last_series_2 = data.series_2.entry_id;
			// Update series 1
			add_chart_point_live(chart_obj,1,data.series_2.arduino_t_stamp,data.series_2.temperature);
		}
	//
	});
}

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Add a single point to a chart series
function add_chart_point_live(tmp_chart_data,series_id,val_x,val_y)
{
	// Temp chart point
	var x = 0, y = 0;

	// Parse point
	x = eval(val_x);
	y = parseFloat(val_y);
	// Add point to chart	
	chart_obj.series[series_id].addPoint([ x, y ],1,true);
}

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Update live data table
function update_live_data()
{
	var get_request = $.get("chart_update.php",{ 'ver': new Date().getTime() , 'ax' : 'live_table' });
	get_request.done(function(data){
		countdown = 5;
		// Update data
		//$('#live_data').html(data.entry_id + ":" + data.arduino_t_stamp + " : " + data.temperature);
		$('#live_data').html(data);
		
		// Get current entry_id
		var current_entry_id = $('#entry_id').html();
		//console.log('Current Entry: ' + current_entry_id);
		//console.log('Last    Entry: ' + last_entry_id);
		
		// Check if it's not the same as the last entry we recieved?
		if(current_entry_id != last_entry_id)
		{
			// Save the current entry id as the last
			last_entry_id = current_entry_id;
			// Reset stale count
			stale_count = 0;
			$('#stale_count').html(stale_count);
		}
		else
		{
			// Update stale counter
			stale_count++;
			$('#stale_count').html(stale_count);
		}
		
	});
}

function update_countdown()
{
	$('#update_countdown').html(countdown);
	countdown = countdown - 1;
}

// Turn live data on and off
function toggle_live()
{
	console.log("Toggle live");
	if(live_data)
	{
		console.log("True");
		// Update live data first and set timer
		update_live_data();
		t1 = setInterval(update_live_data,5000);
		t2 = setInterval(update_countdown,1000);
		t2 = setInterval(update_chart_live,5000);
		// Set button label
		$("#toggle_live").html('Turn Live Data - OFF');
		live_data = false;
	}
	else
	{
		console.log("False");
		clearInterval(t1);
		clearInterval(t2);
		clearInterval(t3);
		$("#toggle_live").html('Turn Live Data - ON');
		$('#live_data').html('');
		live_data = true;
	}
	
}