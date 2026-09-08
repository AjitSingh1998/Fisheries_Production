<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Setup extends MY_Controller {
	var $userID, $actions;
	
	public function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('setup_model', 'SM');
    }
	
	function index(){
		redirect('lock/setup/group_type');
	}

	function __callbackUnlockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/unlock');
	}

	//Listing, Add, Edit Group Type
	function group_type(){
		$actions = checkUserPermission('lock/setup/group_type', $this->uri->segment(4));
		
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
			$crud->add_action('Unlock', 'triggerBulkDelete text-danger',site_url('bulk_action/action/unlock'),'fa fa-unlock',array($this,'__callbackUnlockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Unlock', site_url('bulk_action/action/unlock'), ' text-danger', 'fa fa-unlock', 'editable');		
		}
		
		
		$crud->set_subject('Group Type');
		$crud->set_table(MAINGROUP_TYPE);		
		$crud->where(array(MAINGROUP_TYPE.'.editable'=>'Lock'));
		$crud->columns('Name', 'govt_charges', 'status', 'editable');	
		$crud->fields('Name', 'govt_charges', 'status', 'added_by', 'added_date', 'updated_by', 'updated_date', 'action_microtime');	
		$crud->required_fields('Name', 'status');
		
		$crud->field_type('added_by','invisible');
		$crud->field_type('added_date','invisible');
		$crud->field_type('updated_by','invisible');
		$crud->field_type('updated_date','invisible');
		$crud->field_type('action_microtime','invisible');
		
		$crud->callback_before_insert(array($this,'__callbackBeforeInsert'));
 		$crud->callback_before_update(array($this,'__callbackBeforeUpdate'));
		
		$output = $crud->render();
		$data = array('page_title'=> 'Group Type', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', 'Group Type');
		$this->template->layout($outputData);
	}
	
	
	
	//Listing
	function main_group(){
		$actions = checkUserPermission('lock/setup/main_group', $this->uri->segment(4));
		
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
		
		$crud->set_subject('Main Group');
		$crud->set_table(MAINGROUP);
		$crud->set_relation('Type', MAINGROUP_TYPE, 'Name');		
		$crud->where(array(MAINGROUP.'.editable'=>'Lock'));
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Name', 'Type', 'bank_name', 'branch_name', 'ifsc_code', 'account_number', 'major_fee', 'minor_fee', 'sawal_fee', 'status', 'editable');	
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Menu item', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);
		
		$this->template->set('scriptsrc', array(site_url('lock/assets/js/setup.js')));
		
		$this->template->set('document_title', 'Main Group');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and update fishingpoint
	
	
	//Listing
	function fishing_points(){
		$actions = checkUserPermission('lock/setup/fishing_points', $this->uri->segment(4));		
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

		
		$crud->set_subject('Fishing Point');
		$crud->set_table(FISHINGPOINTS);
		$crud->where(array(FISHINGPOINTS.'.editable'=>'Lock'));
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Name', 'Remark','status', 'editable');
			
		$output = $crud->render();

		$data = array('page_title'=> 'Fishing Point', 
					  'content_view'=>'setup/setting',);
					  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('scriptsrc', array(site_url('lock/assets/js/setup.js')));
		
		$this->template->set('document_title', 'Fishing Point');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and update fishingpoint
	
	
	//Listing
	function fisherman(){
		$this->actions = checkUserPermission('lock/setup/fisherman', $this->uri->segment(4));
		$actions = $this->actions;

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

		
		$crud->set_subject('Fisherman');
		$crud->set_table(FISHERMAN);
		$crud->set_relation('MainGroup', MAINGROUP, 'Name');
		$crud->where(array(FISHERMAN.'.editable'=>'Lock'));
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Code','Name','Contact','doc_number','Village','MainGroup','MajorFee','MinorFee','SawalFee','status', 'editable');
		$crud->display_as('doc_number', 'Aadhar');
				
		$output = $crud->render();
		$data = array('page_title'=> 'Fisherman', 'content_view'=>'setup/setting');		  
		$outputData = array_merge((array)$output, $data);	
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/select2/dist/css/select2.min.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('lock/assets/js/setup.js')
												));
		$this->template->set('document_title', 'Fisherman');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and edit fisherman
	
	
	//Ajax function when transfer liability
	function transfer_liability($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('group_type_id'=>'',
								'MainGroup'=>'',
								'Code'=>'',
								'Name'=>''
								);
		$data['page_title'] = 'Transfer Liability';
		$data['action_mode'] = 'edit';
		$data['form_action'] = site_url('setup/transfer_liability');
		
		$form_valid = $this->__setFormRules('transfer_liability');
		if($form_valid){
			$post = $this->input->post();
			$sf = $this->input->post('sf');
			$action_mode = $post['action_mode'];
			$liability_data = array(); //Secondary fisher data
			$result = '';
			if($id != NULL && $action_mode=='edit'){
				if(isset($sf['id']) && !empty($sf['id'])){
					foreach($sf['id'] as $key=>$value){
						$liability_data = array('primary_id'	=> $post['pf_id'],
											  'secondary_id'	=> $sf['id'][$key],
											  'amount'			=> $sf['amount'][$key],
											  'added_by'		=> $this->userID,
											  'added_date'		=> get_datetime('Y-m-d H:i:s'),
											  'action_microtime'=> microtime(true),
											  );
					
						$where = array( 'primary_id' => $post['pf_id'], 'secondary_id' => $sf['id'][$key] );
						$check_existence = $this->db->where($where)->count_all_results(TRANSFERRED_LIABILITY);
						if($check_existence){
							unset($liability_data['added_date'], $liability_data['added_by']);
							$liability_data['updated_date'] = get_datetime('Y-m-d H:i:s');
							$liability_data['updated_by'] = $this->userID;
							$result = $this->db->where($where)->update(TRANSFERRED_LIABILITY, $liability_data);
						}else{
							$result = $this->db->insert(TRANSFERRED_LIABILITY, $liability_data);
						}
					}
				}
			}
			
			if($result){
				$data['status'] = 'success';
				$data['msg'] = 'Data inserted successfully.';
			}else{
				$data['status'] = 'danger';
				$data['msg'] = 'Failed to insert.';
			}
		}
		
		if($id != NULL){
			$data['page_title'] = 'Transfer Liability';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('setup/transfer_liability/'.$id);
			$data['outward_items'] = $this->SM->get_liability_data($id);
			$data['return_items'] = $this->SM->get_outward_return_items($id);
			
			$fisherman_data = $this->SM->get_fisherman_data($id);
			if(!empty($fisherman_data)){
				$data['dbdata'] = $fisherman_data;
			}
		}

		$data['mg_type'] = $this->SM->get_mgtype_data(); // Get all maingroup_type data	
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['setup_form'] = $this->load->view('setup/forms/transfer_liability_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Ajax functions
	//Select fisherman (like dailytoll) ajax request
	function ajax_fisherman(){
		$data['result1'] = '';
		$data['result2'] = '';
		$q = $this->input->get('q');
		$f_ids = $this->input->get('f_ids');
		if(!empty($q)){
			$data = $this->SM->get_fisherman($q, $f_ids, $offset = 0, $limit = 10);
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function ajax_get_maingroup(){
		$data = array('status'=>'danger', 'msg'=>'Maingroup data not found', 'data'=>'<option value="">No group found</option>');
		$mg_type = $this->input->post('mg_type');
		if(!empty($mg_type)){
			$result = $this->SM->get_all_mg_data($mg_type);
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
	
	//Select2-ajax fisherman ajax request
	function ajax_get_mg_fishers(){
		$data = array('status'=>'danger', 'msg'=>'Fisherman data not found', 'data'=>'<option value="">No fisherman found</option>');
		$maingroup = $this->input->post('maingroup');
		$f_ids = $this->input->post('f_ids');
		if(!empty($maingroup)){
			$result = $this->SM->get_all_mg_fisherman($maingroup, $f_ids);
			$op_html = '<option value="">Select Fisherman</option>';
			if(!empty($result)){
				foreach($result['result1'] as $res){
					$op_html .= '<option value="'.$res['id'].'">'.$res['text'].'</option>>';
				}
				$data = array('status'=>'success', 'msg'=>'Fishers data.', 'data1'=>$op_html, 'data2'=>$result['result2']);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function check_liability_amount(){
		$data = FALSE;
		$post = $this->input->post();
		$remaining_amount = $post['remaining_amount'];
		$sf = $this->input->post('sf');
		$amount_total = 0.00;
		if(isset($sf['id']) && !empty($sf['id'])){
			foreach($sf['id'] as $key=>$value){
				$amount_total = $amount_total + $sf['amount'][$key];
			}
			if($amount_total <= $remaining_amount){
				$data = TRUE;
			}
		}
		return $data;
	}
	
	function check_fisherman_code(){
		$data = FALSE;
		$code = $this->input->post('code');
		$result = $this->db->where('Code', $code)->count_all_results(FISHERMAN);
		if($result == 0){
			$data = TRUE;
		}
		return $data;
	}
	
	function check_document_number(){
		$data = FALSE;
		$doc_number = $this->input->post('doc_number');
		$result = $this->db->where('doc_number', $doc_number)->count_all_results(FISHERMAN);
		if($result == 0){
			$data = TRUE;
		}
		return $data;
	}
	
	private function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case'add_main_group':
				$this->form_validation->set_rules('group_name', 'Main Group Name', 'trim|required|min_length[2]');
				$this->form_validation->set_rules('type', 'Group Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('major_fee', 'Major Fee', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('minor_fee', 'Minor Fee', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('sawal_fee', 'Sawal Fee', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('status', 'Status', 'trim|required|min_length[2]|in_list[Active,Inactive]', array('in_list'=>'Invalid status field.'));
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));			
			break;
			
			case'add_fisherman':
				$this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[1]|callback_check_fisherman_code', array('check_fisherman_code' => 'Code is already used, Please provide different code.'));
				$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]');
				$this->form_validation->set_rules('maingroup_type', 'Group Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('maingroup', 'Main Group', 'trim|required|min_length[1]');
				//$this->form_validation->set_rules('doc_name', 'Document Name', 'trim|required|min_length[1]');
				//$this->form_validation->set_rules('doc_number', 'Document Number', 'trim|required|min_length[1]|callback_check_document_number', array('check_document_number' => 'Document number is already used, Please provide different number.'));
				$this->form_validation->set_rules('majorfee', 'Major Fee', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('minorfee', 'Minor Fee', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('sawalfee', 'Sawal Fee', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('status', 'Status', 'trim|required|min_length[2]|in_list[Active,Inactive]', array('in_list'=>'Invalid status field.'));
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));
			break;
			
			case'fishingpoint':
				$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]');
				$this->form_validation->set_rules('status', 'Status', 'trim|required|min_length[2]|in_list[Active,Inactive]', array('in_list'=>'Invalid status field.'));
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));			break;
			
			case'transfer_liability':
				$this->form_validation->set_rules('sf[amount]', 'Liability Amount', 'trim|required|numeric|callback_check_liability_amount', array('check_liability_amount' => 'Total liability amount must be less than or equals to remaining amount'));
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));			
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
}