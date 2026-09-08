<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Products extends MY_Controller {
	var $userID;
	var $CI;
	public function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('products_model', 'PM');
    }
	
	function index(){
		redirect('setup/products/products');
	}
	
	function callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/mark_delete');
	}
	function __callbackLockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/lock');
	}	
	
	//Listing, Add, Edit Product Type
	function product_type(){
		$actions = checkUserPermission('setup/products/product_type', $this->uri->segment(4));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
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
		
		$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
		$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
		$crud->set_subject('Product Type');
		$crud->set_table(PRODUCT_TYPE);
		$crud->where(array(PRODUCT_TYPE.'.status !='=>'Deleted'));
		$crud->where(array(PRODUCT_TYPE.'.editable !='=>'Lock'));
		
		
		$crud->columns('Name', 'status');	
		$crud->fields('Name', 'status', 'added_by', 'added_date', 'updated_by', 'updated_date',  'action_microtime');
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
		
		$data = array('page_title'=> 'Product Type', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		
		$this->template->set('document_title', 'Product Type');
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
	function product_category(){
		$actions = checkUserPermission('setup/products/product_category', $this->uri->segment(4));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');

			$crud->set_edit_url_path(site_url('setup/products/add_product_category'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{	
			$crud->set_add_url_path(site_url('setup/products/add_product_category'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		$crud->unset_delete();
		if(in_array('delete', $actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('setup/products/view_product_category'));
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
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}	
		
		
		$crud->set_subject('Product Category');
		$crud->set_table(PRODUCT_CATEGORY);
		$crud->set_relation('Type_id', PRODUCT_TYPE, 'Name', array('status'=>'Active'));
		$crud->where(array(PRODUCT_CATEGORY.'.status !='=>'Deleted'));
		$crud->where(array(PRODUCT_CATEGORY.'.editable !='=>'Lock'));
		
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Name', 'Type_id', 'Remark','status');	
		$crud->display_as('Type_id', 'Type');
				
		$output = $crud->render();
		
		$data = array('page_title'=> 'Product Category', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('scriptsrc', array(site_url('setup/assets/js/setup.js')));
		$this->template->set('document_title', 'Products Category');
		$this->template->layout($outputData);
		
	}
	
	//Ajax function when add and update jaaltype
	function add_product_category($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 'Type'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Add Product Category';
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('setup/products/add_product_category');
		
		$form_valid = $this->__setFormRules('add_product_category');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			$prepData = array('Name'			=>$post['name'],
							  'Type_id'			=>$post['type'],
							  'Remark'			=>$post['remark'],
							  'status'			=>$post['status'],
							  'added_by'		=>$this->userID,
							  'added_date'		=>get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=>microtime(true),
							  );
			
			
			if($id != NULL && $action_mode=='edit'){
				//do update
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('ID', $id)->update(PRODUCT_CATEGORY, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/products/add_product_category/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(PRODUCT_CATEGORY, $prepData);
				$id = $this->db->insert_id();
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/products/add_product_category/'.$id);
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
			$data['page_title'] = 'Edit Product Category';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('setup/products/add_product_category/'.$id);
			$pc_data = $this->PM->get_pc_data($id); // get product category data
			if(!empty($pc_data)){
				$data['dbdata'] = $pc_data;
			}
		}
		
		$data['pr_type'] = $this->PM->get_prtype_data(); // Get all product type data
		$data['setup_form'] = $this->load->view('setup/forms/add_product_category_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function view_product_category($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 'Type'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Product Category';
		
		if($id != NULL){
			$pc_data = $this->PM->get_pc_data($id); // get product category data
			if(!empty($pc_data)){
				$data['dbdata'] = $pc_data;
			}
		}
		
		$data['pr_type'] = $this->PM->get_prtype_data(); // Get all product type data
		$data['setup_form'] = $this->load->view('setup/forms/view_product_category_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Listing
	function products(){
		$columns = array('Type' =>'pt.Name', 'category'=>'pc.Name', 'code'=>'p.code', 'Name'=>'p.Name', 'Rate'=>'p.Rate', 'in_stock'=>'in_stock', 
		'status' => 'p.status','editable'=>'p.editable'); 
		$search_field = $this->input->post('search_field');
		if(!empty($search_field)){
			foreach($search_field as $key => $value){
				if(array_key_exists($value, $columns)){
					$search_field[$key] = $columns[$value];					
				}							
			}			
			$_POST['search_field'] = $search_field;
		}
				
		$actions = checkUserPermission('setup/products/products', $this->uri->segment(4));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->add_bulk_action('Active', site_url('bulk_action/action/active'), '', 'fa fa-check', 'status');
			$crud->add_bulk_action('Inactive', site_url('bulk_action/action/inactive'), '', 'fa fa-ban', 'status');
			
			$crud->set_edit_url_path(site_url('setup/products/add_product'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('setup/products/add_product'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		$crud->unset_delete();
		if(in_array('delete', $actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('setup/products/view_product'));
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
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger', 'fa fa-lock', 'editable');		
		}		
		
		$crud->set_subject('Products');
		$crud->set_table(PRODUCT);
					
		$crud->set_model('grocery_crud_custom_query_model');
		
		//(SELECT pi2.Rate FROM ".PRODUCT_INWARD." pi2 WHERE pi2.product_id=p.ID ORDER BY pi2.action_microtime DESC LIMIT 0,1 ) as Rate,			
		/*
			SUM(DISTINCT pi.quantity) as inward,
			SUM(pir.quantity) as inward_return, 
			SUM(poi.quantity) as outward, 
			SUM(poir.quantity) as outward_return, 		
			
			 (((
				IFNULL(SUM(DISTINCT pi.quantity), 0) + 
				IFNULL(SUM(DISTINCT poir.quantity), 0)
			) - (
				IFNULL(SUM(DISTINCT pir.quantity), 0) + 
				IFNULL(SUM(DISTINCT poi.quantity), 0)
			)) + p.Stock ) as current_stock	
			
			FROM ".PRODUCT." p
			LEFT JOIN ".PRODUCT_TYPE." pt ON pt.ID = p.type
			LEFT JOIN ".PRODUCT_CATEGORY." pc ON pc.ID = p.Category_Id 
			
			LEFT JOIN (SELECT product_id, sum(quantity) as quantity FROM ".PRODUCT_INWARD." GROUP BY product_id) 
						pi ON pi.product_id = p.ID AND pi.status = 'Active'
			LEFT JOIN (SELECT product_id, sum(quantity) as quantity FROM ".PRODUCT_INWARD_RETURN." GROUP BY product_id) 
						pir ON pir.product_id = p.ID AND pir.status = 'Active'

			LEFT JOIN (SELECT product_id, sum(quantity) as quantity FROM ".PRODUCT_OUTWARD_ITEM." GROUP BY product_id) 
						poi ON poi.product_id = p.ID AND poi.status = 'Active'
			LEFT JOIN (SELECT product_id, sum(quantity) as quantity FROM ".PRODUCT_OUTWARD_ITEM_RETURN." GROUP BY product_id) 
						poir ON poir.product_id = p.ID AND poir.status = 'Active'
						
		*/
		
		$sql = "SELECT 
			
			p.ID, p.code, p.Name as Name, p.Rate, p.Stock as opening_stock, p.status, p.editable, pt.Name as Type, pc.Name as category, 
		
			IFNULL(pi.quantity,0) as inward, 
			IFNULL(pir.quantity,0) as inward_return, 
			IFNULL(poi.quantity,0) as outward, 
			IFNULL(poir.quantity,0) as outward_return, 		
			
			 (((
				IFNULL(pi.quantity, 0) + 
				IFNULL(poir.quantity, 0)
			) - (
				IFNULL(pir.quantity, 0) + 
				IFNULL(poi.quantity, 0)
			)) + p.Stock ) as current_stock	
			
			FROM ".PRODUCT." p
			LEFT JOIN ".PRODUCT_TYPE." pt ON pt.ID = p.type
			LEFT JOIN ".PRODUCT_CATEGORY." pc ON pc.ID = p.Category_Id 
			
			LEFT JOIN (SELECT product_id, status, SUM(quantity) as quantity FROM ".PRODUCT_INWARD." WHERE status = 'Active' GROUP BY product_id) 
						pi ON(pi.product_id = p.ID)
						
			LEFT JOIN (SELECT product_id, status, SUM(quantity) as quantity FROM ".PRODUCT_INWARD_RETURN." WHERE status = 'Active' GROUP BY product_id) 
						pir ON(pir.product_id = p.ID)

			LEFT JOIN (SELECT product_id, status, SUM(quantity) as quantity FROM ".PRODUCT_OUTWARD_ITEM." WHERE status = 'Active' GROUP BY product_id) 
						poi ON(poi.product_id = p.ID)
						
			LEFT JOIN (SELECT product_id, status, SUM(quantity) as quantity FROM ".PRODUCT_OUTWARD_ITEM_RETURN." WHERE status = 'Active' GROUP BY product_id) 
						poir ON(poir.product_id = p.ID)
						
			WHERE p.editable != 'Lock' AND p.status != 'Deleted'
			";
		$sql_group_by = " GROUP BY p.ID ORDER BY  ";
		//echo $sql, $sql_group_by;
		//die;
		$crud->basic_model->set_custom_query($sql);				
		$crud->order_by('p.added_date', 'DESC');
		$crud->columns('Type', 'category', 'Name', 'Rate', 'opening_stock', 'inward', 'inward_return', 'outward', 'outward_return', 'current_stock', 'status');
		$crud->display_summary('opening_stock', 'inward', 'inward_return', 'outward', 'outward_return', 'current_stock');
		$crud->field_without_sorter(array('Rate', 'opening_stock', 'inward', 'inward_return', 'outward', 'outward_return'));
		$crud->unset_search(array('Rate', 'opening_stock', 'inward', 'inward_return', 'outward', 'outward_return', 'current_stock'));
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'All Products', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('scriptsrc', array(site_url('setup/assets/js/setup.js')));
		$this->template->set('document_title', 'All Products');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and update nav_products
	function add_product($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		
		$data['dbdata'] = array('type'=>'', 'Category_Id'=>'', 'code'=>'', 'Name'=>'', 'Rate'=>'', 'Stock'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Add Product';
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('setup/products/add_product');
		
		$form_valid = $this->__setFormRules('add_product');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			$prepData = array('type' 			=> $post['type'],
							  'Category_Id' 	=> $post['category'],
							  'Name'			=> $post['name'],
							  'Rate'			=> $post['rate'],
							  'Stock'			=> $post['Stock'],
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
				$result = $this->db->where('ID', $id)->update(PRODUCT, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/products/add_product/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(PRODUCT	, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('setup/products/add_product/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data inserted successfully.';
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to insert. Please try again.';
				}
			}
		}else{
			//Form validation error
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		if($id != NULL){
			//printr($id);
			$data['page_title'] = 'Edit Product';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('setup/products/add_product/'.$id);
			$product_data = $this->PM->get_product_data($id); // get nav product data
			//printr($dbdata);
			if(!empty($product_data)){
				$data['dbdata'] = $product_data;
			}
		}
		
		$data['pr_type'] = $this->PM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('setup/assets/js/products.js'));
		$data['setup_form'] = $this->load->view('setup/forms/add_products_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function view_product($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		
		$data['dbdata'] = array('type'=>'', 'Category_Id'=>'', 'code'=>'', 'Name'=>'', 'Rate'=>'', 'Stock'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Product';
		
		if($id != NULL){
			$product_data = $this->PM->get_product_data($id); // get nav product data
			//printr($dbdata);
			if(!empty($product_data)){
				$data['dbdata'] = $product_data;
			}
		}
		
		$data['pr_type'] = $this->PM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('setup/assets/js/products.js'));
		$data['setup_form'] = $this->load->view('setup/forms/view_products_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Get product category according to product type
	function ajax_pr_cat(){
		$data = array('status'=>'danger', 'msg'=>'Product category data not found', 'data'=>'<option value="">No category found</option>');
		$ptype = $this->input->post('ptype');
		//printr($ptype);
		if(!empty($ptype)){
			$result = $this->PM->get_all_pr_cat($ptype);
			$options = '<option value="">Category</option>';
			if(!empty($result)){
				foreach($result as $res){
					$options .= '<option value="'.$res['ID'].'">'.$res['Name'].'</option>';
				}
				$data = array('status'=>'success', 'msg'=>'Category data.', 'data'=>$options);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	private function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case'add_product_category':
				$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[2]');
				$this->form_validation->set_rules('type', 'Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('status', 'Status', 'trim|required|min_length[2]|in_list[Active,Inactive]', array('in_list'=>'Invalid status field.'));
				$this->form_validation->set_rules('action_mode', 'Action Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));			break;
			break;
			case'add_product':
				$this->form_validation->set_rules('type', 'Type', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[1]');
				//$this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('rate', 'Rate', 'trim|required|numeric|greater_than[0]');
				
				$this->form_validation->set_rules('status', 'Status', 'trim|required|min_length[2]|in_list[Active,Inactive]', array('in_list'=>'Invalid status field.'));
				$this->form_validation->set_rules('action_mode', 'Action Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));			break;
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
}