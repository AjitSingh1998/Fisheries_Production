function calculateTableColumnSize(){
	var report_header = $('#header_data tr th');
	var report_body = $('#body_data tr').children();
	var report_footer = $('#footer_data tr').children();
	//console.log(report_body);
	$.each(report_header, function(index, element){
		var width = $(this).width();
		$(report_body[index]).css('width',width);
		$(report_footer[index]).css('width',width);		
	});
}

$(document).ready(function(){
	
	$('#import_form').on('submit', function(event){
		event.preventDefault();
		show_loader($('#panel_import_excel'));
		var data_id = $('#data_id').val();
		var data_type = $('#data_type').val();
		if(data_id && data_type){
			var form_data = new FormData(this);
			$.ajax({
				url: site_url + "excel_import/ajax_import_data",
				method:"POST",
				data: form_data,
				contentType:false,
				cache:false,
				processData:false,
				error: function(error) {
					alert('Error: ' + error.status+ ' - '+ error.statusText);
					hide_loader($('.modal_panel_container'));
					$('.modal_panel_container .panel').removeClass('load1 csspinner');
				},
				success:function(response, textStatus, xhr){
					var ct = xhr.getResponseHeader("content-type") || "";
					if (ct.indexOf('json') > -1) {
						if(response.status == 'success'){
							$('#header_data').html(response.header_data);
							$('#body_data').html(response.body_data);
							$('#footer_data').html(response.footer_data);
							$('.fileupload-exists').trigger('click');
							$('#file_path').val(response.file_path);
							$('#action_mode').val('save');
							$('#save_excel_data').removeClass('hidden');
						}else if(response.status == 'error'){
							$('#body_data').html(response.message);
							$('.fileupload-exists').trigger('click');
							$('#save_excel_data').addClass('hidden');
							$('#file_path').val('');
							$('#action_mode').val('check');
						}else{
							alert(response.message);
							$('.fileupload-exists').trigger('click');
							$('#save_excel_data').addClass('hidden');
							$('#file_path').val('');
							$('#action_mode').val('check');
						}
						calculateTableColumnSize();
					}else{
						alert('Invalid response type, Please reload the page and try again.');
					}
					hide_loader($('#panel_import_excel'));
				}
			});
		}else{
			alert('Invalid Request: Please reload the page and try again!');			
		}
	});
	
	$('#save_excel_data').on('click', function(){
		show_loader($('#panel_import_excel'));
		var data_id = $('#data_id').val();
		var data_type = $('#data_type').val();
		var action_mode = $('#action_mode').val();
		var file_path = $('#file_path').val();
		
		var form_data = {data_id:data_id, data_type:data_type, action_mode:action_mode, file_path:file_path};
		form_data[csrf_token_name] = csrf_token_value;
		$.ajax({
			url: site_url + "excel_import/ajax_import_data",
			method:"POST",
			data: form_data,
			error: function(error) {
				alert('Error: ' + error.status+ ' - '+ error.statusText);
				hide_loader($('.modal_panel_container'));
				$('.modal_panel_container .panel').removeClass('load1 csspinner');
			},
			success:function(response, textStatus, xhr){
				var ct = xhr.getResponseHeader("content-type") || "";
				if (ct.indexOf('json') > -1) {
					if(response.status == 'success'){
						if(!confirm(response.message+' Do you want to import more data?')){
							location.reload();
						}else{
							$('#header_data').html("");
							$('#body_data').html("");
							$('#footer_data').html("");
							$('#file_path').val("");
							$('#action_mode').val('check');
							$('#save_excel_data').addClass('hidden');
						}
					}else{
						$('#header_data').html("");
						$('#body_data').html(response.message);
						$('#footer_data').html("");
						$('#file_path').val("");
						$('#action_mode').val('check');
						$('#save_excel_data').addClass('hidden');
					}
				}else{
					alert('Invalid response type, Please reload the page and try again.');
				}
				hide_loader($('#panel_import_excel'));
			}
		});
	});
	
});