<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Administration extends MY_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->library('grocery_CRUD');
		//$this->load->language('settings', $this->language);
    } 
		
	public function index(){
		$this->administrators();
	}
	
	function __callbackUnlockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/unlock');
	}
	
	public function administrators(){		
		//$this->load->language('settings', $this->language);		
		$actions = checkUserPermission('lock/administration/administrators', $this->uri->segment(4));		
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
		
		$crud->set_subject('Admin');
		$crud->set_table(ADMINISTRATOR);
		$crud->set_relation('group_id', ADMINISTRATOR_GROUP, 'group_name');
		$crud->where(array(ADMINISTRATOR.'.editable'=>'Lock'));
		
		$crud->columns('group_id', 'first_name', 'last_name', 'email', 'phone_number', 'profileImage','status','editable');
		$crud->set_field_upload('profileImage', 'adminProfileImage/150150/');
		$crud->fields('group_id', 'first_name', 'last_name', 'email', 'phone_number', 'password', 'status');
		$crud->required_fields('group_id', 'first_name', 'last_name', 'email', 'phone_number', 'password', 'status');
		
		$crud->display_as(array('group_id' => 'Job Rol'));
		$crud->callback_before_insert(array($this,'encrypt_password_callback'));
		$crud->callback_before_update(array($this,'encrypt_password_callback'));
		$crud->callback_edit_field('password', array($this,'decrypt_password_callback'));

		$output = $crud->render();
		$heading = array('page_title'=>'Manage Administrators','content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $heading);
				
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($outputData);
	}
	
	function admin_group(){		
		//$this->load->language('settings', $this->language);		
		$actions = checkUserPermission('lock/administration/admin_group', $this->uri->segment(4));		
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
		
		$crud->set_subject('Group');
		$crud->set_table(ADMINISTRATOR_GROUP);
		$crud->where(array(ADMINISTRATOR_GROUP.'.editable'=>'Lock'));
		
		$crud->columns('group_name', 'created_date', 'status','editable');
		$crud->fields('created_date', 'group_name', 'status');
		
		$crud->field_type('created_date', 'hidden', get_date('Y-m-d H:i:s'));
		
		$crud->required_fields('group_name', 'status');
		
		$output = $crud->render();
		$heading = array('page_title'=>'Manage Admin Group','content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $heading);	
		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($outputData);
	}
}