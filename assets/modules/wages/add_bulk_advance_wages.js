
$(document).ready(function(e) {
	$('body').off('click', '.save_row');
	$('body').off('click', '.edit_row');
	$('body').off('click', '.delete_row');
	
	var fisherman_data = '';
	$('.select-fisherman').select2({
	  placeholder: "Select Fisherman",
	  ajax: {
		url: site_url+'wages/fisherman',
		dataType: 'json',
		data: function (params) {
		  return {
			q: params.term, // search term
			f_ids: function(){
				var values = $("input[name='fisherman_id']").map(function(){return $(this).val();}).get();
				return values;
			}
		  };
		},
		processResults: function (data) {
		  // Tranforms the top-level key of the response object from 'items' to 'results'
		  //console.log(data.results.result1); return '';
		  fisherman_data = data.result2;
		  return {
			results: data.result1
		  };
		}
	  },
	  minimumInputLength : 1,
	  allowClear: true
	});
	
	//Add new row
	$('.add_fisherman').on('click', function(){
		var common_date = $('input[name="common_date"]').val();
		var common_remark = $('textarea[name="common_remark"]').val();
		
		var total_wt = 1, c_fisher = $('tr'), tr = $('#advance_wages tr.editable');
		var fisher_id 	= $('.select-fisherman option:selected').val();
		var fisher_name = $('.select-fisherman option:selected').text(); // Code ( Name )
		//console.log(fisherman_data[fisher_id]);
		if(fisher_id != '' && typeof fisher_id != 'undefined' && fisher_name != '' && typeof fisher_name != 'undefined'){
			if(tr.length){
				alert('Please save the eddited fisher data before adding new.');
				tr.addClass('danger');
				tr.find('button.save_row').focus();
				return false;
			}else{
				var f_group = fisherman_data[fisher_id].Group; // Maingroup id
				var f_maingroup = fisherman_data[fisher_id].MainGroup; // Maingroup id
				var f_name = fisherman_data[fisher_id].Name; // Name			
				
				var row_asset = $('table.asset #row_asset').clone();
				row_asset.find('.f_group').val(f_group);
				row_asset.find('.f_maingroup').val(f_maingroup);
				row_asset.find('.fisherman_id').val(fisher_id);				
				row_asset.find('.f_name').val(f_name);
				row_asset.find('.fisherman_name').text(fisher_name);
				row_asset.find('input[name="date"]').val(common_date);
				row_asset.find('input[name="date"]').datepicker({format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true});
				
				row_asset.find('textarea[name="remark"]').val(common_remark);
				
				row_asset.removeClass('hidden');
				row_asset.removeAttr('id');
				$('#advance_wages').append(row_asset);
				$('.select-fisherman option:selected').remove();
				$('.select-fisherman').val('').trigger("change");
				fisherman_data = '';
			}
		}else{
			alert('Please select fisherman before clicking the ADD Button!');
			$('.select-fisherman').focus();
			return false;
		}
	});
	
	//Save row
	$('body').on('click', '.save_row', function(){
		var $this = $(this);
		var editable 	= $this.closest('tr.editable');
		var amount = editable.find('input[name="amount"]').val();
		var date = editable.find('input[name="date"]').val();
		if(amount != '' && date != ''){
			var form_data = {};
			form_data[csrf_token_name] = csrf_token_value;
			editable.find('input, textarea').each(function(index, element) {
				var $ele = $(element);
				var name = $ele.attr('name');
				var val = $ele.val();
				form_data[name] = val;
			});
			//console.log(form_data);
			$.ajax({
				type: "POST",
				url: site_url+"wages/save_bulk_advance_wages",
				data: form_data,
				error: function (xhr, ajaxOptions, thrownError) {
					alert('Response - '+ xhr.status+':  '+thrownError+' Please contact to development department.');
				},
				success: function( data ) {
						if(data.status == 'success'){
							editable.find('.action').val('edit');
							editable.find('.delete_row').attr('data-advid', data.data.advid);
							$this.text('Edit');
							$this.removeClass('btn-success save_row').addClass('btn-warning edit_row');							
							editable.find('input[type="text"], textarea').each(function(index, element) {
								var $ele = $(element);
								$ele.prop('disabled', true);
								$ele.addClass('disabled');
							});
							editable.removeClass('editable').removeClass('danger').addClass('saved');							
						}else{
							editable.addClass('danger');
							alert(data.message);
							editable.find('input[name="amount"]').focus().select();
						}
					}						
			});
			
		}else{
			editable.addClass('danger');
			alert("Please enter the data before save.");
			editable.find('input[name="amount"]').focus().select();
		}
	});
	
	//Edit row
	$('body').on('click', '.edit_row', function(){
		var $this = $(this);
		var tbody = $this.closest('#advance_wages');
		var tr_edit = tbody.find('tr.editable');
		if(!tr_edit.length){
			var editable 	= $this.closest('.saved');							
			editable.find('input[type="text"], textarea').each(function(index, element) {
				var $ele = $(element);
				$ele.prop('disabled', false);
				$ele.removeClass('disabled');
			});
			editable.removeClass('saved').addClass('editable');
			$this.text('Update');
			$this.removeClass('btn-warning edit_row').addClass('btn-info save_row');
		}else{			
			alert('Please save the current editable row before edit other row.');
			tr_edit.addClass('danger');
			tr_edit.find('input[name="amount"]').focus().select();			
		}
	});
	
	$('body').on('click', '.delete_row', function(){
		var $this = $(this);
		var tr = $this.closest('tr');
		var conf = confirm('Are you sure to delete ?');
		if(conf){
			var advid = $(this).attr('data-advid');
			var form_data = { 'advid': advid};
			form_data[csrf_token_name] = csrf_token_value;
			if(advid){
				$.ajax({
					type: "POST",
					url: site_url+"wages/delete_advance_wages",
					data: form_data,
					error: function (xhr, ajaxOptions, thrownError) {
						alert('Response - '+ xhr.status+':  '+thrownError+' Please contact to development department.');
					},
					success: function( data ) {
						if(data.status == 'success'){
							tr.remove();
						}else{
							alert(data.message);
							tr.addClass('danger');
							tr.find('input[name="amount"]').focus().select();	
						}
						//alert(data.msg);
					}
				});
			}else{
				tr.remove();
			}
		}
	});
	
	$('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true,
		todayHighlight: true
	});
	
});

	function deleteSelectedProductRow($this){
		$this.closest('tr').remove();
		var tbody = $('tbody#advance_wages');
		var g_qty_total = 0.00;
		var g_wt_total = 0.00;
		
		if(tbody.find('td.cc').length){
			tbody.find('td.cc').each(function(index, element) {
			var ele = $(element);
			var name = ele.data('name');
			var e_vl = parseFloat(ele.text());
			var column_total = 0.00;
			tbody.find('td[data-name="'+name+'"]').each(function(index, element) {
				var ele2 = $(element);
				column_total = column_total+parseFloat(ele2.text());
			});
			$('tfoot tr.g_total th.'+name).text(column_total.toFixed(2));
			if(ele.hasClass('qty')){
				g_qty_total = e_vl+g_qty_total;
			}else if(ele.hasClass('wt')){
				g_wt_total = e_vl+g_wt_total;
			}
		});
		}else{
			$('tfoot tr.g_total th.text-center').text('0.00');
		}
		
		$('tfoot tr.g_total th.gtqty').text(g_qty_total.toFixed(2));
		$('tfoot tr.g_total th.gtwt').text(g_wt_total.toFixed(2));
		
		return {'tqty':g_qty_total, 'twt':g_wt_total}
	}
