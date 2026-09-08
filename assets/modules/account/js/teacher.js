var Teacher = function () {	
	var runUserGroupValidatorStep1 = function (){
        var formBD = jQuery('.usergroup-form');
        var errorHandlerBD = jQuery('.errorHandler', formBD);
        var successHandlerBD = jQuery('.successHandler', formBD);
		formBD.validate({
			errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.attr("type") == "radio" || element.attr("type") == "checkbox") { 
				// for chosen elements, need to insert the error after the chosen container
                    error.insertAfter(jQuery(element).closest('.form-group').children('div').children().last());
                } else if (element.attr("name") == "dd" || element.attr("name") == "mm" || element.attr("name") == "yyyy") {
                    error.insertAfter(jQuery(element).closest('.form-group').children('div'));
                } else {
                    error.insertAfter(element);
                    // for other inputs, just perform default behavior
                }
            },
			ignore: "",
            rules: {
                user_group: {
                    required: true
                }
            },
			messages: {	
				user_group: {
					required: "Please select who you are ?.",
				}
			},            
			
			highlight: function (element) {
                jQuery(element).closest('.help-block').removeClass('valid');
                // display OK icon
               jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                jQuery(element).closest('.form-group').removeClass('has-error');
                // set error class to the control group
            },
            success: function (label, element) {
                label.addClass('help-block valid');
                // mark the current input as valid and display OK icon
               jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
            },
			invalidHandler: function (event, validator) { //display error alert on form submit
                successHandlerBD.hide();
                errorHandlerBD.show();
            },
            submitHandler: function (form) {
                successHandlerBD.show();
                errorHandlerBD.hide();
                form.submit();
            }
        });
    };	
	var runUserBasicInfoValidatorStep2 = function (){
        var formBD = jQuery('.basicinfo-form');
        var errorHandlerBD = jQuery('.errorHandler', formBD);
        var successHandlerBD = jQuery('.successHandler', formBD);
		formBD.validate({
			errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.attr("type") == "radio" || element.attr("type") == "checkbox") { 
				// for chosen elements, need to insert the error after the chosen container
                    error.insertAfter(jQuery(element).closest('.form-group').children('div').children().last());
                } else if (element.attr("name") == "dd" || element.attr("name") == "mm" || element.attr("name") == "yyyy") {
                    error.insertAfter(jQuery(element).closest('.form-group').children('div'));
                } else {
                    error.insertAfter(element);
                    // for other inputs, just perform default behavior
                }
            },
			ignore: "",
            rules: {
                full_name: {
                    required: true
                },
                email: {
                    required: true
                },
                phone: {
                    required: true
                },
                password: {
                    required: true
                }
            },
			messages: {	
				full_name: {
					required: "Please enter your full name!",
				},
				email: {
					required: "Please enter your valid email!",
				},
				phone: {
					required: "Please enter your working phone number!",
				},
				password: {
					required: "Please enter password!",
				}
			},            
			
			highlight: function (element) {
                jQuery(element).closest('.help-block').removeClass('valid');
                // display OK icon
               jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                jQuery(element).closest('.form-group').removeClass('has-error');
                // set error class to the control group
            },
            success: function (label, element) {
                label.addClass('help-block valid');
                // mark the current input as valid and display OK icon
               jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
            },
			invalidHandler: function (event, validator) { //display error alert on form submit
                successHandlerBD.hide();
                errorHandlerBD.show();
            },
            submitHandler: function (form) {
                successHandlerBD.show();
                errorHandlerBD.hide();
                form.submit();
            }
        });
    };
	var runUserServicetypeValidatorStep3 = function (){
        var formBD = jQuery('.servicetype-form');
        var errorHandlerBD = jQuery('.errorHandler', formBD);
        var successHandlerBD = jQuery('.successHandler', formBD);
		formBD.validate({
			errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.attr("type") == "radio" || element.attr("type") == "checkbox") { 
				// for chosen elements, need to insert the error after the chosen container
                    error.insertAfter(jQuery(element).closest('.form-group').children('div').children().last());
                } else if (element.attr("name") == "dd" || element.attr("name") == "mm" || element.attr("name") == "yyyy") {
                    error.insertAfter(jQuery(element).closest('.form-group').children('div'));
                } else {
                    error.insertAfter(element);
                    // for other inputs, just perform default behavior
                }
            },
			ignore: "",
            rules: {
                servicetype: {
                    required: true
                }
            },
			messages: {	
				servicetype: {
					required: "Please select your professional service type!",
				}
			},            
			
			highlight: function (element) {
                jQuery(element).closest('.help-block').removeClass('valid');
                // display OK icon
               jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                jQuery(element).closest('.form-group').removeClass('has-error');
                // set error class to the control group
            },
            success: function (label, element) {
                label.addClass('help-block valid');
                // mark the current input as valid and display OK icon
               jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
            },
			invalidHandler: function (event, validator) { //display error alert on form submit
                successHandlerBD.hide();
                errorHandlerBD.show();
            },
            submitHandler: function (form) {
                successHandlerBD.show();
                errorHandlerBD.hide();
                form.submit();
            }
        });
    };  	
 return {
        init: function () {	
			 runUserGroupValidatorStep1();
			 runUserBasicInfoValidatorStep2();
			 runUserServicetypeValidatorStep3();
        }
    };
}();

