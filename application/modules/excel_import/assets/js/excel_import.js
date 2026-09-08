

$(document).ready(function(){

	//load_data();

	function load_data()
	{
		$.ajax({
			url:"<?php echo site_url(); ?>excel_import/fetch",
			method:"POST",
			success:function(data){
				$('#customer_data').html(data);
			}
		})
	}
	
	$('#download').on('click', function(event){
		var data_type = $('#data_type').val();
		if(data_type){
		
		}else{
			alert('Select Data Type');
			$('#data_type').focus();
		}
	});

	$('#import_form').on('submit', function(event){
		event.preventDefault();
		var data_type = $('#data_type').val();
		if(data_type){
			var form_data = new FormData(this);
			$.ajax({
			url: site_url + "excel_import/import",
			method:"POST",
			data: form_data,
			contentType:false,
			cache:false,
			processData:false,
			success:function(response){
				if(response.status == 'success'){
					$('#header_data').html(response.header_data);
					$('#body_data').html(response.body_data);
					$('#footer_data').html(response.footer_data);
					$('#inputGroupFile01').val('');
				}else{
					alert(response.message);
				}
				//load_data();
				//alert(data);
			}
		});
		}else{
			alert('Select Data Type');
			$('#data_type').focus();
		}
	});

});