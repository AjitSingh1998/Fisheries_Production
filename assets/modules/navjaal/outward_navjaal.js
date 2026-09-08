// JavaScript Document
$(document).ready(function() {
	$(".loadInIframe").on("click", function(event){											
		event.preventDefault();
		var url = $(this).attr("href");
		$.getJSON(url, null, function (data) {
			var new_html = $(data.setup_form);
			new_html.find('.datepicker').datepicker({
				dateFormat: 'dd M, yy',
				autoclose: true,
				todayHighlight: true
			});
			
			new_html.find('.select-fisherman').select2({
			  placeholder: "Select Fisherman",
			  ajax: {
				url: site_url+'navjaal/ajax_fisherman',
				dataType: 'json',
				data: function (params) {
				  return {
					q: params.term, // search term
					f_ids: function(){
						var values = new_html.find("#samiti").val();
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
			
			$('#formholder').html(new_html);
		});
	});
	
	$('body').on('change', '#samiti', function(){
		$this = $(this);
		var form = $this.closest('form');
		var val = $this.val();
		if(val != '' && val != 'undefined'){
			form.find('.select-fisherman').prop('disabled', false);
		}else{
			form.find('.select-fisherman').prop('disabled', true);
		}
	});
	
	//Add product row
	$('body').on('click', '.add_prow', function(){
		var $this = $(this);
		var pt_tbody = $this.closest('.product_tbl').find('tbody');
		var asset_tr = $('.asset_tbl tr').clone();
		pt_tbody.append(asset_tr);
	});
	
	//Remove product row
	$('body').on('click', '.remove_prow', function(){
		$(this).closest('tr').remove();
	});
	
	$('body').on('click', '.close_form', function(){
		var $this = $(this);
		swal({
			title: "Note",
			text: "Are you sure you want to close form?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, Close it!",
			closeOnConfirm: true
		}, function() {
			$this.closest('.container-fluid').remove();
		});
		e.preventDefault
	});
	
	$('body').on('click', '.save_form', function(){
		var $this = $(this);
		alert('Hello');
		return false;
		
		/*
		var my_form = $this.closest('form');
		var url = my_form.attr('action');
		datatosend = my_form.serialize();
		
		$.ajax({
			url:url, 
			type:"POST",
			data:datatosend
		}).done(function(response){
			$('#formholder').html(response.setup_form);
			if(response.status != 'undefined' && response.status == 'success'){
				$('#formholder').find('form input[type="text"]').each(function(index, element) {
                    $(element).val('');
                });
				
				swal({
					title: "Note",
					text: response.msg,
					confirmButtonColor: "#007AFF"
				});
				
			}
			if(response.status != 'undefined' && response.status == 'danger'){
				swal({
					title: "Note",
					text: response.msg,
					confirmButtonColor: "#007AFF"
				});
			}
			
		});
		*/
	});
	
});