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
			
			$('#formholder').html(new_html);
		});
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