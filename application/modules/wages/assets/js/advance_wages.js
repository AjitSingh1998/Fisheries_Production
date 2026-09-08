// JavaScript Document
$(document).ready(function() {
	$('body').off('select2:select', '.select-fisherman');
	$('body').off('select2:unselecting', '.select-fisherman');
	
	var form = $('form#adv_wages_form');
	
	var select_fisherman = $('.select-fisherman');
	var fisherman_data = '';
	select_fisherman.select2({
	  placeholder: "Select Fisherman",
	  ajax: {
		url: site_url+'wages/fisherman',
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
		$('#mg_type').val('');
		$('#maingroup').val('');
		$('#f_cn').val('');
		fisherman_data = '';
	});
	
	$('.datepicker').datepicker({
		dateFormat: 'dd M yy',
		autoclose: true,
		todayHighlight: true
	});
	
});