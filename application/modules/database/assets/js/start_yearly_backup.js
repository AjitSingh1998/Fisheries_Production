var SetupWebsite = function(){
   	var process='continue', message = 'Processing <img src="'+base_url+'database/assets/images/loading.gif" style="width:100px;" class="img img-responsive">';
   	var start_table = $('#all_tables tr:eq(0)').attr('id');
   	var i = 0;
	var new_db_name = $('#new_db_name').val();
   	var websetupprocess = function(next_table, next_process){
		i++;
		var datatosend = {};
		var table 					= (next_table) ? next_table: start_table;
		datatosend['i']				= i;
		datatosend['table'] 		= table;
		datatosend['new_db_name']	= new_db_name;
		datatosend['process'] 		= (next_process) ? next_process: process;
		datatosend[csrf_token_name] = csrf_token_value;
		jQuery.ajax({
			url: site_url+"database/ajax_backup_db",
			async: true, 
			type: "POST",
			data: datatosend,
			beforeSend: function(){
				$('#'+table).find('.status').html(message);
			},
			success:function(response){
				var status 				= response.status;
				var records_backup 		= response.records_backup;
				var previous_table 		= response.previous_table;
				var previous_table_msg 	= response.previous_table_msg;
				var next_table 			= response.next_table;
				var next_process 		= response.next_process;
				var response_msg 		= response.message;
				
				if(status == "success"){
					$('#'+previous_table).find('.records_backup').html(records_backup);
					$('#'+previous_table).find('.status').html(previous_table_msg);
					$('#'+previous_table).find('.response').html(response_msg);					
					if(i<=32){
						websetupprocess(next_table, 'continue');	
					}
				}
			},
			error: function( data, status, error ){
				 alert('Some error has been occurred during process, Please reload the page!');
			}
		}); 
	}
	
    return {
        //main function to initiate template pages
        init: function () {
            websetupprocess();
        }
    };
}();

jQuery(document).ready(function(){
	SetupWebsite.init();	
});

