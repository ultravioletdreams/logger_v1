/*--------------------------------------------------------------------------------------------------------------------------------*/
// startup)charts.js - Initialise highcarts charts.
/*--------------------------------------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Start execution when document is ready
$(document).ready(function(){ startup(); });

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Initialise / Start javascript functionality here.
function startup()
{
	// Attach button events handlers
	$('#last_10').click(function(){ get_chart_data('10 minute', 1); });
	$('#last_20').click(function(){ get_chart_data('20 minute', 1); });
	$('#last_30').click(function(){ get_chart_data('30 minute', 1); });
	$('#last_hour').click(function(){ get_chart_data('1 hour' , 1); });
	$('#last_hour_2').click(function(){ get_chart_data('2 hour' , 1); });
	$('#last_hour_6').click(function(){ get_chart_data('6 hour' , 1); });
	$('#last_hour_12').click(function(){ get_chart_data('12 hour' , 1); });
	$('#last_day').click(function(){ get_chart_data('1 day'   , 1); });
	$('#last_day_2').click(function(){ get_chart_data('2 day'   , 5); });
	$('#last_day_3').click(function(){ get_chart_data('3 day'   , 10); });
	$('#last_day_4').click(function(){ get_chart_data('4 day'   , 10); });
	$('#last_day_7').click(function(){ get_chart_data('7 day' , 20); });
	$('#last_day_10').click(function(){ get_chart_data('10 day' , 50); });
	$('#last_day_20').click(function(){ get_chart_data('20 day' , 50); });
	$('#log_interval').click(function(){ get_chart_data_range(); });
	// Initialise chart
	chart_init();
	// Update chart with data
	get_chart_data('10 minute', 1);
}

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Highcharts object
var chart_obj;

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Chart initialise
function chart_init()
{
    chart_obj = new Highcharts.Chart({
        chart: { zoomType: 'x', renderTo: 'container_0', defaultSeriesType: 'spline' },
        title: { text: ''},
        xAxis: { type: 'datetime' },
        yAxis: { title: { text: 'Temp.' } }, 
        series: [{ name: 'sensor_1', data: []},{ name: 'sensor_2', data: [] }]
    });  
}

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Request chart data and update chart
function get_chart_data(data_interval,skip)
{
	chart_obj.showLoading();
	var json_request = $.getJSON("chart_update.php",{ 'ver': new Date().getTime() , 'ax' : 'last' , 'ix' : data_interval , 'sx' : skip});
	json_request.done(update_chart);
}

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Request chart data and update chart - Using a date range
function get_chart_data_range()
{
	// Get date from and to values
	var date_from = $.datepicker.formatDate( "yy-mm-dd", $('#date_from').datepicker( "getDate" ));//.formatDate("yy-mm-dd");//.datepicker("option","yy-mm-dd");
	var date_to   = $.datepicker.formatDate( "yy-mm-dd", $('#date_to'  ).datepicker( "getDate" ));
	console.log('Date From: ' + date_from + ' To: ' + date_to);
	
	var json_request = $.getJSON("chart_update.php",{ 'ver': new Date().getTime() , 'ax' : 'range' , 'df' : date_from , 'dt' : date_to });
	json_request.done(update_chart);
}

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Update chart with requested data
function update_chart(json_data)
{
	console.log('Chart Update Started...');
	//console.log(json_data.title);
	//console.log(json_data.chart_1);
	
	chart_data_1 = json_data.chart_1;
	chart_data_2 = json_data.chart_2;
	
	// Updtae chart title & subtitle
	//chart_obj.title.update({ text: json_data.summary_1});
	//chart_obj.subtitle.update({ text: json_data.summary_1 });
	$('#sensor_1').html(json_data.summary_1);
	$('#sensor_2').html(json_data.summary_2);

	// Clear series data
	chart_obj.series[0].setData([]);
	chart_obj.series[1].setData([]);
	
	add_chart_point(chart_data_1,0,0,1);
	add_chart_point(chart_data_2,1,0,1);

	// Redraw chart with new data
	chart_obj.redraw();
	chart_obj.hideLoading();
	console.log('Chart Update Finished.');
}

/*--------------------------------------------------------------------------------------------------------------------------------*/
// Add a point to a chart series
function add_chart_point(tmp_chart_data,series_id,x_index,y_index)
{
	// Temp chart point
	var x = 0, y = 0;
	
	// For each data entry create a chart point
	$.each(tmp_chart_data, function( index ) 
	{
	  // Parse point
	  x = eval(tmp_chart_data[index][x_index]);
	  y = parseFloat(tmp_chart_data[index][y_index]);
	  // Add point to chart	
	  chart_obj.series[series_id].addPoint([ x, y ],false);
	});
}