$(document).ready(function(){
	// 1
	Teacher.init();
	$('.select2').select2();
/* step 10 */
	$('.day-circle').on('click',function(){		
		if($(this).hasClass('btn-info')){					
			var day_name = $(this).closest('.form-group').find('input[type="checkbox"]').attr('id');			
			$(this).closest('.form-group.day_hours').nextAll('.form-group.break_group_'+day_name).remove();					
			$(this).parent('.btn-group').find('.day-unit').addClass('hidden');				
			$(this).removeClass('btn-info');						
			$(this).find('.glyphicon').removeClass('glyphicon-ok');
			$(this).addClass('circle-inactive');					
		//} else if($(this).hasClass('circle-inactive')){
		} else {
			$(this).removeClass('circle-inactive');
			$(this).addClass('btn-info');				
			$(this).find('.glyphicon').addClass('glyphicon-ok');
			$(this).parent('.btn-group').find('.day-unit').removeClass('hidden');
		}
	});
	
	$('.add_break_hour').click(function(){		
		$('html, body').animate({
			scrollTop: $(this).closest('.form-group.day_hours').offset().top - 90
		}, 500);		
		
		var parent_el = $(this).closest('.form-group');
		var $cloned = parent_el.clone();
		var day_name = $cloned.find('input[type="checkbox"]').attr('id');
		$cloned.removeClass('day_hours').addClass('break_group_'+day_name);
		
		$cloned.find('.'+day_name+'_fh').attr('name',day_name+'[break][from_hour][]');
		$cloned.find('.'+day_name+'_fm').attr('name',day_name+'[break][from_min][]');
		$cloned.find('.'+day_name+'_fampm').attr('name',day_name+'[break][from_ampm][]');
		
		$cloned.find('.'+day_name+'_th').attr('name',day_name+'[break][to_hour][]');
		$cloned.find('.'+day_name+'_tm').attr('name',day_name+'[break][to_min][]');
		$cloned.find('.'+day_name+'_tampm').attr('name',day_name+'[break][to_ampm][]');
		
		$cloned.find('input[type="checkbox"]').remove();		
		$cloned.find('.btn-group label.day-circle').remove();
		$cloned.find('.btn-group label.day-dr').remove();
		
		// unhide hidden fields in add more
		$cloned.find('.hidden_field').removeClass('hidden_field');	
		
		var break_input = $('<label class="break_name"></label>');
		break_input.append('<input type="text" placeholder="Title" name="'+day_name+'[break][name][]" class="form-control" />');		
		$cloned.find('.btn-group').before(break_input);			
		
		var remove_button = $('<a href="javascript:void(0);" class="text-danger"><i class="fa fa-trash"></i> Remove</a>');
		$cloned.find('.btn-group label:last-child').html(remove_button);
		
		remove_button.click(function(){
			$(this).closest('.form-group').remove();
		});
		
		$cloned.find('select').each(function(index, element) {
			if(index==1 || index==4){
				$(this).val('00');
			}
			if(index==2 || index==5){
				$(this).val('pm');
			}
		});
		var total = $('.break_group_'+day_name).length;
		if(total > 0){
			$('.hour div.break_group_'+day_name+':last').after($cloned);
		}else{
			parent_el.after($cloned);
		}
	});
/* step 10 */

});

