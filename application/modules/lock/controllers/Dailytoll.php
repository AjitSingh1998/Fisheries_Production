<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Dailytoll extends MY_Controller {
	// url_encryptor("encrypt", 1);
	// url_encryptor("decrypt", 1);
	var $userID, $actions;
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('dailytoll_model', 'DT');		
	}

	function __callbackUnlockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/unlock');
	}

	function index(){		
		$search_field = $this->input->post('search_field');
		$search_text = $this->input->post('search_text');
		if(!empty($search_field)){
			foreach($search_field as $key => $value){
				if($value == 'Date'){
					if(array_key_exists($key, $search_text)){
					 	$date = str_replace('/', '-', $search_text[$key]); 
						$date = date('Y-m-d', strtotime($date));
						$search_text[$key] = $date;
					}
				}
			}
			$_POST['search_text'] = $search_text;
		}
		
		
		$actions = checkUserPermission('lock/dailytoll/index', $this->uri->segment(4));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		
		$crud->unset_read();		
		$crud->unset_edit();		
		$crud->unset_add();		
		$crud->unset_delete();
		$crud->unset_export();
		$crud->unset_print();
		
		if(in_array('unlock', $actions)){
			$crud->add_action('Unlock', 'triggerBulkDelete  text-danger',site_url('bulk_action/action/unlock'),'fa fa-unlock',array($this,'__callbackUnlockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Unlock', site_url('bulk_action/action/unlock'), ' text-danger', 'fa fa-unlock', 'editable');		
		}
		
		$crud->set_subject('Daily Toll');
		$crud->set_table(DAILYTOLL);
		
		$crud->set_relation('Point', FISHINGPOINTS, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		
		$crud->where(array(DAILYTOLL.'.editable ='=>'Lock'));
		$crud->order_by('Date', 'DESC');
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('page_code', 'Date', 'Point', 'total_qty', 'total_wt','added_by','status','editable');
		
		
		$output = $crud->render();
		$data = array('page_title'=> 'Manage Menu item', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);			
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												site_url('lock/assets/js/setup.js')
												));
		
		$this->template->set('document_title', 'Daily Toll');
		$this->template->layout($outputData);
	}

	//Select fisherman ajax request
	function fisherman(){
		$data['result1'] = '';
		$data['result2'] = '';
		$q = $this->input->get('q');
		$f_ids = $this->input->get('f_ids');
		if(!empty($q)){
			$data = $this->DT->get_fisherman($q, $f_ids, $offset = 0, $limit = 10);
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function insertDailytoll(){
		$postData = $this->input->post();
		/*
		$data = array('status' => "success", "msg" => "Daily Toll detail inserted successfully.", "dti_id" => url_encryptor("encrypt", 1),  "dtollid" => 11);
		*/
		//printr($postData);
		
		$checkForm = $this->__setFormRules('dailytoll');
		if($checkForm){
			$data = $this->DT->insertDailytoll($postData, $this->userID);
		}else{
			$errors = validation_errors();
			$data = array('status' => "danger", "msg" => $errors);
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function delete_dtoll(){
		$data = array('status' => "danger", "msg" => "Invalid request, Please try again.");
		$dti_id = $this->input->post('dti_id');
		if(!empty($dti_id)){
			$DailytollInfoId = url_encryptor("decrypt", $dti_id);
			$result = $this->db->delete(DAILYTOLLINFO, array('ID'=>$DailytollInfoId));
			if($result){
				
				$data = array('status' => "success", "msg" => "Daily Toll info deleted.");
			}else{
				$data = array('status' => "success", "msg" => "Daily Toll info deletion failed. Please try again later.");
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function upd_on_del(){
		$data = array('status' => "danger", "msg" => 'Error in deleting');
		$post = $this->input->get();
		$this->form_validation->set_data($post);
		$checkForm = $this->__setFormRules('upd_on_del');
		if($checkForm){
			$dt_id = $post['dt_id'];
			$total_qty = $post['gqty'];
			$total_wt = $post['gwt'];
			$this->db->where('ID',$dt_id)->update(DAILYTOLL, array('total_qty'=>$total_qty, 'total_wt'=>$total_wt));
		}else{
			$errors = validation_errors();
			$data = array('status' => "danger", "msg" => $errors);
		}
		//$this->output->set_content_type('application/json');
		//$this->output->set_output(json_encode($data));
	}
	
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case'main_group':
				$this->form_validation->set_rules('group_name', 'Main Group Name', 'trim|required|min_length[2]');
				$this->form_validation->set_rules('type', 'Group Type', 'trim|required|min_length[2]');
			break;
			case'dailytoll':
				$this->form_validation->set_rules('twt', 'Total Weight', 'trim|required|numeric|greater_than[0]');
				return $this->form_validation->run($this);
			break;
			case'update_dailytoll':
				$this->form_validation->set_rules('date', 'Date', 'trim|required');
				$this->form_validation->set_rules('point', 'Point', 'trim|required');
			break;
			case'upd_on_del':
				$this->form_validation->set_rules('dt_id', 'Total Weight', 'trim|required');
				$this->form_validation->set_rules('gqty', 'Total Weight', 'trim|required');
				$this->form_validation->set_rules('gwt', 'Total Weight', 'trim|required');
				return $this->form_validation->run($this);
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}

}