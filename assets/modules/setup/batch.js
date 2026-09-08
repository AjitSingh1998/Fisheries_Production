// JavaScript Document
$(document).ready(function() {
	
	$('#tab').on('click', 'a', function(){
		$('a.open-dr').removeClass('active');
		var location_type = $(this).data('location_type');
		$('#location_type').val(location_type);
		if(location_type == 1){
			//$('#address_line1').attr('required','required');
			//$('#city').attr('required','required');
			$('.label_address_line1').addClass('required');
			$('.label_city').addClass('required');
			$('.adrs').removeClass('hidden');
			$('.require_address').addClass('hidden');
			$('.add_ofc_address').addClass('hidden');
		}else if(location_type == 2){
			$('.require_address').removeClass('hidden');
			var address_line1 = $('#address_line1').val();
			var address_line2 = $('#address_line2').val();
			var city = $('#city').val();
			$('.label_address_line1').removeClass('required');
			$('.label_city').removeClass('required');
			$('#address_line1').removeAttr('required');
			$('#city').removeAttr('required');
			if(address_line1 || address_line2 || city){
				$('.adrs').removeClass('hidden');
			}else{
				$('.adrs').addClass('hidden');
				$('.add_ofc_address').removeClass('hidden');
			}
		}
	});
	
	function removeDayGrp( day_name){
			// alert(day_name);
			$('.break_group_'+day_name).remove();
	}
	
	$('.day-circle').on('click',function(){		
		if($(this).hasClass('btn-info')){
			$(this).removeClass('btn-info');
						
			// $(this).addClass('add_break_hour');
			
			$(this).find('.glyphicon').removeClass('glyphicon-ok');
			$(this).addClass('circle-inactive');
			$(this).parent('.btn-group').find('.day-unit').addClass('hidden');
			var day_name = $(this).closest('.form-group').find('input[type="checkbox"]').attr('id');
			
			$(this).closest('.form-group').find('.break_group_'+day_name).addClass('psypawan');	
			
			$(this).closest('.form-group').nextAll('.break_group_'+day_name).remove();	
			
		   removeDayGrp( day_name );
			
		//	setTimeout(
			// alert('ss')
			//	$(this).closest('.form-group').nextAll('.break_group_'+day_name).remove(),
			// ,	50);
			// $(this).closest('.btn-group').find('.day-unit').removeClass('hidden');
					
		} else if($(this).hasClass('circle-inactive')){
			$(this).removeClass('circle-inactive');
			$(this).addClass('btn-info');
			
			$(this).removeClass('add_break_hour');
			
			// $(this).addClass('add_break_hour');
			
			// $(this).closest('.form-group').find('.break_group_'+day_name).addClass('psypawan');	
			
			$(this).find('.glyphicon').addClass('glyphicon-ok');
			$(this).parent('.btn-group').find('.day-unit').removeClass('hidden');
		}
	});
	
	$('.add_ofc_address').on('click',function(){
		$('.adrs').removeClass('hidden');
		$(this).addClass('hidden');
	});
	
	
	$('.add_break_hour').click(function(){
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
		
		// location
		var location_name = '<option value=""> Branch* </option>';
		if(json_location.length){
			var $locationObj = $.parseJSON(json_location);
			for(var i=0; i < $locationObj.length;i++){
				location_name += '<option value="'+$locationObj[i].branch_id+'">'+$locationObj[i].branch_name+'</option>';
			}			
		}	

		var location_input = $('<label class="break_name Xcourse_name"></label>');
		location_input.append('<select required name="'+day_name+'[break][location][]" class="form-control">'+location_name+'</select>');
		$cloned.find('.btn-group').before(location_input);
		
		// course
		var course_name = '';
		if(json_course.length){
			var $courseObj = $.parseJSON(json_course);
			for(var i=0; i < $courseObj.length;i++){
				course_name += '<option value="'+$courseObj[i]+'">'+$courseObj[i]+'</option>';
			}			
		}	

		var course_input = $('<label class="break_name Xcourse_name"></label>');
		course_input.append('<select required name="'+day_name+'[break][course][]" class="form-control">'+course_name+'</select>');
		$cloned.find('.btn-group').before(course_input);
		
		var break_input = $('<label class="break_name"></label>');
		break_input.append('<input type="text" placeholder="Batch Name" name="'+day_name+'[break][name][]" class="form-control" />');		
		
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
	
});

$('.remove_batch_edit').click(function(){
	$(this).closest('.form-group').remove();
});