/* step 10 */
	$('.remove_batch_edit').click(function(){
		$(this).closest('.form-group').remove();
	});
/* step 10 */

/* step 3 */
	$('select[name="servicetype"]').on('change' , function(){
		var $this = $(this);
		var servicetype = $this.val();
		if(servicetype == 'other'){
			$('#other_servicetype').removeClass('hidden');		
		} else {
			$('#other_servicetype').addClass('hidden');
		}
	});
/* step 3 */

/* step 4 */
	$('select[name="subjects[]"]').on('change' , function(e){ 
		var $this = $(this);
		var subject = $this.val();	
		if (/other/i.test(subject)) {	
			$('.other_subject').removeClass('hidden');		
			$('#subject_display').removeClass('hidden');
			$('.other_subject input.addOther').val('');	
			$('#subject_display').prop('checked',false);
		} else {
			if(confirm('Are you sure you want to remove others subject ?')){
				$('.other_subject').addClass('hidden');
				$('#subject_display').addClass('hidden');
				$('.other_subject input.addOther').val('');
				$('#subject_display').prop('checked',false);
				$('.editOtherSubject').remove();
			} else {				
				$('#user_subjects option[value="other"]').prop('selected',true ); 
			 }
		}
	});

	$('#addMoreSubject').on('click' , function(){	
		$(this).prop('disabled', true);	
		var uniqueID = 'Uid_'+Date.now();	
		var $html = '<div class="row other_subject" id="'+uniqueID+'">'+
				   '<input type="hidden" name="group[other_subject_ui][]" value="'+uniqueID+'" />'+
				   '<div class="form-group col-md-10">'+
				   '<label class="control-label">  Subject name </label>'+
				   '<input type="text" name="group[other_subject][]" value="" class="form-control addOther"  placeholder="Enter Other Subject name." />'+
				   '</div>'+ 
				   '<div class="form-group col-md-2" style="margin-top: 22px;">'+
				   '<button type="button" class="btn btn-danger" onclick="return removeRow(\''+uniqueID+'\');" > +  Remove </button>'+
				   '</div>'+
				   '</div>';
			$('#add_other_subject').append($html);
			$(this).prop('disabled', false);
	});

	function removeRow(rowID){
			$('#'+rowID).remove();
	}
/* step 4 */

/* step 6 */
// teaching location address ?
	$('input[name="isselfteachingaddress"]').on('change' , function(){
		 var $this = $(this);
		 if($this.prop("checked") == true){
			$('.self_location_address').find('input').val('');
			$('.self_location_address').removeClass('hidden');
		 } else {
			 if(confirm('Are you sure you want to remove self teaching address ?')){
				$('.self_location_address').find('input').val('');			
				$('.self_location_address').addClass('hidden');
			 } else {				
				$('input[name="isselfteachingaddress"]').prop('checked',true);
			 }
		 }
	});

