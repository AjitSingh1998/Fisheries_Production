<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Dailytoll extends MY_Controller {
	// url_encryptor("encrypt", 1);
	// url_encryptor("decrypt", 1);
	var $userID, $userInfo, $actions, $datetime;
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->userInfo = loginUserInfo();
		$this->load->model('dailytoll_model', 'DT');
		$this->datetime = get_datetime('l, d M, Y h:i A');		
	}

	function __callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/mark_delete');
	}

	function __callbackLockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/lock');
	}

	function index(){
		$search_field = $this->input->post('search_field');
		$search_text = $this->input->post('search_text');
		$fields = array('Date'=>'Date','point_name'=>'fp.Name');
		if(!empty($search_field)){
			foreach($search_field as $key => $value){
				if($value == 'Date'){
					if(array_key_exists($key, $search_text)){
					 	$date = str_replace('/', '-', $search_text[$key]); 
						$date = date('Y-m-d', strtotime($date));
						$search_text[$key] = $date;
					}
				}
				if(array_key_exists($value,$fields)){
					$search_field[$key] = $fields[$value];
				}
			}
			$_POST['search_text'] = $search_text;
			$_POST['search_field'] = $search_field;
		}
		
		$this->actions = checkUserPermission('dailytoll', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();		
		$crud->unset_delete();
					
		if(in_array('edit', $this->actions)){
			$crud->add_action('Edit Date', 'loadActionForm', site_url('dailytoll/update/'), 'fa fa-pencil', '', 'popupmodal');
			$crud->set_edit_url_path(site_url('dailytoll/add'));			
		}else{
			$crud->unset_edit();
		}
		if(!in_array('add', $this->actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('dailytoll/add'));
		}
		
		if(in_array('delete', $this->actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', site_url('bulk_action/action/mark_delete'), 'fa fa-trash', array($this, '__callbackDeleteActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(!in_array('export', $this->actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $this->actions)){
			$crud->unset_print();
		}
		
		if(in_array('lock', $this->actions)){			
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger','fa fa-lock', 'editable');
		}
		
		if(in_array('view', $this->actions)){
			$crud->set_read_url_path(site_url('dailytoll/view_dailytoll'));
		}else{
			$crud->unset_read();
		}
		
		//printr($this->actions);
		
		$crud->set_add_button_class('btn btn-primary loadInIframe');
		
		$crud->set_subject('Daily Toll');
		
		$crud->set_table(DAILYTOLL);		
		/*
		$crud->set_relation('Point', FISHINGPOINTS, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		
		$crud->where(array(DAILYTOLL.'.status !='=>'Deleted'));
		$crud->where(array(DAILYTOLL.'.editable !='=>'Lock'));
		
		$crud->order_by('Date', 'DESC');
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('page_code', 'Date', 'Point', 'total_qty', 'total_wt','added_by');
		*/
		
		$crud->set_model('grocery_crud_custom_query_model');
		$crud->basic_model->set_custom_query("
			SELECT 
			dt.ID, dt.page_code, dt.Date, dt.Point, dt.total_qty, dt.total_wt,			
			dti.manjor_wt, dti.Local_minor_wt, dti.sawal_wt, 
			fp.Name as point_name, CONCAT(ad.first_name, ' ', last_name) as added_by
			
			FROM ".DAILYTOLL." dt
			
			LEFT JOIN (SELECT DailytollId, 
						IFNULL(SUM(Tmwt), 0) as manjor_wt, 
						IFNULL(SUM(Localminor), 0) as Local_minor_wt, 
						IFNULL(SUM(Swt), 0) as sawal_wt 
						FROM  ".DAILYTOLLINFO." GROUP BY DailytollId) dti ON dti.DailytollId = dt.ID
						
			LEFT JOIN ".FISHINGPOINTS." fp ON fp.ID = dt.Point
			LEFT JOIN ".ADMINISTRATOR." ad ON ad.admin_id = dt.added_by 
			
			WHERE dt.status != 'Deleted' AND dt.editable != 'Lock' 
			");
		
		$crud->columns('page_code', 'Date', 'point_name', 'manjor_wt', 'Local_minor_wt', 'sawal_wt', 'total_qty', 'total_wt','added_by');
		
		$crud->display_summary('manjor_wt', 'Local_minor_wt', 'sawal_wt', 'total_qty', 'total_wt');

		
		
		$output = $crud->render();
		$data = array('page_title'=> 'Manage Menu item', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);			
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												site_url('dailytoll/assets/js/setup.js')
												));
		
		$this->template->set('document_title', 'Daily Toll');
		$this->template->layout($outputData);
	}
	
	function add($dt_id=NULL){
		
		$data['dt_data'] = '';
		if(!empty($dt_id) && $dt_id !== NULL){
			$result = $this->DT->get_dailytoll($dt_id);
			if(!empty($result)){
				$data['dt_data'] = $result;
			}else{
				$this->messageci->set('Invalid request','error');
				redirect('dailytoll', 'refresh');
			}
		}
		
		$data['dti_data']		= $this->DT->get_dailytoll_info($dt_id);
		$data['fishingpoints']  = $this->DT->get_fishing_points();
		$data['lastdailytoll']  = $this->DT->get_last_dailytoll($this->userID);

		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('dailytoll/assets/js/jquery.table2excel.min.js'),
												site_url('dailytoll/assets/js/jQuery.print.min.js'),
												site_url('dailytoll/assets/js/dailytoll.js')
												));
		
		//printr($data['dt_data']);
		$data['content_view'] 	= 'dailytoll/add_dailytoll_v';
		$data['page_title'] 	= 'Add New Dailytoll';
		$this->template->set('document_title', 'Add New Dailytoll');
		$this->template->layout($data);
	}
	
	function view_dailytoll($dt_id=NULL){
		
		$data['dt_data'] = '';
		if(!empty($dt_id) && $dt_id !== NULL){
			$result = $this->DT->get_dailytoll($dt_id);
			if(!empty($result)){
				$data['dt_data'] = $result;
			}else{
				$this->messageci->set('Invalid request','error');
				redirect('dailytoll', 'refresh');
			}
		}
		
		$data['dti_data']		= $this->DT->get_dailytoll_info($dt_id);
		$data['fishingpoints']  = $this->DT->get_fishing_points();

		
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('dailytoll/assets/js/jquery.table2excel.min.js'),
												site_url('dailytoll/assets/js/jQuery.print.min.js'),
												site_url('dailytoll/assets/js/dailytoll.js')
												));
		
		//printr($data['dt_data']);
		$data['content_view'] 	= 'dailytoll/view_dailytoll_v';
		$data['page_title'] 	= 'Dailytoll';
		$this->template->set('document_title', 'Dailytoll');
		$this->template->layout($data);
	}
	
	function update($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('ID'=>'', 
								'Date'=>'', 
								'Point'=>'' 
								);
		$data['page_title'] = 'Update Date and Point';		
		$data['action_mode'] = 'edit';
		$data['form_action'] = site_url('dailytoll/update/'.$id);
		
		$form_valid = $this->__setFormRules('update_dailytoll');
		if($form_valid){
			$post = $this->input->post();
			
			$action_mode = $post['action_mode'];
			$date = get_date('Y-m-d', str_replace('/', '-', $post['date']));
			$point = $post['point'];
			
			$prepData = array('Date'			=> $date,
							  'Point'			=> $point,
							  'updated_date'	=> get_datetime('Y-m-d H:i:s'),
							  'updated_by'		=> $this->userID
							  );
			
			if($id != NULL && $action_mode=='edit'){
				//do update
				$result = $this->db->where('ID', $id)->update(DAILYTOLL, $prepData);
				if($result){
					$this->db->where('DailytollId', $id)->update(DAILYTOLLINFO, array('toll_date' => $date, 'Point' => $point));
					$userActivityLogArray = array(
						'activity_by'			=> $this->userID,
						'activity' 				=> 'Update',
						'table_name'			=> 'DAILYTOLL,DAILYTOLLINFO',
						'data_id'				=> $id,
						'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' updated dailytoll data to date-point('.$post['date'].'-'.$point.') on '.$this->datetime,
						'activity_url'			=> 'dailytoll/update/',
						'activity_date'			=> get_datetime('Y-m-d H:i:s')
					 );
					$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
					
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('dailytoll/update/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		if($id != NULL){
			$data['page_title'] = 'Update Date and Point';	
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('dailytoll/update/'.$id);
			$dailytoll_data = $this->DT->get_dailytoll($id);
			if(!empty($dailytoll_data)){
				$data['dbdata'] = $dailytoll_data;
			}
		}
		
		$data['fishingpoints']  = $this->DT->get_fishing_points();
		$data['setup_form'] = $this->load->view('dailytoll/update_dailytoll_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
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
				
				$userActivityLogArray = array(
					'activity_by'			=> $this->userID,
					'activity' 				=> 'Delete',
					'table_name'			=> 'DAILYTOLLINFO',
					'data_id'				=> $DailytollInfoId,
					'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' deleted dailytoll info data on '.$this->datetime,
					'activity_url'			=> 'dailytoll/delete_dtoll',
					'activity_date'			=> get_datetime('Y-m-d H:i:s')
				 );
				$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
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