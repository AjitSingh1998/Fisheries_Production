<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Common extends CI_Model {
	/**
	 * Function		:	generateSelectBox
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	String	-	$table
	 					Array	-	$oData
						String	-	$select
						Array	-	$where
	 * Return		: 	dropdown list html
	 * Description	: 	This function generate of dropdown list of table values.
	**/
	function generateSelectBox($table, $oData, $select='', $where='') {
        $option = '';	
        $this->db->from($table);
		if(isset($where) && $where !='' && is_array($where)){
			$this->db->where($where);
		}
        $this->db->where('status', 'Active');
        $result = $this->db->get()->result_array();
        if ( is_array($result) && count($result) > 0 ) {			
			foreach($result as $value){
				if(is_array($select)){
					$selected = (in_array($value[$oData['value']],$select))?'SELECTED':'';
				}else{
					$selected = ($select==$value[$oData['value']])?'SELECTED':'';
				}
				
				$option .= '<option '.$selected.' value="'.$value[$oData['value']].'">'.$value[$oData['option']].'</option>'."\n";			
			}
        }
		
		return $option;
		
    }
	
	/**
	 * Function		:	generateCheckBox
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	String	-	$table
	 					Array	-	$oData
						String	-	$select
						Array	-	$where
	 * Return		: 	html of checkbox
	 * Description	: 	This function generate of checkbox view of table values.
	**/
	function generateCheckBox($table, $oData, $select='', $where='', $name='') {
        $option = '';	
        $this->db->from($table);
		if(isset($where) && $where !='' && is_array($where)){
			$this->db->where($where);
		}
        $this->db->where('status', 'Active');
        $result = $this->db->get()->result_array();
        if ( is_array($result) && count($result) > 0 ) {			
			foreach($result as $value){
				if(is_array($select)){
					$selected = (in_array($value[$oData['value']],$select))?'CHECKED':'';
				}else{
					$selected = ($select==$value[$oData['value']])?'CHECKED':'';
				}
				
				$option .= '<label class="checkbox-inline">
							<input type="checkbox" class="square-red" name="'.$name.'[]" value="'.$value[$oData['value']].'" '.$selected.'>'.$value[$oData['option']].'</label>';
			}
        }else{
			$option = 'Speciality Data not found';
		}
		
		return $option;
		
    }
	
	/**
	 * Function		:	getAdminGroupList
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	NULL
	 * Return		: 	admin groups
	 * Description	: 	This function return the active admin groups.
	**/
	function getAdminGroupList(){
		
		/*if(loginUserInfo('group_id') == '1'){
			$this->db->where("(business_id = '0' OR business_id = '".loginCompanyInfo('company_id')."') AND status = 'Active'", NULL, false);
		}else if(loginUserInfo('group_id') == '2'){
			$this->db->where("((business_id = '0' AND group_id = 2) OR business_id = '".loginCompanyInfo('company_id')."') AND status = 'Active'", NULL, false);
		}else{
			$this->db->where("business_id = '". loginCompanyInfo('company_id'). "' AND status = 'Active'", NULL, false);
		}*/
		
		if(loginUserInfo('group_id') == 1){
			$this->db->where("group_id != ", 2);
		}else if(loginUserInfo('group_id') > 2){
			$this->db->where("group_id > ", 2);
		}
		
		$this->db->where("status = 'Active'", NULL, false);
		$result = $this->db->get(ADMINISTRATOR_GROUP)->result_array();
		
		// Create a multidimensional array to conatin a list of items and parents
		return $result;
				
	}
		
	/**
	 * Function		:	getAllMenuList
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	NULL
	 * Return		: 	menu array
	 * Description	: 	This function return menu array.
	**/
	function getAllMenuList($page_menu_id = ''){
		
		$result = array();
		if(is_array($page_menu_id) && !empty($page_menu_id)){
			$this->db->where_in('page_menu_id', $page_menu_id);
		}
		$result = $this->db->where('status', 'Active')->order_by('parent_id')->order_by('menu_order')->get(ADMIN_PAGE_MENU)->result_array();	
		
		// Create a multidimensional array to conatin a list of items and parents
		$menu = array(
			'items' => array(),
			'parents' => array()
		);		
		// Builds the array lists with data from the menu table
		if(!empty($result)){
			foreach($result as $items)
			{
				// Creates entry into items array with current menu item id ie. $menu['items'][1]
				$menu['items'][$items['page_menu_id']] = $items;
				// Creates entry into parents array. Parents array contains a list of all items with children
				$menu['parents'][$items['parent_id']][] = $items['page_menu_id'];
			}
		}
		//echo '<pre>'; print_r($menu); die;
		return $menu;		
		//return $menu;		
	}	
	
	/**
	 * Function		:	getLeftNavigationMenuList
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	NULL
	 * Return		: 	left naviagation menu array
	 * Description	: 	This function return menu array.
	**/
	function getLeftNavigationMenuList($group_id = ''){
		// Select all entries from the menu table
		$result = $this->db->query("SELECT agp.actions, apm.page_menu_id, apm.parent_id, apm.menu_url, apm.menu_level, apm.menu_title, apm.icon_class FROM ".ADMINISTRATOR_GROUP_PERMISSION." agp LEFT JOIN ".ADMIN_PAGE_MENU." apm ON(apm.page_menu_id = agp.menu_id) WHERE agp.group_id='".$group_id."' AND apm.status = 'Active' ORDER BY apm.menu_order ASC");
		// Create a multidimensional array to conatin a list of items and parents
		$menu = array(
			'items' => array(),
			'parents' => array()
		);
		$assignedMenu = array();
		// Builds the array lists with data from the menu table
		foreach($result->result_array() as $items)
		{
			// Creates entry into items array with current menu item id ie. $menu['items'][1]
			$menu['items'][$items['page_menu_id']] = $items;
			// Creates entry into parents array. Parents array contains a list of all items with children
			$menu['parents'][$items['parent_id']][] = $items['page_menu_id'];
			if($items['menu_url'] != ''){
				//$method = explode('/', $items['menu_url']);				
				$assignedMenu[$items['menu_url']]= $items['actions'];
			}
		}
		$this->session->set_userdata(array(SESSION_NAME . '_permission' => $assignedMenu));
		//echo '<pre>'; print_r($menu); die;		
		return $menu;
	
	}
	
	/**
	 * Function		:	getAssignedMenuToGroup
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	Integer	- group_id
	 * Return		: 	menu array group
	 * Description	: 	This function return assigned menu group array.
	**/
	function getAssignedMenuToGroup($group_id = ''){
		/*if($group_id > 2){
			$this->db->where('business_id', loginCompanyInfo('company_id'));
		}
		if($group_id > 2){
			
		}*/
		$this->db->where('group_id', $group_id);
		$result = $this->db->get(ADMINISTRATOR_GROUP_PERMISSION);
		
		$menu = array();
		foreach($result->result_array() as $items)
		{
			$menu[$items['menu_id']] = $items;
		}
		return $menu;
	}
	
	function getClientGroupList(){
		$result = $this->db->get(USERS_GROUP)->result_array();
		return $result;				
	}
	
	function getClientAssignedMenu($group_id = ''){
		$this->db->where('group_id', $group_id);
		$result = $this->db->get(USERS_GROUP_PERMISSION);
		
		$menu = array();
		foreach($result->result_array() as $items)
		{
			$menu[$items['menu_id']] = $items;
		}
		return $menu;
	}
	
	function getAllUserAdminMenuList(){
				
		$result = $this->db->where('status', 'Active')->order_by('parent_id')->order_by('menu_order')
				  ->get(ADMIN_PAGE_MENU)->result_array();
		
		// Create a multidimensional array to conatin a list of items and parents
		$menu = array(
			'items' => array(),
			'parents' => array()
		);		
		// Builds the array lists with data from the menu table
		if(!empty($result)){
			foreach($result as $items)
			{
				// Creates entry into items array with current menu item id ie. $menu['items'][1]
				$menu['items'][$items['page_menu_id']] = $items;
				// Creates entry into parents array. Parents array contains a list of all items with children
				$menu['parents'][$items['parent_id']][] = $items['page_menu_id'];
			}
		}
		//echo '<pre>'; print_r($menu); die;
		return $menu;		
		//return $menu;		
	}	
}
