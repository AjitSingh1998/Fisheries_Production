var notLocked = true;
$.fn.animateHighlight = function(highlightColor, duration) {
	var highlightBg = highlightColor || "#FFFF9C";
	var animateMs = duration || 1500;
	var originalBg = this.css("backgroundColor");
	if (notLocked) {
		notLocked = false;
		this.stop().css("background-color", highlightBg)
			.animate({backgroundColor: originalBg}, animateMs);
		setTimeout( function() { notLocked = true; }, animateMs);
	}
};	

var Custom = function () {
    var closeSidebar = function () {
		$(document).on('click', '.close-sidebar', function(){
			var $this = $(this);
			var $data = $this.data();
			var close_element = $data.closeid;
			var expand_element = $data.expandid;
							
			if(close_element.length){
				$('#'+close_element).remove();
			}else{
				alert('Close element id is not defined!');
			}
			if(expand_element.length){
				var removeclass = $data.removeclass;
				var addclass = $data.addclass;
				if(removeclass.length && addclass.length){
					$('#'+expand_element).removeClass(removeclass);
					$('#'+expand_element).addClass(addclass);
				}else{
					alert('Expand element remove class and add class is missing!');
				}
				
			}
		});	
	};
	
	var setBackupDatabase = function(){
		$('#business_database').on('change', function(){
			var selected_db = $(this).val();
			var data_to_send = {db_name:selected_db};
			data_to_send[csrf_token_name] = csrf_token_value;
			$.ajax({
				url: site_url + 'database/set_backup_db',
				data: data_to_send, 
				type:'POST',
				success: function(result, status, jqXHR){
					//alert(result.extramsg);
					if(result.status === 'success'){
						location.reload();
					}
										
					var default_db = $("#database_container select").data('ddb'); 
					$("#database_container select").val(default_db);
					
					var message = '<div class="alert alert-'+ result.status +'"><button data-dismiss="alert" class="close">Ã—</button><i class="fa fa-check-circle"></i> '+result.message+'</div>';
					$.toaster({settings:{timeout:10000,toast:{template:message}}, message:''});
				},
				error: function(errorMSG){alert(errorMSG);}
			
			});
			
		});
	};
	
	var animateHighlight = function(){
		if($("#database_container").length){
			setInterval(function(){
				var default_db = $("#database_container select").data('ddb'); 
				var selecteddb = $("#database_container select").val();
				if(selecteddb != default_db){
					$("#database_container").animateHighlight("#dd0000", 3000);
				}
			}, 5000);
		}
	};
		
    return {
        //main function to initiate template pages
        init: function () {
            closeSidebar();
			setBackupDatabase();
			animateHighlight();
        }
    };
}();

$(document).ready(function(){
	Custom.init();
	$(".open_aside_modal").on("click", function(event){
		event.preventDefault();
		
		var $this = $(this);
		var modal = $('.modal');
		
		var urll = $this.data("url");
		var mclass = $this.data("class");
		var style = $this.data("style");
		
		if(style == 'top'){
			modal.removeClass('horizontal right left bottom').addClass('vertical top');
			$('#common-modal-asset .modal-body').css('cssText', 'max-height:568px !important;');
		}else if(style == 'right'){
			modal.removeClass('vertical top left bottom').addClass('horizontal right');
			$('#common-modal-asset .modal-body').css('cssText', 'max-height:500px !important;');
		}else if(style == 'bottom'){
			modal.removeClass('horizontal right left top').addClass('vertical bottom');
			$('#common-modal-asset .modal-body').css('cssText', 'max-height:568px !important;');
		}else if(style == 'left'){
			modal.removeClass('vertical top right bottom').addClass('horizontal left');
			$('#common-modal-asset .modal-body').css('cssText', 'max-height:500px !important;');
		}else{
			modal.removeClass('vertical horizontal top bottom left right');
			$('#common-modal-asset .modal-body').css('cssText', '');
		}
		
		modal.addClass(mclass);
		show_loader($('.modal_panel_container'));
		$('#common-modal-asset .modal-body').empty();
		$('#common-modal-asset').modal({ backdrop: 'static', keyboard: false });
		$.getJSON(urll, null, function (response) {
			
			$('#common-modal-asset .modal-title').addClass('text-center').html(response.page_title);
			$('#common-modal-asset .modal-body').html(response.setup_form);
			$('#common-modal-asset .modal-footer').show();
			$('.ajax-response').html('');
			hide_loader($('.modal_panel_container'));	
							
		}).error(function(error) {
			alert(error.status+ ' - '+ error.statusText);
			hide_loader($('.modal_panel_container'));
			$('#common-modal-asset').modal('hide');
		});
	});
});