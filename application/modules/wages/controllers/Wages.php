<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[AllowDynamicProperties]
class Wages extends MY_Controller {
	var $userID, $axis_group_serial_number, $axis_fisherman_serial_number ;
	
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('wages_model', 'WM');
		
		$this->axis_group_serial_number = AXIS_GROUP_SERIAL_NUMBER;
		$this->axis_fisherman_serial_number = AXIS_FISHERMAN_SERIAL_NUMBER;
	}
	
	function index(){
		redirect('wages/advance_wage');
	}
	
	function __callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/mark_delete');
	}
	
	function __callbackLockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/lock');
	}	
	
	function add_bulk_advance_wage(){
			
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('wages/assets/js/add_bulk_advance_wages.js')
												));
		
		//printr($data['dt_data']);
		$data['page_title'] 	= 'Add Bulk Advance Wages';
		$data['content_view'] 	= 'wages/add_bulk_advance_wages_v';		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	
	}
	
	function save_bulk_advance_wages(){
		$checkFormValidation = $this->__setFormRules('save_bulk_advance_wages');
		$postData = $this->input->post();
		if($checkFormValidation){
			$date = get_date('Y-m-d', str_replace('/', '-', $postData['date']));			
			$prepData = array('group_type' 		=> $postData['f_group'],
							  'maingroup_id'	=> $postData['f_maingroup'],
							  'Fisherman'		=> $postData['fisherman_id'],
							  'Date'			=> $date,
							  'Amount'			=> $postData['amount'],
							  'remark'			=> $postData['remark'],
							  'added_by'		=> $this->userID,
							  'added_date'		=> get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=> microtime(true),
							  );
			
			$where = array( 'group_type'=>$postData['f_group'],
							'maingroup_id'=>$postData['f_maingroup'],
							'Fisherman'=>$postData['fisherman_id'],
							'Date'=>$date,
							'status'=>'Active');
			$result = $this->db->select('ID')->where($where)->get(WAGES_ADVANCE)->result_array();
			//printr($result);
			if(!empty($result) && $postData['action'] == 'edit'){
				$id = $result[0]['ID'];
				
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				
				$result = $this->db->where('ID', $id)->update(WAGES_ADVANCE, $prepData);
				$postData['advid'] = $id;
				if($result){
					$data = array('status' => 'success', 'message'=> 'Data successfully saved', 'data' => $postData);
				}else{
					$data = array('status' => 'error', 'message'=> 'Data adding failed, please try again', 'data' => $postData);
				}
			}elseif(empty($result) && $postData['action'] == 'add'){
				$result = $this->db->insert(WAGES_ADVANCE, $prepData);
				$id = $this->db->insert_id();
				$postData['advid'] = $id;
				if($result){
					$data = array('status' => 'success', 'message'=> 'Data successfully saved', 'data' => $postData);
				}else{
					$data = array('status' => 'error', 'message'=> 'Data adding failed, please try again', 'data' => $postData);
				}
			}elseif(!empty($result) && $postData['action'] == 'add'){
				$data = array('status' => 'error', 'message'=> 'Advance is already given to this fisherman at '.$postData['date'], 'data' => '');
			}
		}else{
			$data = array('status' => 'error', 'message'=> validation_errors(), 'data' => $postData);
		}		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function delete_advance_wages(){
		$advid = $this->input->post('advid');
		$postData = $this->input->post();
		if($advid != ''){			
			$result = $this->db->where('ID', $advid)->delete(WAGES_ADVANCE);
			if($result){
				$data = array('status' => 'success', 'message'=> 'Data successfully deleted', 'data' => $postData);
			}else{
				$data = array('status' => 'error', 'message'=> 'Data deletion failed, please try again', 'data' => $postData);
			}			
		}else{
			$data = array('status' => 'error', 'message'=> 'Row ID not found, please try again!', 'data' => $postData);
		}		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Listing
	function advance_wage_09July2018(){
		$actions = checkUserPermission('wages/advance_wage', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_edit_url_path(site_url('wages/add_advance_wage'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('wages/add_bulk_advance_wage'));
			//$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('wages/view_advance_wage'));
			$crud->set_read_button_class('loadActionForm');
		}else{
			$crud->unset_read();
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		
		$crud->unset_delete();
		if(in_array('delete',$actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}		
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}	
		
		$crud->set_subject('Advance to Fisherman');
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('Fisherman', FISHERMAN, 'Name');
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.status !='=>'Deleted'));
		$crud->where(array(WAGES_ADVANCE.'.editable !='=>'Lock'));
		$crud->where(array(WAGES_ADVANCE.'.Fisherman !='=>'0'));
			
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('Date', 'Fisherman', 'maingroup_id', 'Amount', 'added_by', 'Remark');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		
		$output = $crud->render();
		$data = array('page_title'=> 'Advance to Fisherman', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('wages/assets/js/setup.js')
											));
		
		$this->template->set('document_title', 'Advance to Fisherman');
		$this->template->layout($outputData);
		
	}
	
	function advance_wage(){
		$actions = checkUserPermission('wages/advance_wage', $this->uri->segment(3));
		
		$filter_data = $this->input->post('fileter_fisherman');			
		if($filter_data){
			$filter['from_date']	  = $this->input->post('from_date');
			$filter['to_date']		  = $this->input->post('to_date');
			$filter['receipt_number'] = $this->input->post('receipt_number');
			$filter['party_id']		  = $this->input->post('party_id');
			$filter['party_name'] 	  = $this->input->post('party_name');
			$this->session->set_userdata('fisherman_advance_wages_filter', $filter);
		}
		$filterdata_sess = $this->session->userdata('fisherman_advance_wages_filter');
		if(!isset($filterdata_sess) && !is_array($filterdata_sess)){
			$filterdata_sess = array('from_date'=>'', 'to_date'=>'', 'receipt_number'=>'', 'party_id' => '', 'party_name'=>'');
		}
		
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_edit_url_path(site_url('wages/add_advance_wage'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('wages/add_bulk_advance_wage'));
			//$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('wages/view_advance_wage'));
			$crud->set_read_button_class('loadActionForm');
		}else{
			$crud->unset_read();
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		
		$crud->unset_delete();
		if(in_array('delete',$actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}		
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}	
		
		
		$columns = array('Date' => WAGES_ADVANCE . '.Date'); 
		$search_field = $this->input->post('search_field');
		$search_text = $this->input->post('search_text');
		if(!empty($search_field)){
			foreach($search_field as $key => $value){
				if(array_key_exists($value, $columns)){					
					$search_field[$key] = $columns[$value];
				}
				if($value == 'Date'){
					$search_text[$key] = date('Y-m-d', strtotime(str_replace('/', '-', $search_text[$key])));
				}	
			}
			$_POST['search_text'] = $search_text;
			$_POST['search_field'] = $search_field;
		}		
		//printr($_POST['search_text']);
		
		$crud->set_subject('Advance to Fisherman');
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('Fisherman', FISHERMAN, 'Name');
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.status !='=>'Deleted'));
		$crud->where(array(WAGES_ADVANCE.'.editable !='=>'Lock'));
		$crud->where(array(WAGES_ADVANCE.'.Fisherman !='=>'0'));
		
		if(isset($filterdata_sess['from_date']) && !empty($filterdata_sess['from_date'])){
			$date_string = str_replace('/','-', $filterdata_sess['from_date']);
			$dt = get_date('Y-m-d', $date_string);
			$crud->where(array(WAGES_ADVANCE.'.Date >='=> $dt));
		}		
		if(isset($filterdata_sess['to_date']) && !empty($filterdata_sess['to_date'])){
			$date_string = str_replace('/','-', $filterdata_sess['to_date']);
			$dt = get_date('Y-m-d', $date_string);
			$crud->where(array(WAGES_ADVANCE.'.Date <='=> $dt));
		}
		if(isset($filterdata_sess['receipt_number']) && !empty($filterdata_sess['receipt_number'])){
			$crud->where(array(WAGES_ADVANCE.'.receipt_number'=>$filterdata_sess['receipt_number']));
		}
		if(isset($filterdata_sess['party_id']) && !empty($filterdata_sess['party_id'])){
			$crud->where(array(WAGES_ADVANCE.'.Fisherman'=>$filterdata_sess['party_id']));
		}
		
		
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('Date', 'Fisherman', 'maingroup_id', 'Amount', 'added_by', 'Remark');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		$crud->display_summary('Amount');
		$output = $crud->render();
		$data = array('page_title'=> 'Advance to Fisherman', 'content_view'=>'wages/fisherman_advance_wages_listing_v');
					  
		$outputData = array_merge((array)$output, $data, $filterdata_sess);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('wages/assets/js/setup.js')
											));
											
		$this->template->set('scripts', "<script>
											$(document).ready(function() { 
												$('.datepicker').datepicker({
													format: 'dd/mm/yyyy',
													autoclose: true,
													todayHighlight: true
												});
												var fisherman_data = '';
												$('.select2').select2({
												  placeholder: \"Select Fisherman\",
												  ajax: {
													url: site_url+'wages/fisherman',
													dataType: 'json',
													data: function (params) {
													  return {
														q: params.term, // search term
														f_ids: function(){
															var values = $(\"input[name='fisherman_id']\").map(function(){return $(this).val();}).get();
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
												$('.select2').on(\"select2:selecting\", function(e) { 
												   $('input[name=\"party_name\"]').val(e.params.args.data.text);
												});
											});
											</script>");
											
		$this->template->set('document_title', 'Advance to Fisherman');
		$this->template->layout($outputData);
		
	}
	
	function add_advance_wage($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('group_type'	=> '',
								'maingroup_id'	=> '',
								'Fisherman'		=> '',
								'receipt_number'=> '',
								'Date'			=> '',
								'Amount'		=> '',
								'remark'		=> '',
								'f_cn'			=> ''
								);
		$data['page_title'] = 'Add Advance to Fisherman';	
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('wages/add_advance_wage');
		
		
		$form_valid = $this->__setFormRules('add_advance_wage');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode']; 
			//Preparing outward data
			$prepData = array('group_type' 		=> $post['group_type'],
							  'maingroup_id'	=> $post['maingroup'],
							  'Fisherman'		=> $post['fisherman'],
							  'receipt_number'	=> $post['receipt_number'],
							  'Date'			=> get_date('Y-m-d', $post['date']),
							  'Amount'			=> $post['amount'],
							  'remark'			=> $post['remark'],
							  'added_by'		=> $this->userID,
							  'added_date'		=> get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=> microtime(true),
							  );
			
			//printr($prepData);
			if($id != NULL && $action_mode == 'edit'){
				//do update
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('ID', $id)->update(WAGES_ADVANCE, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('wages/add_advance_wage/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(WAGES_ADVANCE, $prepData);
				$id = $this->db->insert_id();
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('wages/add_advance_wage/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data inserted successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to insert. Please try again.';
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		if($id != NULL){
			//printr($id);
			$data['page_title'] = 'Edit Advance to Fisherman';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('wages/add_advance_wage/'.$id);
			$advance_wages = $this->WM->get_advance_wages($id);
			//printr($dbdata);
			if(!empty($advance_wages)){
				$data['dbdata'] = $advance_wages;
			}
		}
		
		$data['mg_type'] = $this->WM->get_mgtype_data(); // Get all maingroup_type data
		$data['scriptsrc'] = array(site_url('wages/assets/js/advance_wages.js'));
		$data['setup_form'] = $this->load->view('wages/add_advance_wage_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function view_advance_wage($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('group_type'	=> '',
								'maingroup_id'	=> '',
								'Fisherman'		=> '',
								'receipt_number'=> '',
								'Date'			=> '',
								'Amount'		=> '',
								'remark'		=> '',
								'f_cn'			=> ''
								);
		$data['page_title'] = 'Advance to Fisherman';
		
		if($id != NULL){
			$advance_wages = $this->WM->get_advance_wages($id);
			//printr($dbdata);
			if(!empty($advance_wages)){
				$data['dbdata'] = $advance_wages;
			}
		}
		
		$data['mg_type'] = $this->WM->get_mgtype_data(); // Get all maingroup_type data
		$data['scriptsrc'] = array(site_url('wages/assets/js/advance_wages.js'));
		$data['setup_form'] = $this->load->view('wages/view_advance_wage_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	/*
	--Fisherman Wages Functions Start
	*/
	//Listing: Fisherman Wages
	function wages_sheet(){
		$actions = checkUserPermission('wages/wages_sheet', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_edit_url_path(site_url('wages/add_wages_sheet'));
			$crud->set_edit_button_class('btn btn-primary loadInIframe');
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('wages/add_wages_sheet'));
			$crud->set_add_button_class('btn btn-primary loadInIframe');
		}
		
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('wages/view_wages_sheet'));
		}else{
			$crud->unset_read();
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		$crud->unset_delete();
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
	
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
		
		$crud->set_subject('Fisherman Wages');
		$crud->set_table(WAGES);
		$crud->set_relation('MainGroup', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES.'.status !='=>'Deleted'));
		$crud->where(array(WAGES.'.editable !='=>'Lock'));
		$crud->where(array(WAGES.'.wages_for'=>'Fisherman'));
		
		$crud->columns('from_date', 'to_date', 'MainGroup', 'added_by');
		
		if($crud->getState() == 'ajax_list'){
			$postData = $this->input->post();
			if(isset($postData['search_field']) && !empty($postData['search_field'])){
				foreach($postData['search_field'] as $key=>$value){
					if($value == 'from_date' || $value == 'to_date' ){
						$_POST['search_text'][$key] = get_date('Y-m-d', str_replace('/', '-', $postData['search_text'][$key]));
					}
				}
			}
		}
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Wages Sheet', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		//$data['scriptsrc'] = array(base_url('assets/modules/wages/advance_wages.js'));
		$this->template->set('document_title', 'Fisherman Wages');
		$this->template->layout($outputData);
	}
	
	//ADD: Fisherman Wages
	function add_wages_sheet($wage_id=NULL){
		$data['page_title'] 	= 'Calculate Fisherman Wages';
		$data['wages_data'] = array('ID'=>'', 'from_date'=>'', 'to_date'=>'', 'MainGroup'=>'');
		$data['wage_items'] = '';
		
		if(!empty($wage_id) && $wage_id !== NULL){
			$data['page_title'] 	= 'Edit Fisherman Wages';
			$result = $this->WM->get_wages_data($wage_id);
			
			if(isset($result['wage_info']) && !empty($result['wage_info'])){
				$data['wages_data'] = $result['wage_info'];
				
				$from_date = @$data['wages_data']['from_date'];
				$to_date = @$data['wages_data']['to_date'];
				$maingroup = @$data['wages_data']['MainGroup'];
				$data['fdt'] = $from_date;
				$data['tdt'] = $to_date;
				$data['mg'] = $maingroup;
				$data['wage_items'] = $this->WM->get_wages_items($from_date, $to_date, $maingroup, $wage_id);
				
				$view_data = $this->load->view('wages/wages_list_data_v', $data, true);
				$data['wage_items'] = $view_data;
			}else{
				$this->messageci->set('Invalid request','error');
				redirect('wages/wages_sheet', 'refresh');
			}
		}
		
		$data['all_maingroup']	= $this->WM->get_all_maingroup_data();
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/bootstrap-switch/static/stylesheets/bootstrap-switch.css')
												));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/bootstrap-switch/static/js/bootstrap-switch.min.js'),
												site_url('wages/assets/js/jquery.table2excel.min.js'),
												site_url('wages/assets/js/jQuery.print.min.js'),
												site_url('wages/assets/js/wages_sheet.js')
												));
		
		//printr($data['dt_data']);
		$data['content_view'] 	= 'wages/add_wages_sheet_v';
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	//Ajax: Get Fisherman calculated dailytoll
	function ajax_wages_item(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> No data found, Please try again.</div>', 'data' => '');
		$formValidation = $this->__setFormRules('ajax_wages_item');
		if($formValidation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$maingroup = $this->input->post('maingroup');
			
			$result = $this->db->query("SELECT * from ".WAGES." WHERE wages_for = 'Fisherman' AND status='Active' AND MainGroup = '".$maingroup."' AND
										((from_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(to_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(from_date <= '".$from_date."' AND to_date >= '".$to_date."'))")->result_array();
			if(!empty($result)){
				$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> There is already calculated wages for this time period of this selected maingroup. Please click <a class="label label-info" href="'.site_url('wages/add_wages_sheet/'.@$result[0]['ID']).'">here</a> to load calculated wages </div>', 'data' => '');
			}else{
				$wages_data['wage_items'] = $this->WM->get_wages_items($from_date, $to_date, $maingroup);
				$wages_data['fdt'] = $from_date;
				$wages_data['tdt'] = $to_date;
				$wages_data['mg'] = $maingroup;
				//printr($wages_data['wage_items']);
				if(!empty($wages_data['wage_items'])){
					$view_data = $this->load->view('wages/wages_list_data_v', $wages_data, true);
					$data = array('status' => 'success', 'message' => 'Wages data', 'data' => $view_data);
				}else{
					$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> No wages data found on selected dates, Please try again.</div>', 'data' => '');
				}
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data' => '');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Save: Fihserman wages
	function save_wage_row(){
		$data = array('status' => 'dagner', 'message' => 'There is some error, Please try again.', 'data' => '');
		
		$form_validation = $this->__setFormRules('insert_wage_sheet');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			
			$group_type_id = $this->input->post('group_type_id');
			$MainGroup = $this->input->post('MainGroup');
			$FishermanId = $this->input->post('FishermanId');
			
			$MajorFee = $this->input->post('MajorFee');
			$MinorFee = $this->input->post('MinorFee');
			$SawalFee = $this->input->post('SawalFee');
			
			$major_wt = $this->input->post('major_wt');
			$minor_wt = $this->input->post('minor_wt');
			$sawal_wt = $this->input->post('sawal_wt');
			
			$major_wage = $this->input->post('major_wage');
			$minor_wage = $this->input->post('minor_wage');
			$sawal_wage = $this->input->post('sawal_wage');
			
			$TotalWage = $this->input->post('TotalWage');
			
			$group_liability = $this->input->post('group_liability');
			$advance_wages = $this->input->post('advance_wages');
			
			$GovDeduction = $this->input->post('GovDeduction');
			
			$GroupLiability = $this->input->post('gld_amount');
			$AdvanceLiability = $this->input->post('ald_amount');
			
			$final_wages = $this->input->post('final_wages');
			
			$mode = $this->input->post('mode');
			$wages_id = $this->input->post('wages_id');
			
			$wages_data = array('wages_for' 	=> 'Fisherman',
								'from_date' 	=> $from_date,
								'to_date' 		=> $to_date,
							    'MainGroup' 	=> $MainGroup,
							    'added_by' 		=> $this->userID,
							    'added_date' 	=> get_datetime('Y-m-d H:i:s'),
							    'action_microtime' => microtime(true)
							   );
			
			if(!empty($wages_id)){
				//update wage
				unset($wages_data['added_by'], $wages_data['added_date']);
				$wages_data['updated_by'] = $this->userID; 
				$wages_data['updated_date'] = get_datetime('Y-m-d H:i:s');
				
				$result = $this->db->where(array('ID'=>$wages_id))->update(WAGES, $wages_data);
			}else{
				//insert wage
				$result = $this->db->query("SELECT * from ".WAGES." WHERE wages_for = 'Fisherman' AND status='Active' AND MainGroup = '".$MainGroup."' AND
										((from_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(to_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(from_date <= '".$from_date."' AND to_date >= '".$to_date."'))")->result_array();
				if(!empty($result)){
					$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> There is already calculated wages for this time period of this selected maingroup. Please click <a class="label label-info" href="'.site_url('wages/add_wages_sheet/'.@$result[0]['ID']).'">here</a> to load calculated wages </div>', 'data' => '');
				}else{
					$result = $this->db->insert(WAGES, $wages_data);
					$wages_id = $this->db->insert_id();
				}
				
				
			}
			
			if(!$result){
				$data = array('status' => 'danger', 'message' => 'Wages updated failed', 'data' => '' );
			}else{
				$wages_item_data = array( 'wages_id' 			=> $wages_id,
										  'wages_for' 			=> 'Fisherman',
										  'from_date' 			=> $from_date,
										  'to_date' 			=> $to_date,
										  
										  'group_type_id' 		=> $group_type_id,
										  'MainGroup' 			=> $MainGroup,
										  'FishermanId' 		=> $FishermanId,
										  
										  'MajorFee' 			=> $MajorFee,
										  'MinorFee' 			=> $MinorFee,
										  'SawalFee' 			=> $SawalFee,
										  
										  'major_wt' 			=> $major_wt,
										  'minor_wt' 			=> $minor_wt,
										  'sawal_wt' 			=> $sawal_wt,
										  
										  'major_wage' 			=> $major_wage,
										  'minor_wage' 			=> $minor_wage,
										  'sawal_wage' 			=> $sawal_wage,
										  
										  'TotalWage' 			=> $TotalWage,
										  
										  'group_liability' 	=> $group_liability,
										  'advance_wages' 		=> $advance_wages,
										  
										  'GovDeduction' 		=> $GovDeduction,
										  
										  'GroupLiabilityDeduction' => $GroupLiability,
										  'AdvanceWagesDeduction' 	=> $AdvanceLiability,
										  
										  'final_wages' 		=> $final_wages,
										  'added_by' 			=> $this->userID,
							      		  'added_date' 			=> get_datetime('Y-m-d H:i:s'),
							    	      'action_microtime' 	=> microtime(true)
										  );
				
				$mode = $this->input->post('mode');
				$wages_item_id = $this->input->post('item_id');
				
				if($mode == 'add'){
					
					$where = array('wages_for' 		=> 'Fisherman',
								   'from_date' 		=> $from_date,
								   'to_date' 		=> $to_date,								  
								   'group_type_id' 	=> $group_type_id,
								   'MainGroup' 		=> $MainGroup,
								   'FishermanId' 	=> $FishermanId,
								   'status'			=> 'Active');
					$checkExistance = $this->db->where($where)->count_all_results(WAGESITEM);
					if($checkExistance <= 0){
						$result = $this->db->insert(WAGESITEM, $wages_item_data);
						$wages_item_id = $this->db->insert_id();
						
						if($wages_item_id){
							$this->db->where(array('wages_item_id' => $wages_item_id))->delete(LIABILITY_DEDUCTION);
							$liability_data = $this->insert_liability_data($wages_id, $wages_item_id, $GroupLiability, $FishermanId, $from_date, $to_date, $MainGroup);
							$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'edit', 'liability_data'=>$liability_data);
							$data = array('status' => 'success', 'message' => 'Wages item inserted.', 'data' => $response_data );
						}else{
							$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'add', 'liability_data'=>'');
							$data = array('status'=>'danger', 'message'=>'Failed to insert wages items, Please try again.', 'data'=>$response_data );
						}
						
					}else{
						$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'add', 'liability_data'=>'');
						$data = array('status'=>'danger', 'message'=>'<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i>You have already calculated wages for this fisherman, Please reload the page and try again.</div>', 'data'=>$response_data);						
						
					}
					
					
				}elseif($mode == 'edit' && !empty($wages_item_id)){
					unset($wages_item_data['added_by'], $wages_item_data['added_date']);
					$wages_item_data['updated_by'] = $this->userID; 
					$wages_item_data['updated_date'] = get_datetime('Y-m-d H:i:s');
					
					$result = $this->db->where(array('ID'=>$wages_item_id))->update(WAGESITEM, $wages_item_data);
					if($result){
						$this->db->where(array('wages_item_id' => $wages_item_id))->delete(LIABILITY_DEDUCTION);						
						
						$liability_data = $this->insert_liability_data($wages_id, $wages_item_id, $GroupLiability, $FishermanId, $from_date, $to_date, $MainGroup);
						
						$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'edit', 'liability_data'=>$liability_data);
						$data = array('status'=>'success', 'message'=>'Wages item updated.', 'data'=>$response_data );
					}else{
						$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'edit', 'liability_data'=>'');
						$data = array('status'=>'danger', 'message'=>'Failed to updated wages items, Please try again.', 'data'=>$response_data );
					}
				}else{
					$response_data = array('wages_id' => $wages_id, 'item_id' => '', 'mode' => 'add', 'liability_data'=>'');
					$data = array('status' => 'danger', 'message' => 'Invalid request, Please reload the page.', 'data' => '' );
				}
			}
		}else{
			$wages_id = $this->input->post('wages_id');
			$mode = $this->input->post('mode');
			$wages_item_id = $this->input->post('item_id');
			$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>$mode, 'liability_data'=>'');
			
			$data = array('status'=>'dagner', 'message'=>validation_errors(), 'data'=>$response_data);
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function insert_liability_data($wages_id, $wages_item_id, $group_liability_deduction_amount, $FishermanId, $from_date, $to_date, $maingroup){
		
		$sql = 'SELECT sf.Primary, sf.Secondary, sf.group_type_id, sf.maingroup_id,
				
				IFNULL((SELECT fm.products_balance FROM '.FISHERMAN.' fm WHERE fm.status="Active" AND fm.ID = sf.Primary), 0.00) as products_balance,
				
				IFNULL((SELECT SUM(po.grand_total) FROM '.PRODUCT_OUTWARD.' po WHERE po.status="Active" AND po.outward_to="Fisherman" AND po.fisherman_id = sf.Primary), 0.00) as group_liability,
				
				IFNULL((SELECT SUM(poir.total_price) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.status="Active" AND poir.outward_to="Fisherman" AND poir.fisherman_id = sf.Primary ), 0.00) as returned_amt,
				
				IFNULL((SELECT SUM(cdp.product_liability) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status = "Active" AND cdp.fisherman_id=sf.Primary AND cdp.deposited_by = "Fisherman"), 0.00) as cash_deposited,
				
				IFNULL((SELECT (SUM(ld.amount) + SUM(ld.advance_deduction)) FROM '.LIABILITY_DEDUCTION.' ld WHERE ld.deducted_for = sf.Primary AND ld.status="Active"), 0.00) as group_liability_deducted
		
		FROM '.SECONDARYFISHERMAN.' sf 
		WHERE sf.Secondary = "'.$FishermanId.'" AND sf.group_status = "Joined" GROUP BY sf.Primary, sf.Secondary ORDER BY sf.ID ASC, sf.join_date ASC';
		
		$result = $this->db->query($sql)->result_array();
		/* 	products_balance debit, 
			group_liability debit, 
			returned_amt credit
			cash_deposited credit
			group_liability_deducted credit
		*/
		//printr($result);
		$response_data = array();
		$liability_data = array();
		if(!empty($result)){
			$total_group_liability = 0;
			$secondary_deposited_amount = 0;
			foreach($result as $res){
				$total_liability = ($res['group_liability'] + $res['products_balance']) - ($res['returned_amt'] + $res['group_liability_deducted'] + $res['cash_deposited']);
				if($res['Primary'] == $FishermanId){
					$secondary_deposited_amount = $total_liability;
				}
				$total_group_liability += $total_liability;
			}
			$total_members = count($result);
			$i = 0;	
			foreach($result as $res){
				$i++;
				$group_liability = $res['group_liability'] + $res['products_balance'];
				$total_personal_liability = $group_liability - ($res['returned_amt'] + $res['group_liability_deducted'] + $res['cash_deposited']);
				
				$liabData = array(
								  'wages_id' 		=> $wages_id,
								  'wages_item_id' 	=> $wages_item_id,
								  'deducted_by' 	=> $FishermanId,
								  'deducted_for' 	=> $res['Primary'],
								  'group_type_id' 	=> $res['group_type_id'],
								  'maingroup_id' 	=> $res['maingroup_id'],
								  'from_date' 		=> $from_date,
								  'to_date' 		=> $to_date,										  
								  'added_by' 		=> $this->userID,
								  'added_date' 		=> get_datetime('Y-m-d H:i:s'),
								  'action_microtime' => microtime(true) 
								  );
				//check if fisherman is primary or secondary
				//if fisherman is primary start
				// check group liability less than 0 and deposit amount as advance liability deduction.
				if($total_group_liability <= 0 && $FishermanId == $res['Primary'] && $FishermanId == $res['Secondary']){
					$liabData['advance_deduction'] = $group_liability_deduction_amount;
					$this->db->insert(LIABILITY_DEDUCTION, $liabData);
					$liability_data[] = $liabData;
					$group_liability_deduction_amount = 0;
					break;
				// check personal liability greater than 0 and deposit personal liability deduction.
				} else if($total_personal_liability > 0 && $FishermanId == $res['Primary'] && $FishermanId == $res['Secondary']){
					// check personal liability is bigger than deposit amount
					$remaining = $group_liability_deduction_amount - $total_personal_liability;
					$amount = $remaining >= 0 ? $total_personal_liability : $group_liability_deduction_amount;
					$liabData['amount'] = $amount;
					$this->db->insert(LIABILITY_DEDUCTION, $liabData);
					$liability_data[] = $liabData;
					//check total members in the group, if there is no more members then deposit remaining amount as advance liability deduction.
					if($total_members == 1 && ($total_group_liability - $total_personal_liability) >= 0 && $remaining > 0){
						$liabData['amount'] = 0;
						$liabData['advance_deduction'] = $remaining;
						$this->db->insert(LIABILITY_DEDUCTION, $liabData);
						$liability_data[] = $liabData;
						$group_liability_deduction_amount = 0;
					}else{
						// continue loop to deposit remaining amount to other primary member's liability.
						$group_liability_deduction_amount = $remaining;
					}	
				// deposite remaing amount to primary fisherman.
				}
				//if fisherman is primary end
				//if fisherman is secondary start
				else {
					if($FishermanId != $res['Primary']){
						if($secondary_deposited_amount < 0){
							$already_deposited = -($secondary_deposited_amount);
							$remaining = $already_deposited - $total_personal_liability;
							// check including entered dedopsite amount
							$including_entered_amount = 0;
							if($remaining <= 0){
								$including_entered_amount =	($already_deposited + $group_liability_deduction_amount) - $total_personal_liability;
								$amount = $including_entered_amount >= 0 ? $total_personal_liability : ($already_deposited+$group_liability_deduction_amount);
								$remaining_secondary_deposited_amount = $amount;
							}else{
								$amount = $total_personal_liability;
								$remaining_secondary_deposited_amount = -($remaining);
							}
							// Secondary paying total liability of primary fisherman
							$liabData['amount'] = $amount;
							$this->db->insert(LIABILITY_DEDUCTION, $liabData);
							$liability_data[] = $liabData;
							
							// parimary fisherman paying extra amount back to secondry fisherman
							/*if($res['group_liability_deducted'] > 0 && $res['group_liability_deducted'] != NULL){
								$liabData['amount'] = $res['group_liability_deducted'];
								$liabData['deducted_by'] = $res['Primary'];
								$liabData['deducted_for'] = $FishermanId;
								$this->db->insert(LIABILITY_DEDUCTION, $liabData);
								$liability_data[] = $liabData;
							}*/
							// deduct deposited amount to primary from secondry fisherman
							$reverse_deposit = ($remaining_secondary_deposited_amount < 0) ? $amount : $already_deposited;
							$liabData['amount'] = 0;
							$liabData['advance_deduction'] = -($reverse_deposit);
							$liabData['deducted_for'] = $FishermanId;
							$this->db->insert(LIABILITY_DEDUCTION, $liabData);
							$liability_data[] = $liabData;
							
							/*if($total_members == $i && ($amount - $reverse_deposit) > 0){
								$liabData['amount'] = 0; //($amount - $reverse_deposit);
								$liabData['advance_deduction'] = ($amount - $reverse_deposit);
								$liabData['deducted_for'] = $FishermanId;
								$this->db->insert(LIABILITY_DEDUCTION, $liabData);
								$liability_data[] = $liabData;								
							}*/
													
							$secondary_deposited_amount = $remaining_secondary_deposited_amount;
							$group_liability_deduction_amount = $including_entered_amount > 0 ? $including_entered_amount : $group_liability_deduction_amount;
							
							if($group_liability_deduction_amount > 0 && $secondary_deposited_amount > 0){
								break;
							}
						}else{
							if($group_liability_deduction_amount > 0){
								$remaining = $group_liability_deduction_amount - $total_personal_liability;
								$amount = $remaining >= 0 ? $total_personal_liability : $group_liability_deduction_amount;
								
								$liabData['amount'] = $amount;
								$this->db->insert(LIABILITY_DEDUCTION, $liabData);
								$liability_data[] = $liabData;
								if($remaining > 0){
									$group_liability_deduction_amount = $remaining;
								}else{
									$group_liability_deduction_amount = 0;
									break;
								}
							}else{
								break;
							}
						}
						//$where = array('deducted_by' => $FishermanId, 'deducted_for' => $FishermanId);
						//$advance_deposit = $this->db->select('SUM(advance_deduction) as advance_deposit')->get_where(LIABILITY_DEDUCTION, $where)->result_array();
						//if(!empty($advance_deposit)){
							//$dep_amount = $advance_deposit[0]['advance_deposit'];
							//$liabData['amount'] = $dep_amount;
							//$this->db->insert(LIABILITY_DEDUCTION, $liabData);
						//}
						//echo 'c='.$i.', PID='.$res['Primary'].', PL='.$total_personal_liability.', SEC_DEP = '. $dep_amount.', total_l = '.$total_group_liability .' ==========';
						//die;					
					}
				}
				//if fisherman is secondary end
			}
		
			$response_data = $liability_data;
		}		
		//printr($response_data);
		return $response_data;
	}	
	
	function insert_liability_data_MANISH($wages_id, $wages_item_id, $group_liability_deduction_amount, $FishermanId, $from_date, $to_date, $maingroup){
		
		$sql = 'SELECT sf.Primary, sf.Secondary, sf.group_type_id, sf.maingroup_id,
				
				IFNULL((SELECT fm.products_balance FROM '.FISHERMAN.' fm WHERE fm.status="Active" AND fm.ID = sf.Primary), 0.00) as products_balance,
				
				IFNULL((SELECT SUM(po.grand_total) FROM '.PRODUCT_OUTWARD.' po WHERE po.status="Active" AND po.outward_to="Fisherman" AND po.fisherman_id = sf.Primary), 0.00) as group_liability,
				
				IFNULL((SELECT SUM(poir.total_price) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.status="Active" AND poir.outward_to="Fisherman" AND poir.fisherman_id = sf.Primary ), 0.00) as returned_amt,
				
				IFNULL((SELECT SUM(cdp.product_liability) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status = "Active" AND cdp.fisherman_id=sf.Primary AND cdp.deposited_by = "Fisherman"), 0.00) as cash_deposited,
				
				IFNULL((SELECT (SUM(ld.amount) + SUM(ld.advance_deduction)) FROM '.LIABILITY_DEDUCTION.' ld WHERE ld.deducted_for = sf.Primary AND ld.status="Active"), 0.00) as group_liability_deducted
		
		FROM '.SECONDARYFISHERMAN.' sf 
		WHERE sf.Secondary = "'.$FishermanId.'" AND sf.group_status = "Joined" GROUP BY sf.Primary, sf.Secondary ORDER BY sf.ID ASC, sf.join_date ASC';
		
		$result = $this->db->query($sql)->result_array();
		//printr($result);
		$response_data = array();
		$liability_data = array();
		$liabData = array();
		if(!empty($result)){
			if($group_liability_deduction_amount < 0 || $group_liability_deduction_amount > 0){
				$total_group_members = count($result);
				$total_group_liability = 0;
				$liability_amount_array = array();
				foreach($result as $res){
					$total_liability = ($res['group_liability'] + $res['products_balance']) - ($res['returned_amt'] + $res['group_liability_deducted'] + $res['cash_deposited']);
					$liability_amount_array[] = array('deducted_for' 	=> $res['Primary'], 
													  'group_type_id' 	=> $res['group_type_id'],
								  					  'maingroup_id' 	=> $res['maingroup_id'],
													  'user_liability'	=> $total_liability
													  );
					$total_group_liability += $total_liability;
				}
				
				//printr($liability_amount_array);
				if($total_group_members == 1){
					//For single member
					$liability = $liability_amount_array[0];
					
					$liabData = array(
									  'wages_id' 		=> $wages_id,
									  'wages_item_id' 	=> $wages_item_id,
									  'deducted_by' 	=> $FishermanId,
									  'deducted_for' 	=> $liability['deducted_for'],
									  'group_type_id' 	=> $liability['group_type_id'],
									  'maingroup_id' 	=> $liability['maingroup_id'],
									  'from_date' 		=> $from_date,
									  'to_date' 		=> $to_date,										  
									  'added_by' 		=> $this->userID,
									  'added_date' 		=> get_datetime('Y-m-d H:i:s'),
									  'action_microtime' => microtime(true) 
									  );
					
					if($liability['user_liability'] > 0){
						$remaining = $liability['user_liability'] - $group_liability_deduction_amount;							
						if($remaining >= 0){
							$liabData['amount'] = $group_liability_deduction_amount;
							$liabData['advance_deduction'] = 0;
						}else{
							$liabData['amount'] = $liability['user_liability'];
							$liabData['advance_deduction'] = abs($remaining);
						}
					}else{
						$liabData['amount'] = 0;
						$liabData['advance_deduction'] = $group_liability_deduction_amount;
					}
					//printr($liabData);
					$this->db->insert(LIABILITY_DEDUCTION, $liabData);
				}
				else{
				//For mulitple members
					
					foreach($liability_amount_array as $liability){
						$liabData = array(
										  'wages_id' 		=> $wages_id,
										  'wages_item_id' 	=> $wages_item_id,
										  'deducted_by' 	=> $FishermanId,
										  'deducted_for' 	=> $liability['deducted_for'],
										  'group_type_id' 	=> $liability['group_type_id'],
										  'maingroup_id' 	=> $liability['maingroup_id'],
										  'from_date' 		=> $from_date,
										  'to_date' 		=> $to_date,										  
										  'added_by' 		=> $this->userID,
										  'added_date' 		=> get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true) 
										  );
						if($liability['user_liability'] > 0){
							if($group_liability_deduction_amount <= $liability['user_liability']){							
								$liabData['amount'] = $group_liability_deduction_amount;
								$group_liability_deduction_amount = 0;
								$this->db->insert(LIABILITY_DEDUCTION, $liabData);
								break;
							}else{
								$liabData['amount'] = $liability['user_liability'];
								$group_liability_deduction_amount = $group_liability_deduction_amount - $liability['user_liability'];
								$this->db->insert(LIABILITY_DEDUCTION, $liabData);
							}
						}
					} //Foreach end;
					//If $group_liability_deduction_amount is still have some amount greater than 0, it will recorded as advance_deduction
					if($group_liability_deduction_amount > 0){
						$liabData = array(
								  'wages_id' 			=> $wages_id,
								  'wages_item_id' 		=> $wages_item_id,
								  'amount' 				=> 0,
								  'advance_deduction'	=> $group_liability_deduction_amount,
								  'deducted_by' 		=> $FishermanId,
								  'deducted_for' 		=> $FishermanId,
								  'group_type_id' 		=> $result[0]['group_type_id'],
								  'maingroup_id' 		=> $result[0]['maingroup_id'],
								  'from_date' 			=> $from_date,
								  'to_date' 			=> $to_date,										  
								  'added_by' 			=> $this->userID,
								  'added_date' 			=> get_datetime('Y-m-d H:i:s'),
								  'action_microtime' 	=> microtime(true) 
								  );
						$this->db->insert(LIABILITY_DEDUCTION, $liabData);
					}
				}
			}else{
				$liabData = array(
								  'wages_id' 			=> $wages_id,
								  'wages_item_id' 		=> $wages_item_id,
								  'amount' 				=> 0,
								  'advance_deduction'	=> 0,
								  'deducted_by' 		=> $FishermanId,
								  'deducted_for' 		=> $FishermanId,
								  'group_type_id' 		=> $result[0]['group_type_id'],
								  'maingroup_id' 		=> $result[0]['maingroup_id'],
								  'from_date' 			=> $from_date,
								  'to_date' 			=> $to_date,										  
								  'added_by' 			=> $this->userID,
								  'added_date' 			=> get_datetime('Y-m-d H:i:s'),
								  'action_microtime' 	=> microtime(true) 
								  );
				$this->db->insert(LIABILITY_DEDUCTION, $liabData);
			}
		}		
		//printr($response_data);
		return $response_data;
	}	

	/*
	--Fisherman Wages Functions End
	*/
	
	
	/*
	--Samiti Wages Functions Start
	*/
	//Listing: Samiti Wages
	function calculate_samiti_wages(){
		$actions = checkUserPermission('wages/calculate_samiti_wages', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_edit_url_path(site_url('wages/add_samiti_wages'));
			$crud->set_edit_button_class('btn btn-primary loadInIframe');
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('wages/add_samiti_wages'));
			$crud->set_add_button_class('btn btn-primary loadInIframe');
		}
		
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('wages/view_samiti_wages'));
		}else{
			$crud->unset_read();
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		$crud->unset_delete();
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
	
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
		
		$crud->set_subject('Samiti Wages');
		$crud->set_table(WAGES);
		$crud->set_relation('MainGroup', MAINGROUP_TYPE, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES.'.status !='=>'Deleted'));
		$crud->where(array(WAGES.'.editable !='=>'Lock'));
		$crud->where(array(WAGES.'.wages_for'=>'Group'));
		
		$crud->columns('from_date', 'to_date', 'MainGroup', 'added_by');
		$crud->display_as('MainGroup', 'Group Type');
		
		if($crud->getState() == 'ajax_list'){
			$postData = $this->input->post();
			if(isset($postData['search_field']) && !empty($postData['search_field'])){
				foreach($postData['search_field'] as $key=>$value){
					if($value == 'from_date' || $value == 'to_date' ){
						$_POST['search_text'][$key] = get_date('Y-m-d', str_replace('/', '-', $postData['search_text'][$key]));
					}
				}
			}
		}
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Wages Sheet', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		//$data['scriptsrc'] = array(base_url('assets/modules/wages/advance_wages.js'));
		$this->template->set('document_title', 'Samiti Wages');
		$this->template->layout($outputData);
	}
	
	//ADD: Samiti Wages
	function add_samiti_wages($wage_id=NULL){
		$data['page_title'] 	= 'Calculate Samiti Wages';
		$data['wages_data'] = array('ID'=>'', 'from_date'=>'', 'to_date'=>'', 'MainGroup'=>'');
		$data['wage_items'] = '';
		
		if(!empty($wage_id) && $wage_id !== NULL){
			$data['page_title'] 	= 'Edit Samiti Wages';
			$result = $this->WM->get_wages_data($wage_id);
			
			if(!empty($result)){
				$data['wages_data'] = $result['wage_info'];
				
				$from_date = @$data['wages_data']['from_date'];
				$to_date = @$data['wages_data']['to_date'];
				$maingroup = @$data['wages_data']['MainGroup'];
				$data['fdt'] = $from_date;
				$data['tdt'] = $to_date;
				$data['mg'] = $maingroup;
				$data['wage_items'] = $this->WM->get_samiti_wages_items($from_date, $to_date, $maingroup, $wage_id);
				
				$view_data = $this->load->view('wages/samiti_wages/ajax_samiti_wages_list_v', $data, true);
				$data['wage_items'] = $view_data;
			}else{
				$this->messageci->set('Invalid request','error');
				redirect('wages/wages_sheet', 'refresh');
			}
		}
		
		$data['groups']	= $this->WM->get_mgtype_data();
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/bootstrap-switch/static/stylesheets/bootstrap-switch.css')
												 ));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/bootstrap-switch/static/js/bootstrap-switch.min.js'),
												site_url('wages/assets/js/jquery.table2excel.min.js'),
												site_url('wages/assets/js/jQuery.print.min.js'),
												site_url('wages/assets/js/samiti_wages_sheet.js')
												)
							 );
		
		//printr($data['dt_data']);
		$data['content_view'] 	= 'wages/samiti_wages/add_samiti_wages_v';
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	//Ajax: Get Samiti calculated dailytoll
	function ajax_samiti_wages_item(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> No data found, Please try again.</div>', 'data' => '');
		$formValidation = $this->__setFormRules('ajax_samiti_wages_item');
		if($formValidation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('maingroup');
			
			$result = $this->db->query("SELECT * from ".WAGES." WHERE wages_for = 'Group' AND status='Active' AND MainGroup = '".$group_type."' AND
										((from_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(to_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(from_date <= '".$from_date."' AND to_date >= '".$to_date."'))")->result_array();
			if(!empty($result)){
				$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> There is already calculated wages for this time period of this selected group. Please click <a class="label label-info" href="'.site_url('wages/add_samiti_wages/'.@$result[0]['ID']).'">here</a> to load calculated wages </div>', 'data' => '');
			}else{
				$wages_data['wage_items'] = $this->WM->get_samiti_wages_items($from_date, $to_date, $group_type);
				$wages_data['fdt'] = $from_date;
				$wages_data['tdt'] = $to_date;
				$wages_data['mg'] = $group_type;
				//printr($wages_data['wage_items']);
				if(!empty($wages_data['wage_items'])){
					$view_data = $this->load->view('wages/samiti_wages/ajax_samiti_wages_list_v', $wages_data, true);
					$data = array('status' => 'success', 'message' => 'Wages data', 'data' => $view_data);
				}else{
					$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> No wages data found on selected dates, Please try again.</div>', 'data' => '');
				}
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data' => '');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Save: Samiti wages
	function save_samiti_wages_row(){
		$data = array('status' => 'dagner', 'message' => 'There is some error, Please try again.', 'data' => '');
		
		$form_validation = $this->__setFormRules('insert_wage_sheet');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			
			$group_type_id = $this->input->post('group_type_id');
			$MainGroup = $this->input->post('MainGroup');
			$FishermanId = $this->input->post('FishermanId');
			
			$MajorFee = $this->input->post('MajorFee');
			$MinorFee = $this->input->post('MinorFee');
			$SawalFee = $this->input->post('SawalFee');
			
			$major_wt = $this->input->post('major_wt');
			$minor_wt = $this->input->post('minor_wt');
			$sawal_wt = $this->input->post('sawal_wt');
			
			$major_wage = $this->input->post('major_wage');
			$minor_wage = $this->input->post('minor_wage');
			$sawal_wage = $this->input->post('sawal_wage');
			
			$TotalWage = $this->input->post('TotalWage');
			
			$group_liability = $this->input->post('group_liability');
			$advance_wages = $this->input->post('advance_wages');
			
			$GovDeduction = $this->input->post('GovDeduction');
			
			$GroupLiability = $this->input->post('gld_amount');
			$AdvanceLiability = $this->input->post('ald_amount');
			
			$final_wages = $this->input->post('final_wages');
			
			$mode = $this->input->post('mode');
			$wages_id = $this->input->post('wages_id');
			
			$wages_data = array('wages_for' 	=> 'Group',
							    'from_date' 	=> $from_date,
								'to_date' 		=> $to_date,
							    'MainGroup' 	=> $group_type_id,
							    'added_by' 		=> $this->userID,
							    'added_date' 	=> get_datetime('Y-m-d H:i:s'),
							    'action_microtime' => microtime(true)
							   );
			
			if(!empty($wages_id)){
				//update wage
				unset($wages_data['added_by'], $wages_data['added_date']);
				$wages_data['updated_by'] = $this->userID; 
				$wages_data['updated_date'] = get_datetime('Y-m-d H:i:s');
				
				$result = $this->db->where(array('ID'=>$wages_id))->update(WAGES, $wages_data);
			}else{
				//insert wage
				$result = $this->db->query("SELECT * from ".WAGES." WHERE wages_for = 'Group' AND status='Active' AND MainGroup = '".$group_type_id."' AND
										((from_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(to_date BETWEEN '".$from_date."' AND '".$to_date."') OR 
										(from_date <= '".$from_date."' AND to_date >= '".$to_date."'))")->result_array();
				if(!empty($result)){
					$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> There is already calculated wages for this time period of this selected group. Please click <a class="label label-info" href="'.site_url('wages/add_samiti_wages/'.@$result[0]['ID']).'">here</a> to load calculated wages </div>', 'data' => '');
				}else{
					$result = $this->db->insert(WAGES, $wages_data);
					$wages_id = $this->db->insert_id();
				}
			}
			
			if(!$result){
				$data = array('status' => 'danger', 'message' => 'Wages updated failed', 'data' => '' );
			}else{
				$wages_item_data = array( 'wages_id' 			=> $wages_id,
										  'wages_for' 			=> 'Group',
										  'from_date' 			=> $from_date,
										  'to_date' 			=> $to_date,
										  
										  'group_type_id' 		=> $group_type_id,
										  'MainGroup' 			=> $MainGroup,
										  'FishermanId' 		=> $FishermanId,
										  
										  'MajorFee' 			=> $MajorFee,
										  'MinorFee' 			=> $MinorFee,
										  'SawalFee' 			=> $SawalFee,
										  
										  'major_wt' 			=> $major_wt,
										  'minor_wt' 			=> $minor_wt,
										  'sawal_wt' 			=> $sawal_wt,
										  
										  'major_wage' 			=> $major_wage,
										  'minor_wage' 			=> $minor_wage,
										  'sawal_wage' 			=> $sawal_wage,
										  
										  'TotalWage' 			=> $TotalWage,
										  
										  'group_liability' 	=> $group_liability,
										  'advance_wages' 		=> $advance_wages,
										  
										  'GovDeduction' 		=> $GovDeduction,
										  
										  'GroupLiabilityDeduction' => $GroupLiability,
										  'AdvanceWagesDeduction' 	=> $AdvanceLiability,
										  
										  'final_wages' 		=> $final_wages,
										  'added_by' 			=> $this->userID,
							      		  'added_date' 			=> get_datetime('Y-m-d H:i:s'),
							    	      'action_microtime' 	=> microtime(true)
										  );
				
				$mode = $this->input->post('mode');
				$wages_item_id = $this->input->post('item_id');
				
				if($mode == 'add'){
					$where = array('from_date' 		=> $from_date,
								   'wages_for' 		=> 'Group',
								   'to_date' 		=> $to_date,								  
								   'group_type_id' 	=> $group_type_id,
								   'MainGroup' 		=> $MainGroup,
								   'FishermanId' 	=> $FishermanId,
								   'status'			=>'Active');
					$checkExistance = $this->db->where($where)->count_all_results(WAGESITEM);
					if($checkExistance <= 0){
						$result = $this->db->insert(WAGESITEM, $wages_item_data);
						$wages_item_id = $this->db->insert_id();
						
						if($wages_item_id){
							
							$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'edit', 'liability_data'=>'');
							$data = array('status' => 'success', 'message' => 'Wages item inserted.', 'data' => $response_data );
						}else{
							$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'add', 'liability_data'=>'');
							$data = array('status'=>'danger', 'message'=>'Failed to insert wages items, Please try again.', 'data'=>$response_data );
						}
						
					}else{
						$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'add', 'liability_data'=>'');
						$data = array('status'=>'danger', 'message'=>'<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i>There is already calculated wages for this time period of this selected group, Please reload the page and try again.</div>', 'data'=>$response_data);						
						
					}
				}elseif($mode == 'edit' && !empty($wages_item_id)){
					unset($wages_item_data['added_by'], $wages_item_data['added_date']);
					$wages_item_data['updated_by'] = $this->userID; 
					$wages_item_data['updated_date'] = get_datetime('Y-m-d H:i:s');
					$result = $this->db->where(array('ID'=>$wages_item_id))->update(WAGESITEM, $wages_item_data);
					
					if($result){
						$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'edit', 'liability_data'=>'');
						$data = array('status'=>'success', 'message'=>'Wages item updated.', 'data'=>$response_data );
					}else{
						$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>'edit', 'liability_data'=>'');
						$data = array('status'=>'danger', 'message'=>'Failed to updated wages items, Please try again.', 'data'=>$response_data );
					}
				}else{
					$response_data = array('wages_id' => $wages_id, 'item_id' => '', 'mode' => 'add', 'liability_data'=>'');
					$data = array('status' => 'danger', 'message' => 'Invalid request, Please reload the page.', 'data' => '' );
				}
			}
		}else{
			$wages_id = $this->input->post('wages_id');
			$mode = $this->input->post('mode');
			$wages_item_id = $this->input->post('item_id');
			$response_data = array('wages_id'=>$wages_id, 'item_id'=>$wages_item_id, 'mode'=>$mode, 'liability_data'=>'');
			
			$data = array('status'=>'dagner', 'message'=>validation_errors(), 'data'=>$response_data);
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	/*
	--Samiti Wages Functions End
	*/
	
	//Ajax function start
	
	function ajax_get_maingroup(){
		$data = array('status'=>'danger', 'msg'=>'Maingroup data not found', 'data'=>'<option value="">No group found</option>');
		$mg_type = $this->input->post('mg_type');
		if(!empty($mg_type)){
			$result = $this->WM->get_mg_data($mg_type);
			$maingroups = '<option value="">Select Group</option>';
			if(!empty($result)){
				foreach($result as $res){
					$maingroups .= '<option value="'.$res['ID'].'">'.$res['Name'].'</option>>';
				}
				$data = array('status'=>'success', 'msg'=>'Maingroup data.', 'data'=>$maingroups);
			}
		}		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function ajax_get_mg_fishers(){
		$data = array('status'=>'danger', 'msg'=>'Fisherman data not found', 'data'=>'<option value="">No fisherman found</option>');
		$maingroup = $this->input->post('maingroup');
		$f_ids = $this->input->post('f_ids');
		if(!empty($maingroup)){
			$result = $this->WM->get_mg_fisherman($maingroup, $f_ids);
			$op_html = '<option value="">Select Fisherman</option>';
			if(!empty($result['result1'])){
				foreach($result['result1'] as $res){
					$op_html .= '<option value="'.$res['id'].'">'.$res['text'].'</option>>';
				}
				$data = array('status'=>'success', 'msg'=>'Fishers data.', 'data1'=>$op_html, 'data2'=>$result['result2']);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Get daily toll info by fisherman id
	function dailytoll_info(){
		$fid = $this->input->get('fid');
		$fdt = $this->input->get('fdt');
		$tdt = $this->input->get('tdt');
		$mg = $this->input->get('mg');
		if($fid != NULL){
			$result = $this->db->select('Name')->get_where(FISHERMAN, array('ID'=>$fid))->result_array();
			$fName = '';
			if(!empty($result)){
				$data['fName'] = $result[0]['Name'];
			}
			$data['fdt'] = get_date('',$fdt);
			$data['tdt'] =  get_date('',$tdt);
			
			
			$data['page_title'] = '<button type="button" data-pid="table#fisher-wages" data-fname="'.$data['fName'].' Toll Info" class="btn btn-warning export_fisherman_data"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                        			<button type="button" data-pid="table#fisher-wages" class="btn btn-success print_fisherman_data"> <i class="fa fa-print"></i> Print</button>';
									
			$data['dti_data'] = $this->WM->get_fisherman_dti($fid, $fdt, $tdt, $mg);
			//printr($data['dti_data']);
			$data['from_date'] = $fdt;
			$data['to_date'] = $tdt;
			
			$data['setup_form'] = $this->load->view('fisherman_dailytoll_info_v', $data, true);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($data));
		}
		
	}
	
	//Show secondary fisheman liabilities
	function ajax_user_liab_info(){
		$fid = $this->input->get('fid');
		$fdt = $this->input->get('fdt');
		$tdt = $this->input->get('tdt');
		$mg = $this->input->get('mg');
		if($fid != NULL){
			$result = $this->db->select('Name')->get_where(FISHERMAN, array('ID'=>$fid))->result_array();
			$fName = '';
			if(!empty($result)){
				$data['fName'] = $result[0]['Name'];
			}
			$data['fdt'] = get_date('',$fdt);
			$data['tdt'] =  get_date('',$tdt);
			
			
			$data['page_title'] = '<button type="button" data-pid="table#fisher-wages" data-fname="'.$data['fName'].' Toll Info" class="btn btn-warning export_fisherman_data"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                        			<button type="button" data-pid="table#fisher-wages" class="btn btn-success print_fisherman_data"> <i class="fa fa-print"></i> Print</button>';
									
			$data['liability_info'] = $this->WM->get_group_liability($fid, $fdt, $tdt, $mg);
			//printr($data['dti_data']);
			$data['from_date'] = $fdt;
			$data['to_date'] = $tdt;
			$data['page_title'] = 'Group Liability Info';
			$data['setup_form'] = $this->load->view('ajax_group_liability_info_v', $data, true);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($data));
		}
	}
	
	function view_wages_sheet($wage_id=NULL){
		$data['page_title'] 	= 'Wages Sheet';
		$data['wages_data'] = array('ID'=>'', 'from_date'=>'', 'to_date'=>'', 'MainGroup'=>'');
		$data['wage_items'] = '';
		
		if(!empty($wage_id) && $wage_id !== NULL){
			$data['page_title'] 	= 'Wages Sheet';
			$result = $this->WM->get_wages_data($wage_id);
			
			if(!empty($result)){
				$data['wages_data'] = $result['wage_info'];
				
				$from_date = @$data['wages_data']['from_date'];
				$to_date = @$data['wages_data']['to_date'];
				$maingroup = @$data['wages_data']['MainGroup'];
				$data['fdt'] = $from_date;
				$data['tdt'] = $to_date;
				$data['mg'] = $maingroup;
				$data['wage_items'] = $this->WM->get_wages_items($from_date, $to_date, $maingroup, $wage_id);
				
				$view_data = $this->load->view('wages/view_wages_list_data_v', $data, true);
				$data['wage_items'] = $view_data;
			}else{
				$this->messageci->set('Invalid request','error');
				redirect('wages/wages_sheet', 'refresh');
			}
		}
		
		$data['all_maingroup']	= $this->WM->get_all_maingroup_data();
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												site_url('wages/assets/js/jquery.table2excel.min.js'),
												site_url('wages/assets/js/jQuery.print.min.js'),
												site_url('wages/assets/js/wages_sheet.js')
												)
							 );
		
		//printr($data['dt_data']);
		$data['content_view'] 	= 'wages/view_wages_sheet_v';
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}

	//Adv to samiti
	function samiti_advance_wages_09July2018(){
		$actions = checkUserPermission('wages/samiti_advance_wages', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('wages/add_samiti_advance_wages'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_edit_url_path(site_url('wages/add_samiti_advance_wages'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		
		if(!in_array('view', $actions)){
			$crud->unset_read();
		}else{
			$crud->set_read_url_path(site_url('wages/view_samiti_advance_wages'));
			$crud->set_read_button_class('loadActionForm');
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		
		$crud->unset_delete();
		if(in_array('delete',$actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}		
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
				
		
		$crud->set_subject('Advance to Samiti');
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.status !='=>'Deleted'));
		$crud->where(array(WAGES_ADVANCE.'.editable !='=>'Lock'));
		$crud->where(array(WAGES_ADVANCE.'.Fisherman'=>0));
		
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('Date', 'receipt_number', 'maingroup_id', 'Amount', 'added_by');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		
		$output = $crud->render();
		$data = array('page_title'=> 'Advance to Samiti', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('wages/assets/js/setup.js')
											));
		
		$this->template->set('document_title', 'Advance to Samiti');
		$this->template->layout($outputData);
	}
	
	function samiti_advance_wages(){
		$actions = checkUserPermission('wages/samiti_advance_wages', $this->uri->segment(3));
		$filter_data = $this->input->get('fileter_samiti');			
		if($filter_data){
			$filter['from_date'] = $this->input->get('from_date');
			$filter['to_date'] = $this->input->get('to_date');
			$filter['receipt_number'] = $this->input->get('receipt_number');
			$filter['party_name'] = $this->input->get('party_name');
			
			$this->session->set_userdata('samiti_advance_wages_filter', $filter);
		}
		$filterdata_sess = $this->session->userdata('samiti_advance_wages_filter');
		if(!isset($filterdata_sess) && !is_array($filterdata_sess)){
			$filterdata_sess = array('from_date'=>'', 'to_date'=>'', 'receipt_number'=>'', 'party_name'=>'');
		}
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('wages/add_samiti_advance_wages'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_edit_url_path(site_url('wages/add_samiti_advance_wages'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		
		if(!in_array('view', $actions)){
			$crud->unset_read();
		}else{
			$crud->set_read_url_path(site_url('wages/view_samiti_advance_wages'));
			$crud->set_read_button_class('loadActionForm');
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		
		$crud->unset_delete();
		if(in_array('delete',$actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}		
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
				
		
		$crud->set_subject('Advance to Samiti');
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.status !='=>'Deleted'));
		$crud->where(array(WAGES_ADVANCE.'.editable !='=>'Lock'));
		$crud->where(array(WAGES_ADVANCE.'.Fisherman'=>0));
		
		if(isset($filterdata_sess['from_date']) && !empty($filterdata_sess['from_date'])){
			$date_string = str_replace('/','-', $filterdata_sess['from_date']);
			$dt = get_date('Y-m-d', $date_string);
			$crud->where(array(WAGES_ADVANCE.'.Date >='=> $dt));
		}		
		if(isset($filterdata_sess['to_date']) && !empty($filterdata_sess['to_date'])){
			$date_string = str_replace('/','-', $filterdata_sess['to_date']);
			$dt = get_date('Y-m-d', $date_string);
			$crud->where(array(WAGES_ADVANCE.'.Date <='=> $dt));
		}
		if(isset($filterdata_sess['receipt_number']) && !empty($filterdata_sess['receipt_number'])){
			$crud->where(array(WAGES_ADVANCE.'.receipt_number'=>$filterdata_sess['receipt_number']));
		}
		if(isset($filterdata_sess['party_name']) && !empty($filterdata_sess['party_name'])){
			$crud->where(array(WAGES_ADVANCE.'.maingroup_id'=>$filterdata_sess['party_name']));
		}		
		
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('Date', 'receipt_number', 'maingroup_id', 'Amount', 'added_by');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		$crud->display_summary('Amount');
		$output = $crud->render();
		
		$data = array('page_title'=> 'Advance to Samiti', 'content_view'=>'wages/samiti_advance_wages_listing_v');
		
		//$data = array_merge($data, $filterdata_sess);
		  
		$outputData = array_merge((array)$output, $data, $filterdata_sess);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('wages/assets/js/setup.js')
											));
		$this->template->set('scripts', "<script>
											$(document).ready(function() { 
												$('.datepicker').datepicker({
													format: 'dd/mm/yyyy',
													autoclose: true,
													todayHighlight: true
												});
												$('.select2').select2();
											});
											</script>");
		$this->template->set('document_title', 'Advance to Samiti');
		$this->template->layout($outputData);
	}
	
	function view_samiti_advance_wages($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('group_type'	=> '',
								'maingroup_id'	=> '',
								'Fisherman'		=> '',
								'receipt_number'=> '',
								'advance_to'	=> '',
								'Date'			=> '',
								'Amount'		=> '',
								'remark'		=> ''
								);
		$data['page_title'] = 'Advance to Samiti';	
		
		if($id != NULL){
			$advance_wages = $this->WM->get_advance_wages($id);
			if(!empty($advance_wages)){
				$data['dbdata'] = $advance_wages;
			}
		}
		
		$data['allgrouptype'] = $this->WM->get_mgtype_data();
		$data['scriptsrc'] = array(site_url('wages/assets/js/samiti_advance_wages.js'));
		$data['setup_form'] = $this->load->view('viewer_pages/view_samiti_advance_wages_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//ADD Adv to samiti
	function add_samiti_advance_wages($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('group_type'	=> '',
								'maingroup_id'	=> '',
								'Fisherman'		=> '',
								'receipt_number'=> '',
								'advance_to'	=> '',
								'Date'			=> '',
								'Amount'		=> '',
								'remark'		=> ''
								);
		$data['page_title'] = 'Add Advance to Samiti';	
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('wages/add_samiti_advance_wages');
		
		
		$form_valid = $this->__setFormRules('add_samiti_advance_wages');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode']; 
			//Preparing outward data
			$prepData = array('group_type' 		=> $post['group_type'],
							  'maingroup_id'	=> $post['maingroup'],
							  'Fisherman'		=> 0,//$post['fisherman'],
							  'advance_to'		=> 'Group',
							  'receipt_number'	=> $post['receipt_number'],
							  'Date'			=> get_date('Y-m-d', str_replace('/', '-', $this->input->post('date'))),
							  'Amount'			=> $post['amount'],
							  'remark'			=> $post['remark'],
							  'added_by'		=> $this->userID,
							  'added_date'		=> get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=> microtime(true),
							  );
			
			//printr($prepData);
			if($id != NULL && $action_mode == 'edit'){
				//do update
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('ID', $id)->update(WAGES_ADVANCE, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('wages/add_advance_wage/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(WAGES_ADVANCE, $prepData);
				$id = $this->db->insert_id();
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('wages/add_advance_wage/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data inserted successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to insert. Please try again.';
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		if($id != NULL){
			//printr($id);
			$data['page_title'] = 'Edit Advance to Samiti';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('wages/add_samiti_advance_wages/'.$id);
			$advance_wages = $this->WM->get_advance_wages($id);
			//printr($dbdata);
			if(!empty($advance_wages)){
				$data['dbdata'] = $advance_wages;
			}
		}
		
		$data['allgrouptype'] = $this->WM->get_mgtype_data();
		//$data['allmaingroup'] = $this->WM->get_mg_data();
		$data['scriptsrc'] = array(site_url('wages/assets/js/samiti_advance_wages.js'));
		$data['setup_form'] = $this->load->view('wages/samiti_advance_wages_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function fisherman_wages_sheet(){
		$actions = checkUserPermission('wages/fisherman_wages_sheet', $this->uri->segment(3));
		
		$data = array('from_date' => '', 
					  'to_date' => '', 
					  'group_type' => '', 
					  'maingroup' => '', 
					  'serial_no'=> AXIS_FISHERMAN_SERIAL_NUMBER, 
					  'sheet_for' => 'axis');
		
		$data['allgrouptype'] = $this->WM->get_mgtype_data();
		$data['allmaingroup'] = $this->WM->get_mg_data();
		$data['page_title'] = 'Fisherman Wages Sheet';
		$data['content_view'] = 'wages/fisherman_wages_sheet_for_bank_v';
		$data['data_url'] = 'wages/ajax_fisherman_wages_sheet';
			
		$this->template->set('document_title', $data['page_title']);
		
		$stylesheet_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
									base_url('assets/plugins/DataTables/media/css/jquery.dataTables.min.css'),
									base_url('assets/plugins/select2/dist/css/select2.min.css')
									);
		$scriptsrc_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
									base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
									base_url('assets/plugins/DataTables/media/js/jquery.dataTables.min.js'),
									site_url('wages/assets/js/jquery.table2excel.min.js'),
									site_url('wages/assets/js/jQuery.print.min.js'),
									site_url('wages/assets/js/reports.js'),
									site_url('wages/assets/js/wages_bank_sheet.js'),
									site_url('wages/assets/js/extra.js')
									);
		$this->template->set('stylesheet', $stylesheet_array);	
		$this->template->set('scriptsrc', $scriptsrc_array);
		$this->template->layout($data);
	}
	
	function ajax_fisherman_wages_sheet(){
		$form_validation = $this->__setFormRules('ajax_fisherman_wages_sheet');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			$serial_no = $this->input->post('serial_no');
			$sheet_for = $this->input->post('sheet_for');
			$where = '';
			if(!empty($from_date)){
				$where .=  " AND wi.from_date >='".$from_date."' ";
			}
			if(!empty($to_date)){
				$where .=  " AND wi.to_date <='".$to_date."' ";
			}
			if(!empty($group_type)){
				$where .=  " AND wi.group_type_id ='".$group_type."' ";
			}
			if(!empty($maingroup)){
				$where .=  " AND wi.MainGroup ='".$maingroup."' ";
			}
			
			$sql = "SELECT ANY_VALUE(wi.ID) as ID, 
					   MAX(wi.from_date) as from_date, 
					   MAX(wi.to_date) as to_date, 
					   SUM(wi.final_wages) as final_wages, 
					   fm.Name as ben_name, fm.IfscCode, 
					   fm.AccountNo as ben_AccountNo
				FROM ".WAGESITEM." wi
				LEFT JOIN ".FISHERMAN." fm ON fm.ID = wi.FishermanId
				LEFT JOIN ".MAINGROUP." mg ON mg.ID = wi.MainGroup  
				WHERE wi.status != 'Deleted' AND wi.wages_for = 'Fisherman' ". $where ." GROUP BY wi.FishermanId, fm.Name, fm.IfscCode, fm.AccountNo ORDER BY fm.Code ASC";
			
			$result = $this->db->query($sql)->result_array();
			//echo $this->db->last_query();
			$thead = $tbody = '';
			
			if(!empty($result)){
				if($sheet_for == 'axis'){
					$thead = '<tr>
								<th> A </th>
								<th> Amount </th>
								<th> Date </th>
								<th> Beneficiary a/c name </th>
								<th> Beneficiary a/c no </th>
								<th> SMS EMAIL (Debit Party) </th>
								<th> Email/Mobile Details </th>
								<th> Sender a/c name (Debit a/c name) </th>
								<th> Sender a/c no (Debit a/c no) </th>
								<th> S.No. </th>
								<th> Beneficiary Bank IFSC Code </th>
								<th> Sender a/c type (Debit a/c type) </th>
								<th> Send to Rec (Transaction Narration) </th>
							  </tr>';
				}elseif($sheet_for == 'icici'){
					$thead = '<tr>
								<th> Tran Refernece Number </th>
								<th> Amount </th>
								<th> Sender a/c type (Debit a/c type) </th>
								<th> Sender a/c no (Debit a/c no) </th>
								<th> Sender name (Debit a/c name) </th>
								<th> SMS EMAIL (Debit Party) </th>
								<th> Email/Mobile Details </th>
								<th> Beneficiary Bank IFSC Code </th>
								<th> Beneficiary a/c Type </th>
								<th> Beneficiary a/c no </th>
								<th> Beneficiary a/c name </th>
								<th> Send to Rec (Transaction Narration) </th>
							  </tr>';
				}
				
				$mso = 'mso-number-format:"\@"';
				$total_amount = 0;
				foreach($result as $res){
					if($res['final_wages'] > 0){
						if($sheet_for == 'axis'){
						$tbody .= '<tr>';
						$tbody .= '  <td style='.$mso.'>N</td>';
						$tbody .= '  <td>'.$res['final_wages'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.get_date('d-m-Y').'</td>';
						$tbody .= '  <td style='.$mso.'>'.$res['ben_name'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.$res['ben_AccountNo'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.AXIS_EMAIL.'</td>';
						$tbody .= '  <td style='.$mso.'>'.AXIS_MOBILE.'</td>';
						$tbody .= '  <td style='.$mso.'>'.AXIS_ACCOUNT_NAME.'</td>';
						$tbody .= '  <td style='.$mso.'>'.AXIS_ACCOUNT_NUMBER.'</td>';
						$tbody .= '  <td style='.$mso.'>'.$serial_no++.'</td>';
						$tbody .= '  <td style='.$mso.'>'.$res['IfscCode'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.AXIS_ACCOUNT_TYPE.'</td>';
						$tbody .= '  <td style='.$mso.'>'.FISHERMAN_PARTICULAR.'</td>';
						$tbody .= '</tr>';
												
					}elseif($sheet_for == 'icici'){
						
						$tbody .= '<tr>';
						$tbody .= '  <td style='.$mso.'></td>';
						$tbody .= '  <td>'.$res['final_wages'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.ICICI_ACCOUNT_TYPE.'</td>';
						$tbody .= '  <td style='.$mso.'>'.ICICI_ACCOUNT_NUMBER.'</td>';
						$tbody .= '  <td style='.$mso.'>'.ICICI_ACCOUNT_NAME.'</td>';
						$tbody .= '  <td style='.$mso.'>'.ICICI_EMAIL.'</td>';
						$tbody .= '  <td style='.$mso.'>'.ICICI_MOBILE.'</td>';
						$tbody .= '  <td style='.$mso.'>'.$res['IfscCode'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.ICICI_BENEFICIARY_ACCOUNT_TYPE.'</td>';
						$tbody .= '  <td style='.$mso.'>'.$res['ben_AccountNo'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.$res['ben_name'].'</td>';
						$tbody .= '  <td style='.$mso.'>'.FISHERMAN_PARTICULAR.'</td>';
						$tbody .= '</tr>';
					}
					}
					$total_amount += $res['final_wages'];
				}
				$table = array('thead'=>$thead, 'tbody'=>$tbody, 'total_amount'=>$total_amount);
				$data = array('status' => 'success', 'message' => 'Wages Sheet', 'data'=>$table);
			}else{
				$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button>No Data Found</div>', 'data' => '');
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function samiti_wages_sheet(){
		$data = array('from_date' => '', 
					  'to_date' => '', 
					  'group_type' => '', 
					  'maingroup' => '', 
					  'serial_no' => AXIS_GROUP_SERIAL_NUMBER, 
					  'sheet_for' => 'axis');
		
		$actions = checkUserPermission('wages/samiti_wages_sheet', $this->uri->segment(3));
		
		$data['allgrouptype'] = $this->WM->get_mgtype_data();
		$data['allmaingroup'] = $this->WM->get_mg_data();
		$data['page_title'] = 'Samiti Wages Sheet';
		$data['content_view'] = 'wages/samiti_wages_sheet_for_bank_v';
		$data['data_url'] = 'wages/ajax_samiti_wages_sheet';
		$this->template->set('document_title', $data['page_title']);
		
		$stylesheet_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
									base_url('assets/plugins/DataTables/media/css/jquery.dataTables.min.css'),
									base_url('assets/plugins/select2/dist/css/select2.min.css')
									);
		$scriptsrc_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
									base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
									base_url('assets/plugins/DataTables/media/js/jquery.dataTables.min.js'),
									site_url('wages/assets/js/jquery.table2excel.min.js'),
									site_url('wages/assets/js/jQuery.print.min.js'),
									site_url('wages/assets/js/reports.js'),
									site_url('wages/assets/js/wages_bank_sheet.js'),
									site_url('wages/assets/js/extra.js')
									);
		$this->template->set('stylesheet', $stylesheet_array);	
		$this->template->set('scriptsrc', $scriptsrc_array);
		$this->template->layout($data);
	
	}
	
	function ajax_samiti_wages_sheet(){
		$form_validation = $this->__setFormRules('ajax_samiti_wages_sheet');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			$serial_no = $this->input->post('serial_no');
			$sheet_for = $this->input->post('sheet_for');
			$where = '';
			if(!empty($from_date)){
				$where .=  " AND wi.from_date >='".$from_date."' ";
			}
			if(!empty($to_date)){
				$where .=  " AND wi.to_date <='".$to_date."' ";
			}
			if(!empty($group_type)){
				$where .=  " AND wi.group_type_id ='".$group_type."' ";
			}
			if(!empty($maingroup)){
				$where .=  " AND wi.MainGroup ='".$maingroup."' ";
			}
			
			$sql = "SELECT ANY_VALUE(wi.ID) as ID, MAX(wi.from_date) as from_date, MAX(wi.to_date) as to_date, 
					   SUM(wi.final_wages) as final_wages, 
					   mg.ifsc_code as IfscCode, 
					   mg.account_number as ben_AccountNo, 
					   mg.Name as ben_name
				FROM ".WAGESITEM." wi
				LEFT JOIN ".MAINGROUP." mg ON mg.ID = wi.MainGroup  
				WHERE wi.status != 'Deleted' AND wi.wages_for = 'Group' ". $where ." GROUP BY wi.MainGroup, mg.ifsc_code, mg.account_number, mg.Name ORDER BY MAX(wi.from_date) ASC";
			
			$result = $this->db->query($sql)->result_array();
			//printr($result);
			//echo $this->db->last_query();
			$thead = $tbody = '';
			
			if(!empty($result)){
				if($sheet_for == 'axis'){
					$thead = '<tr>
								<th> A </th>
								<th> Amount </th>
								<th> Date </th>
								<th> Beneficiary a/c name </th>
								<th> Beneficiary a/c no </th>
								<th> SMS EMAIL (Debit Party) </th>
								<th> Email/Mobile Details </th>
								<th> Sender a/c name (Debit a/c name) </th>
								<th> Sender a/c no (Debit a/c no) </th>
								<th> S.No. </th>
								<th> Beneficiary Bank IFSC Code </th>
								<th> Sender a/c type (Debit a/c type) </th>
								<th> Send to Rec (Transaction Narration) </th>
							  </tr>';
				}elseif($sheet_for == 'icici'){
					$thead = '<tr>
								<th> Tran Refernece Number </th>
								<th> Amount </th>
								<th> Sender a/c type (Debit a/c type) </th>
								<th> Sender a/c no (Debit a/c no) </th>
								<th> Sender name (Debit a/c name) </th>
								<th> SMS EMAIL (Debit Party) </th>
								<th> Email/Mobile Details </th>
								<th> Beneficiary Bank IFSC Code </th>
								<th> Beneficiary a/c Type </th>
								<th> Beneficiary a/c no </th>
								<th> Beneficiary a/c name </th>
								<th> Send to Rec (Transaction Narration) </th>
							  </tr>';
				}
				
				$mso = 'mso-number-format:"\@"';
				$total_amount = 0;
				foreach($result as $res){
					if($res['final_wages'] > 0){
						if($sheet_for == 'axis'){
							$tbody .= '<tr>';
							$tbody .= '  <td style='.$mso.'>N</td>';
							$tbody .= '  <td>'.$res['final_wages'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.get_date('d-m-Y').'</td>';
							$tbody .= '  <td style='.$mso.'>'.$res['ben_name'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.$res['ben_AccountNo'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.AXIS_EMAIL.'</td>';
							$tbody .= '  <td style='.$mso.'>'.AXIS_MOBILE.'</td>';
							$tbody .= '  <td style='.$mso.'>'.AXIS_ACCOUNT_NAME.'</td>';
							$tbody .= '  <td style='.$mso.'>'.AXIS_ACCOUNT_NUMBER.'</td>';
							$tbody .= '  <td style='.$mso.'>'.$serial_no++.'</td>';
							$tbody .= '  <td style='.$mso.'>'.$res['IfscCode'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.AXIS_ACCOUNT_TYPE.'</td>';
							$tbody .= '  <td style='.$mso.'>'.GROUP_PARTICULAR.'</td>';
							$tbody .= '</tr>';	
						}elseif($sheet_for == 'icici'){
							$tbody .= '<tr>';
							$tbody .= '  <td style='.$mso.'></td>';
							$tbody .= '  <td>'.$res['final_wages'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.ICICI_ACCOUNT_TYPE.'</td>';
							$tbody .= '  <td style='.$mso.'>'.ICICI_ACCOUNT_NUMBER.'</td>';
							$tbody .= '  <td style='.$mso.'>'.ICICI_ACCOUNT_NAME.'</td>';
							$tbody .= '  <td style='.$mso.'>'.ICICI_EMAIL.'</td>';
							$tbody .= '  <td style='.$mso.'>'.ICICI_MOBILE.'</td>';
							$tbody .= '  <td style='.$mso.'>'.$res['IfscCode'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.ICICI_BENEFICIARY_ACCOUNT_TYPE.'</td>';
							$tbody .= '  <td style='.$mso.'>'.$res['ben_AccountNo'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.$res['ben_name'].'</td>';
							$tbody .= '  <td style='.$mso.'>'.GROUP_PARTICULAR.'</td>';
							$tbody .= '</tr>';
						}
					}
					$total_amount += $res['final_wages'];
				}
				$table = array('thead'=>$thead, 'tbody'=>$tbody, 'total_amount'=>$total_amount);
				$data = array('status' => 'success', 'message' => 'Wages Sheet', 'data'=>$table);
			}else{
				$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button>No Data Found</div>', 'data' => '');
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function cash_deposited_payment(){
		$actions = checkUserPermission('wages/cash_deposited_payment', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		$crud->unset_delete();
		
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_edit_url_path(site_url('wages/add_cash_deposit'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('wages/add_cash_deposit'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
			$crud->set_add_button_text('Deposit Cash Payment');
		}
		
		if(!in_array('view', $actions)){
			$crud->unset_read();
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		if(in_array('delete', $actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', site_url('bulk_action/action/mark_delete'), 'fa fa-trash', array($this, '__callbackDeleteActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(in_array('lock', $actions)){			
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger','fa fa-lock', 'editable');
		}
		
		$crud->set_subject('Cash Deposited Payment');
		$crud->set_table(CASH_DEPOSITED_PAYMENT);
		$crud->set_relation('fisherman_id', FISHERMAN, 'Name');
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('group_type_id', MAINGROUP_TYPE, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(CASH_DEPOSITED_PAYMENT.'.status !='=>'Deleted', CASH_DEPOSITED_PAYMENT.'.editable !='=>'Lock'));
		
		
		$crud->columns('deposited_by', 'deposit_date', 'receipt_number', 'maingroup_id', 'fisherman_id', 'product_liability', 'wages_liability', 'remark', 'added_by');
		$crud->display_as(array('maingroup_id'=>'Maingroup', 'fisherman_id'=>'Fisherman'));
		$output = $crud->render();
		
		$data['page_title'] = 'Cash Received Payment';
		$data['content_view'] = 'setup/setting';
					  
		$outputData = array_merge((array)$output, $data);
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('wages/assets/js/setup.js')
											));
		
		$this->template->set('document_title', 'Cash Deposited Payment');
		$this->template->layout($outputData);
	}
	
	function add_cash_deposit($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('group_type_id'		=> '',
								'maingroup_id'		=> '',
								'fisherman_id'		=> '',
								'deposited_by'		=> '',
								'product_liability'	=> '',
							  	'wages_liability'	=> '',
								'receipt_number'	=> '',
								'deposit_date'		=> '',
								'remark'			=> ''
								);
		$data['page_title'] = 'Deposit Cash Payment';	
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('wages/add_cash_deposit');
		
		
		$form_valid = $this->__setFormRules('add_cash_deposited');
		if($form_valid){
			$post = $this->input->post();
			$fisherman_id = $post['fisherman_id'];
			if($post['deposited_by'] == 'Group'){
				$fisherman_id = '0';
			}
			$action_mode = $post['action_mode']; 
			//Preparing outward data
			$prepData = array('deposited_by'		=> $post['deposited_by'],
							  'deposit_date'		=> get_date('Y-m-d', str_replace('/', '-', $this->input->post('deposit_date'))),
							  'group_type_id' 		=> $post['group_type_id'],
							  'maingroup_id'		=> $post['maingroup_id'],
							  'fisherman_id'		=> $fisherman_id,
							  'product_liability'	=> $post['product_liability'],
							  'wages_liability'		=> $post['wages_liability'],
							  'receipt_number'		=> $post['receipt_number'],
							  'remark'				=> $post['remark'],
							  'added_by'			=> $this->userID,
							  'added_date'			=> get_datetime('Y-m-d H:i:s'),
							  'action_microtime'	=> microtime(true),
							  );
			
			//printr($prepData);
			if($id != NULL && $action_mode == 'edit'){
				//do update
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('deposit_id', $id)->update(CASH_DEPOSITED_PAYMENT, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('wages/add_cash_deposit/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(CASH_DEPOSITED_PAYMENT, $prepData);
				$id = $this->db->insert_id();
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('wages/add_cash_deposit/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data inserted successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to insert. Please try again.';
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		if($id != NULL){
			//printr($id);
			$data['page_title'] = 'Edit Deposit Cash Payment';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('wages/add_cash_deposit/'.$id);
			$deposited_data = $this->WM->get_deposited_cash_payment($id);
			//printr($dbdata);
			if(!empty($deposited_data)){
				$data['dbdata'] = $deposited_data;
			}
		}
		
		$data['mg_type'] = $this->WM->get_mgtype_data(); // Get all maingroup_type data
		
		$data['scriptsrc'] = array(site_url('wages/assets/js/cash_deposit_sheet.js'));
		
		$data['setup_form'] = $this->load->view('wages/add_cash_deposited_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	//Select fisherman (like dailytoll) ajax request
	function fisherman(){
		$data['result1'] = '';
		$data['result2'] = '';
		$q = $this->input->get('q');
		$f_ids = $this->input->get('f_ids');
		if(!empty($q)){
			$data = $this->WM->get_fisherman($q, $f_ids, $offset = 0, $limit = 10);
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Form rule validation callback functions
	function validate_date(){
		$data = FALSE;
		$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
		$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
		if(strtotime($to_date) >= strtotime($from_date)){
			$data = TRUE;
		}
		return $data;
	}
	
	function validate_ald_amount(){
		$data = TRUE;
		$ald_amount = $this->input->post('ald_amount');
		$advance_wages = $this->input->post('advance_wages');
		if($ald_amount > $advance_wages ){
			$data = FALSE;
		}
		return $data;
	}
	
	private function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'add_advance_wage':
				$this->form_validation->set_rules('group_type', 'Group Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('maingroup', 'Main Group', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('fisherman', 'Fisherman', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[1]');
				//$this->form_validation->set_rules('receipt_number', 'Receipt Number', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list' => 'Invalid action performed. Please reload the page and try again.'));
			break;
			case 'save_bulk_advance_wages':
				$this->form_validation->set_rules('f_group', 'Group Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('f_maingroup', 'Main Group', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('fisherman_id', 'Fisherman ID', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('f_name', 'Fisherman Name', 'trim|required|min_length[1]');				
				$this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('amount', 'Amount', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('action', 'Mode', 'trim|required|in_list[add,edit]', array('in_list' => 'Invalid action performed. Please reload the page and try again.'));
			break;
			case 'add_samiti_advance_wages':
				$this->form_validation->set_rules('group_type', 'Group Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('maingroup', 'Main Group', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[1]');
				//$this->form_validation->set_rules('receipt_number', 'Receipt Number', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list' => 'Invalid action performed. Please reload the page and try again.'));
			break;
			case 'add_cash_deposited':
				$this->form_validation->set_rules('deposited_by', 'Deposited By', 'trim|required|min_length[1]|in_list[Fisherman,Group]');
				$this->form_validation->set_rules('group_type_id', 'Group Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('maingroup_id', 'Main Group', 'trim|required|min_length[1]');
				if($this->input->post('deposited_by') == 'Fisherman'){
					$this->form_validation->set_rules('fisherman_id', 'Fisherman', 'trim|required|min_length[1]');
				}
				$this->form_validation->set_rules('deposit_date', 'Date', 'trim|required|min_length[1]');
				
				if(empty($this->input->post('product_liability')) && empty($this->input->post('wages_liability'))){
					$this->form_validation->set_rules('product_liability', 'Product Liability', 'trim|required|numeric|greater_than[0]', array('required' => 'Either Product Liability or Wages Liability is required.'));
					$this->form_validation->set_rules('wages_liability', 'Wages Liability', 'trim|required|numeric|greater_than[0]', array('required' => 'Either Product Liability or Wages Liability is required.'));
				}
				
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list' => 'Invalid action performed. Please reload the page and try again.'));
			break;
			case 'ajax_samiti_wages_item':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To date must be equal to or greater than from date.'));
				$this->form_validation->set_rules('maingroup', 'Group', 'trim|required|min_length[1]');
			break;
			case 'ajax_wages_item':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To date must be equal to or greater than from date.'));
				$this->form_validation->set_rules('maingroup', 'Maingroup', 'trim|required|min_length[1]');
			break;
			case 'insert_wage_sheet':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To date must be greater than from date.'));
				$this->form_validation->set_rules('MainGroup', 'Maingroup', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('gld_amount', 'Group Liability Deduction', 'trim|numeric');
				//$this->form_validation->set_rules('ald_amount', 'Advance Liability Deduction', 'trim|numeric|callback_validate_ald_amount', array('validate_ald_amount' => 'Advance Liability Deduction must be equal or less than Advance Wages Liability.'));
				$this->form_validation->set_rules('final_wages', 'Net Amount', 'trim|required|numeric');
				$this->form_validation->set_rules('mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list' => 'Invalid action performed. Please reload the page and try again.'));
			break;
			
			case 'ajax_fisherman_wages_sheet':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To date must be greater than from date.'));
			break;
			
			case 'ajax_samiti_wages_sheet':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To date must be greater than from date.'));
			break;
			
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
}
