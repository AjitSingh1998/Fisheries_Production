// JavaScript Document
$(document).ready(function() {
	$('.add_sfm').prop('disabled', true);
	$('body').off('change', '#maingroup_type');
	$('body').off('click', '.remove_row');
	
	//On change primary fisher main group type
	var maingroup = $('#maingroup');
	$('#maingroup_type').on('change', function(){
		$this = $(this);
		var mg_type = $this.val();
		if(mg_type != '' && mg_type != 'undefined'){
			var datatosend = {'mg_type' : mg_type};
			datatosend[csrf_token_name] = csrf_token_value;
			$.ajax({
				url : site_url+'setup/ajax_get_maingroup', 
				type : "POST",
				data : datatosend
			}).done(function(response){
				maingroup.prop('disabled', false);
				maingroup.html(response.data);
			});
		}else{
			maingroup.val('');
			maingroup.html('<option value="">Select Group</option>');
			maingroup.prop('disabled', true);
		}
	});
	
	var select_fisherman = $('.select-fisherman');
	var fisherman_data = '';
	select_fisherman.select2({
	  placeholder: "Select Fisherman",
	  ajax: {
		url: site_url+'setup/ajax_fisherman',
		dataType: 'json',
		data: function (params) {
		  return {
			q: params.term, // search term
			f_ids: function(){
				var values = $("input[class='f_ids']").map(function(){return $(this).val();}).get();
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
	  allowClear: true,
	  dropdownParent: $('.select-fisherman').parent(),
	  escapeMarkup: function (m) {
			return m;
	  }
	});
	
	select_fisherman.on('select2:select', function (e) {
		$('.add_sfm').prop('disabled', false);
	});
	
	select_fisherman.on('select2:unselecting', function (e) {
		$('.add_sfm').prop('disabled', true);
	});
	
	//Add new row
	$('.add_sfm').on('click', function(){
		var tbody = $('#secondary');
		var fisher_id 	= $('.select-fisherman option:selected').val();
		var fisher_name = $('.select-fisherman option:selected').text();
		//console.log(fisherman_data[fisher_id]);
		var prep_tr = ''
		if(fisher_id != '' && fisher_id != 'undefined' && fisher_name != '' && fisher_name != 'undefined' && fisherman_data != ''){
			var f_maingroup = fisherman_data[fisher_id].MainGroup;
			var f_maingroup_name = fisherman_data[fisher_id].MainGroupName;
			var f_code 		= fisherman_data[fisher_id].Code;
			var f_name 		= fisherman_data[fisher_id].Name;
			prep_tr = '<tr>'+
							'<td>'+f_code+'</td>'+
							'<td>'+f_maingroup_name+
								'<input type="hidden" name="sf[samiti][]" value="'+f_maingroup+'">'+
								'<input type="hidden" name="sf[code][]" value="'+f_code+'">'+
								'<input type="hidden" name="sf[name][]" value="'+f_name+'">'+
								'<input type="hidden" class="f_ids" name="sf[id][]" value="'+fisher_id+'">'+
							'</td>'+
							'<td>'+f_name+'</td>'+
							'<td>'+
								'<button type="button" class="btn btn-danger btn-xs remove_row" data-id="" title="Delete Fisherman">'+
									'<span class="glyphicon glyphicon-remove-sign"></span>'+
								'</button>'+
							'</td>'+
						'</tr>';
			tbody.append(prep_tr);
			/*
			sf_grouptype.val('').trigger("change");
			sf_maingroup.val('').trigger("change");
			sf_fisherman.val('').trigger("change");
			*/
			//sf_maingroup.empty().append('<option value="">Select Maingroup</option>').prop('disabled', true);
			//sf_fisherman.empty().append('<option value="">Select Fisherman</option>').prop('disabled', true);
			$('.add_sfm').prop('disabled', true);
			//$('.select-fisherman option:selected').remove();
			select_fisherman.val('').trigger("change");
			fisherman_data = '';
			//var objDiv = document.getElementById("asset-modal-body");
			//objDiv.scrollTop = objDiv.scrollHeight;
		}else{
			//console.log(fisherman_data);
		}
	});
	
	//Remove product row
	$('body').on('click', '.remove_row', function(){
		var $this = $(this);
		var id = $this.data('id');
		var conf = confirm('Are you sure you want to delete');
		if(conf){
			if(id){
				var datatosend = {'db_id' : id};
				datatosend[csrf_token_name] = csrf_token_value;
				$.ajax({
					url : site_url+'setup/remove_secondary_fisherman', 
					type : "POST",
					data : datatosend
				}).done(function(response){
					if(response.status == 'success'){
						$this.closest('tr').remove();
					}
				});
			}else{
				$this.closest('tr').remove();
			}
		}
	});
});