<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Yearly_backup extends MY_Controller {
	var $userID;
	function __construct()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		parent::__construct();
		$this->userID = checkUserLogin();	
		$this->load->dbutil();
		$this->load->dbforge();
		$this->load->helper('file');
		$this->load->helper('download');
		$this->load->helper('database');
		$this->load->library('zip');
		$this->load->model('database_model', 'DM');
	}
	
	function add_newdb(){
		
		$formValidation = $this->__setFormRules('add_newdb');
		if($formValidation){
			$db_id = $this->DM->create_db($this->userID);
			if($db_id){
				$this->messageci->set('New DB created successfully!','success');
				redirect(site_url('database/yearly_backup/export/'.$db_id));
			}else{
				$this->messageci->set('DB creation failed, Please try again!','error');
			}
		}
		
		
		
		$actions = checkUserPermission('database/backup', '');
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();					
		$crud->unset_delete();
		$crud->unset_edit();
		$crud->unset_read();
		
		if(!in_array('export', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_button_text('Backup Database');
			$crud->set_add_button_class('btn btn-primary');
			$crud->set_add_url_path(site_url('database/export'));
		}
		
		if(in_array('delete', $actions)){
			//$crud->add_action('Delete', 'triggerBulkDelete text-danger', '', 'fa fa-trash', array($this, '__callbackCustomActionButton'), 'dialogbox');
			$crud->add_bulk_action('Delete', site_url('bulk_action/action/mark_delete'), ' text-danger', 'fa fa-trash', 'status');		
		}		
		
		if(in_array('import', $actions)){
			//$crud->add_action('Import', 'text-danger', site_url('database/import/'), 'fa fa-upload', '', 'dialogbox');
		}
		
		if(in_array('download', $actions)){
			$crud->add_action('Download', '', site_url('database/download/db/'), 'fa fa-download', '', '');
		}
					
		$crud->set_subject('Database Backup');
		$crud->set_table(DATABASE_BACKUP);
		$crud->set_relation('backup_by', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->where('backup_type', 'yearly');
		
		$crud->columns('backup_dbname', 'backup_path', 'backup_date', 'backup_by');	
		
		$output = $crud->render();
		
		$data['page_title'] = 'Database backup';
		$data['content_view'] = 'database/backup_v';	
		$data['go_back_url'] = site_url('database/setting');
		$data['page_title'] = 'Yearly Backup';
		$data['content_view'] = 'database/add_newdb_v';
		
		$outputData = array_merge((array)$output, $data);
						
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
		
	}
	
	function export($db_id = NULL){

		if($db_id !== NULL){
			$result = $this->db->get_where(DATABASE_BACKUP, array('backup_id'=>$db_id))->result_array();
			if(!empty($result)){
				$dbname = $result[0]['backup_dbname'];
				$backup_path = FCPATH . 'DB_BACKUP/'.$dbname.'/';
				if(!file_exists($backup_path) && !is_dir($backup_path)){
					$result = mkdir($backup_path, 0755, true);
					if(!$result){
						$this->messageci->set('Failed to create backup directory, please try again!', 'error');
					}else{
						$result = $this->db->where('backup_id', $db_id)->update(DATABASE_BACKUP, array('backup_path' => $backup_path));
						$this->messageci->set('Yearly Backup directory created!', 'success');					
					}
				}	
			}else{
				$this->messageci->set('Invalid Request: There is not found any backup process', 'error');
				redirect(site_url('database/yearly_backup/add_newdb'));
			}
		}else{
			$this->messageci->set('Invalid Request: backup id is missing', 'error');
			redirect(site_url('database/yearly_backup/add_newdb'));
		}
		
		$data['save_backup_url'] = 'database/yearly_backup/save_backup';
		$data['export_button'] = ''; 
		$data['backup_id'] = $db_id;
		$data['db_tables'] = $this->DM->getAllTables();
				
		$data['page_title'] = 'Backup Database';
		$data['content_view'] = 'database/yearly_export_v';
		
		$this->template->set('scriptsrc', array(site_url('database/assets/js/backup.js')));
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function save_backup(){
		$backup_id = $this->input->post('backup_id');
		$table_name = $this->input->post('table_name');
		$response = array('status'=>'error', 'message'=>'Invalid request: Please click on <strong>Start Backup</strong> button to backup database!');
		
		if(!empty($backup_id) && !empty($table_name)){	
			// check backup id process existance and fetch backup path
			$result = $this->db->where('backup_id', $backup_id)->get(DATABASE_BACKUP)->result_array();
			if(!empty($result)){
				$backup_path = $result['0']['backup_path'];
				// check backup path existance
				if(file_exists($backup_path) && is_dir($backup_path)){
					//check table existance 
					$allTables = $this->DM->getAllTables();
					if(in_array($table_name, $allTables)){
						// check already backuped file for this table name
						$result = $this->db->get_where(DATABASE_BACKUP_TABLES, array('backup_id' => $backup_id, 'table_name' => $table_name))->result_array();
						if(!empty($result)){
							$tbl = $result[0];
							$download_url = site_url('databse/download/table/'.$tbl['table_id']);
							$response = array('status'		=>'success', 
											  'message'		=>'System taken backup of this table "'.$tbl['table_name'].'"', 
											  'table_name' 	=> $tbl['table_name'],
											  'status_icon' => '<span class="text-success"><i class="fa fa-check"></i></span>',
											  'action_button' => '<a href="'.$download_url.'" class="btn btn-sm btn-warning">Download</a>');
												  
						}else{
							$prefs = array(
										'tables'        => array($table_name),   		// Array of tables to backup.
										'format'        => 'zip',                       // gzip, zip, txt
										'filename'      => $table_name.'_backup.sql',   // File name - NEEDED ONLY WITH ZIP FILES
										'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
										'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
										'newline'       => "\n"                         // Newline character used in backup file
							);
							$backup_filename = $table_name.'.zip';
							$result = $this->DM->export_database($prefs, $backup_filename, $backup_path);
							if($result){
									
								$dataSet = array('backup_id'	=>$backup_id, 
												 'table_name' 	=> $table_name, 
												 'file_path' 	=> $backup_path.'/'.$backup_filename,
												 'backup_date' 	=> get_date('Y-m-d H-i-s'), 
												 'backup_by' 	=> $this->userID);
												 
								$result = $this->db->insert(DATABASE_BACKUP_TABLES, $dataSet);
								$table_id = $this->db->insert_id();
								$download_url = site_url('database/download/table/'.$table_id);
								$response = array('status'		=>'success', 
												  'message'		=>'System taken backup of this table "'.$table_name.'"', 
												  'table_name' 	=> $table_name,
												  'status_icon' => '<span class="text-success"><i class="fa fa-check"></i></span>',
												  'action_button' => '<a href="'.$download_url.'" class="btn btn-sm btn-warning">Download</a>');	
							}
						}
					}else{
						$response = array('status'=>'error', 'message'=>'Invalid Request: There is not found "'.$table_name.'" table, please try again');
					}
				}else{
					$response = array('status'=>'error', 'message'=>'Invalid Request: There is not found backup store path "'.$backup_path.'" , please try again');
				}
			}else{
				$response = array('status'=>'error', 'message'=>'Invalid Request: There is not found any backup process');
			}
			
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}
	
	function import($backup_id = NULL){
		
		$backup = '';
		if($backup_id == NULL){
			$this->messageci->set('Backup DB ID missing Please refresh the page!', 'error');
		}else{
			$backup = $this->DM->getBackupDBInfo($backup_id);			
		}
		
		$data['db_tables'] = $backup;
		$data['default_db'] = $this->db->database;		
		$data['backup_id'] = $backup_id;
		
		$data['page_title'] = 'Import Database';
		$data['content_view'] = 'database/yearly_import_v';		
		$this->template->set('scriptsrc', array(site_url('database/assets/js/import.js')));
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function import_data(){
		$checkValidation = $this->__setFormRules('import_data');
		$response = array('status' => 'error', 'message' => 'Invalid Request: Required data not found!');
		if($checkValidation){
			$backup_id = $this->input->post('backup_id'); 
			$table_name =$this->input->post('table_name');
			/*$tmp_import_path = $this->session->userdata('tmp_import_path');
			if(!isset($tmp_import_path) && empty($tmp_import_path)){
				$current_time_dir = time();
				$tmp_import_path = APPPATH.'tmp/'.$current_time_dir.'/';
				$this->session->set_userdata(array('tmp_import_path' => $tmp_import_path));
			}*/
			
			//check table backup existance
			$backup = $this->DM->getBackupDBInfo($backup_id, $table_name);
			if(!empty($backup)){
				//check backup file existance
				$backup_dbname = $backup['backup_dbname'];
				$default_db = $this->db->database;
				$sql = "INSERT INTO ".$backup_dbname.".".$table_name." SELECT * FROM ".$default_db.".".$table_name;
				$query_result = $this->db->query($sql);
				if($query_result){
					$return_response = array('status'	    => 'success',
											'message'	    => 'Data imported successfully !', 
											'process'	    => '', 
											'table_name'    => $table_name,
											'status_icon'   => '<span class="text-success"><i class="fa fa-check"></i></span>',
											'action_button' => '<button type="button" disabled="disabled" class="btn btn-sm btn-warning">Done</button>');
				}else{
					$return_response = array('status'	    => 'error',
											'message'	    => 'Data importion failed!', 
											);
				}
				/*if(file_exists($backup['file_path']) && is_file($backup['file_path'])){
					$import_result = array();
					$import_file = $tmp_import_path.$table_name.'.sql';
					//check import file existance 
					if($import_file != '' && file_exists($import_file) && is_file($import_file)){
						$response = $this->DM->importSQLFileData($import_file, $backup['backup_dbname'], $tmp_import_path);	
						$response['table_name']	= $table_name;		
					}else{						
						if(mkpath($tmp_import_path)){
							$import_file = extract_zip($backup['file_path'], $tmp_import_path);
							if($import_file !='' && is_file($import_file) && file_exists($import_file)){
								$response = $this->DM->importSQLFileData($import_file, $backup['backup_dbname'], $tmp_import_path);
								$response['table_name']	= $table_name;
							}else{
								$response = array('status' => 'error', 'message' => $import_file . ' This import file not exists!');
							}
						}else{
							$response = array('status' => 'error', 'message' => 'Destination directory creation failed!');
						}
					}
					
				}else{
					$response = array('status' => 'error', 'message' => $backup['file_path'] . ' Backup file not exists');
				}*/
				
				
			}else{
				$response = array('status' => 'error', 'message' => 'Requested Table backup not found to import!');
			}
		}else{
			$message = validation_errors();
			$response = array('status' => 'error', 'message' => 'Validation error: <br>'.$message);
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}	
		
	function fisherman_balance(){
		
		$group_type = $this->db->get_where(MAINGROUP, array('status' => 'Active'))->result_array();
		$group_id = '';
		if(isset($group_type) && !empty($group_type)){
			$group_id = $group_type[0]['ID'];
		}		
		$group_id = $this->input->get('group_type') ? $this->input->get('group_type') : $group_id;
		
		$data['group_id'] = $group_id;
		$data['group_type'] = $group_type;
		$data['closing_balance'] = $f_balance = $this->DM->fisherman_closing_balance(NULL, $group_id);
		
		
		
		
		/*
		include APPPATH . 'config/database.php';
		$default_db = $this->load->database($active_group, TRUE);
		
		$products = $wages = 0;
		$html = '<table>';
		foreach($f_balance as $balance){
			
			$product_liab = ($balance['opening_product_balance'] + $balance['outward_liability']);
			$deposit_prod_liab = ($balance['outward_returned_amt'] + $balance['outward_liability_deducted'] + $balance['cash_product_liability']);
			$products_balance = $product_liab - $deposit_prod_liab;
			
			$wages_liability = ($balance['opening_wages_balance'] + $balance['advance_wages']);
			$deposit_wages_liab = ($balance['advance_wages_deduction'] + $balance['cash_wages_liability']);
			$wages_balance =  $wages_liability - $deposit_wages_liab;
			
			$dataSet = array('products_balance'=>$products_balance, 'wages_balance' => $wages_balance);
			$result = $default_db->where(array('Code'=>$balance['Code'], 'ID' =>$balance['ID']))->update(FISHERMAN, $dataSet);		
			
			$html .= '<tr id="fisherman_'.$balance['ID'].'" class="not-updated">
					<td style="width:55px;">'.$balance['Code'].'</th>
					<td style="width:170px;">'.$balance['Name'].'</td>
					<td style="width:95px;">'.$balance['opening_product_balance'].'</td>
					<td style="width:95px;">'.$balance['opening_wages_balance'].'</td>
					<td style="width:95px;">'.$balance['outward_liability'].'</td>
					<td style="width:95px;">'.$balance['advance_wages'].'</td>
					<td style="width:95px;">'.$balance['outward_returned_amt'].'</td>
					<td style="width:95px;">'.$balance['outward_liability_deducted'].'</td>
					<td style="width:95px;">'.$balance['advance_wages_deduction'].'</td>
					<td style="width:95px;">'.$balance['cash_product_liability'].'</td>
					<td style="width:95px;">'.$balance['cash_wages_liability'].'</td>
					<td style="width:95px;">'.number_format($products_balance).'</td>
					<td style="width:95px;">'.number_format($wages_balance,2,'.','').'</td>
				 </tr>';	
			
			$products = $products + $products_balance;
			$wages = $wages + $wages_balance;
		}		
		echo $html .= '</table>';		
		die; */
		
		
		
		
		
		
		$data['page_title'] = 'Fisherman Closing Balance';
		$data['content_view'] = 'database/fisherman_closing_balance_v';	
		
		$css = array(base_url('assets/plugins/DataTables/media/css/jquery.dataTables.min.css'));
		
		$js = array(base_url('assets/plugins/DataTables/media/js/jquery.dataTables.min.js'),
					site_url('database/assets/js/jquery.table2excel.min.js'),
					site_url('database/assets/js/jQuery.print.min.js'),
					site_url('database/assets/js/closing_balance.js'),
					site_url('database/assets/js/extra.js'));


		$this->template->set('stylesheet', $css);
		$this->template->set('scriptsrc', $js);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function samiti_balance(){
		$group_type = $this->db->get_where(MAINGROUP_TYPE, array('status' => 'Active'))->result_array();
		$group_id = '';
		if(isset($group_type) && !empty($group_type)){
			$group_id = $group_type[0]['ID'];
		}
		
		$group_id = $this->input->get('group_type') ? $this->input->get('group_type') : $group_id;
		
		$data['group_id'] = $group_id;
		$data['group_type'] = $group_type;
		$data['closing_balance'] = $this->DM->samiti_closing_balance(NULL, $group_id);
		
		$data['page_title'] = 'Samiti Closing Balance';
		$data['content_view'] = 'database/samiti_closing_balance_v';	
		
		$css = array(base_url('assets/plugins/DataTables/media/css/jquery.dataTables.min.css'));
		
		$js = array(base_url('assets/plugins/DataTables/media/js/jquery.dataTables.min.js'),
					site_url('database/assets/js/jquery.table2excel.min.js'),
					site_url('database/assets/js/jQuery.print.min.js'),
					site_url('database/assets/js/closing_balance.js'),
					site_url('database/assets/js/extra.js'));


		$this->template->set('stylesheet', $css);
		$this->template->set('scriptsrc', $js);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function update_fisherman_balance($fid = NULL){
		$response = array('status'=>'error', 'message'=>'Invalid request: Select Fisherman not found!');
		if($fid !== NULL){
			
			//check fisherman
			$check = $this->db->where('ID', $fid)->count_all_results(FISHERMAN);
			if($check){
				$balance = $this->DM->fisherman_closing_balance($fid);
				
				$product_liab = ($balance['opening_product_balance'] + $balance['outward_liability']);
				$deposit_prod_liab = ($balance['outward_returned_amt'] + $balance['outward_liability_deducted'] + $balance['cash_product_liability']);
				$products_balance = $product_liab - $deposit_prod_liab;
				
				$wages_liability = ($balance['opening_wages_balance'] + $balance['advance_wages']);
				$deposit_wages_liab = ($balance['advance_wages_deduction'] + $balance['cash_wages_liability']);
				$wages_balance =  $wages_liability - $deposit_wages_liab;
				

				include APPPATH . 'config/database.php';
				
				$default_db = $this->load->database($active_group, TRUE);
				
				$dataSet = array('products_balance'=>$products_balance, 'wages_balance' => $wages_balance);
				$result = $default_db->where('ID', $fid)->update(FISHERMAN, $dataSet);
				if($result){
					$response = array('status'=>'success', 'message'=>'Closing/Opening balance updated successfully');
				}
			}else{
				$response = array('status'=>'error', 'message'=>'Select Fisherman not found!');
			}
		}		
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}
	
	function update_samiti_balance($samiti_id = NULL){
		
		$response = array('status'=>'error', 'message'=>'Invalid request: Select Samiti not found!');
		if($samiti_id !== NULL){
			//check fisherman
			$check = $this->db->where('ID', $samiti_id)->count_all_results(MAINGROUP);
			if($check){
				$balance = $this->DM->samiti_closing_balance($samiti_id);
							
				$product_liab = ($balance['opening_product_balance'] + $balance['outward_liability']);
				$deposit_prod_liab = ($balance['outward_returned_amt'] + $balance['GroupLiabilityDeduction'] + $balance['cash_product_liability']);
				$product_balance = $product_liab - $deposit_prod_liab;
				
				$wages_liability = ($balance['opening_wages_balance'] + $balance['advance_wages']);
				$deposit_wages_liab = ($balance['AdvanceWagesDeduction'] + $balance['cash_wages_liability']);
				$wages_balance =  $wages_liability - $deposit_wages_liab;
				
				include APPPATH . 'config/database.php';
				$default_db = $this->load->database($active_group, TRUE);				
				$dataSet = array('product_balance'=>$product_balance, 'wages_balance' => $wages_balance);
				$result = $default_db->where('ID', $samiti_id)->update(MAINGROUP, $dataSet);
				if($result){
					$response = array('status'=>'success', 'message'=>'Closing/Opening balance updated successfully');
				}
			}else{
				$response = array('status'=>'error', 'message'=>'Select Samiti not found!');
			}
		}		
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	
	}
	
	function assign_database(){
		
		$validateForm = $this->__setFormRules('assign_database');
		if($validateForm){
			$db_name = $this->input->post('db_name');
			$admin_name = $this->input->post('admin_name');
			$prepare_data = array('db_id' => $db_name, 'admin_id'=>$admin_name, 'assign_date'=> get_datetime('Y-m-d H:i:s'),'assign_by' => $this->userID);
			$checkAdmin = $this->db->where(array('admin_id'=>$admin_name, 'db_id'=>$db_name))->count_all_results(DATABASE_SETTING);
			if(!$checkAdmin){
				$this->db->insert(DATABASE_SETTING, $prepare_data);
			}
			redirect(site_url('database/setting'));
		}
		
		$data['admin_list'] =$this->db->get_where(ADMINISTRATOR, array('status'=> 'Active'))->result_array();
		$data['db_list'] =$this->db->get_where(DATABASE_BACKUP, array('backup_type' => 'Yearly'))->result_array();
		$data['page_title'] = 'Assign local Database';
		$data['content_view'] = 'database/assign_database_v';
		$data['go_back_url'] = site_url('database/setting');
				
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
		
	function delete_data(){		
		
		$backup = array('psac_activity_log', 'psac_cash_deposited_payment', 'psac_dailytoll', 'psac_dailytollinfo', 'psac_liability_deduction', 'psac_product_inward', 'psac_product_inward_return', 'psac_product_outward', 'psac_product_outward_item', 'psac_product_outward_item_return', 'psac_transferred_liability', 'psac_wages', 'psac_wagesitem', 'psac_wages_advance');
		
		$data['db_tables'] = $backup;		
		$data['backup_id'] = 'hello';
		$data['db_name'] = $this->db->database;
		
		$data['page_title'] = 'Delete Data From Current Business Session';
		$data['content_view'] = 'database/yearly_delete_data_v';		
		$this->template->set('scriptsrc', array(site_url('database/assets/js/delete_data.js')));
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function ajax_delete_data(){
		$backup_id = $this->input->post('backup_id');
		$table_name = $this->input->post('table_name');
		$response = array('status'=>'error', 'message'=>'Invalid request: Please click on <strong>Start Backup</strong> button to backup database!');
		
		if(!empty($backup_id) && !empty($table_name)){
			
			$sql = "DELETE FROM ".$table_name."";
			$delete_result = $this->db->query($sql);
			if($delete_result){
				$response = array('status'		=>'success', 
								  'message'		=>'System taken backup of this table "'.$table_name.'"', 
							      'table_name' 	=> $table_name,
							      'status_icon' => '<span class="text-success"><i class="fa fa-check"></i></span>',
							      'action_button' => '<a href="#" class="btn btn-sm btn-warning">Deleted</a>');
			}else{
				$response = array('status'	=>'error', 
								  'message'		=>'Data deletion failed from the table "'.$table_name.'"', 
								 );
			}
			
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}
	
	function complete(){}
	
	function yearly_save_backup(){
		$backup_id = $this->input->post('backup_id');
		$table_name = $this->input->post('table_name');
		$response = array('status'=>'error', 'message'=>'Invalid request: Please click on <strong>Start Backup</strong> button to backup database!');
		
		if(!empty($backup_id) && !empty($table_name)){			
			// check backup id process existance and fetch backup path
			$result = $this->db->where('db_id', $backup_id)->get(DATABASE_SETTING)->result_array();
			if(!empty($result)){
				$backup_path = $result['0']['backup_path'];
				// check backup path existance
				if(file_exists($backup_path) && is_dir($backup_path)){
					//check table existance 
					$allTables = $this->DM->getAllTables();
					if(in_array($table_name, $allTables)){
						// check already backuped file for this table name
						$result = $this->db->get_where(DATABASE_SETTING_TABLES, array('db_id' => $backup_id, 'table_name' => $table_name))->result_array();
						if(!empty($result)){
							$tbl = $result[0];
							$download_url = site_url('databse/download/table/'.$tbl['table_id']);
							$response = array('status'		=>'success', 
											  'message'		=>'System taken backup of this table "'.$tbl['table_name'].'"', 
											  'table_name' 	=> $tbl['table_name'],
											  'status_icon' => '<span class="text-success"><i class="fa fa-check"></i></span>',
											  'action_button' => '<a href="'.$download_url.'" class="btn btn-sm btn-warning">Download</a>');
												  
						}else{
							$prefs = array(
										'tables'        => array($table_name),   		// Array of tables to backup.
										'format'        => 'zip',                       // gzip, zip, txt
										'filename'      => $table_name.'_backup.sql',   // File name - NEEDED ONLY WITH ZIP FILES
										'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
										'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
										'newline'       => "\n"                         // Newline character used in backup file
							);
							$backup_filename = $table_name.'.zip';
							$result = $this->DM->export_database($prefs, $backup_filename, $backup_path);
							if($result){
									
								$dataSet = array('db_id'		=>$backup_id, 
												 'table_name' 	=> $table_name, 
												 'file_path' 	=> $backup_path.'/'.$backup_filename,
												 'backup_date' 	=> get_date('Y-m-d H-i-s'), 
												 'backup_by' 	=> $this->userID);
												 
								$result = $this->db->insert(DATABASE_SETTING_TABLES, $dataSet);
								$table_id = $this->db->insert_id();
								$download_url = site_url('database/download/table/'.$table_id);
								$response = array('status'		=>'success', 
												  'message'		=>'System taken backup of this table "'.$table_name.'"', 
												  'table_name' 	=> $table_name,
												  'status_icon' => '<span class="text-success"><i class="fa fa-check"></i></span>',
												  'action_button' => '<a href="'.$download_url.'" class="btn btn-sm btn-warning">Download</a>');	
							}
						}
					}else{
						$response = array('status'=>'error', 'message'=>'Invalid Request: There is not found "'.$table_name.'" table, please try again');
					}
				}else{
					$response = array('status'=>'error', 'message'=>'Invalid Request: There is not found backup store path "'.$backup_path.'" , please try again');
				}
			}else{
				$response = array('status'=>'error', 'message'=>'Invalid Request: There is not found any backup process');
			}
			
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}
	
	function download($type = NULL, $id = NULL){
		$download_file = '';
		if($type != NULL && $id != NULL){
			if($type === 'table'){
				// check table backup existance
				$result = $this->db->get_where(DATABASE_BACKUP_TABLES, array('table_id'=>$id))->result_array();
				if(!empty($result)){
					$tbackup = $result[0];
					if(!empty($tbackup['file_path']) && file_exists($tbackup['file_path']) && is_file($tbackup['file_path'])){
						$download_file = $tbackup['file_path'];
						force_download($download_file, NULL);
						exit;
					}else{
						$this->messageci->set('Download Failed: Table backup file not found!', 'error');
					}
				}else{
					$this->messageci->set('Invalid request: There is not such record to download!', 'error');
				}
			}
			if($type === 'db'){
				$result = $this->db->get_where(DATABASE_BACKUP, array('backup_id'=>$id))->result_array();
				if(!empty($result)){
					$tbackup = $result[0];
					if(!empty($tbackup['backup_path']) && file_exists($tbackup['backup_path']) && is_dir($tbackup['backup_path'])){
						$db_name = $this->db->database;
						$download_file = $db_name . '_' . get_datetime('d-M-Y h-i-s A'); //basename($download_path);
						
						$download_path = $tbackup['backup_path'];						
						$this->zip->read_dir($download_path, FALSE);
						$this->zip->download($download_file.'.zip');
						exit;			
					}else{
						$this->messageci->set('Download Failed: Database backup file not found!', 'error');
					}
				}else{
					$this->messageci->set('Invalid request: There is not such record to download database!', 'error');
				}
			}	
		}		
		
	}
	
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'add_newdb':
				$this->form_validation->set_rules('display_name', 'Yearly Backup Name', 'trim|required|min_length[2]');
			break;
			case 'import_data':
				$this->form_validation->set_rules('backup_id', 'Yearly Backup ID', 'trim|required');
				$this->form_validation->set_rules('table_name', 'DB Table Name', 'trim|required');
			break;
			case 'assign_database':
				$this->form_validation->set_rules('db_name', 'Database Name', 'trim|required');
				$this->form_validation->set_rules('admin_name', 'Admin Name', 'trim|required');
			break;
			
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}

	
}




