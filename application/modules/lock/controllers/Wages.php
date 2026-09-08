<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Wages extends MY_Controller {
	var $userID;
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
	}
	
	function index(){
		redirect('lock/wages/advance_wage');
	}

	function __callbackUnlockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/unlock');
	}
	
	function samiti_advance_wages(){
		$actions = checkUserPermission('lock/wages/advance_wage', $this->uri->segment(4));
		$data = array('page_title'=> 'Locked Advance to Samiti', 'content_view'=>'setup/setting');
		
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
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.editable'=>'Lock')); //Only locked samiti wages
		$crud->where(array(WAGES_ADVANCE.'.advance_to'=>'Group')); // Only samiti wages
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Date', 'receipt_number', 'maingroup_id', 'Amount', 'added_by','editable');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		
		$output = $crud->render();
		
		
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function advance_wage(){
		$actions = checkUserPermission('lock/wages/advance_wage', $this->uri->segment(4));
		$data = array('page_title'=> 'Locked Advance to Fisherman', 'content_view'=>'setup/setting');
		
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
				
		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('Fisherman', FISHERMAN, 'Name');
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.editable'=>'Lock', WAGES_ADVANCE.'.advance_to'=>'Fisherman'));
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('Date', 'receipt_number', 'Fisherman', 'maingroup_id', 'Amount', 'added_by','status','editable');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		
		$output = $crud->render();
		
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function wages_sheet(){
		$actions = checkUserPermission('lock/wages/wages_sheet', $this->uri->segment(4));
		$data = array('page_title'=> 'Locked Fisherman Wages', 'content_view'=>'setup/setting');
		
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
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES);
		$crud->set_relation('MainGroup', MAINGROUP, 'Name');
		$crud->where(array(WAGES.'.editable'=>'Lock'));
		$crud->columns('from_date', 'to_date', 'MainGroup','status','editable');
			
		$output = $crud->render();
		
		
		$outputData = array_merge((array)$output, $data);		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function calculate_samiti_wages(){
		$actions = checkUserPermission('lock/wages/calculate_samiti_wages', $this->uri->segment(4));
		$data = array('page_title'=> 'Locked Samiti Wages', 'content_view'=>'setup/setting');
		
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
		/*
		if(in_array('unlock', $actions)){
			$crud->add_action('Unlock', 'triggerBulkDelete text-danger',site_url('bulk_action/action/unlock'),'fa fa-unlock',array($this,'__callbackUnlockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Unlock', site_url('bulk_action/action/unlock'), ' text-danger', 'fa fa-unlock', 'editable');		
		}
		*/
		if(in_array('unlock', $actions)){
			$crud->add_action('Unlock', 'triggerBulkDelete text-danger', '', 'fa fa-unlock', array($this, '__callbackUnlockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Unlock', site_url('bulk_action/action/unlock'), ' text-danger','fa fa-unlock', '');
		}
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES);
		$crud->set_relation('MainGroup', MAINGROUP_TYPE, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES.'.editable'=>'Lock'));
		$crud->where(array(WAGES.'.wages_for'=>'Group'));
		
		$crud->columns('from_date', 'to_date', 'MainGroup', 'added_by');
		$crud->display_as('MainGroup', 'Group Type');
		$output = $crud->render();
		
		
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function cash_deposited_payment(){
		$actions = checkUserPermission('lock/wages/cash_deposited_payment', $this->uri->segment(4));
		$data = array('page_title'=> 'Locked Cash Deposited Payment', 'content_view'=>'setup/setting');
		
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
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(CASH_DEPOSITED_PAYMENT);
		$crud->set_relation('fisherman_id', FISHERMAN, 'Name');
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('group_type_id', MAINGROUP_TYPE, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(CASH_DEPOSITED_PAYMENT.'.editable'=>'Lock')); //Only locked CASH_DEPOSITED_PAYMENT
		
		$crud->columns('deposited_by', 'deposit_date', 'receipt_number', 'maingroup_id', 'fisherman_id', 'product_liability', 'wages_liability', 'remark', 'added_by');
		$crud->display_as(array('maingroup_id'=>'Maingroup', 'fisherman_id'=>'Fisherman'));
		$output = $crud->render();
		$outputData = array_merge((array)$output, $data);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
}
