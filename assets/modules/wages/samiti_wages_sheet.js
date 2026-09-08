// JavaScript Document

$(document).ready(function() {
	hide_loader($('#loader'));
	
	$('body').off('keyup', '.gld_amount');
	$('body').off('keyup', '.ald_amount');
	$('body').off('click', '.save_wage_row');
	$('body').off('click', '.edit_wage_row');
	
	$('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true,
		todayHighlight: true
	});
	
	$('.search_samiti_wages').on('click', function(){
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var maingroup = $('#maingroup').val();
		
		var datatosend = {'from_date' : from_date, 'to_date' : to_date, 'maingroup' : maingroup};
		datatosend[csrf_token_name] = csrf_token_value;
		$.ajax({
			url : site_url+'wages/ajax_samiti_wages_item', 
			type : "POST",
			beforeSend: show_loader($('#loader')),
			data : datatosend,
			success: function(response){
						if(response.status == 'success'){
							//console.log(response.data);
							$('tbody#wages_list').html(response.data);
							$('.export_data').prop('disabled', false);
							$('.print_data').prop('disabled', false);
							$('.ajax-response').html('');
						}else{
							$('.ajax-response').html(response.message);
							$('tbody#wages_list').html('');
						}
						hide_loader($('#loader'));
					},
			error: function(){alert('There is some error please reload the page and try again'); hide_loader($('#loader'));}
		})
	});
	
	$('body').on('click', '.save_wage_row', function(){
		show_loader($('#loader'));		
		var $this 			= $(this);
		var wages_id		= $('.wages_id').val();
		var from_date 		= $('#from_date').val();
		var to_date 		= $('#to_date').val();
		var editable 		= $this.closest('tr');
		var maingroup 		= $('#maingroup').val();
		var maingroup_name 	= $('#maingroup option:selected').text();
		
		var form_data 		= {'wages_id': wages_id, 'from_date': from_date, 'to_date': to_date};
		form_data[csrf_token_name] = csrf_token_value;
		var remaining_grp_liability = editable.find('.grp_liab').text();
		form_data['remaining_grp_liability'] = remaining_grp_liability;
		editable.find('input').each(function(index, element) {
			var $ele = $(element);
			var name = $ele.attr('name');
			var val = $ele.val();
			form_data[name] = val;
		});
					
		$.ajax({
			type: "POST",
			url: site_url+"wages/save_samiti_wages_row",
			data: form_data,
			error: function (xhr, ajaxOptions, thrownError) {
				alert('Response - '+ xhr.status+':  '+thrownError+' Please contact to development department.');
				hide_loader($('#loader'));
			},
			
			success: function( data ) {
				//console.log(data);
				if(data.status == 'success'){
					
					editable.find('.mode').val(data.data.mode);
					editable.find('.item_id').val(data.data.item_id);
					$('.wages_id').val(data.data.wages_id);
					
					$this.text('Edit');
					$this.removeClass('btn-success save_wage_row').addClass('btn-warning edit_wage_row');
					
					editable.find('input').each(function(index, element) {
						var $ele = $(element);
						$ele.prop('disabled', true);
						$ele.addClass('disabled');
					});
					editable.removeClass('editable').removeClass('danger').addClass('saved');
					
					//Disable input, select and button on success
					$('#from_date').remove();
					$('.fdate_grp').append('<input type="text" id="from_date" readonly="readonly" class="disabled" value="'+from_date+'" />');
					$('#to_date').remove();
					$('.tdate_grp').append('<input type="text" id="to_date" readonly="readonly" class="disabled" value="'+to_date+'" />');
					$('#maingroup').empty().append('<option value="'+maingroup+'">'+maingroup_name+'</option>');
					$('.search_wages').prop('disabled', true);
					
				}else{
					editable.find('.mode').val(data.data.mode);
					editable.find('.item_id').val(data.data.item_id);
					$('.wages_id').val(data.data.wages_id);
					
					editable.addClass('danger');
					$('.ajax-response').html(data.message).focus();
					//alert(data.message);
				}
				hide_loader($('#loader'));
			}						
		});
	});
	
	//Edit row
	$('body').on('click', '.edit_wage_row', function(){
		var $this = $(this);
		var editable 	= $this.closest('tr');
		editable.find('input').each(function(index, element) {
			var $ele = $(element);
			$ele.prop('disabled', false);
			$ele.removeClass('disabled');
		});
		//editable.removeClass('saved').addClass('editable');
		$this.text('Update');
		$this.removeClass('btn-warning edit_wage_row').addClass('btn-success save_wage_row');
		editable.find('.final_wages').addClass('disabled');
		
	});
	
	//On Enter group liability deduction
	$('body').on('keyup', '.gld_amount', function(){
		var tr = $(this).closest('tr');
		var gld_amount = parseFloat($(this).val() =='' ? 0 : $(this).val());
		var grp_liab = parseFloat(tr.find('input[name="group_liability"]').val());
		var net_amount = parseFloat(tr.find('input[name="net_amount"]').val() == '' ? 0 : tr.find('input[name="net_amount"]').val());
		//console.log(gld_amount);
		if(!isNaN(gld_amount)){
			//console.log('GLD');
			var ald_amount = parseFloat(tr.find('input[name="ald_amount"]').val() =='' ? 0 : tr.find('input[name="ald_amount"]').val());
			tr.find('.final_wages').val(net_amount-(gld_amount+ald_amount));
			tr.find('.grp_liab').html(grp_liab-gld_amount);
		}/*else{
			$(this).val(0);
		}*/
	});
	
	//On Enter advance liability deduction
	$('body').on('keyup', '.ald_amount', function(){
		var tr = $(this).closest('tr');
		var ald_amount = parseFloat($(this).val() =='' ? 0 : $(this).val());
		var adv_liab = parseFloat(tr.find('input[name="advance_wages"]').val());
		var net_amount = parseFloat(tr.find('input[name="net_amount"]').val() == '' ? 0 : tr.find('input[name="net_amount"]').val());
		//console.log(ald_amount);
		if(!isNaN(ald_amount)){
			//console.log('ALD');
			var gld_amount = parseFloat(tr.find('input[name="gld_amount"]').val() =='' ? 0 : tr.find('input[name="gld_amount"]').val());
			tr.find('.final_wages').val(net_amount-(gld_amount+ald_amount));
			tr.find('.adv_liab').html(adv_liab-ald_amount);
		}/*else{
			$(this).val(0);
		}*/
	});
	
	$('body').on('click', '.export_data', function(){
		var pid = $(this).data('pid');
		var fname = $(this).data('fname');
		$(pid).table2excel({
			filename: fname
		});
	});
	
	$('body').on('click', '.print_data', function(){
		var pid = $(this).data('pid');
		$(pid).print({noPrintSelector: ".no-print"});
	});
	
	//Strict input box to accept only numeric(with floting point) values
	$('body').on("keypress keyup blur", '.gld_amount, .ald_amount', function (event) {
		var t_val = $(this).val($(this).val().replace(/[^0-9\-\.]/g,''));
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57) && event.which != 45) {
			event.preventDefault();
		}
    });
	
});