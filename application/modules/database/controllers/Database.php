<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Database extends MY_Controller {
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
		$this->load->library('zip');
		$this->load->helper('database');
		$this->load->model('database_model', 'DM');
	}
	
	function update_backup_path(){
		//$backup_path = ROOTDIR . DB_BACKUP_DIR . $dbname. '-' .get_date('d-M-Y h-i-s A');
		/*$result = $this->db->get('psac_database_backup')->result_array();
		foreach($result as $table){
			$dr = ROOTDIR . DB_BACKUP_DIR;
			echo '<br>' .$table['backup_path'];
			//echo '<br>' .$new_path = str_replace('E:\htdocs\SIMRAN\bansagar\DB_BACKUP/', $dr, $table['backup_path']);
			//$this->db->where('backup_id', $table['backup_id'])->update('psac_database_backup', array('backup_path' => $new_path));
		}
		*/
		$result = $this->db->group_by('backup_id')->get('psac_database_backup_tables')->result_array();
		foreach($result as $table){
			$dr = ROOTDIR . DB_BACKUP_DIR;
			//echo '<br>' .dirname($table['file_path']);
			
			echo '<br>' .$new_path = str_replace('E:\htdocs\SIMRAN\bansagar\DB_BACKUP/', $dr, dirname($table['file_path']));
			
			$this->db->where('backup_id', $table['backup_id'])->update('psac_database_backup', array('backup_path' => $new_path));
			
			//$this->db->where('table_id', $table['table_id'])->update('psac_database_backup_tables', array('file_path' => $new_path));
		}
	}
	
	//Common funcitons
	function __callbackDeleteActionButton($primary_key, $row){ 
		return site_url('bulk_action/action/mark_delete');
	}
	
	function setting(){
		
		$actions = checkUserPermission('database/setting', $this->uri->segment(3));
		//printr($actions);
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		$crud->unset_jquery();
		$crud->unset_bootstrap();
		$crud->unset_common_search();					
		
		$crud->unset_edit();
		$crud->unset_read();
		
		if(!in_array('backup', $actions)){
			$crud->unset_add();
		}else{
			$crud->set_add_button_text('Assign Database');
			$crud->set_add_button_class('btn btn-primary');
			$crud->set_add_url_path(site_url('database/assign_database'));
		}
				
		if(!in_array('delete', $actions)){
			$crud->unset_delete();
		}
					
		$crud->set_subject('Database Setting');
		$crud->set_table(DATABASE_SETTING);
		$crud->set_relation('admin_id', ADMINISTRATOR, '{first_name} {last_name}');
		$crud->set_relation('db_id', DATABASE_BACKUP, 'display_name');		
		$crud->set_relation('assign_by', ADMINISTRATOR, '{first_name} {last_name}');
		
		$crud->columns('admin_id', 'db_id', 'assign_date', 'assign_by');
			
		//$crud->callback_column('global_database', array($this, '__global_db_callback'));
		//$crud->callback_column('local_database', array($this, '__local_db_callback'));
		
		$output = $crud->render();
		
		$data['page_title'] = 'Database Setting';
		$data['content_view'] = 'database/setting_v';	
		
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
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
	
	// following code for database weekly backup 
		
	function backup(){
		
		$actions = checkUserPermission('database/backup', $this->uri->segment(3));
		
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
		$crud->where('backup_type', 'Weekly');
		
		$crud->columns('backup_dbname', 'display_name', 'backup_path', 'backup_date', 'backup_by');	
		$crud->order_by('backup_date', 'DESC');
		$output = $crud->render();
		
		$data['page_title'] = 'Database backup';
		$data['content_view'] = 'database/backup_v';	
		
		$outputData = array_merge((array)$output, $data);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($outputData);
	}
	
	function export(){
		$checkForm = $this->__setFormRules('export');
		if($checkForm){
			$dbname = $this->input->post('dbname');
			$backup_name = $this->input->post('backup_name');
			$backup_path = ROOTDIR . DB_BACKUP_DIR . $dbname. '-' .get_date('d-M-Y h-i-s A');
			if(!file_exists($backup_path) && !is_dir($backup_path)){
				$result = mkdir($backup_path, 0755, true);
				if(!$result){
					$this->messageci->set('Failed to create backup directory, please try again!', 'error');
				}
			}
			
			$date = get_date('Y-m-d H-i-s');
			$dataSet = array('backup_dbname' => $dbname, 'display_name' => $backup_name, 'backup_type' => 'Weekly', 'backup_path' => $backup_path, 'backup_date' => $date, 'backup_by' => $this->userID);
			$result = $this->db->insert(DATABASE_BACKUP, $dataSet);
			$backup_id = $this->db->insert_id();
			if($backup_id){					
				redirect(site_url('database/start_backup/'.$backup_id));
			}else{
				$this->messageci->set('Failed to store backup information in DB, please try again!', 'error');
			}
		}
		
		include APPPATH . 'config/database.php';
		
		$allDatabase[] = array('backup_dbname' => @$db[$active_group]['database'], 'display_name' => 'Current Database');
		$default_db = $this->load->database($active_group, TRUE);
		$query = $default_db->get_where(DATABASE_BACKUP, array('backup_type' => 'Yearly'));
		$default_db->close();
		if($query !== NULL && $query->num_rows() > 0){

			$dblist = $query->result_array();
			$allDatabase = array_merge($allDatabase, $dblist);
		}
		
		$data['save_backup_url'] = 'database/save_backup';
		$data['export_button'] = '<a href="'.site_url('database/export/start').'" class="btn btn-primary">Start Backup</a>';
		$data['allDatabase'] = $allDatabase;
				
		$data['page_title'] = 'All Database';
		$data['content_view'] = 'database/select_db_to_export_v';
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function start_backup($backup_id = NULL){
		// check backup_id existance 
		$tableList = array();
		if($backup_id !== NULL){
			$query = $this->db->get_where(DATABASE_BACKUP, array('backup_id' => $backup_id));
			if($query !== NULL && $query->num_rows() > 0){
				$result = $query->result_array();
				$dbname = $result['0']['backup_dbname'];
				if (!$this->dbutil->database_exists($dbname)){						
					$this->messageci->set('Invalid Request: Database not exists', 'error');
					redirect(site_url('database/export'));
				}else{					
					include APPPATH . 'config/database.php';
					$selected_db = $this->load->database($dbname, TRUE);
					$result = $selected_db->query('show tables')->result_array();
					if(!empty($result)){
						foreach($result as $table){
							if(is_array($table)) 
							foreach($table as $table_name){
								$tableList[] = $table_name;
							}
						}
					}
					
				}
			}else{
				$this->messageci->set('Invalid Request: There is not found any backup process', 'error');
				redirect(site_url('database/export'));
			}
		}else{
			$this->messageci->set('Invalid Request: backup id is missing', 'error');
			redirect(site_url('database/export/'));
		}				
	
		
		$data['save_backup_url'] = 'database/save_backup';
		$data['export_button'] = ''; 
		$data['backup_id'] = $backup_id;
		$data['db_tables'] = $tableList;
				
		$data['page_title'] = 'Backup Database';
		$data['content_view'] = 'database/export_v';
		
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
				$dbname = $result['0']['backup_dbname'];
				// Check DB Existance 
				if ($this->dbutil->database_exists($dbname)){
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
								$this->db->close();
								include APPPATH . 'config/database.php';
								$this->load->database($dbname);
								$result = $this->DM->export_database($prefs, $backup_filename, $backup_path);
								$this->db->close();
								$backup_db = $this->session->userdata(SESSION_NAME . '_backup_database');
								if(isset($backup_db) && !empty($backup_db)){
									$this->load->database($backup_db);
								}else{
									$this->load->database(DEFAULT_DB_NAME);
								}								
								
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
					$response = array('status'=>'error', 'message'=>'Invalid Request: Database not exists!');
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
	
	function set_backup_db(){
		$db_name = $this->input->post('db_name');
		$response = array('status' => 'danger', 'message'=>'Invalid Request: DB Name not found!');
		
		if($db_name){		
			include APPPATH.'config/database.php';
			$db_config = @$db[$db_name];
			if(isset($db_config) && !empty($db_config)){				
				$password = decrypt_data(loginUserInfo('password'));				
				$connInfo = array('hostname' => $db_config['hostname'],
								  'username' => $db_config['username'],
								  'password' => $db_config['password'],
								  'database' => $db_config['database'],
								  'dbdriver' => $db_config['dbdriver']);
				$db_conn = $this->load->database($connInfo, TRUE);
				$this->load->model('account/account_model', 'AM');
				// check user existance in backup db
				$email = loginUserInfo('email');			
				$userInfo = $this->DM->checkUserExistanceInBackupDB($email, $db_conn);
				if($userInfo){
					$this->AM->set_session($userInfo);
					$this->AM->set_session_companyData($userInfo['business_id']);
					$this->session->set_userdata(array(SESSION_NAME . '_backup_database' => $db_name));
					$response = array('status' => 'success', 'message'=> 'Backup DB '.$db_name.' has been set, please wait till load the data!');
				}else{
					$response = array('status' => 'danger', 'message'=> 'Your account is not found in backup DB ('.$db_name.')!');
				}			
			}else{
				$response = array('status' => 'danger', 'message'=> $db_name . ' Backup DB not defined in database config file!');
			}
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($response));
	}
	
				
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'add_newdb':
				$this->form_validation->set_rules('display_name', 'Database Name', 'trim|required|min_length[2]');
			break;
			case 'set_local':
				$this->form_validation->set_rules('admin_name', 'Admin Name', 'trim|required');
				$this->form_validation->set_rules('db_name', 'Database Name', 'trim|required');
			break;
			case 'export':
				$this->form_validation->set_rules('dbname', 'Database Name', 'trim|required');
				$this->form_validation->set_rules('backup_name', 'Backup Name', 'trim|required');
			break;
			case 'import_data':
				$this->form_validation->set_rules('backup_id', 'Backup DB ID', 'trim|required');
				$this->form_validation->set_rules('table_name', 'Table Name', 'trim|required');
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




