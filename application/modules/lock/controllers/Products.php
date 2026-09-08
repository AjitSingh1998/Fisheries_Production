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
		redirect('lock/products/products');
	}

	function __callbackUnlockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/unlock');
	}

	//Listing, Add, Edit Product Type
	function product_type(){
		$actions = checkUserPermission('lock/products/product_type', $this->uri->segment(4));
		
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

		$crud->set_subject('Product Type');
		$crud->set_table(PRODUCT_TYPE);
		$crud->where(array(PRODUCT_TYPE.'.editable'=>'Lock'));
		$crud->columns('Name', 'status', 'editable');	
		$crud->fields('Name', 'status', 'action_microtime', 'added_by', 'added_date', 'updated_by', 'updated_date');
		$crud->required_fields('Name', 'status');
		
		$crud->field_type('added_by','invisible');
		$crud->field_type('added_date','invisible');
		$crud->field_type('updated_by','invisible');
		$crud->field_type('updated_date','invisible');
		$crud->field_type('action_microtime','invisible');
		
		$crud->callback_before_insert(array($this,'__callbackBeforeInsert'));
 		$crud->callback_before_update(array($this,'__callbackBeforeUpdate'));
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Product Type', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		
		$this->template->set('document_title', 'Product Type');
		$this->template->layout($outputData);
	}
	
	
	//Listing
	function product_category(){
		$actions = checkUserPermission('lock/products/product_category', $this->uri->segment(4));
		
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

		
		$crud->set_subject('Product Category');
		$crud->set_table(PRODUCT_CATEGORY);
		$crud->set_relation('Type_id', PRODUCT_TYPE, 'Name', array('status'=>'Active'));
		$crud->where(array(PRODUCT_CATEGORY.'.editable'=>'Lock'));
		$crud->order_by('added_date', 'DESC');
		$crud->columns('Name', 'Type_id', 'Remark','status', 'editable');	
		$crud->display_as('Type_id', 'Type');
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Product Category', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('scriptsrc', array(site_url('lock/assets/js/setup.js')));
		$this->template->set('document_title', 'Products Category');
		$this->template->layout($outputData);
		
	}
	
	//Ajax function when add and update jaaltype
	
	
	//Listing
	function products(){
		$columns = array('Type' =>'pt.Name', 'category'=>'pc.Name', 'code'=>'p.code', 'Name'=>'p.Name', 'Rate'=>'p.Rate', 'in_stock'=>'in_stock', 
		'status' => 'p.status','editable'=>'p.editable'); 
		$search_field = $this->input->post('search_field');
		
		if(!empty($search_field)){
			foreach($search_field as $key => $value){
				if(array_key_exists($value,$columns)){
					$search_field[$key] = $columns[$value];
				}
			}
			$_POST['search_field'] = $search_field;
		}
		
		$actions = checkUserPermission('lock/products/products', $this->uri->segment(4));
		
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

		
		$crud->set_subject('Products');
		$crud->set_table(PRODUCT);
		
		/*
		$crud->set_relation('type', PRODUCT_TYPE, 'Name');
		$crud->set_relation('Category_Id', PRODUCT_CATEGORY, 'Name');
		$crud->order_by('added_date', 'DESC');
		*/		
		//LEFT JOIN ( (select DailytollId, SUM(Tqty) as total_qty, SUM(Twt) as total_wt from ".DAILYTOLLINFO." GROUP BY DailytollId)) dti ON dti.DailytollId = dt.ID
		//dti.total_qty, dti.total_wt,
		/*
		
		LEFT JOIN ".PRODUCT_INWARD_RETURN." pir ON pir.product_id = p.ID

		LEFT JOIN ".PRODUCT_OUTWARD_ITEM." poi ON poi.product_id = p.ID
		LEFT JOIN ".PRODUCT_OUTWARD_ITEM_RETURN." poir ON poir.product_id = p.ID
		WHERE p.ID != 0 
			
			
			
			((
				IFNULL((SELECT SUM(pi.quantity) FROM ".PRODUCT_INWARD."  pi WHERE pi.product_id = p.ID), 0) + 
				IFNULL((SELECT SUM(poir.quantity) FROM ".PRODUCT_OUTWARD_ITEM_RETURN." poir WHERE poir.product_id = p.ID), 0)
			) - (
				IFNULL((SELECT SUM(pir.quantity) FROM ".PRODUCT_INWARD_RETURN." pir WHERE pir.product_id = p.ID), 0) + 
				IFNULL((SELECT SUM(poi.quantity) FROM ".PRODUCT_OUTWARD_ITEM." poi WHERE poi.product_id = p.ID), 0)
			)) as in_stock
			
			*/	
			
				
				
					
		$crud->set_model('grocery_crud_custom_query_model');
						
		$crud->basic_model->set_custom_query("SELECT p.ID, p.code, p.Name as Name, p.status, p.editable, pt.Name as Type, pc.Name as category, 
			 
			(SELECT pi2.Rate FROM ".PRODUCT_INWARD." pi2 WHERE pi2.product_id=p.ID ORDER BY pi2.action_microtime DESC LIMIT 0,1 ) as Rate,
			
			((
				IFNULL(SUM(pi.quantity), 0) + 
				IFNULL(SUM(poir.quantity), 0)
			) - (
				IFNULL(SUM(pir.quantity), 0) + 
				IFNULL(SUM(poi.quantity), 0)
			)) as in_stock
			
			
			FROM ".PRODUCT." p
			LEFT JOIN ".PRODUCT_TYPE." pt ON pt.ID = p.type
			LEFT JOIN ".PRODUCT_CATEGORY." pc ON pc.ID = p.Category_Id 
			
			LEFT JOIN ".PRODUCT_INWARD." pi ON pi.product_id = p.ID
			LEFT JOIN ".PRODUCT_INWARD_RETURN." pir ON pir.product_id = p.ID

			LEFT JOIN ".PRODUCT_OUTWARD_ITEM." poi ON poi.product_id = p.ID
			LEFT JOIN ".PRODUCT_OUTWARD_ITEM_RETURN." poir ON poir.product_id = p.ID 
			WHERE p.ID != '' AND p.editable = 'Lock'
			", "GROUP BY p.ID ORDER BY p.added_date DESC");
				
		
		$crud->columns('Type', 'category', 'code', 'Name', 'Rate', 'in_stock', 'status', 'editable');
		$crud->field_without_sorter(array('Rate', 'in_stock'));
		$crud->unset_search(array('Rate', 'in_stock'));
		
		$output = $crud->render();
		
		$data = array('page_title'=> 'Products', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('scriptsrc', array(site_url('lock/assets/js/setup.js')));
		$this->template->set('document_title', 'Products');
		$this->template->layout($outputData);
	}
	
	//Ajax function when add and update nav_products

	
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
				$this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('status', 'Status', 'trim|required|min_length[2]|in_list[Active,Inactive]', array('in_list'=>'Invalid status field.'));
				$this->form_validation->set_rules('action_mode', 'Action Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));			break;
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
	
}