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
		redirect('setup/group_type');
	}
	
	function __callbackCustomActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/mark_delete');
	}
	
	function __callbackLockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/lock');
	}	
	
	//Listing, Add, Edit Group Type
	function group_type(){
		$actions = checkUserPermission('setup/group_type', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();					
		$crud->unset_delete();
		
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-trash', array($this, '__callbackCustomActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(!in_array('read', $actions)){
			$crud->unset_read();
		}
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
		$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock', array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
			
		
		$crud->set_subject('Group Type');
		$crud->set_table(MAINGROUP_TYPE);
		$crud->where(array(MAINGROUP_TYPE.'.status'=>'Active'));
		$crud->where(array(MAINGROUP_TYPE.'.editable'=>'Unlock'));
		$crud->columns('Name', 'govt_charges', 'status');	
		$crud->fields('Name', 'govt_charges', 'status', 'added_by', 'added_date', 'updated_by', 'updated_date', 'action_microtime');	
		$crud->required_fields('Name', 'status');
		
		$crud->field_type('added_by','invisible');
		$crud->field_type('added_date','invisible');
		$crud->field_type('updated_by','invisible');
		$crud->field_type('updated_date','invisible');
		$crud->field_type('action_microtime','invisible');
		
		$crud->callback_before_insert(array($this,'__callbackBeforeInsert'));
 		$crud->callback_before_update(array($this,'__callbackBeforeUpdate'));
		
		$crud->unset_read_fields('added_by', 'added_date', 'updated_by', 'updated_date', 'action_microtime');
		$output = $crud->render();
		$data = array('page_title'=> 'Group Type', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', 'Group Type');
		$this->template->layout($outputData);
	}
	
	function __callbackBeforeInsert($post_array){
		$post_array['added_by'] = $this->userID;
		$post_array['added_date'] = get_datetime('Y-m-d H:i:s');
		$post_array['action_microtime'] = microtime(true);
		return $post_array;
	}
	
	function __callbackBeforeUpdate($post_array){
		$post_array['updated_by'] = $this->userID;
		$post_array['updated_date'] = get_datetime('Y-m-d H:i:s');
		$post_array['action_microtime'] = microtime(true);
		return $post_array;
	}
	
	//Listing
	function main_group(){
		$actions = checkUserPermission('setup/main_group', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		$crud->unset_delete();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-trash', array($this, '__callbackCustomActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('setup/view_maingroup'));
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
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock', array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
		
		
		$crud->set_edit_url_path(site_url('setup/add_main_group'));
		$crud->set_edit_button_class('btn btn-default loadActionForm');

		$crud->set_add_url_path(site_url('setup/add_main_group'));
		$crud->set_add_button_class('btn btn-primary loadActionForm');
		
		$crud->set_subject('Main Group');
		$crud->set_table(MAINGROUP);
		$crud->set_relation('Type', MAINGROUP_TYPE, 'Name');		
		$crud->where(array(MAINGROUP.'.status !='=>'Deleted'));
		$crud->where(array(MAINGROUP.'.editable !=' => 'Lock'));
		
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Name', 'Type', 'bank_name', 'branch_name', 'ifsc_code', 'account_number', 'major_fee', 'minor_fee', 'sawal_fee', 'product_balance',  'wages_balance', 'status');	
		
		$crud->display_summary('product_balance',  'wages_balance');		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Menu item', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);
		
		$this->template->set('scriptsrc', array(site_url('setup/assets/js/setup.js')));
		
		$this->template->set('document_title', 'Main Group');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and update fishingpoint
	function add_main_group($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 
								'Type'=>'', 
								'bank_name'=>'', 
								'branch_name'=>'', 
								'ifsc_code'=>'', 
								'account_number'=>'', 
								'major_fee'=>'', 
								'minor_fee'=>'', 
								'sawal_fee'=>'', 
								'Remark'=>'',
								'product_balance'=>'',
								'wages_balance'=>'',
								'Remark'=>'',
								'status'=>'');
		$data['page_title'] = 'Add Main Group';		
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('setup/add_main_group');
		
		$form_valid = $this->__setFormRules('add_main_group');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			$prepData = array('Name'			=> $post['group_name'],
							  'Type'			=> $post['type'],
							  'bank_name'		=> $post['bank_name'],
							  'branch_name'		=> $post['branch_name'],
							  'ifsc_code'		=> $post['ifsc_code'],
							  'account_number'	=> $post['account_number'],
							  'major_fee'		=> $post['major_fee'],
							  'minor_fee'		=> $post['minor_fee'],
							  'sawal_fee'		=> $post['sawal_fee'],
							  'product_balance'=>$post['product_balance'],
							  'wages_balance' => $post['wages_balance'],
							  'Remark'			=> $post['remark'],
							  'status'			=> $post['status'],
							  'added_by'		=> $this->userID,
							  'added_date'		=> get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=> microtime(true),
							  );
			
			if($id != NULL && $action_mode=='edit'){
				//do update
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('ID', $id)->update(MAINGROUP, $prepData);
				if($result){
					//Update major, minor and sawal fee of all fisherman of this maingroup.
					$upd_where = array('group_type_id'=>$post['type'], 'MainGroup'=>$id);
					$upd_data = array('MajorFee' => $post['major_fee'], 'MinorFee' => $post['minor_fee'], 'SawalFee' => $post['sawal_fee']);
					$this->db->where($upd_where)->update(FISHERMAN, $upd_data);
					
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/add_main_group/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(MAINGROUP, $prepData);
				$id = $this->db->insert_id();
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/add_main_group/'.$id);
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
			$data['page_title'] = 'Edit Main Group';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('setup/add_main_group/'.$id);
			$mg_data = $this->SM->get_mg_data($id);
			if(!empty($mg_data)){
				$data['dbdata'] = $mg_data;
			}
		}
		
		$data['mg_type'] = $this->SM->get_mgtype_data();
		$data['setup_form'] = $this->load->view('setup/forms/add_main_group_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Ajax function when add and update fishingpoint
	function view_maingroup($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 
								'Type'=>'', 
								'bank_name'=>'', 
								'branch_name'=>'', 
								'ifsc_code'=>'', 
								'account_number'=>'', 
								'major_fee'=>'', 
								'minor_fee'=>'', 
								'sawal_fee'=>'',
								'product_balance'=>'',
								'wages_balance'=>'', 
								'Remark'=>'',
								'status'=>'');
		$data['page_title'] = 'Main Group';
		
		
		if($id != NULL){
			$mg_data = $this->SM->get_mg_data($id);
			if(!empty($mg_data)){
				$data['dbdata'] = $mg_data;
			}
		}
		
		$data['mg_type'] = $this->SM->get_mgtype_data();
		$data['setup_form'] = $this->load->view('setup/forms/view_maingroup_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Listing
	function fishing_points(){
		$actions = checkUserPermission('setup/fishing_points', $this->uri->segment(3));
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		$crud->unset_delete();

		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}

		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-trash', array($this, '__callbackCustomActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('setup/view_fishingpoint'));
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
	
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock', array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
				
		$crud->set_edit_url_path(site_url('setup/add_fishingpoint'));
		$crud->set_edit_button_class('btn btn-default loadActionForm');

		$crud->set_add_url_path(site_url('setup/add_fishingpoint'));
		$crud->set_add_button_class('btn btn-primary loadActionForm');
		
		$crud->set_subject('Fishing Point');
		$crud->set_table(FISHINGPOINTS);
		$crud->where(array(FISHINGPOINTS.'.status !='=>'Deleted'));
		$crud->where(array(FISHINGPOINTS.'.editable !=' => 'Lock'));
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Name', 'Remark','status');
				
		$output = $crud->render();

		$data = array('page_title'=> 'Fishing Point', 
					  'content_view'=>'setup/setting',);
					  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('scriptsrc', array(site_url('setup/assets/js/setup.js')));
		
		$this->template->set('document_title', 'Fishing Point');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and update fishingpoint
	function add_fishingpoint($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Add Fishing Point';		
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('setup/add_fishingpoint');
		
		$form_valid = $this->__setFormRules('fishingpoint');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			$prepData = array('Name'			=> $post['name'],
							  'Remark'			=> $post['remark'],
							  'status'			=> $post['status'],
							  'added_by'		=> $this->userID,
							  'added_date'		=> get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=> microtime(true),
							  );
			
			
			if($id != NULL && $action_mode=='edit'){
				//do update
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('ID', $id)->update(FISHINGPOINTS, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/add_fishingpoint/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(FISHINGPOINTS, $prepData);
				$id = $this->db->insert_id();
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/add_fishingpoint/'.$id);
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
			$data['page_title'] = 'Edit Fishing Point';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('setup/add_fishingpoint/'.$id);
			$fpoint_data = $this->SM->get_fpoint_data($id);
			if(!empty($fpoint_data)){
				$data['dbdata'] = $fpoint_data;
			}
		}
		
		$data['setup_form'] = $this->load->view('setup/forms/add_fishingpoint_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Ajax function when add and update fishingpoint
	function view_fishingpoint($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Fishing Point';
		
		if($id != NULL){
			$fpoint_data = $this->SM->get_fpoint_data($id);
			if(!empty($fpoint_data)){
				$data['dbdata'] = $fpoint_data;
			}
		}
		
		$data['setup_form'] = $this->load->view('setup/forms/view_fishingpoint_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Listing
	function fisherman(){
		$columns = array('Code' =>'fm.Code', 'Name' =>'fm.Name', 'group_name'=>'mg.Name', 'Village'=>'fm.Village', 'MajorFee'=>'fm.MajorFee', 'MinorFee'=>'fm.MinorFee', 'SawalFee'=>'fm.SawalFee', 'status' => 'fm.status'); 
		$search_field = $this->input->post('search_field');
		$search_text = $this->input->post('search_text');
		$primary_where = $secondary_where = '';
		if(!empty($search_field)){
			foreach($search_field as $key => $value){
				if(array_key_exists($value,$columns)){
					$search_field[$key] = $columns[$value];
				}
				if($value == 'primary_man'){					
					$stext = $search_text[$key];
					$primary_where = " AND fm2.Name like '%".$stext."%'";
					unset($search_text[$key]);
					unset($search_field[$key]);
				}
				if($value == 'secondary_man'){
					$stext = $search_text[$key];
					$secondary_where = " AND fm2.Name like '%".$stext."%'";
					unset($search_text[$key]);
					unset($search_field[$key]);
				}
				
			}
			$_POST['search_field'] = $search_field;
			$_POST['search_text'] = $search_text;
		}
		
		$order_by = $this->input->post('order_by');		
		if(!empty($order_by)){
			foreach($order_by as $key => $value){
				if($value == 'Code'){
					$order_by[$key] = 'cast(Code as unsigned)';
				}
			}
			$_POST['order_by'] = $order_by;
		}
		
		$this->actions = checkUserPermission('setup/fisherman', $this->uri->segment(3));
		$actions = $this->actions;

		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();		
		$crud->unset_delete();

			
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('setup/add_fisherman'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
		
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{			
			$crud->set_edit_url_path(site_url('setup/add_fisherman'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
			
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		}
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', site_url('bulk_action/action/mark_delete'), 'fa fa-trash', array($this, '__callbackCustomActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('setup/view_fisherman'));
			$crud->set_read_button_class('loadActionForm');
		}else{
			$crud->unset_read();
		}
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock', array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
			
		if(in_array('transfer_liability', $this->actions)){
			$crud->add_action('Transfer Liability', 'loadActionForm', site_url('setup/transfer_liability/'), 'fa fa-arrows-h', '', 'popupmodal');
		}
		
		$crud->set_subject('Fisherman');
		$crud->set_table(FISHERMAN);
		//$crud->set_relation('MainGroup', MAINGROUP, 'Name');
		//$crud->where(array(FISHERMAN.'.status !='=>'Deleted'));
		//$crud->where(array(FISHERMAN.'.editable !=' => 'Lock'));
		//$crud->set_relation_n_n('Primary', SECONDARYFISHERMAN, FISHERMAN, 'ID', 'Secondary', 'Name', '', array('group_status' => 'Joined'));
		//$crud->order_by('added_date', 'DESC');
		//$crud->columns('Code','Name','MainGroup','MajorFee','MinorFee','SawalFee', 'products_balance', 'wages_balance', 'status');
		//$crud->display_as('doc_number', 'Aadhar');
		
		$crud->set_model('grocery_crud_custom_query_model');
		$sql = "
			SELECT fm.*, mg.Name as group_name,
			
			 (SELECT GROUP_CONCAT(fm2.Name SEPARATOR ', ') FROM ".SECONDARYFISHERMAN." sf LEFT JOIN ".FISHERMAN." fm2 ON sf.Secondary = fm2.ID WHERE sf.Secondary = fm.ID AND sf.Primary != fm.ID AND sf.group_status = 'Joined' ".$primary_where.") AS primary_man,
			 
			 (SELECT GROUP_CONCAT(fm2.Name SEPARATOR ', ') FROM ".SECONDARYFISHERMAN." sf LEFT JOIN ".FISHERMAN." fm2 ON sf.Secondary = fm2.ID WHERE sf.Secondary != fm.ID AND sf.Primary = fm.ID AND sf.group_status = 'Joined' ".$secondary_where.") AS secondary_man
			 
			 FROM ". FISHERMAN." fm LEFT JOIN ".MAINGROUP." mg ON(mg.ID = fm.MainGroup)
			 
			 WHERE fm.status != 'Deleted' AND fm.editable != 'Lock'
		";
		
		$crud->basic_model->set_custom_query($sql);		
		
		$crud->columns('Code','Name', 'Village', 'group_name','MajorFee','MinorFee','SawalFee', 'products_balance',  'wages_balance', 'status', 'primary_man', 'secondary_man');
		
		$crud->display_summary('products_balance',  'wages_balance');
		
		$crud->unset_search(array('primary_man', 'secondary_man'));
		$crud->field_without_sorter(array('primary_man', 'secondary_man'));
		
		$output = $crud->render();
		$data = array('page_title'=> 'Fisherman', 'content_view'=>'setup/setting');		  
		$outputData = array_merge((array)$output, $data);	
		
		$this->template->set('stylesheet', array(base_url('assets/plugins/select2/dist/css/select2.min.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('setup/assets/js/setup.js')
												));
		$this->template->set('document_title', 'Fisherman');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and edit fisherman
	function add_fisherman($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('ID'=>'',
								'group_type_id'=>'',
								'MainGroup'=>'',
								'Code'=>'',
								'Name'=>'',
								'Contact'=>'',
								'doc_name'=>'',
								'doc_number'=>'',
								'AccountNo'=>'',
								'IfscCode'=>'',
								'Bank'=>'',
								'Branch'=>'',
								'Village'=>'',
								'MajorFee'=>'',
								'MinorFee'=>'',
								'SawalFee'=>'',
								'product_balance'=>'',
								'wages_balance'=>'',
								'status'=>''
								);
		$data['page_title'] = 'Add Fisherman';		
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('setup/add_fisherman');
		
		$form_valid = $this->__setFormRules('add_fisherman');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			$prepData = array('group_type_id'	=>$post['maingroup_type'],
							  'MainGroup'		=>$post['maingroup'],
							  'Code'			=>$post['code'],
							  'Name'			=>$post['name'],
							  'Contact'			=>$post['contact'],
							  'doc_name'		=>$post['doc_name'],
							  'doc_number'		=>$post['doc_number'],
							  'AccountNo'		=>$post['accountno'],
							  'IfscCode'		=>$post['ifsccode'],
							  'Bank'			=>$post['bank'],
							  'Branch'			=>$post['branch'],
							  'Village'			=>$post['village'],
							  'MajorFee'		=>$post['majorfee'],
							  'MinorFee'		=>$post['minorfee'],
							  'SawalFee'		=>$post['sawalfee'],
							  'products_balance'=>$post['products_balance'],
							  'wages_balance'   =>$post['wages_balance'],
							  'Remark'			=>$post['remark'],
							  'status'			=>$post['status'],
							  'added_by'		=>$this->userID,
							  'added_date'		=>get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=>microtime(true),
							  );
			
			$sf = $this->input->post('sf');
			$sf_data = array(); //Secondary fisher data
			if($id != NULL && $action_mode=='edit'){
				//UPDATE				
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('ID', $id)->update(FISHERMAN, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/add_fisherman/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
					
					if(isset($sf['id']) && !empty($sf['id'])){
						
						foreach($sf['id'] as $key=>$value){
							
							$where = array( 'Primary' => $id, 'Secondary' => $value, 'group_status' => 'Joined' );
							$check_existence = $this->db->where($where)->count_all_results(SECONDARYFISHERMAN);
							if(!$check_existence){
								$sf_data[] = array('group_type_id'	=> $post['maingroup_type'],
												   'maingroup_id'	=> $post['maingroup'],
												   'Primary'		=> $id,
												   'Secondary'		=> $value, //id of secondary fisherman
												   'Type'			=> 'Secondary',
												   'join_date' 		=> get_date('Y-m-d H:i:s'),
												   'group_status' 	=> 'Joined',
												   'added_by'		=> $this->userID,
												   'added_date'		=> get_datetime('Y-m-d H:i:s'),
												   'action_microtime'=>microtime(true)
												   );
							}
						}
						if(!empty($sf_data)){
							$this->db->insert_batch(SECONDARYFISHERMAN, $sf_data);
						}
					}
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//iNSERT
				$result = $this->db->insert(FISHERMAN, $prepData);
				$pf_id = $this->db->insert_id(); //Primary fisher id
				if($pf_id){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/add_fisherman/'.$pf_id);
					$data['status'] = 'success';
					$data['msg'] = 'Data inserted successfully.';
					
					//Create primary fisherman as self secondary fisherman of type Primary
					$p_data = array('group_type_id'	=> $post['maingroup_type'],
									'maingroup_id'	=> $post['maingroup'],
									'Primary'		=> $pf_id,
									'Secondary'		=> $pf_id,
									'Type'			=> 'Primary',
									'join_date' 	=> get_date('Y-m-d H:i:s'),
									'group_status' 	=> 'Joined',
									'added_by'		=> $this->userID,
									'added_date'	=> get_datetime('Y-m-d H:i:s'),
									'action_microtime'=>microtime(true)
								   );
					$this->db->insert(SECONDARYFISHERMAN, $p_data);
					
					if(isset($sf['id']) && !empty($sf['id'])){
						foreach($sf['id'] as $key=>$value){
							$sf_data[] = array('group_type_id'	=> $post['maingroup_type'],
							  				   'maingroup_id'	=> $post['maingroup'],
											   'Primary'		=> $pf_id,
											   'Secondary'		=> $value, //id of secondary fisherman
											   'Type'			=> 'Secondary',
											   'join_date' 		=> get_date('Y-m-d H:i:s'),
											   'group_status' 	=> 'Joined',
											   'added_by'		=> $this->userID,
											   'added_date'		=> get_datetime('Y-m-d H:i:s'),
											   'action_microtime'=>microtime(true)
											   );
						}
						$this->db->insert_batch(SECONDARYFISHERMAN, $sf_data);
					}
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
			$data['page_title'] = 'Edit Fisherman';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('setup/add_fisherman/'.$id);
			$fisher_data = $this->SM->get_fisher_data($id);
			if(!empty($fisher_data)){
				$data['dbdata'] = $fisher_data;
			}
		}
		
		$data['mg_type'] = $this->SM->get_mgtype_data(); // Get all maingroup_type data		
		$data['samiti'] = $this->SM->get_samiti();
		$data['scriptsrc'] = array(site_url('setup/assets/js/fisherman.js'));
		$data['setup_form'] = $this->load->view('setup/forms/add_fisherman_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function remove_secondary_fisherman(){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$db_id = $this->input->post('db_id');
		if(!empty($db_id)){
			$prepData = array('group_status' => 'Left',
							  'left_date' 	 => get_date('Y-m-d H:i:s'),
							  'updated_date' => get_datetime('Y-m-d H:i:s'),
							  'updated_by' 	 => $this->userID
							  );
			$result = $this->db->where(array('ID'=>$db_id))->update(SECONDARYFISHERMAN, $prepData);
			if($result){
				$data = array('status'=>'success', 'msg'=>'Fisherman removed.');
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Ajax function when add and edit fisherman
	function view_fisherman($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('ID'=>'',
								'group_type_id'=>'',
								'MainGroup'=>'',
								'Code'=>'',
								'Name'=>'',
								'Contact'=>'',
								'doc_name'=>'',
								'doc_number'=>'',
								'AccountNo'=>'',
								'IfscCode'=>'',
								'Bank'=>'',
								'Branch'=>'',
								'Village'=>'',
								'MajorFee'=>'',
								'MinorFee'=>'',
								'SawalFee'=>'',
								'product_balance'=>'',
								'wages_balance'=>'',
								'status'=>''
								);
		$data['page_title'] = 'Fisherman';
		
		if($id != NULL){
			$fisher_data = $this->SM->get_fisher_data($id);
			if(!empty($fisher_data)){
				$data['dbdata'] = $fisher_data;
			}
		}
		
		$data['mg_type'] = $this->SM->get_mgtype_data(); // Get all maingroup_type data		
		$data['samiti'] = $this->SM->get_samiti();
		$data['scriptsrc'] = array(site_url('setup/assets/js/fisherman.js'));
		$data['setup_form'] = $this->load->view('setup/forms/view_fisherman_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Change toll rate
	function change_toll_rate(){		
		$actions = checkUserPermission('setup/change_toll_rate', $this->uri->segment(3));
		
		$data['allgrouptype'] = $this->SM->get_mgtype_data();
		$data['page_title'] = 'Change Toll Rate';
		$data['content_view'] = 'setup/change_toll_rate_v';
		$data['data_url'] = site_url('setup/ajax_change_toll_rate');
		$this->template->set('document_title', $data['page_title']);
		
		$stylesheet_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
									base_url('assets/plugins/select2/dist/css/select2.min.css')
									);
		$scriptsrc_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
									base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
									site_url('setup/assets/js/change_toll_rate.js')
									);
		$this->template->set('stylesheet', $stylesheet_array);	
		$this->template->set('scriptsrc', $scriptsrc_array);
		$this->template->layout($data);
	}
	
	function ajax_change_toll_rate(){
		$data = array('status' => 'failed', 'message'=>'Operation Failed, Please try again.');
		$formValidation = $this->__setFormRules('change_toll_rate');
		if($formValidation){
			$post_data = $this->input->post();
			//printr($post_data);
			$change_for 	= $post_data['change_for'];
			$group_type 	= $post_data['group_type'];
			$maingroup_id 	= $post_data['maingroup_id'];
			$fisherman_id 	= $post_data['fisherman_id'];
			$major_rate 	= $post_data['major_rate'];
			$minor_rate 	= $post_data['minor_rate'];
			$sawal_rate 	= $post_data['sawal_rate'];
			$from_date 		= get_datetime('Y-m-d', str_replace('/','-',$post_data['from_date']));
			$to_date 		= get_datetime('Y-m-d', str_replace('/','-',$post_data['to_date']));
			
			
			if($change_for == 'Fisherman'){
				$this->db->where(array('CompanyId'=>$fisherman_id));
			}
			$this->db->where(array('group_type'=>$group_type, 'Samiti'=>$maingroup_id, 'toll_date >='=>$from_date, 'toll_date <='=>$to_date));
			$count_record = $this->db->count_all_results(DAILYTOLLINFO);
			//printr($count_record);
			
			if($count_record > 0){
				if($change_for == 'Fisherman'){
					$this->db->where(array('CompanyId'=>$fisherman_id));
				}
				$this->db->where(array('group_type'=>$group_type, 'Samiti'=>$maingroup_id, 'toll_date >='=>$from_date, 'toll_date <='=>$to_date));
				$result = $this->db->update(DAILYTOLLINFO, array('MajorFee'=>$major_rate, 'MinorFee'=>$minor_rate, 'SawalFee'=>$sawal_rate));
				//echo $this->db->last_query();
				//$affected_rows = $this->db->affected_rows();
				if($result){
					$message = '<div class="alert alert-success">
									<button data-dismiss="alert" class="close">×</button>
									<i class="fa fa-check-circle"></i>
									Toll Rate successfully changed.
								</div>';
					$data = array('status'=>'success', 'message'=>$message);
				}else{
					$message = '<div class="alert alert-danger">
									<button data-dismiss="alert" class="close">×</button>
									<i class="fa fa-times-circle"></i>
									Toll Rate failed to changed, Please reload the page and try again.
								</div>';
					$data = array('status'=>'failed', 'message'=>$message);
				}
			}else{
				$message = '<div class="alert alert-danger">
								<button data-dismiss="alert" class="close">×</button>
								<i class="fa fa-times-circle"></i>
								No record found. Please try again.
							</div>';
				$data = array('status'=>'failed', 'message'=>$message);
			}
		}else{
			$data['status'] = 'failed';
			$data['message'] = validation_errors();
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
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
		$action_mode = $this->input->post('action_mode');		
		$code = $this->input->post('code');
		if($action_mode == 'edit'){
			$f_ids = $this->uri->segment('3');
			$this->db->where('ID !=', $f_ids, false);
		}
		$this->db->where('Code', $code);
		$result = $this->db->count_all_results(FISHERMAN);
		$this->db->last_query();
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
	
	/*
	function validate_date_BK(){
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		if(strtotime($from_date) > strtotime($to_date)){
			$this->form_validation->set_message('validate_date', 'The {field} field must be greater than "from date"');
			return FALSE;
		}
		return TRUE;
	}
	*/
	
	function validate_date(){
		$data = FALSE;
		$from_date = str_replace('/', '-', $this->input->post('from_date'));
		$to_date = str_replace('/', '-', $this->input->post('to_date'));
		if(strtotime($to_date) >= strtotime($from_date)){
			$this->form_validation->set_message('validate_date', 'The {field} field must be greater than "from date"');
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
				//$this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[1]');
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
			
			case'change_toll_rate':
				$this->form_validation->set_rules('change_for', 'Change For', 'trim|required|in_list[Fisherman,Group]');
				$this->form_validation->set_rules('group_type', 'Group Type', 'trim|required|integer|greater_than[0]');
				$this->form_validation->set_rules('maingroup_id', 'Main Group', 'trim|required|integer|greater_than[0]');
				if($this->input->post('change_for') == 'Fisherman'){
					$this->form_validation->set_rules('fisherman_id', 'Fisherman', 'trim|required|integer|greater_than[0]');
				}
				$this->form_validation->set_rules('major_rate', 'Major Rate', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('minor_rate', 'Minor Rate', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('sawal_rate', 'Sawal Rate', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|callback_validate_date');
			break;
			
			case'transfer_liability':
				$this->form_validation->set_rules('sf[amount]', 'Liability Amount', 'trim|required|numeric|callback_check_liability_amount', array('check_liability_amount' => 'Total liability amount must be less than or equals to remaining amount'));
				$this->form_validation->set_rules('action_mode', 'Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));			
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
}