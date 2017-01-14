// frontend scripts
//console.log( soulmatch_data.options );

jQuery(function($){

	$.each(soulmatch_data.options,function(i, value){
	
		$(value.selector).matchHeight({
			byRow:value.byrow,
		});	
	
	}
	
	);


});




