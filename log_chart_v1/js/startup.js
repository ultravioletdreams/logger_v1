/*--------------------------------------------------------------------------------------------------------------------------------*/
// startup.js - Initialise javascript.
/*--------------------------------------------------------------------------------------------------------------------------------*/

var chart_0;
var last_entry_id = 0; // Track log entry id
var same_entry_count = 0;

// Run startup when document is ready
$(document).ready(function(){ startup(); });

// Startup function
function startup()
{
	// Initialise chart
	chart_init(chart_0,'container_0','SHT15 Temperature Sensor 1 - Last 150 entries - 1');
	
	// Update charts
	get_data_init();
	setTimeout(start_update,2000);
}	

// Start update timer after 2 seconds
function start_update()
{
	setInterval(get_data_live,2000);
}

// Chart initialise
function chart_init(chart_obj,html_target,chart_title)
{
    chart_obj = new Highcharts.Chart({
        chart: { zoomType: 'x', renderTo: html_target, defaultSeriesType: 'spline' },
        title: { text: chart_title},
        xAxis: { type: 'datetime' },
        yAxis: { title: { text: 'Temp.' } },
        series: [{ name: 'sensor_1', data: [] }]
    });  

console.log(chart_obj.title.textStr);

chart_0 = chart_obj;
chart_obj = false;	
}

// Request chart data
function get_data_init()
{
	var tmp_post = $.getJSON("php/chart_update.php",{ 'ver': new Date().getTime() , 'ax' : 'last' });
	tmp_post.done(update_chart_init);
}

function get_data_live()
{
	var tmp_post = $.getJSON("php/chart_update.php",{ 'ver': new Date().getTime() , 'ax' : 'live' });
	tmp_post.done(update_chart_live);
}

function update_chart_live(data)
{		
	// Debug data
	console.log(data.entry_id);
	
	// Check if entry is different or don't update
	if(last_entry_id != data.entry_id)
	{	
		// Record current log entry id as last.
		last_entry_id = data.entry_id;
		same_entry_count = 0;
		
		// Get shift amount chart series data.
		var series = chart_0.series[0],
		shift = series.data.length > 120; // shift if the series is longer than 20
		console.log(series.data.length + ' : ' + shift);

		// Parse JSON for Highcharts
		var x = Date.UTC(data.year,data.month-1,data.day,data.hour,data.minute,data.second);
		console.log("DATE FORMAT: " + x);
		var y = parseFloat(data.temp);

		// Add it
		chart_0.series[0].addPoint([ x, y ],true,shift);
	}
	else
	{
		same_entry_count++;
		console.log("SAME ENTRY: " + same_entry_count);
	}
}

function update_chart_init(data)
{		
	// Debug data
	//console.log("YEAR: " + data);
	var x = 0, y = 0;

	// Add it
	$.each(data, function( index ) 
	{
	  console.log( index + ": " + data[index][0] + ' : ' + data[index][1]);
	  x = eval(data[index][0]);
	  y = parseFloat(data[index][1]);
	  console.log('X: ' + x + ' Y: ' + y);
	  chart_0.series[0].addPoint([ x, y ],false);
	});

	chart_0.redraw();

	
}