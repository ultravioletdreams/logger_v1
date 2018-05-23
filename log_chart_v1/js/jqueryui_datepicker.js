/*--------------------------------------------------------------------------------------------------------------------------------*/
// JQuery UI initialisation
/*--------------------------------------------------------------------------------------------------------------------------------*/
  
  // Init date picker 1 - From 
  $( function() {
       $( "#date_from" ).datepicker().datepicker("option","dateFormat","yy-mm-dd");
	   $( "#date_to" ).datepicker().datepicker("option","dateFormat","yy-mm-dd");
	   
	   // Link to date minimum to from date.
	   $( "#date_from" ).on("change", function() {
		   
			$( "#date_to" ).datepicker( "option", "minDate", $( this ).datepicker("getDate"));   
		
	   });
	   
	   

	   
	   });   
	   
	   
	   
  
