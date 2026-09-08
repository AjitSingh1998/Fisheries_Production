// JavaScript Document
$(document).ready(function() {
	$('body').off('change', '.pr_type');
	var form = $('form#product_form');
	
	//On Change Product Type
	$('body').on('change', '.pr_type', function(){
		$this = $(this);
		var category = $('.category');
		var ptype = $this.val();
		
		if(ptype != '' && ptype != 'undefined'){
			var datatosend = {'ptype' : ptype};
			datatosend[csrf_token_name] = csrf_token_value;
			
			$.ajax({
				url : site_url+'setup/products/ajax_pr_cat', 
				type : "POST",
				data : datatosend
			}).done(function(response){
				category.prop('disabled', false);
				category.html(response.data);
			});
		}else{
			category.prop('disabled', true);
			category.html('<option value="">No Category</option>');
		}
	});
	
});