// associated with any coaching?
	$('input[name="isassociatedcoaching"]').on('change' , function(){
		 var $this = $(this);
		 if($this.prop("checked") == true){
			$('select[name="associated_coachings[]"]').each(function () { 
				$(this).select2('val', '')
			});
			$('.form-group').find('input.other_coaching').val('');
			$('.coaching_associated').removeClass('hidden');
		 } else { 
		 	 if(confirm('Are you sure you want to remove associated coachings?')){
				$('select[name="associated_coachings[]"]').each(function () { 
					$(this).select2('val', '')
				});
				$('.form-group').find('input.other_coaching').val('');
				$('.coaching_associated').addClass('hidden');
			 } else {							
				$('input[name="isassociatedcoaching"]').prop('checked',true);
			 }
		 }
	});

	$('select[name="associated_coachings[]"]').on('change' , function(){
		var $this = $(this);
		var coachings = $this.val();	
		if (/other/i.test(coachings)) {	
			$('.other_coaching_cont').removeClass('hidden');
			// $('input[name="other_coaching"]').val('');
		} else { 
			//if(confirm('Are you sure you want to remove other coaching?')){
				$('.other_coaching_cont').addClass('hidden');
				$('input[name="other_coaching"]').val('');
			//} else {
				//other_coaching				
				// $('select[name="associated_coachings[]"] option[value="Other"]').prop('selected',true );
			//}
		}
	});
/* step 6 */


// select2:unselecting 
$('#associatedCoachings').on('select2:unselecting', function(e) {
	// alert($(this).val());
});




/* step 7  */
	$('button.addQualification').on('click', function(){
		var section_id 		= $('select[name="user_qualifications"]').val();
		var section_text 	= $('select[name="user_qualifications"] option:selected').text();		
		var cat_id 			= $('select[name="user_qualifications"] option:selected').data('catid'); 
		var cat_title 		= $('select[name="user_qualifications"] option:selected').data('cattitle'); 
		var quali_id 		= $('select[name="user_qualifications"] option:selected').data('qualiid'); 
		var quali_title 	= $('select[name="user_qualifications"] option:selected').data('qualititle'); 
		var uniqueID = 'Uid_'+Date.now();		
		html_data = '';
		if(section_id){
			html_data += '<div class="label label-warning selectedQualifications" style="margin:4px;">';			
			html_data += '<input name="group[teacher_quali_ui][]" class="ts_section" type="hidden" value="'+uniqueID+'" /> ';						
			html_data += '<input name="group[teacher_quali_catid][]" class="ts_section" type="hidden" value="'+cat_id+'" /> ';
			html_data += '<input name="group[teacher_quali_cattitle][]" class="ts_section" type="hidden" value="'+cat_title+'" /> ';
			html_data += '<input name="group[teacher_quali_id][]" class="ts_section" type="hidden" value="'+quali_id+'" /> ';
			html_data += '<input name="group[teacher_quali_title][]" class="ts_section" type="hidden" value="'+quali_title+'" /> ';						
			html_data += '<input name="group[teacher_quali_name][]" class="ts_section" type="hidden" value="'+section_text+'" /> ';			
			html_data += '<input name="group[teacher_quali_val][]" class="ts_section" type="hidden" value="'+section_id+'" /> ';			
			html_data += section_text; 						
			html_data += '<a href="javascript:void(0);" class="remove_section" data-section_id="'+section_id+'" data-section_text="'+section_text+'">X</a>'; 
			html_data += '</div>';			
			$('#qualiItemsContainer').append(html_data);			
			$('select[name="user_qualifications"] option[value="'+section_id+'"]').remove();
			$('select[name="user_qualifications"]').val(null).trigger("change"); 
		} else {
			alert('Please select qualification first!');
		}	
	});
	

	$(document).on('click', '.remove_section', function(){
		var section_id = $(this).data('section_id');
		var section_text = $(this).data('section_text');
		$('select[name="user_qualifications"]').append('<option value="'+section_id+'">'+section_text+'</option>');
		$(this).parent().remove(); 
	});

	function removeQualification(row_id){
		var section_id = $('#'+row_id).find('a').data('section_id');
		var section_text = $('#'+row_id).find('a').data('section_text');
		$('select[name="user_qualifications"]').append('<option value="'+section_id+'">'+section_text+'</option>');
		$('#'+row_id).remove(); 
	} 
/* step 7  */