<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Stocks extends MY_Controller {
	var $userID;
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('stocks_model', 'SM');
	}
	
	function index(){ 
		redirect('stocks/outward');
	}
	
	function callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/mark_delete');
	}
	
	function __callbackLockActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/lock');
	}
	
	//Listing
	function outward(){
		$order_by = $this->input->post('order_by');		
		if(!empty($order_by)){
			foreach($order_by as $key => $value){
				if($value == 'receipt_number'){
					$order_by[$key] = 'cast(receipt_number as unsigned)';
				}
			}
			$_POST['order_by'] = $order_by;
		}
			
		$this->actions = checkUserPermission('stocks/outward', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
						
		if(!in_array('export', $this->actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $this->actions)){
			$crud->unset_print();
		}
				
		if(!in_array('edit', $this->actions)){
			$crud->unset_edit();
		}else{			
			$crud->set_edit_url_path(site_url('stocks/add_outward'));
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		
		if(!in_array('add', $this->actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('stocks/add_outward'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		if(in_array('view', $this->actions)){
			$crud->set_read_url_path(site_url('stocks/view_outward'));
			$crud->set_read_button_class('loadActionForm');
		}else{
			$crud->unset_read();
		}
		
		//$crud->unset_read();
		$crud->unset_delete();
		if(in_array('delete', $this->actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(in_array('lock', $this->actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger','fa fa-lock', 'editable');
		}
		
		if(in_array('return', $this->actions)){
			$crud->add_action('Outward Return', 'loadActionForm', site_url('stocks/outward_return/'),'fa fa-plus','', 'popupmodal');
		}
		
		$crud->set_subject('Outward Product');
		
		$crud->set_table(PRODUCT_OUTWARD);
		$crud->set_relation('fisherman_id', FISHERMAN, 'Name');
		$crud->set_relation('main_group_id', MAINGROUP, 'Name');
		$crud->where(array(PRODUCT_OUTWARD.'.status !='=>'Deleted'));
		$crud->where(array(PRODUCT_OUTWARD.'.editable !='=>'Lock'));
		
		$crud->set_relation_n_n('products', PRODUCT_OUTWARD_ITEM, PRODUCT, 'outward_id', 'product_id', 'Name');
		
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('outward_date', 'receipt_number', 'fisherman_id', 'main_group_id', 'sub_total','cash_received','grand_total', 'products', 'remark');
		$crud->display_summary('sub_total','cash_received','grand_total');
		$crud->display_as(array('receipt_number'=>'Receipt', 'outward_date'=>'Date', 'fisherman_id'=>'Fisherman', 'main_group_id'=>'Group/Party'));				
				
		$output = $crud->render();
		$data = array('page_title'=> 'Outward Product', 'content_view'=>'setup/setting');		  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('stocks/assets/js/setup.js')
											));
		$this->template->set('document_title', 'Outward Products');
		$this->template->layout($outputData);	
	}
	
	function add_outward($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('outward_to'	=> '',
								'receipt_number'=> '',
								'group_type_id'	=> '',
								'maingroup_id'	=> '',
								'fisherman_id'	=> '',
								'sub_total'		=> '',
								'cash_received'	=> '',
								'grand_total'	=> '',
								'outward_date'	=> '',
								'remark'		=> '',
								'f_cn'			=> '',
								'status'		=> ''
								);
		$data['page_title'] = 'Add Outward Products';		
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('stocks/add_outward');
		
		$form_valid = $this->__setFormRules('add_outward');
		if($form_valid){
			$post = $this->input->post();
			
			$action_mode = $post['action_mode'];
			//Preparing outward data
			$prepData = array('outward_to'		=> $post['outward_to'],
							  'receipt_number'	=> $post['receipt_number'],
							  'group_type' 		=> $post['group_type_id'],
							  'main_group_id'	=> $post['maingroup_id'],
							  'fisherman_id'	=> $post['fisherman_id'],
							  'sub_total'		=> $post['sub_total'],
							  'cash_received'	=> $post['cash_received'],
							  'grand_total'		=> $post['grand_total'],
							  'outward_date'	=> get_date('Y-m-d', $post['outward_date']),
							  'remark'			=> $post['remark'],
							  'added_by'		=> $this->userID,
							  'added_date'		=> get_datetime('Y-m-d H:i:s'),
							  'action_microtime'=> microtime(true),
							  );
			
			if($id != NULL && $action_mode=='edit'){
				//do update
				unset($prepData['added_date'], $prepData['added_by']);
				$prepData['updated_date'] = get_datetime('Y-m-d H:i:s');
				$prepData['updated_by'] = $this->userID;
				$result = $this->db->where('ID', $id)->update(PRODUCT_OUTWARD, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('stocks/add_outward/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
					
					//Preparing outward items for insert
					$prod_items = array();
					if(isset($post['product']) && !empty($post['product'])){
						$products = $post['product'];
						for($i=0; $i<count($products['type']); $i++){
							
							$product = @explode('__', $products['name'][$i]);
							$product_id = isset($product) ? $product[0] : 0;
							
							$prod_items = array('outward_id' 		=> $id,
												'outward_to'		=> $post['outward_to'],
												'group_type' 		=> $post['group_type_id'],
												'main_group_id' 	=> $post['maingroup_id'],
												'fisherman_id' 		=> @$post['fisherman_id'],
												'product_type' 		=> $products['type'][$i],
												'product_category'	=> $products['category'][$i],
												'product_id' 		=> $product_id,
												'quantity' 			=> $products['qty'][$i],
												'rate' 				=> $products['rate'][$i],
												'returnable' 		=> $products['return'][$i],
												'total_price' 		=> $products['total'][$i],
												'outward_date'		=> get_date('Y-m-d', $post['outward_date']),
												'added_by'			=> $this->userID,
												'added_date'		=> get_datetime('Y-m-d H:i:s'),
												'action_microtime'	=> microtime(true),
												);
							$where = array('ID' => @$products['ID'][$i], 'outward_id' => $id);
							$result = $this->db->where($where)->count_all_results(PRODUCT_OUTWARD_ITEM);
							if($result){
								//Update existing item
								unset($prod_items['added_date'], $prod_items['added_by']);
								$prod_items['updated_date'] = get_datetime('Y-m-d H:i:s');
								$prod_items['updated_by'] = $this->userID;
								$result = $this->db->where($where)->update(PRODUCT_OUTWARD_ITEM, $prod_items);
							}else{
								//Insert new item
								$result = $this->db->insert(PRODUCT_OUTWARD_ITEM, $prod_items);
							}
						}
					}
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}elseif($action_mode=='add'){
				//do insert
				$result = $this->db->insert(PRODUCT_OUTWARD, $prepData);
				$outward_id = $this->db->insert_id();
				if($outward_id){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('stocks/add_outward/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data inserted successfully.';
					
					//Preparing outward items for insert
					$prod_items = array();
					if(isset($post['product']) && !empty($post['product'])){
						$products = $post['product'];
						for($i=0; $i<count($products['type']); $i++){
							$product = @explode('__', $products['name'][$i]);
							$product_id = isset($product) ? $product[0] : 0;
							$prod_items[] = array('outward_id' 		=> $outward_id,
												  'group_type' 		=> $post['group_type_id'],
												  'main_group_id' 	=> $post['maingroup_id'],
												  'fisherman_id' 	=> $post['fisherman_id'],
												  'product_type' 	=> $products['type'][$i],
												  'product_category'=> $products['category'][$i],
												  'product_id' 		=> $product_id,
												  'quantity' 		=> $products['qty'][$i],
												  'rate' 			=> $products['rate'][$i],
												  'returnable' 		=> $products['return'][$i],
												  'total_price' 	=> $products['total'][$i],
												  'outward_date'	=> get_date('Y-m-d', $post['outward_date']),
												  'added_by'		=> $this->userID,
												  'added_date'		=> get_datetime('Y-m-d H:i:s'),
												  'action_microtime'=> microtime(true),
												  );
						}
					}
					$this->db->insert_batch(PRODUCT_OUTWARD_ITEM, $prod_items);
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to insert. Please try again.';
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		$data['outward_items'] = array();
		
		if($id != NULL){
			$data['page_title'] = 'Edit Outward Products';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('stocks/add_outward/'.$id);
			$data['outward_items'] = $this->SM->get_outward_items($id);
			$outward_data = $this->SM->get_outward_data($id);
			if(!empty($outward_data)){
				$data['dbdata'] = $outward_data;
			}
		}
		
		$data['mg_type'] = $this->SM->get_mgtype_data(); // Get all maingroup_type data
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('stocks/assets/js/outward.js'));
		$data['setup_form'] = $this->load->view('stocks/add_outward_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	function view_outward($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('receipt_number'=> '',
								'group_type'	=> '',
								'main_group_id'	=> '',
								'fisherman_id'	=> '',
								'sub_total'		=> '',
								'cash_received'	=> '',
								'grand_total'	=> '',
								'outward_date'	=> '',
								'remark'		=> '',
								'f_cn'			=> '',
								'status'		=> ''
								);
		$data['page_title'] = 'Outward Products';		
		
		$data['outward_items'] = array();
		
		if($id != NULL){
			$data['outward_items'] = $this->SM->get_outward_items($id);
			$outward_data = $this->SM->get_outward_data($id);
			if(!empty($outward_data)){
				$data['dbdata'] = $outward_data;
			}
		}
		
		$data['mg_type'] = $this->SM->get_mgtype_data(); // Get all maingroup_type data
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('stocks/assets/js/outward.js'));
		$data['setup_form'] = $this->load->view('stocks/view_outward_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	// returned outward listing	
	function returned_outward_OLD15June2018(){
		$order_by = $this->input->post('order_by');		
		if(!empty($order_by)){
			foreach($order_by as $key => $value){
				if($value == 'receipt_number'){
					$order_by[$key] = 'cast(receipt_number as unsigned)';
				}
			}
			$_POST['order_by'] = $order_by;
		}
			
		$this->actions = checkUserPermission('stocks/returned_outward', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
						
		if(!in_array('export', $this->actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $this->actions)){
			$crud->unset_print();
		}
				
		if(!in_array('edit', $this->actions)){
			$crud->unset_edit();
		}
		
		if(!in_array('add', $this->actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_url_path(site_url('stocks/add_outward'));
			$crud->set_add_button_class('btn btn-primary loadActionForm');
		}
		
		if(in_array('view', $this->actions)){
			$crud->set_read_url_path(site_url('stocks/view_outward'));
			$crud->set_read_button_class('loadActionForm');
		}else{
			$crud->unset_read();
		}
		
		//$crud->unset_read();
		$crud->unset_delete();
		if(in_array('delete', $this->actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}
		
		if(in_array('lock', $this->actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger','fa fa-lock', 'editable');
		}
		
		$crud->set_subject('Returned Outward');
		
		$crud->set_table(PRODUCT_OUTWARD_ITEM_RETURN);
		$crud->set_relation('fisherman_id', FISHERMAN, 'Name');
		$crud->set_relation('main_group_id', MAINGROUP, 'Name');
		$crud->set_relation('product_id', PRODUCT, 'Name');
		$crud->set_relation('product_category', PRODUCT_CATEGORY, 'Name');
		$crud->set_relation('product_type', PRODUCT_TYPE, 'Name');
		
		$crud->where(array(PRODUCT_OUTWARD_ITEM_RETURN.'.status !='=>'Deleted'));
		$crud->where(array(PRODUCT_OUTWARD_ITEM_RETURN.'.editable !='=>'Lock'));
				
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('outward_to', 'fisherman_id', 'main_group_id', 'product_type', 'product_category', 'product_id', 'quantity', 'rate', 'total_price', 'return_date');
		$crud->display_as(array('fisherman_id'=>'Fisherman', 'main_group_id'=>'Group', 'product_type'=>'Type', 'product_category'=>'Category', 'product_id'=>'Product'));				
		
		$crud->fields('fisherman_id', 'main_group_id', 'product_id', 'quantity', 'rate', 'total_price', 'return_date');
		$crud->field_type('fisherman_id', 'readonly');
		$crud->field_type('main_group_id', 'readonly');
		$crud->field_type('product_id', 'readonly');
		$crud->field_type('return_date', 'readonly');
		
		
		$output = $crud->render();
		$data = array('page_title'=> 'Returned Outward', 'content_view'=>'setup/setting');		  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('stocks/assets/js/setup.js')
											));
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);	
	}
	
	function returned_outward(){
		$order_by = $this->input->post('order_by');		
		if(!empty($order_by)){
			foreach($order_by as $key => $value){
				if($value == 'receipt_number'){
					$order_by[$key] = 'cast(receipt_number as unsigned)';
				}
			}
			$_POST['order_by'] = $order_by;
		}
			
		$this->actions = checkUserPermission('stocks/returned_outward', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
		$crud->unset_read();
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();
		if(!in_array('export', $this->actions)){
			$crud->unset_export();
		}
		if(!in_array('print', $this->actions)){
			$crud->unset_print();
		}	
		
		$crud->set_subject('Returned Outward');
		
		$crud->set_table(PRODUCT_OUTWARD_ITEM_RETURN);
		$crud->set_relation('outward_id', PRODUCT_OUTWARD, '{receipt_number} - {return_remark}');
		$crud->set_relation('fisherman_id', FISHERMAN, 'Name');
		$crud->set_relation('main_group_id', MAINGROUP, 'Name');
		$crud->set_relation('product_id', PRODUCT, 'Name');
		$crud->set_relation('product_category', PRODUCT_CATEGORY, 'Name');
		$crud->set_relation('product_type', PRODUCT_TYPE, 'Name');
		
		$crud->where(array(PRODUCT_OUTWARD_ITEM_RETURN.'.status !='=>'Deleted'));
		$crud->where(array(PRODUCT_OUTWARD_ITEM_RETURN.'.editable !='=>'Lock'));
				
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('outward_to', 'fisherman_id', 'main_group_id', 'product_type', 'product_category', 'product_id', 'quantity', 'rate', 'total_price', 'return_date', 'outward_id');
		$crud->display_as(array('fisherman_id'=>'Fisherman', 'main_group_id'=>'Group', 'product_type'=>'Type', 'product_category'=>'Category', 'product_id'=>'Product', 'outward_id'=>'Receipt No - Remark'));	
		$crud->display_summary('quantity', 'rate', 'total_price');		
		$output = $crud->render();
		$data = array('page_title'=> 'Returned Outward', 'content_view'=>'setup/setting');		  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('stocks/assets/js/setup.js')
											));
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);	
	}
	
	// add return outtward through ajax
	function outward_return($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('receipt_number'=> '',
								'outward_to'	=> '',
								'group_type'	=> '',
								'main_group_id'	=> '',
								'fisherman_id'	=> '',
								'sub_total'		=> '',
								'cash_received'	=> '',
								'grand_total'	=> '',
								'outward_date'	=> '',
								'remark'		=> '',
								'f_cn'			=> '',
								'mg_name'		=> '',
								'mgt_name'		=> ''
								);
		$data['page_title'] = 'Outward Return';	
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('stocks/outward_return');
		
		$form_valid = $this->__setFormRules('outward_return');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			//Preparing outward data
			$prepData = array('return_remark' => $post['return_remark']);
			
			if($id != NULL && $action_mode=='edit'){
				//do update
				$result = $this->db->where('ID', $id)->update(PRODUCT_OUTWARD, $prepData);
				if($result){
					$data['action_mode'] = 'edit';
					$data['form_action'] = site_url('stocks/outward_return/'.$id);
					$data['status'] = 'success';
					$data['msg'] = 'Data updated successfully.';
					
					//Preparing outward items for insert
					$prod_items = array();
					if(isset($post['product']) && !empty($post['product'])){
						$products = $post['product'];
						for($i=0; $i<count($products['type']); $i++){
							$qty = $products['qty'][$i];
							$rate = $products['rate'][$i];
							if($qty > 0 && $rate > 0){
								$prod_items[] = array('outward_id' 		=> $id,
													  'outward_to' 		=> $post['outward_to'],
													  'item_id' 		=> $products['item_id'][$i],
													  'group_type' 		=> $post['mg_type'],
													  'main_group_id' 	=> $post['maingroup'],
													  'fisherman_id' 	=> $post['fisherman'],
													  'product_type' 	=> $products['type'][$i],
													  'product_category'=> $products['category'][$i],
													  'product_id' 		=> $products['name'][$i],
													  'quantity' 		=> $qty,
													  'rate' 			=> $rate,
													  'return_date' 	=> get_date('Y-m-d', $post['return_date']),
													  'total_price' 	=> $products['total'][$i],
													  'added_by'		=> $this->userID,
													  'added_date'		=> get_datetime('Y-m-d H:i:s'),
													  'action_microtime'=> microtime(true),
													  );
							}
						}
					}
					if(!empty($prod_items)){
						$this->db->insert_batch(PRODUCT_OUTWARD_ITEM_RETURN, $prod_items);
					}
				}else{
					$data['status'] = 'danger';
					$data['msg'] = 'Failed to update. Please try again.';
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		
		$data['outward_items'] = array();
		$data['return_items'] = array();
		if($id != NULL){
			$data['page_title'] = 'Outward Return';	
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('stocks/outward_return/'.$id);
			$data['outward_items'] = $this->SM->get_outward_items($id);
			$data['return_items'] = $this->SM->get_outward_return_items($id);
			$outward_data = $this->SM->get_outward_data($id);
			if(!empty($outward_data)){
				$data['dbdata'] = $outward_data;
			}
		}
		
		
		$data['mg_type'] = $this->SM->get_mgtype_data(); // Get all maingroup_type data
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('stocks/assets/js/outward.js'));
		$data['setup_form'] = $this->load->view('stocks/add_outward_return_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	//Listing
	function inward(){
		$order_by = $this->input->post('order_by');		
		if(!empty($order_by)){
			foreach($order_by as $key => $value){
				if($value == 'receipt_number'){
					$order_by[$key] = 'cast(receipt_number as unsigned)';
				}
			}
			$_POST['order_by'] = $order_by;
		}
		$actions = checkUserPermission('stocks/inward', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_add_url_path(site_url('stocks/add_inward'));
			$crud->set_edit_url_path(site_url('stocks/add_inward'));
		}
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_button_class('btn btn-primary loadActionForm');
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('stocks/view_inward'));
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
		
		$crud->unset_delete();
		if(in_array('delete', $actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}	
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger','fa fa-lock', 'editable');
		}
		
		$crud->set_subject('Inward Product');
		$crud->set_table(PRODUCT_INWARD);
		$crud->order_by('inward_date', 'DESC');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->set_relation('product_id', PRODUCT, 'Name');
		$crud->set_relation('product_type', PRODUCT_TYPE, 'Name');
		$crud->where(array(PRODUCT_INWARD.'.status !='=>'Deleted'));
		$crud->where(array(PRODUCT_INWARD.'.editable !='=>'Lock'));
		
		$crud->columns('inward_date', 'receipt_number', 'product_type', 'product_id', 'vendor_name', 'rate', 'quantity', 'added_by','editable');
		
		$crud->display_summary('quantity');
		
		$crud->display_as(array('receipt_number'=>'Receipt','product_id'=>'Product','vendor_name'=>'Vendor'));
				
		$output = $crud->render();
		$data = array('page_title'=> 'Inward Products', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												site_url('stocks/assets/js/setup.js')
											));
				
		$this->template->set('document_title', 'Inward Products');
		$this->template->layout($outputData);
		
	}
	
	function add_inward($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		
		$data['page_title'] = 'Add Inward Products';		
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('stocks/add_inward');
		
		$form_valid = $this->__setFormRules('add_inward');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			if($id != NULL && $action_mode=='edit'){
				//do update
				//Preparing outward items for insert
				$prod_items = array();
				if(isset($post['product']) && !empty($post['product'])){
					$products = $post['product'];
					for($i=0; $i<count($products['type']); $i++){
						
						$product = @explode('__', $products['name'][$i]);
						$product_id = isset($product) ? $product[0] : 0;
						
						$prod_items = array('receipt_number'	=> $post['receipt_number'],
											'product_type' 		=> $products['type'][$i],
											'product_category'	=> $products['category'][$i],
											'product_id' 		=> $product_id,
											'vendor_name' 		=> $post['vendor_name'],
											'inward_date'		=> get_date('Y-m-d', $post['inward_date']),
											'rate' 				=> $products['rate'][$i],
											'quantity' 			=> $products['qty'][$i],
											'total_price' 		=> $products['total'][$i],
											'remark'			=> $post['remark'],
											'updated_by'		=> $this->userID,
											'updated_date'		=> get_datetime('Y-m-d H:i:s'),
											'action_microtime'	=> microtime(true),
											);
						$result = $this->db->where(array('ID'=>$id))->update(PRODUCT_INWARD, $prod_items);
						if($result){
							$data['action_mode'] = 'edit';
							$data['form_action'] = site_url('stocks/add_inward/'.$id);
							$data['status'] = 'success';
							$data['msg'] = 'Data updated successfully.';
						}else{
							$data['status'] = 'danger';
							$data['msg'] = 'Failed to update. Please try again.';
						}
					}
				}
			}elseif($action_mode=='add'){
				//do insert
				//Preparing outward items for insert
				$prod_items = array();
				if(isset($post['product']) && !empty($post['product'])){
					$products = $post['product'];
					for($i=0; $i<count($products['type']); $i++){
						$product = @explode('__', $products['name'][$i]);
						$product_id = isset($product) ? $product[0] : 0;
						
						$prod_items[] = array('receipt_number'	=> $post['receipt_number'],
											  'product_type' 	=> $products['type'][$i],
											  'product_category'=> $products['category'][$i],
											  'product_id' 		=> $product_id,
											  'vendor_name' 	=> $post['vendor_name'],
											  'inward_date'		=> get_date('Y-m-d', $post['inward_date']),
											  'rate' 			=> $products['rate'][$i],
											  'quantity' 		=> $products['qty'][$i],
											  'total_price' 	=> $products['total'][$i],
											  'remark'			=> $post['remark'],
											  'added_by'		=> $this->userID,
											  'added_date'		=> get_datetime('Y-m-d H:i:s'),
											  'action_microtime'=> microtime(true),
											  );
					}
					$result = $this->db->insert_batch(PRODUCT_INWARD, $prod_items);
					$id = $this->db->insert_id();
					if($result){
						$data['action_mode'] = 'edit';
						$data['form_action'] = site_url('stocks/add_inward/'.$id);
						$data['status'] = 'success';
						$data['msg'] = 'Data inserted successfully.';
					}else{
						$data['status'] = 'danger';
						$data['msg'] = 'Failed to insert. Please try again.';
					}
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		$data['info'] = array();
		$data['inward_item'] = array();
		
		if($id != NULL){
			$data['page_title'] = 'Edit Inward Products';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('stocks/add_inward/'.$id);
			$inward = $this->SM->get_inward_data($id);
			$data['info'] = $inward['info']; 
			$data['inward_item'] = $inward['items'];
		}
		
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('stocks/assets/js/inward.js'));
		$data['setup_form'] = $this->load->view('stocks/add_inward_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	function view_inward($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		
		$data['page_title'] = 'Inward Products';
		
		$data['info'] = array();
		$data['inward_item'] = array();
		
		if($id != NULL){
			$data['page_title'] = 'Inward Products';
			$inward = $this->SM->get_inward_data($id);
			$data['info'] = $inward['info']; 
			$data['inward_item'] = $inward['items'];
		}
		
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('stocks/assets/js/inward.js'));
		$data['setup_form'] = $this->load->view('stocks/view_inward_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	//Listing
	function returns(){
		$order_by = $this->input->post('order_by');		
		if(!empty($order_by)){
			foreach($order_by as $key => $value){
				if($value == 'receipt_number'){
					$order_by[$key] = 'cast(receipt_number as unsigned)';
				}
			}
			$_POST['order_by'] = $order_by;
		}
		
		$actions = checkUserPermission('stocks/returns', $this->uri->segment(3));
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();
					
		if(!in_array('edit', $actions)){
			$crud->unset_edit();
		}else{
			$crud->set_add_url_path(site_url('stocks/add_returns'));
			$crud->set_edit_url_path(site_url('stocks/add_returns'));
		}
		
		if(!in_array('add', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_button_class('btn btn-primary loadActionForm');
			$crud->set_edit_button_class('btn btn-default loadActionForm');
		}
		if(in_array('view', $actions)){
			$crud->set_read_url_path(site_url('stocks/view_returns'));
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
		$crud->unset_delete();
		if(in_array('delete', $actions)){			
			$crud->add_action('Delete', 'triggerBulkDelete text-danger',site_url('bulk_action/action/mark_delete'),'fa fa-trash',array($this,'callbackDeleteActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}		
		
		if(in_array('lock', $actions)){
			$crud->add_action('Lock Data', 'triggerBulkDelete text-danger',site_url('bulk_action/action/lock'),'fa fa-lock',array($this,'__callbackLockActionButton'), 'dialogbox');			
			$crud->add_bulk_action('Lock Data', site_url('bulk_action/action/lock'), ' text-danger','fa fa-lock', 'editable');
		}
				
		
		$crud->set_subject('Inward Return');
		$crud->set_table(PRODUCT_INWARD_RETURN);
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->set_relation('product_id', PRODUCT, 'Name');
		$crud->set_relation('product_type', PRODUCT_TYPE, 'Name');
		$crud->where(array(PRODUCT_INWARD_RETURN.'.status !='=>'Deleted'));
		$crud->where(array(PRODUCT_INWARD_RETURN.'.editable !='=>'Lock'));
		
		$crud->columns('return_date', 'receipt_number', 'product_type', 'product_id', 'vendor_name', 'rate', 'quantity', 'added_by', 'editable');
		$crud->display_as(array('receipt_number'=>'Receipt','product_id'=>'Product','vendor_name'=>'Vendor'));
		
		$crud->display_summary('quantity');
		
		$output = $crud->render();
		$data = array('page_title'=> 'Inward Products', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);
				
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												site_url('stocks/assets/js/setup.js')
											));
				
		$this->template->set('document_title', 'Inward Return');
		$this->template->layout($outputData);
		
	}
	
	function add_returns($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Add Inward Return';	
		$data['action_mode'] = 'add';
		$data['form_action'] = site_url('stocks/add_returns');
		
		$form_valid = $this->__setFormRules('add_returns');
		if($form_valid){
			$post = $this->input->post();
			$action_mode = $post['action_mode'];
			if($id != NULL && $action_mode=='edit'){
				//do update
				//Preparing outward items for insert
				$prod_items = array();
				if(isset($post['product']) && !empty($post['product'])){
					$products = $post['product'];
					for($i=0; $i<count($products['type']); $i++){
						$prod_items = array(  'receipt_number'	=> $post['receipt_number'],
											  'product_type' 	=> $products['type'][$i],
											  'product_category'=> $products['category'][$i],
											  'product_id' 		=> $products['name'][$i],
											  'vendor_name' 	=> $post['vendor_name'],
											  'return_date'		=> get_date('Y-m-d', $post['return_date']),
											  'rate' 			=> $products['rate'][$i],
											  'quantity' 		=> $products['qty'][$i],
											  'total_price' 	=> $products['total'][$i],
											  'remark'			=> $post['remark'],
											  'updated_by'		=>$this->userID,
											  'updated_date'	=>get_datetime('Y-m-d H:i:s'),
											  'action_microtime'=>microtime(true),
											  );
						$result = $this->db->where(array('ID'=>$id))->update(PRODUCT_INWARD_RETURN, $prod_items);
						if($result){
							$data['action_mode'] = 'edit';
							$data['form_action'] = site_url('stocks/add_returns/'.$id);
							$data['status'] = 'success';
							$data['msg'] = 'Data updated successfully.';
						}else{
							$data['status'] = 'danger';
							$data['msg'] = 'Failed to update. Please try again.';
						}
					}
				}
					
			}elseif($action_mode=='add'){
				//do insert
				
				//Preparing outward items for insert
				$prod_items = array();
				if(isset($post['product']) && !empty($post['product'])){
					$products = $post['product'];
					for($i=0; $i<count($products['type']); $i++){
						$prod_items[] = array('receipt_number'	=> $post['receipt_number'],
											  'product_type' 	=> $products['type'][$i],
											  'product_category'=> $products['category'][$i],
											  'product_id' 		=> $products['name'][$i],
											  'vendor_name' 	=> $post['vendor_name'],
											  'return_date'		=> get_date('Y-m-d', $post['return_date']),
											  'rate' 			=> $products['rate'][$i],
											  'quantity' 		=> $products['qty'][$i],
											  'total_price' 	=> $products['total'][$i],
											  'remark'			=> $post['remark'],
											  'added_by'		=>$this->userID,
											  'added_date'		=>get_datetime('Y-m-d H:i:s'),
											  'action_microtime'=>microtime(true),
											  );
					}
					$result = $this->db->insert_batch(PRODUCT_INWARD_RETURN, $prod_items);
					$id = $this->db->insert_id();
					if($result){
						$data['action_mode'] = 'edit';
						$data['form_action'] = site_url('stocks/add_returns/'.$id);
						$data['status'] = 'success';
						$data['msg'] = 'Data inserted successfully.';
					}else{
						$data['status'] = 'danger';
						$data['msg'] = 'Failed to insert. Please try again.';
					}
				}
			}
		}else{
			$data['status'] = 'error';
			$data['msg'] = validation_errors();
		}
		
		$data['info'] = array();
		$data['inward_item'] = array();
		
		if($id != NULL){
			$data['page_title'] = 'Edit Inward Return';
			$data['action_mode'] = 'edit';
			$data['form_action'] = site_url('stocks/add_returns/'.$id);
			$inward = $this->SM->get_inward_return_data($id);
			$data['info'] = $inward['info']; 
			$data['inward_item'] = $inward['items'];
		}
		
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('stocks/assets/js/inward.js'));
		$data['setup_form'] = $this->load->view('stocks/add_return_inward_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}	
	
	function view_returns($id = NULL){
		$data = array('status' => 'danger', 'msg' => 'Invalid Request.');
		$data['dbdata'] = array('Name'=>'', 'Remark'=>'', 'status'=>'');
		$data['page_title'] = 'Inward Return';	
		
		$data['info'] = array();
		$data['inward_item'] = array();
		
		if($id != NULL){
			$inward = $this->SM->get_inward_return_data($id);
			$data['info'] = $inward['info']; 
			$data['inward_item'] = $inward['items'];
		}
		
		$data['pr_type'] = $this->SM->get_prtype_data(); // Get all product type data
		$data['scriptsrc'] = array(site_url('stocks/assets/js/inward.js'));
		$data['setup_form'] = $this->load->view('stocks/view_return_inward_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Select fisherman (like dailytoll) ajax request
	function fisherman(){
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
		$data = array('status'=>'danger', 'msg'=>'Maingroup data not found', 'data'=>'<option value="">No samiti found</option>');
		$mg_type = $this->input->post('mg_type');
		if(!empty($mg_type)){
			$result = $this->SM->get_mg_data($mg_type);
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
	
	//Get product category according to product type
	function ajax_pr_cat(){
		$data = array('status'=>'danger', 'msg'=>'Product category data not found', 'data'=>'<option value="">No category found</option>');
		$ptype = $this->input->post('ptype');
		if(!empty($ptype)){
			$result = $this->SM->get_pr_cat($ptype);
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
	
	//Get product items according to product category
	function ajax_pr_items(){
		$data = array('status'=>'danger', 'msg'=>'Product data not found', 'data'=>'<option value="">No products found</option>');
		$pcat = $this->input->post('pcat');
		if(!empty($pcat)){
			$result = $this->SM->get_pr_items($pcat);
			$options = '<option value="">Product</option>';
			if(!empty($result)){
				foreach($result as $res){
					$options .= '<option value="'.$res['ID'].'__'.$res['Rate'].'">'.$res['Name'].'</option>';
				}
				$data = array('status'=>'success', 'msg'=>'Product data.', 'data'=>$options);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Remove item from outward
	function ajax_remove_item(){
		$data = array('status'=>'danger', 'msg'=>'Item not removed.', 'data'=>'');
		$item_id = $this->input->post('item_id');
		$fid = $this->input->post('fid');
		$outid = $this->input->post('outid');
		
		if($item_id && $outid){
			$result = $this->db->delete(PRODUCT_OUTWARD_ITEM, array('ID' => $item_id, 'outward_id' => $outid));
			if($result){
				$data = array('status'=>'success', 'msg'=>'Item removed.', 'data'=>'');
			}
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function check_receipt_number(){
		$data = FALSE;
		$action_mode = $this->input->post('action_mode');		
		$receipt_number = $this->input->post('receipt_number');
		if($action_mode == 'edit'){
			$ID = $this->uri->segment('3');
			$this->db->where('ID !=', $ID);
		}
		$this->db->where('receipt_number', $receipt_number);
		$result = $this->db->count_all_results(PRODUCT_OUTWARD);
		$this->db->last_query();
		if($result == 0){
			$data = TRUE;
		}
		return $data;
	}
	
	private function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'add_outward':
				$this->form_validation->set_rules('outward_to', 'Outward To', 'trim|required|in_list[Fisherman,Group]');
				$this->form_validation->set_rules('group_type_id', 'Group Type', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('maingroup_id', 'Main Group', 'trim|required|min_length[1]|numeric');
				if($this->input->post('outward_to') == 'Fisherman'){
					$this->form_validation->set_rules('fisherman_id', 'Fisherman', 'trim|required|min_length[1]|numeric');
				}
				
				$this->form_validation->set_rules('receipt_number', 'Receipt number', 'trim|required|min_length[1]|callback_check_receipt_number', array('check_receipt_number' => 'Receipt number is already used, Please enter different number.'));

				$this->form_validation->set_rules('sub_total', 'Sub Total', 'trim|required|numeric');
				$this->form_validation->set_rules('cash_received', 'Cash Received', 'trim|numeric');
				$this->form_validation->set_rules('grand_total', 'Grand Total', 'trim|required|numeric');
				$this->form_validation->set_rules('product[type][]', 'Product Type', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[category][]', 'Product category', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[name][]', 'Product name', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('product[qty][]', 'Product qty', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[rate][]', 'Product rate', 'trim|required|min_length[1]|numeric|greater_than[0]');
				$this->form_validation->set_rules('product[return][]', 'Product return', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('product[total][]', 'Product total', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('action_mode', 'Action Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));
			break;
			case 'add_inward':
				//$this->form_validation->set_rules('receipt_number', 'Receipt Number', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('inward_date', 'Inward Date', 'trim|required');
				$this->form_validation->set_rules('vendor_name', 'Vendor Name', 'trim|required|min_length[2]');
				$this->form_validation->set_rules('product[type][]', 'Product Type', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[category][]', 'Product Category', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[name][]', 'Product Name', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('product[qty][]', 'Product Quantity', 'trim|required|min_length[1]|numeric|greater_than[0]');
				$this->form_validation->set_rules('product[rate][]', 'Product Rate', 'trim|required|min_length[1]|numeric|greater_than[0]');
				$this->form_validation->set_rules('product[total][]', 'Product Total', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('action_mode', 'Action Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));
			break;
			case 'add_returns':
				//$this->form_validation->set_rules('receipt_number', 'Receipt Number', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('return_date', 'Return Date', 'trim|required');
				$this->form_validation->set_rules('vendor_name', 'Vendor Name', 'trim|required|min_length[2]');
				$this->form_validation->set_rules('product[type][]', 'Product Type', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[category][]', 'Product Category', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[name][]', 'Product Name', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[qty][]', 'Product Quantity', 'trim|required|min_length[1]|numeric|greater_than[0]');
				$this->form_validation->set_rules('product[rate][]', 'Product Rate', 'trim|required|min_length[1]|numeric|greater_than[0]');
				$this->form_validation->set_rules('product[total][]', 'Product Total', 'trim|required|min_length[1]|numeric');
			break;
			case 'outward_return':
				$this->form_validation->set_rules('return_date', 'Return Date', 'trim|required');
				$this->form_validation->set_rules('action_mode', 'Action Mode', 'trim|required|in_list[add,edit]', array('in_list'=>'Invalid Request. Please reload the page and try again.'));
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
	
}
