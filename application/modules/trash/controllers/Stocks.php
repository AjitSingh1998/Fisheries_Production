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
		redirect('trash/stocks/outward');
	}
	
	function __callbackDeleteOutwardButton($primary_key, $row){ 
		return site_url('bulk_action/action/delete_outward_product');
	}
	function __callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/delete');
	}
	function __callbackRestoreActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/active');
	}

	//Listing
	function outward(){	
		$this->actions = checkUserPermission('trash/stocks/outward', $this->uri->segment(4));
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
		
		if(in_array('delete', $actions)){
			$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-times', array($this, '__callbackDeleteOutwardButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/delete_outward_product'), ' text-danger','fa fa-times', '');
		}
		if(in_array('restore', $actions)){
			$crud->add_action('Restore', 'triggerBulkDelete', '', 'fa fa-undo', array($this, '__callbackRestoreActionButton'), 'dialogbox');
			$crud->add_bulk_action('Restore', site_url('bulk_action/action/active'), '','fa fa-undo', 'status');
		}
		
		$crud->set_subject('Outward Product');
		$crud->set_table(PRODUCT_OUTWARD);
		$crud->set_relation('fisherman_id', FISHERMAN, 'Name');
		$crud->set_relation('main_group_id', MAINGROUP, 'Name');
		$crud->where(array(PRODUCT_OUTWARD.'.status'=>'Deleted'));
		$crud->order_by('added_date', 'DESC');
		
		$crud->columns('outward_date', 'receipt_number', 'fisherman_id', 'main_group_id', 'grand_total','status', 'editable');
		$crud->display_as(array('receipt_number'=>'Receipt', 'outward_date'=>'Date', 'fisherman_id'=>'Fisherman', 'main_group_id'=>'Group/Party'));				
		
		$output = $crud->render();
		$data = array('page_title'=> 'Outward Product', 'content_view'=>'setup/setting');		  
		$outputData = array_merge((array)$output, $data);	
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css')
												 ));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('trash/assets/js/setup.js')
											));
		$this->template->set('document_title', 'Outward Products');
		$this->template->layout($outputData);	
	}
	
	function outward_return($id = NULL){
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
		$data['scriptsrc'] = array(site_url('trash/assets/js/outward.js'));
		$data['setup_form'] = $this->load->view('stocks/add_outward_return_v', $data, true);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	//Listing
	function inward(){
		$actions = checkUserPermission('trash/stocks/inward', $this->uri->segment(4));
		
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
		
		$crud->set_subject('Inward Product');
		$crud->set_table(PRODUCT_INWARD);
		$crud->order_by('inward_date', 'DESC');
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->set_relation('product_id', PRODUCT, 'Name');
		$crud->set_relation('product_type', PRODUCT_TYPE, 'Name');
		$crud->where(array(PRODUCT_INWARD.'.status'=>'Deleted'));
		
		$crud->columns('inward_date', 'receipt_number', 'product_type', 'product_id', 'vendor_name', 'rate', 'quantity', 'added_by','status','editable');
		$crud->display_as(array('receipt_number'=>'Receipt','product_id'=>'Product','vendor_name'=>'Vendor'));
		
		$output = $crud->render();
		$data = array('page_title'=> 'Inward Products', 'content_view'=>'setup/setting');
		$outputData = array_merge((array)$output, $data);
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												site_url('trash/assets/js/setup.js')
											));
				
		$this->template->set('document_title', 'Inward Products');
		$this->template->layout($outputData);
		
	}
	
	//Listing
	function returns(){
		$actions = checkUserPermission('trash/stocks/returns', $this->uri->segment(4));
		
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
		
		$crud->set_subject('Inward Return');
		$crud->set_table(PRODUCT_INWARD_RETURN);
		$crud->set_relation('added_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->set_relation('product_id', PRODUCT, 'Name');
		$crud->set_relation('product_type', PRODUCT_TYPE, 'Name');
		$crud->where(array(PRODUCT_INWARD_RETURN.'.status'=>'Deleted'));
		$crud->columns('return_date', 'receipt_number', 'product_type', 'product_id', 'vendor_name', 'rate', 'quantity', 'added_by', 'status', 'editable');
		$crud->display_as(array('receipt_number'=>'Receipt','product_id'=>'Product','vendor_name'=>'Vendor'));
		
		$output = $crud->render();
		$data = array('page_title'=> 'Inward Products', 'content_view'=>'setup/setting');
					  
		$outputData = array_merge((array)$output, $data);
				
		$this->template->set('stylesheet', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.css')));
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-ui/jquery-ui-v1.12.1.js'),
												site_url('trash/assets/js/setup.js')
											));
				
		$this->template->set('document_title', 'Inward Return');
		$this->template->layout($outputData);
		
	}
	
	
	//Ajax functions
	
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
					$options .= '<option value="'.$res['ID'].'">'.$res['Name'].'</option>';
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
		
		if($item_id && $fid && $outid){
			$result = $this->db->delete(PRODUCT_OUTWARD_ITEM, array('ID' => $item_id, 'outward_id' => $outid, 'fisherman_id' => $fid));
			if($result){
				$data = array('status'=>'success', 'msg'=>'Item removed.', 'data'=>'');
			}
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	private function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'add_outward':
				$this->form_validation->set_rules('mg_type', 'Group Type', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('maingroup', 'Main Group', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('fisherman', 'Fisherman', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('sub_total', 'Sub Total', 'trim|required|numeric');
				$this->form_validation->set_rules('cash_received', 'Cash Received', 'trim|numeric');
				$this->form_validation->set_rules('grand_total', 'Grand Total', 'trim|required|numeric');
				//$this->form_validation->set_rules('receipt_number', 'Receipt Number', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('product[type][]', 'Product Type', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[category][]', 'Product category', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[name][]', 'Product name', 'trim|required|min_length[1]|numeric');
				$this->form_validation->set_rules('product[qty][]', 'Product qty', 'trim|required|min_length[1]|numeric|greater_than[0]');
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
				$this->form_validation->set_rules('product[name][]', 'Product Name', 'trim|required|min_length[1]|numeric');
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
