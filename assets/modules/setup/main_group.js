// JavaScript Document
$(document).ready(function() {
	$("body").on("click", '.loadInIframe', function(event){											
		event.preventDefault();
		var url = $(this).attr("href");
		$.getJSON(url, null, function (data) {
			$('#formholder').html(data.setup_form);
			$('#formholder input[type="text"]').first().focus();
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
		var my_form = $this.closest('form');
		var url = my_form.attr('action');
		datatosend = my_form.serialize();
		//alert(url);
		$.ajax({
			url:url, 
			type:"POST",
			data:datatosend
		}).done(function(response){
			console.log(response);
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
				$('.gc-container .gc-refresh').trigger('click');				
			}
			if(response.status != 'undefined' && response.status == 'danger'){
				swal({
					title: "Note",
					text: response.msg,
					confirmButtonColor: "#007AFF"
				});
			}
			
		});
	});
	
});