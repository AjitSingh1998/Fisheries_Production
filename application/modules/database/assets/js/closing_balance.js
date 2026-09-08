function update_balance(url, element){
	var row = element.closest('tr');		
	var data_to_send = {};
	data_to_send[csrf_token_name] = csrf_token_value;
	$.ajax({
		url : url,				
		type:'POST',
		data: data_to_send,
		async:true,
		success: function(result, textStatus, jqXHR){
			if(result.status == 'success'){
				row.removeClass('not-updated').addClass('success updated');
				element.focus();				
				element.removeClass('btn-danger update_balance').addClass('btn-warning').off('click').prop('disabled', true);				
			}else{
				element.focus();
				row.addClass('warning');
				element.removeClass('btn-danger update_balance').addClass('btn-warning');//.off('click').prop('disabled', true);
			}				
			row.next('tr').find('button').click();			
		},
		error: function(errorMSG){alert(errorMSG);}
	});
}

$(document).ready(function(e) {
	
	$('.update_closing_balance').on('click', function(){
		if(confirm('Are you sure about to update closing / opening balance !')){
			$('table#tablelist tbody tr:first-child button').trigger('click');
		}
		return false;
	});
	
	$('.update_balance').on('click', function(){
		var url = site_url + $(this).data('url');
		var $this = $(this);
		update_balance(url, $this);
	});

	$('.export_data').on('click', function(){
		//var search_key = $('#search_key').text();
		//var search_date = $('#search_date').text().replace(/\//g, "-");;
		var report_name = $('.panel-heading h3').text();
		var target = $(this).attr('data-target');
		if (typeof target !== typeof undefined && target !== false) {
			$(target).table2excel({
				filename: report_name
			});
		}else{
			$("table.table").table2excel({
				filename: report_name
			});
		}
	});
	
	$('.print_data').on('click', function(){
		var target = $(this).attr('data-target');
		if (typeof target !== typeof undefined && target !== false) {
			$(target).print();
		}else{
			$("table.table").print();
		}
	});
	
	/*if(!$.fn.dataTable.isDataTable('#tablelist') ) {
		$('#tablelist').DataTable({
			dom: 'Bfrtip',
			paging: true,
			searching: true, 
			bInfo: true, 
			"autoWidth": false, 
			"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]/*,
			buttons: [
				'excel', 'print'
			]
		});	
	}*/
});


									 
								 

