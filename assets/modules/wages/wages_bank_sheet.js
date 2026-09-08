$(document).ready(function(e) {	
	$('.get_wages_sheet').on('click', function(){
		var url = $(this).data('url');
		var $search_bar = $(this).closest('div.row');
		var form_data = {};
		$search_bar.find('select, input').each(function(index, element) {
			var name = $(element).attr('name');
			var val = $(element).val();
			form_data[name] = val;
		});
		form_data['sheet_for'] = $('input[name="sheet_for"]:checked').val();
		form_data[csrf_token_name] = csrf_token_value;
		
		$.ajax({
			url : site_url+url, 
			type : "POST",
			beforeSend: function(){
							show_loader($('#report_panel'));						
							if ( $.fn.dataTable.isDataTable('#data-table-grid') ) {
							  $('#data-table-grid').DataTable().destroy();
							}
						},
						
			data : form_data,
			success: function(response){
				if(response.status == 'success'){
					$('.ajax-response').html('');
					$('#sheet_head').html(response.data.thead);
					$('#sheet_body').html(response.data.tbody);
					
					$('.export_data').prop('disabled', false);
					$('#export_data').prop('disabled', false);
					$('.print_data').prop('disabled', false);						
					
					if(!$.fn.dataTable.isDataTable('#data-table-grid') ) {
						var pagination = (url == 'reports/production/ajax_samiti')? false : true;
						$('#data-table-grid').DataTable({
							/*dom: 'Bfrtip',*/
							paging: pagination,
							searching: pagination, 
							bInfo: pagination, 
							"autoWidth": false, 
							"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]/*,
							buttons: [
								'excel', 'print'
							]*/
						});	
					}
				}else{
					$('.ajax-response').html(response.message);
					$('#sheet_head').html('');
					$('#sheet_body').html('');
				}
				
				hide_loader($('#report_panel'));
			}, 
			error: function(){
				alert('There is some error, Please reload the page and try again.');
				hide_loader($('#report_panel'));
			}
		})
	});
});

									 
								 

