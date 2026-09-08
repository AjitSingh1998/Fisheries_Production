// JavaScript Document
$(document).ready(function() {
	$('body').off('group_type', 'change');
	
	$('.group_type').select2({
		dropdownParent: $('.group_type').parent(),
		}
	);
	
	$('.maingroup').select2({
		dropdownParent: $('.maingroup').parent(),
		}
	);
	
	$('.group_type').on('change', function(){
		$this = $(this);
		var mg_type = $this.val();
		if(mg_type != '' && mg_type != 'undefined'){
			var datatosend = {'mg_type' : mg_type};
			datatosend[csrf_token_name] = csrf_token_value;
			$.ajax({
				url : site_url+'wages/ajax_get_maingroup',
				type : "POST",
				beforeSend: function(){
						show_loader($('#panel_container'));
					},
				data : datatosend
			}).done(function(response){
				$('.maingroup').prop('disabled', false);
				$('.maingroup').val('').trigger("change");
				$('.maingroup').html(response.data);
				hide_loader($('#panel_container'));
			});
		}else{
			$('.maingroup').val('').trigger("change");
			$('.maingroup').html('<option value="" selected="selected">Select Maingroup</option>');
			$('.maingroup').prop('disabled', true);
		}
	});
	
	$('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true,
		todayHighlight: true
	});
	
});