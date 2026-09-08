// JavaScript Document

//allow only two places after decimal
function checkDecimal(el){
	var ex = /^\d*(\.\d{0,2})?$/;
	if(ex.test(el.value)==false){
		el.value = parseInt(el.value*100)/100;
	}
}

$(document).ready(function() {	
	$('body').off('keyup', '.amount');
	
	$('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true,
		todayHighlight: true
	});
	
	$('input[name="change_for"]').on('change', function(){
		var deposited_by = $(this).val();
		if(deposited_by == 'Fisherman'){
			$('.fisher_div').show();
		}else if(deposited_by == 'Group'){
			$('.fisherman_id').val('').trigger("change");
			$('.fisher_div').hide();
		}else{
			$('.fisher_div').show();
		}
	});
	
	$('.group_type').select2();
	
	$('.maingroup_id').select2();
	
	$('.fisherman_id').select2();
	
	$('.group_type').on('change', function(){
		$this = $(this);
		var mg_type = $this.val();
		if(mg_type != '' && mg_type != 'undefined'){
			var datatosend = {'mg_type' : mg_type};
			datatosend[csrf_token_name] = csrf_token_value;
			$.ajax({
				url : site_url+'setup/ajax_get_maingroup', 
				type : "POST",
				beforeSend: function(){
						show_loader($('#panel_container'));
					},
				data : datatosend
			}).done(function(response){
				$('.maingroup_id').prop('disabled', false);
				$('.maingroup_id').val('').trigger("change");
				$('.maingroup_id').html(response.data);
				hide_loader($('#panel_container'));
			});
		}else{
			$('.maingroup_id').val('').trigger("change");
			$('.maingroup_id').html('<option value="" selected="selected">Select Maingroup</option>');
			$('.maingroup_id').prop('disabled', true);
			
			$('.fisherman_id').val('').trigger("change");
			$('.fisherman_id').html('<option value="" selected="selected">Select Fisherman</option>');
			$('.fisherman_id').prop('disabled', true);
		}
	});
	
	$('.maingroup_id').on('change', function(){
		$this = $(this);
		var maingroup_id = $this.val();
		if(maingroup_id != '' && maingroup_id != 'undefined'){
			var datatosend = {'maingroup':maingroup_id, 'f_ids':''};
			datatosend[csrf_token_name] = csrf_token_value;
			$.ajax({
				url : site_url+'setup/ajax_get_mg_fishers', 
				type : "POST",
				beforeSend: function(){show_loader($('#panel_container'));},
				data : datatosend
			}).done(function(response){
				$('.fisherman_id').prop('disabled', false);
				$('.fisherman_id').val('').trigger("change");
				$('.fisherman_id').html(response.data1);
				hide_loader($('#panel_container'));
			});
		}else{
			$('.fisherman_id').val('').trigger("change");
			$('.fisherman_id').html('<option value="" selected="selected">Select Fisherman</option>');
			$('.fisherman_id').prop('disabled', true);
		}
	});
	
	//Strict input box to accept only numeric(with or without floting point) values
	$('.strict_numeric').on("keypress keyup blur change", function (event) {
		var t_val = $(this).val($(this).val().replace(/[^0-9\.]/g,''));
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
    });
	
	$('.rate_btn').on('click', function(){
		var datatosend = $('.change_rate_form').serialize();
		var aj_url = $(this).data('url');
		$.ajax({
			url : aj_url, 
			type : "POST",
			data : datatosend,
			success: function(response, status, xhr){
				if(response.status == "success"){
					$('.group_type').val('').trigger("change");
					$('.maingroup_id').val('').trigger("change");
					$('.fisherman_id').val('').trigger("change");
					document.getElementById("change_rate_form").reset();
				}
				$('.ajax-response').html(response.message);
			},
			error: function(response, status, xhr){
				$('.ajax-response').html(response.message);
			}		
		});
	})
	
});