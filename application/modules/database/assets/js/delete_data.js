function delete_data(ele){
		
	var tr = ele.closest('tr');
	tr.find('td.response span.loader').html('<img src="'+site_url+'database/assets/images/loading.gif" class="img img-responsive" style="width:100px;"/>');
	show_loader($('#panel-container'));
	//ele.prop('disable', true);
	//ele.off('click');
	
	var backup_id = ele.data('bid');
	var table = ele.data('table');
	var url = ele.data('url');
	if(table != '' && backup_id != ''){
		var data_to_send = {backup_id: backup_id, table_name:table};
		data_to_send[csrf_token_name] = csrf_token_value;
		$.ajax({
			url : site_url+url,
			data: data_to_send,
			type:'POST',
			async:true,
			beforeSend: function(){},			
			success: function(data){
										
					if(data.status == 'success'){
						tr.find('td.status').html(data.status_icon);
						tr.find('td.action').html(data.action_button);
						tr.find('td.response').html(data.message);
						trigger_backup();											
					}else{
						ele.prop('disable', false);
						tr.find('td.response span.text-message').html(data.message);
					}
					
					hide_loader($('#panel-container'));
								
			},
			error: function(error_msg){
				hide_loader($('#panel-container'));
				ele.prop('disable', false);
				tr.find('td.response').html(error_msg);
			}
		});
	}else{
		alert('Please reload the page, backup process and table not set!');
	}	
}

function trigger_backup(){
	var save_backup = $('#tablelist tbody tr button.delete_data');
	if(save_backup.length > 0){
		$.each(save_backup, function(key, value) {
			if(key === 0) {
				$(this).trigger('click');
				return false;
			}
		});
	}else{
		if(confirm('All table successfully imported, Are you sure about to process next step?')){
			var bid = $('#database_backup_id').val();
			window.location.href =site_url+'database/yearly_backup/fisherman_balance/'+bid;
		}
	}	
}

$(document).ready(function(e) {    
	$('tbody').on('click', '.delete_data', function(){
		var $this = $(this);
		delete_data($this);
	});
	//trigger_backup();
});