<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Wages extends MY_Controller {
	var $userID;
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('wages_model', 'WM');
	}
	
	function index(){
		redirect('trash/wages/advance_wage');
	}
	
	function __callbackDeleteSheetButton($primary_key, $row){ 
		return site_url('bulk_action/action/delete_wages_sheetprint');
	}
	function __callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/delete');
	}
	function __callbackRestoreActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/active');
	}
	function __callbackDeleteCashDepositedPayment($primary_key, $row){ 
		return site_url('bulk_action/action/delete_cash_deposited_payment');
	}
	
	function samiti_advance_wages(){
		$actions = checkUserPermission('trash/wages/samiti_advance_wages', $this->uri->segment(4));
		$data = array('page_title'=> 'Trashed Advance to Samiti', 'content_view'=>'setup/setting');
		
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
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-times', array($this, '__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/delete'), ' text-danger','fa fa-times', '');
		}
		if(in_array('restore', $actions)){
			$crud->add_action('Restore', 'triggerBulkDelete', '', 'fa fa-undo', array($this, '__callbackRestoreActionButton'), 'dialogbox');
			$crud->add_bulk_action('Restore', site_url('bulk_action/action/active'), '','fa fa-undo', 'status');
		}
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.status'=>'Deleted', WAGES_ADVANCE.'.advance_to'=>'Group'));
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('Date', 'receipt_number', 'maingroup_id', 'Amount', 'added_by', 'status', 'editable');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		
		$output = $crud->render();
		
		$outputData = array_merge((array)$output, $data);	
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
		
	}
	
	function advance_wage(){
		$actions = checkUserPermission('trash/wages/advance_wage', $this->uri->segment(4));
		$data = array('page_title'=> 'Trashed Advance to Fisherman', 'content_view'=>'setup/setting');
		
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
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-times', array($this, '__callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/delete'), ' text-danger','fa fa-times', '');
		}
		if(in_array('restore', $actions)){
			$crud->add_action('Restore', 'triggerBulkDelete', '', 'fa fa-undo', array($this, '__callbackRestoreActionButton'), 'dialogbox');
			$crud->add_bulk_action('Restore', site_url('bulk_action/action/active'), '','fa fa-undo', 'status');
		}
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES_ADVANCE);
		$crud->set_relation('Fisherman', FISHERMAN, 'Name');
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES_ADVANCE.'.status'=>'Deleted', WAGES_ADVANCE.'.advance_to'=>'Fisherman'));
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('Date', 'receipt_number', 'Fisherman', 'maingroup_id', 'Amount', 'added_by','status','editable');
		$crud->display_as(array('receipt_number'=>'Receipt', 'maingroup_id'=>'Group/Party'));	
		
		$output = $crud->render();
		
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function wages_sheet(){
		$actions = checkUserPermission('trash/wages/wages_sheet', $this->uri->segment(4));
		$data = array('page_title'=> 'Trahsed Fisherman Wages', 'content_view'=>'setup/setting');
		
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
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-times', array($this, '__callbackDeleteSheetButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/delete_wages_sheetprint'), ' text-danger','fa fa-times', '');
		}
		if(in_array('restore', $actions)){
			$crud->add_action('Restore', 'triggerBulkDelete', '', 'fa fa-undo', array($this, '__callbackRestoreActionButton'), 'dialogbox');
			$crud->add_bulk_action('Restore', site_url('bulk_action/action/active'), '','fa fa-undo', 'status');
		}

		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES);
		$crud->set_relation('MainGroup', MAINGROUP, 'Name');
		$crud->where(array(WAGES.'.status'=>'Deleted'));
		$crud->where(array(WAGES.'.wages_for'=>'Fisherman'));
		$crud->columns('from_date', 'to_date', 'MainGroup','status','status','editable');
			
		$output = $crud->render();
		
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function calculate_samiti_wages(){
		$actions = checkUserPermission('trash/wages/calculate_samiti_wages', $this->uri->segment(4));
		$data = array('page_title'=> 'Trahsed Samiti Wages', 'content_view'=>'setup/setting');
		
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
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-times', array($this, '__callbackDeleteSheetButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/delete_wages_sheetprint'), ' text-danger','fa fa-times', '');
		}
		
		if(in_array('restore', $actions)){
			$crud->add_action('Restore', 'triggerBulkDelete', '', 'fa fa-undo', array($this, '__callbackRestoreActionButton'), 'dialogbox');
			$crud->add_bulk_action('Restore', site_url('bulk_action/action/active'), '','fa fa-undo', 'status');
		}
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(WAGES);
		$crud->set_relation('MainGroup', MAINGROUP_TYPE, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(WAGES.'.status'=>'Deleted'));
		$crud->where(array(WAGES.'.wages_for'=>'Group'));
		
		$crud->columns('from_date', 'to_date', 'MainGroup', 'added_by');
		$crud->display_as('MainGroup', 'Group Type');
		$output = $crud->render();
					  
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function cash_deposited_payment(){
		$actions = checkUserPermission('trash/wages/cash_deposited_payment', $this->uri->segment(4));
		$data = array('page_title' => 'Trashed Cash Deposited Payment', 'content_view' => 'setup/setting');
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_read();
		
		$crud->unset_read();		
		$crud->unset_edit();		
		$crud->unset_add();		
		$crud->unset_delete();
		$crud->unset_export();
		$crud->unset_print();
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-times', array($this, '__callbackDeleteCashDepositedPayment'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/delete_cash_deposited_payment'), ' text-danger','fa fa-times', '');
		}
		
		if(in_array('restore', $actions)){
			$crud->add_action('Restore', 'triggerBulkDelete', '', 'fa fa-undo', array($this, '__callbackRestoreActionButton'), 'dialogbox');
			$crud->add_bulk_action('Restore', site_url('bulk_action/action/active'), '','fa fa-undo', 'status');
		}
		
		$crud->set_subject($data['page_title']);
		$crud->set_table(CASH_DEPOSITED_PAYMENT);
		$crud->set_relation('fisherman_id', FISHERMAN, 'Name');
		$crud->set_relation('maingroup_id', MAINGROUP, 'Name');
		$crud->set_relation('group_type_id', MAINGROUP_TYPE, 'Name');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where(array(CASH_DEPOSITED_PAYMENT.'.status'=>'Deleted'));
		
		
		$crud->columns('deposited_by', 'deposit_date', 'receipt_number', 'maingroup_id', 'fisherman_id', 'product_liability', 'wages_liability', 'remark', 'added_by');
		$crud->display_as(array('maingroup_id'=>'Maingroup', 'fisherman_id'=>'Fisherman'));
		$output = $crud->render();
					  
		$outputData = array_merge((array)$output, $data);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
}
