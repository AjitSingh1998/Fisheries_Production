// JavaScript Document
$(document).ready(function() {
	$('body').off('select2:select', '.select-fisherman');
	$('body').off('click', '.add_prow');
	$('body').off('click', 'tbody#products .remove_prow');
	var form = $('form#outward_form');
	
	var select_fisherman = $('.select-fisherman');
	var fisherman_data = '';
	select_fisherman.select2({
	  placeholder: "Select Fisherman",
	  ajax: {
		url: site_url+'stocks/fisherman',
		dataType: 'json',
		data: function (params) {
		  return {
			q: params.term
		  };
		},
		processResults: function (data) {
		  fisherman_data = data.result2;
		  return {
			results: data.result1
		  };
		}
	  },
	  minimumInputLength : 1,
	  allowClear: true,
	  dropdownParent: $('.select-fisherman').parent(),
	  escapeMarkup: function (m) {
			return m;
	  }
	});
	
	select_fisherman.on('select2:select', function (e) {
		var data = e.params.data;
		$('#mg_type').val(fisherman_data[data.id].Group);
		$('#maingroup').val(fisherman_data[data.id].MainGroup);
		$('#f_cn').val(data.text);
		fisherman_data = '';
	});
	
	select_fisherman.on('select2:unselecting', function (e) {
		//console.log('unselecting');
		$('#mg_type').val('');
		$('#maingroup').val('');
		$('#f_cn').val('');
		fisherman_data = '';
	});
	
	$('input[name="outward_to"]').on('change', function(){
		var deposited_by = $(this).val();
		if(deposited_by == 'Fisherman'){
			$('.fisher_div').show();
		}else if(deposited_by == 'Group'){
			$('.fisher_div').hide();
			$('#fisherman_id').val('').trigger("change");
		}else{
			$('.fisher_div').show();
		}
	});
	
	$('.group_type_id').select2({
		dropdownParent: $('.group_type_id').parent(),
		}
	);
	
	$('.maingroup_id').select2({
		dropdownParent: $('.maingroup_id').parent(),
		}
	);
	
	$('.fisherman_id').select2({
		dropdownParent: $('.fisherman_id').parent(),
		}
	);
	
	$('.group_type_id').on('change', function(){
		$this = $(this);
		var mg_type = $this.val();
		if(mg_type != '' && mg_type != 'undefined'){
			var datatosend = {'mg_type' : mg_type};
			datatosend[csrf_token_name] = csrf_token_value;
			$.ajax({
				url : site_url+'stocks/ajax_get_maingroup', 
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
				url : site_url+'stocks/ajax_get_mg_fishers', 
				type : "POST",
				beforeSend: function(){
						show_loader($('#panel_container'));
					},
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
	
	//Add product row
	$('body').on('click', '.add_prow', function(){
		var $this = $(this);
		var asset_tr = $('table.asset_tbl tr:first').clone();
		$('tbody#products').append(asset_tr);
	});
	
	//Remove product row
	$('body').on('click', 'tbody#products .remove_prow', function(){
		var $this = $(this);
		var item_id = $(this).data('item_id');
		var fid = $(this).data('fid');
		var outid = $(this).data('outid');
		
		var action_mode = $('input[name="action_mode"]').val();
		if(item_id && outid && action_mode == 'edit'){
			var datatosend = {'item_id' : item_id, 'fid' : fid, 'outid' : outid};
			datatosend[csrf_token_name] = csrf_token_value;
			
			$.ajax({
				url : site_url+'stocks/ajax_remove_item', 
				type : "POST",
				beforeSend: function(){
					show_loader($('#panel_container'));
				},
				data : datatosend, 
				success: function(response, status, xhr){
					hide_loader($('#panel_container'));
					var ct = xhr.getResponseHeader("content-type") || "";
					if (ct.indexOf('json') > -1) {
						//If response is json
						if(response.status == 'success'){
							//Item removed
							$this.closest('tr').remove();
							calculate_price();
						}
					}else{
						//If response is not json
						hide_loader($('#panel_container'));
						alert('Invalid response, Please reload the page and try again.');
					}
					
				},
				error: function(response, status, xhr){
					hide_loader($('#panel_container'));
					alert('There is some error in page, Please reload the page and try again.');
				}
			});
		}else{
			$this.closest('tr').remove();
		}
	});
	
	//On Change Product Type
	$('body').on('change', '.pr_type', function(){
		$this = $(this);
		var tr = $this.closest('tr');
		var category = tr.find('.pr_category');
		var name = tr.find('.pr_name');
		var ptype = $this.val();
		
		if(ptype != '' && ptype != 'undefined'){
			var datatosend = {'ptype' : ptype};
			datatosend[csrf_token_name] = csrf_token_value;
			
			$.ajax({
				url : site_url+'stocks/ajax_pr_cat', 
				type : "POST",
				data : datatosend
			}).done(function(response){
				category.prop('disabled', false);
				category.html(response.data);
				name.html('<option value="">Product</option>');
				name.prop('disabled', true);
			});
		}else{
			category.prop('disabled', true);
			category.html('<option value="">No Category</option>');
			name.html('<option value="">Product</option>');
			name.prop('disabled', true);
		}
	});
	
	//On Change Prdouct Category
	$('body').on('change', '.pr_category', function(){
		$this = $(this);
		var tr = $this.closest('tr');
		var name = tr.find('.pr_name');
		
		var pcat = tr.find('.pr_type').val();
		var pcat = $this.val();
		
		if(pcat != '' && pcat != 'undefined'){
			var datatosend = {'pcat' : pcat};
			datatosend[csrf_token_name] = csrf_token_value;
			
			$.ajax({
				url : site_url+'stocks/ajax_pr_items', 
				type : "POST",
				data : datatosend
			}).done(function(response){
				name.prop('disabled', false);
				name.html(response.data);
			});
		}else{
			name.html('<option value="">No Product</option>');
			name.prop('disabled', true);
		}
	});
	
	$('body').on('change', '.pr_name', function(){
		$this = $(this);
		var tr = $this.closest('tr');
		var value = $this.val();
		if(value != ''){
			var pdata = value.split('__');
			var rate = (typeof pdata[1] == "undefined") ? 0 : pdata[1];
			tr.find('.pr_rate').val(rate);
		}
	});
	
	$('body').on('keyup', '.pr_qty, .pr_rate', function(){
		var $this = $(this);
		var tr = $this.closest('tr');
		var qty = tr.find('.pr_qty').val();
		var rate = tr.find('.pr_rate').val();
		var total = (parseFloat(qty)*parseFloat(rate));
		tr.find('.pr_total').val(total.toFixed(2));
		calculate_price();
	});
	
	function calculate_price(){
		var sub_total = 0.00;
		$('tbody#products tr').each(function(index, element) {
            var total = $(element).find('.pr_total').val();
			sub_total = sub_total+parseFloat(total);
        });
		$('.sub_total').val(sub_total.toFixed(2));
		var grand_total = sub_total;
		var cash = $('.cash_received').val();
		if(cash != '' || cash != 'undefinded'){
			grand_total = sub_total - cash;
		}
		$('.grand_total').val(grand_total.toFixed(2));
	}
	
	$('body').on('keyup', '.cash_received', function(){
		var cash = $(this).val();
		var sub_total = $('.sub_total').val();
		
		$('.grand_total').val((sub_total-cash).toFixed(2));
	});
	
	$('.datepicker').datepicker({
		dateFormat: 'dd M yy',
		autoclose: true,
		todayHighlight: true
	});
	
});