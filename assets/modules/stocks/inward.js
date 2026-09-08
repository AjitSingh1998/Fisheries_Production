// JavaScript Document
$(document).ready(function() {
	$('body').off('click', '.add_prow');
	var form = $('form#outward_form');
	
	//Add product row
	$('body').on('click', '.add_prow', function(){
		var $this = $(this);
		var asset_tr = $('table.asset_tbl tr:first').clone();
		$('tbody#products').append(asset_tr);
	});
	
	//Remove product row
	$('body').on('click', '.remove_prow', function(){
		$(this).closest('tr').remove();
		calculate_price();
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
		var sub_total = 0.0;
		$('tbody#products tr').each(function(index, element) {
            var total = $(element).find('.pr_total').val();
			sub_total = sub_total+parseFloat(total);
        });
		$('.sub_total').val(sub_total);
		var grand_total = sub_total;
		var cash = $('.cash_received').val();
		if(cash != '' || cash != 'undefinded'){
			grand_total = sub_total - cash;
		}
		$('.grand_total').val(grand_total);
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