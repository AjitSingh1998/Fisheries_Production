<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Administration extends MY_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->library('grocery_CRUD');
		
    } 
		
	public function index(){
		$this->administrators();
	}
	
	function __unique_field_name($field_name) 
	{
		return 's'.substr(md5($field_name),0,8);
	}
	
	function callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/mark_delete');
	}
	function __callbackLockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/lock');
	}	
	
	function company_details(){
		$actions = checkUserPermission('setup/administration/company_details', $this->uri->segment(4));
		$data['page_title'] = 'Company Details';
		$data['content_view'] = 'setup/administration/company_details';		
		$this->template->set('document_title', 'Company Details');
		$this->template->layout($data);
	}
	
	function settings(){		
			
		$actions = checkUserPermission('setup/administration/settings', $this->uri->segment(4));		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		$crud->unset_read();
		
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();			
		}
		
		if(!in_array('delete', $actions)){			
			$crud->unset_delete();	
		}		
		if(!in_array('export', $actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $actions)){
			$crud->unset_print();
		}
					
		$crud->set_subject('Settings');
		$crud->set_table(SETTINGS);
		
		$crud->columns('keyword', 'value', 'description');
		$crud->fields('keyword', 'value', 'description');
		$crud->unset_texteditor('description');
		$crud->required_fields('keyword', 'value');
		
		$crud->callback_before_insert(array($this,'__setting_name'));
		$crud->callback_before_update(array($this,'__setting_name'));
		
		$crud->callback_after_insert(array($this,'__write_websetting'));
		$crud->callback_after_update(array($this,'__write_websetting'));
		$crud->unique_fields(array('keyword'));

		$output = $crud->render();
		$heading = array('page_title'=>'Application Settings','content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $heading);
				
		$this->template->set('document_title', 'Application Settings');
		$this->template->layout($outputData);
	}
	
	function __write_websetting($post_array, $primary_key){
		$result = $this->db->get(SETTINGS)->result_array();
		$website_settings = '<?php'."\n";
		if(count($result) > 0){
			foreach($result as $setting){
				$setting['value'] = str_replace('"', '\"', $setting['value']);
				$website_settings .= 'define("'.$setting['keyword'].'", "'.$setting['value'].'");'."\n";
			}
		}
		$fileName = APPPATH.'config/website_setting.php';
		$writeSetting = file_put_contents($fileName, $website_settings);
	}
	function __setting_name($post_array){
		$post_array['keyword'] = strtoupper($post_array['keyword']);
		return $post_array;
	}
	
	function administrators(){		
			
		$actions = checkUserPermission('setup/administration/administrators', $this->uri->segment(4));		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		$crud->unset_delete();
		
		if(in_array('edit', $actions)){
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		}else{
			$crud->unset_edit();
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();			
		}
		
		if(in_array('delete', $actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
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
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock', array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
			
		//$crud->set_add_url_path(site_url('setup/administration/add_admin'));
		//$crud->set_edit_url_path(site_url('setup/administration/edit_admin'));
		
		$crud->set_subject('Admin');
		$crud->set_table(ADMINISTRATOR);
		$crud->set_relation('group_id', ADMINISTRATOR_GROUP, 'group_name');
		$crud->where(array(ADMINISTRATOR.'.group_id !=' => '1', ADMINISTRATOR.'.group_id !=' => '2'));
		$crud->where(array(ADMINISTRATOR.'.status !=' => 'Deleted'));
		$crud->where(array(ADMINISTRATOR.'.editable !=' => 'Lock'));
		
		$crud->columns('group_id', 'first_name', 'last_name', 'email', 'phone_number', 'profileImage','status');
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
	
	function encrypt_password_callback($post_array, $primary_key = null){
		$this->load->library('encryption');	
    	$key = $this->config->item('encryption_key');	
    	$config = array( 'driver' => 'openssl', 'cipher' => 'aes-128', 'mode' => 'cbc', 'key' => $key);
    	$this->encryption->initialize($config);	
    	$post_array['password'] = $this->encryption->encrypt($post_array['password']);
    	return $post_array;
		
	}
	
 	function decrypt_password_callback($value){
		$this->load->library('encryption');	
    	$key = $this->config->item('encryption_key');	
    	$config = array( 'driver' => 'openssl', 'cipher' => 'aes-128', 'mode' => 'cbc', 'key' => $key);
    	$this->encryption->initialize($config);	
    	$decrypted_password = $this->encryption->decrypt($value);
    	return "<input type='text' name='password' class='form-control' value='$decrypted_password' />";
	}
	
	function add_admin(){
		$data['page_title'] = 'Add administrator';
		$data['breadcrumb'] = '';
		$data['content_view'] = 'setup/administration/administrators';		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($data);
	}
	
	function edit_admin(){
		$data['page_title'] = 'Edit administrator';
		$data['breadcrumb'] = '<>';
		$data['content_view'] = 'setup/administration/administrators';		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($data);
	}

	function admin_group(){		
			
		$actions = checkUserPermission('setup/administration/admin_group', $this->uri->segment(4));		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		if(in_array('edit', $actions)){
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		}else{
			$crud->unset_edit();
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}
		$crud->unset_delete();
		if(in_array('delete', $actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
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
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}
				
		$crud->set_subject('Group');
		$crud->set_table(ADMINISTRATOR_GROUP);
		$crud->where(array(ADMINISTRATOR_GROUP.'.group_id !=' => '1', ADMINISTRATOR_GROUP.'.group_id !=' => '2'));
		$crud->where(array(ADMINISTRATOR_GROUP.'.status !=' => 'Deleted'));
		$crud->where(array(ADMINISTRATOR_GROUP.'.editable !=' => 'Lock'));
		
		$crud->columns('group_name', 'created_date', 'status');
		$crud->fields('created_date', 'group_name', 'status');
		
		$crud->field_type('created_date', 'hidden', get_date('Y-m-d H:i:s'));
		
		$crud->required_fields('group_name', 'status');
		
		$output = $crud->render();
		$heading = array('page_title'=>'Manage Admin Group','content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $heading);	
		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($outputData);
	}
	
	function menu_items(){
		
		
		$actions = checkUserPermission('setup/administration/menu_items', $this->uri->segment(4));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		
		if(in_array('edit', $actions)){
			$crud->add_bulk_action('Active', site_url('setup/bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('setup/bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		}else{
			$crud->unset_edit();
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}
		if(!in_array('delete', $actions)){
			$crud->unset_delete();
		}
		if(!in_array('read', $actions)){
			$crud->unset_read();
		}
		
		$crud->set_subject('Menu Item');
		$crud->set_table(ADMIN_PAGE_MENU);
		$crud->set_relation('parent_id', ADMIN_PAGE_MENU, 'menu_title', array('parent_id' => '0', 'status' => 'Active'));
		
		$crud->order_by(ADMIN_PAGE_MENU.'.parent_id', 'ASC');	
		
		$crud->columns('parent_id', 'menu_title', 'menu_actions', 'menu_url', 'menu_order', 'status');		
		$crud->display_as('parent_id', 'Menu Category');
		
		$crud->fields('parent_id', 'menu_level', 'menu_title','menu_actions','menu_url','menu_order','status', 'icon_class');
		$crud->required_fields('menu_title','menu_actions','menu_url','menu_order','status');
		
		$crud->callback_field('parent_id',array($this,'field_callback_1'));
		$crud->change_field_type('menu_level', 'hidden');
		
		//callback function for inserting activity log After insert data
		$crud->callback_before_insert(array($this,'__menuItemBeforeInsert'));
		//callback function for inserting activity log After update data
		$crud->callback_before_update(array($this,'__menuItemBeforeInsert'));
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Manage Menu item', 'content_view'=>'setup/setting',);
					  
		$outputData = array_merge((array)$output, $data);		
		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($outputData);
	}
	
	function __menuItemBeforeInsert($post_array){
		$parent_id = $post_array['parent_id'];
		if(!empty($parent_id)){
			$idarray = explode('_', $parent_id);
			$pid = $idarray[0];
			$level = isset($idarray[1]) ? $idarray[1] + 1 : 0;
			$post_array['parent_id'] = $pid;
			$post_array['menu_level'] = $level;						
		}else{			
			$post_array['parent_id'] = 0;
			$post_array['menu_level'] = 0;	
		}
		return $post_array;
	}
	
	function field_callback_1($value = '', $primary_key = null)	{
		$this->db->select('page_menu_id, parent_id, menu_title, menu_level');		
		$this->db->where('status', 'Active');
		$this->db->order_by('parent_id', 'ASC');
		$result = $this->db->get(ADMIN_PAGE_MENU)->result_array();
		
		$menu = array('items' => array(),'parents' => array());
		// Builds the array lists with data from the menu table
		if(isset($result) && !empty($result)){
			foreach($result as $items)
			{
				// Creates entry into items array with current menu item id ie. $menu['items'][1]
				$menu['items'][$items['page_menu_id']] = $items;
				// Creates entry into parents array. Parents array contains a list of all items with children
				$menu['parents'][$items['parent_id']][] = $items['page_menu_id'];
			}
		}
		
		$outut = '<select name="parent_id" class="chosen-select">';		
		$outut .= '<option value="">-- Select Parent menu --</option>';
		$outut .= dropdownMenuList('0', $menu, $value);		
		$outut .= '</select>';
		return $outut;
	}
	
	function permission($group_id = NULL){
		$actions = array('edit');
		// Check view permission to all user except Developer Group.
		if(loginUserInfo('group_id') == 2 || loginUserInfo('group_id') == 1){}
		else{
			$actions = checkUserPermission('setup/administration/permission');
		}
		
		$postData = $this->input->post();
		if(isset($postData) &&  !empty($postData) && $group_id != NULL){
			// Check edit permission to all user except Developer Group.
			if(loginUserInfo('group_id') == 2 || loginUserInfo('group_id') == 1){}
			else{
				$actions = checkUserPermission('setup/permission', 'edit');
			}
				
			$menus = $postData['menu'];
			$preparedData = array();
			foreach($menus['name'] as $menu){
				$menu_id = explode('_', $menu);
				$menuActions = @$menus[$menu];
				$action = '';
				if(isset($menuActions) && is_array($menuActions) && !empty($menuActions)){
					$action = implode('|', $menuActions);
				}
								
				$preparedData[] = array('group_id'=>$group_id,'parent_id'=>$menu_id[0],'menu_id'=>$menu_id[1],'actions'=>trim($action));							
			}
			
			if(isset($preparedData) && count($preparedData) > 0){
				$delete = $this->db->where('group_id', $group_id)->delete(ADMINISTRATOR_GROUP_PERMISSION);
				$result = $this->db->insert_batch(ADMINISTRATOR_GROUP_PERMISSION, $preparedData);
				if($result){					
					//generate activity log data end
					$this->messageci->set('Permissions updated successfully!', 'success');
				}else{
					$this->messageci->set('Updating Permission failed!', 'error');
				}
				redirect(site_url('setup/administration/permission'), 'refresh');
			}			
		}
		
		$group_id = ($group_id == NULL) ? loginUserInfo('group_id') : $group_id;
		$menu_ids = '';
		if($group_id > 2){
			$assigned_menu_list = $this->common->getAssignedMenuToGroup(loginUserInfo('group_id'));
			$menu_ids = array_keys($assigned_menu_list);
		}
		$data['menu_list'] = $this->common->getAllMenuList($menu_ids);		
		$data['group_list'] = $this->common->getAdminGroupList();		
		$data['group_menu_list'] = $this->common->getAssignedMenuToGroup($group_id);
		$data['group_id'] = $group_id;
		$data['actions'] = $actions;		
		
		$data['content_view'] = 'setup/administration/permission';
		$data['page_title'] = 'Set permission to staff group';
		
		$this->template->set('stylesheet', array(base_url('assets/css/extra_style.css')));
		$this->template->set('scriptsrc', array(site_url('setup/assets/js/permission.js')));
		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($data,$this->template_name);
	}
	
	function client_permission($group_id = NULL){	
		$actions = array('edit');
		// Check view permission to all user except Developer Group.
		if(loginUserInfo('group_id') != 1){
			$actions = checkUserPermission('setup/administration/permission');
		}		
		$postData = $this->input->post();
		if(isset($postData) &&  !empty($postData) && $group_id != NULL){
			// Check edit permission to all user except Developer Group.
			if(loginUserInfo('group_id') != 1){
				//$actions = checkUserPermission('setup/permission', 'edit');
			}
			
			$menus = $postData['menu'];
			$preparedData = array();
			foreach($menus['name'] as $menu){
				$menu_id = explode('_', $menu);
				$menuActions = @$menus[$menu];
				$action = '';
				if(isset($menuActions) && is_array($menuActions) && !empty($menuActions)){
					for($i=0; $i < count($menuActions); $i++){
						$action .= $menuActions[$i].'|';
					}
				}
				//$applicationID = ($group_id == 1 || $group_id == 2) ? '0' : loginCompanyInfo('company_id');				
				$preparedData[] = array('group_id'=>$group_id,'parent_id'=>$menu_id[0],'menu_id'=>$menu_id[1],'actions'=>trim($action,'|'));							
			}
			//echo '<pre>'; print_r($preparedData); die;
			if(isset($preparedData) && count($preparedData) > 0){
				$delete = $this->db->where('group_id', $group_id)->delete(USERS_GROUP_PERMISSION);
				$result = $this->db->insert_batch(USERS_GROUP_PERMISSION, $preparedData);
				if($result){					
					//generate activity log data end
					$this->messageci->set('Permissions updated successfully!', 'success');
				}else{
					$this->messageci->set('Updating Permission failed!', 'error');
				}
			}			
		}
		
		$group_id = ($group_id == NULL) ? 1 : $group_id;
		$data['menu_list'] = $this->common->getAllUserAdminMenuList();
		$data['group_list'] = $this->common->getClientGroupList();		
		$data['group_menu_list'] = $this->common->getClientAssignedMenu($group_id);
		
		$data['group_id'] = $group_id;
		$data['actions'] = $actions;		
		
		$data['content_view'] = 'setup/administration/client_permission';
		$data['page_title'] = 'Set permission to Client group';
		
		$this->template->set('stylesheet', array(base_url('assets/css/extra_style.css')));
		$this->template->set('scriptsrc', array(site_url('setup/assets/js/permission.js')));
		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($data,$this->template_name);
		
	
	}
	
	function close_dates(){
		$data['page_title'] = 'close_dates';
		$data['breadcrumb'] = 'services';
		$data['content_view'] = 'setup/administration/close_dates';		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($data);
	}
	
	public function roster(){
		$data['page_title'] = 'Roster';
		$data['breadcrumb'] = 'services';
		$data['content_view'] = 'setup/administration/roster';		
		$this->template->set('document_title', lang('document_title'));
		$this->template->layout($data);
	}
}