$('.export_data').prop('disabled', true);
$('#export_data').prop('disabled', true);
$('.print_data').prop('disabled', true);


$(document).ready(function(e) {
	$('body').off('click', '.loadDailyTollInfo');
	
	$('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true,
		todayHighlight: true
	});
	
	$('.monthpicker').datepicker({
		format: "M-yyyy",
		viewMode: "months", 
		minViewMode: "months",
		autoclose: true,
		todayHighlight: true
	});
	
	$('.yearpicker').datepicker({
		format: "yyyy",
		viewMode: "years", 
		minViewMode: "years",
		autoclose: true,
		todayHighlight: true
	});
	
	$('.group_type').on('change', function(){
		$this = $(this);
		var mg_type = $this.val();
		if(mg_type != '' && mg_type != 'undefined'){
			var datatosend = {'mg_type' : mg_type};
			datatosend[csrf_token_name] = csrf_token_value;
			$.ajax({
				url : site_url+'reports/wages/ajax_get_maingroup', 
				type : "POST",
				beforeSend: function(){
						show_loader($('#report_panel'));
					},
				data : datatosend
			}).done(function(response){
				$('.maingroup').html(response.data);
				$('.maingroup').val("0").trigger("change");
				hide_loader($('#report_panel'));
			});
		}
	});
	
	$('.select2').select2();
	
	$('.select-fisherman').select2({
	  placeholder: "Select Fisherman",
	  ajax: {
		url: site_url+'reports/ajax_fisherman',
		dataType: 'json',
		data: function (params) {
		  return {
			q: params.term // search term
		  };
		},
		processResults: function (data) {
		  // Tranforms the top-level key of the response object from 'items' to 'results'
		  return {
			results: data.result1
		  };
		}
	  },
	  minimumInputLength : 1,
	  allowClear: true
	});
	
	$('.find_report').on('click', function(){
		var url = $(this).data('url');
		var $search_bar = $(this).closest('div.row');
		var datatosend = {};
		$search_bar.find('select, input').each(function(index, element) {
            var name = $(element).attr('name');
            var val = $(element).val();
			datatosend[name] = val;
        });
		getData(url, datatosend);
	});
	
	$('.export_data').on('click', function(){
		//var search_key = $('#search_key').text();
		//var search_date = $('#search_date').text().replace(/\//g, "-");;
		var report_name = $('.panel-heading h3').text();
		var target = $(this).attr('data-target');
		if (typeof target !== typeof undefined && target !== false) {
			$(target).table2excel({
				filename: report_name
			});
		}else{
			$("table.table").table2excel({
				filename: report_name
			});
		}
	});
	
	$('.print_data').on('click', function(){
		var target = $(this).attr('data-target');
		if (typeof target !== typeof undefined && target !== false) {
			$(target).print();
		}else{
			$("table.table").print();
		}
	});
	
	
	$('body').on('click', '.export_fisherman_data', function(){
		var pid = $(this).data('pid');
		var fname = $(this).data('fname');
		$(pid).table2excel({
			filename: fname
		});
	});
	
	$('body').on('click', '.print_fisherman_data', function(){
		var pid = $(this).data('pid');
		$(pid).print({noPrintSelector: ".no-print"});
	});
	
	$('body').on('click', '.loadDailyTollInfo', function(event){
		var $this = $(this);
		event.preventDefault();
		var urll = $this.attr('href');
		$('#daily-toll-info .modal-body').empty();
		$('#daily-toll-info').modal('show');
		$.getJSON(urll, null, function (response) {
			$('#daily-toll-info .modal-title').html(response.page_title);
			$('#daily-toll-info .modal-body').html(response.setup_form);
			// $('#daily-toll-info .modal-body').css('max-height', 500);
		});
	});
});
 

function getData(url, form_data){

	form_data[csrf_token_name] = csrf_token_value;
	$.ajax({
		url : site_url+url, 
		type : "POST",
		
		beforeSend: function(){
						show_loader($('#report_panel'));						
						if ( $.fn.dataTable.isDataTable('#data-table-grid') ) {
						  $('#data-table-grid').DataTable().destroy();
						}
					},
					
		data : form_data
	}).done(function(response){
		
		if(response.status == 'success'){
			$('.ajax-response').html('');
			$('#search_key').html(response.data.search_key);
			$('#search_date').html(response.data.search_date);
			$('span#from_date').html(response.data.from_date);
			$('span#to_date').html(response.data.to_date);
			$('#report_data').html(response.data.tbody);
			$('#report_footer').html(response.data.tfoot);
			if(response.data.hasOwnProperty('colspan')){
				$('#search_date').attr('colspan', response.data.colspan);
			}
			$('.export_data').prop('disabled', false);
			$('#export_data').prop('disabled', false);
			$('.print_data').prop('disabled', false);						
			
			if(!$.fn.dataTable.isDataTable('#data-table-grid') ) {
				var pagination = (url == 'reports/production/ajax_samiti')? false : true;
				$('#data-table-grid').DataTable({
					/*dom: 'Bfrtip',*/
					paging: pagination,
					searching: pagination, 
					bInfo: pagination, 
					"autoWidth": false, 
					"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]/*,
					buttons: [
						'excel', 'print'
					]*/
				});	
			}
			
			
		}else{
			$('.ajax-response').html(response.message);
			$('#report_data').html('');
			$('#report_footer').html('');
		}
		hide_loader($('#report_panel'));
		
	});

}


									 
								 

