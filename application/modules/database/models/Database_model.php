<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Database_model extends CI_Model {

	function getAllTables(){
		$result = $this->db->query('show tables')->result_array();
		$table_name_list = array();
		if(!empty($result)){
			foreach($result as $table){
				if(is_array($table)) 
				foreach($table as $table_name){
					$table_name_list[] = $table_name;
				}
			}
		}
		return $table_name_list;					
	}
	
	function getAllTablesAndRow(){
		$sql = 'SELECT table_name, table_rows FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "'.$this->db->database.'";';
		$table_name_list = $this->db->query($sql)->result_array();
		return $table_name_list;					
	}
	
	function cell_db_backup($filename = '')
	{
		echo $DBUSER=$this->db->username;
		echo $DBPASSWD=$this->db->password;
		echo $DATABASE=$this->db->database;
		die;
		$filename = $filename === '' ? $DATABASE . "-" . date("Y-m-d_H-i-s") . ".sql.gz" : $filename;
		
		$mime = "application/x-gzip";
	
		header( "Content-Type: " . $mime );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	
		// $cmd = "mysqldump -u $DBUSER --password=$DBPASSWD $DATABASE | gzip --best";   
		$cmd = "mysqldump -u $DBUSER --password=$DBPASSWD --no-create-info --complete-insert $DATABASE | gzip --best";
		//mysqldump -uroot -p my_project -r my_project.sql
		passthru( $cmd );
	
		exit(0);
	}
	
	function export_database($prefs, $zip_file_name = NULL, $backup_path = NULL){
		
		/*$prefs = array(
					'tables'        => array('table1', 'table2'),   // Array of tables to backup.
					'ignore'        => array(DAILYTOLL, DAILYTOLLINFO),                     // List of tables to omit from the backup
					'format'        => 'zip',                       // gzip, zip, txt
					'filename'      => 'my_db_backup.sql',          // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
					'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
					'newline'       => "\n"                         // Newline character used in backup file
		);*/
		
		@$backup =& $this->dbutil->backup($prefs); 
		
		$db_name = ($zip_file_name == NULL) ? 'backup-on-'. date("Y-m-d-H-i-s") .'.zip' : $zip_file_name;
		
		$save = ($backup_path == NULL) ? FCPATH.'DB_BACKUP/'.$db_name : $backup_path.'/'.$db_name;
		
		write_file($save, $backup); 
				
		//force_download($db_name, $backup);
		
		return true;
	}
	
	function checkDBExistance($AllDB, $db_prefix = '', $new_db_name = ''){
		
		$dbname = $this->generateDBName($db_prefix, $new_db_name);
		if(!in_array($dbname, $AllDB)){
			$res = $this->dbforge->create_database($dbname);
			if($res){
				return $dbname;
			}
		}else{
			if($new_db_name == ''){
				$this->checkDBExistance($AllDB, $db_prefix);
			}			
		}		
		return false;		
	}
	
	function generateDBName($db_prefix, $new_db_name){
		if($new_db_name == ''){
			$seed = str_split('abcdefghijklmnopqrstuvwxyz'); // and any other characters
			shuffle($seed); // probably optional since array_is randomized; this may be redundant
			$new_db_name = '';
			foreach(array_rand($seed, 6) as $k) $new_db_name .= $seed[$k];
		}
		return $db_prefix.$new_db_name;	
	
	}
	
	function create_db($userID){
		
		$new_db_name = $this->input->post('new_db_name');
		$display_name = $this->input->post('display_name');
		//$AllDB = $this->dbutil->list_databases();
		//include APPPATH . 'config/database.php';
		//$db_prefix = $active_group.'_';
		//$new_db_name = $this->checkDBExistance($AllDB, $db_prefix, $new_db_name);
		if($new_db_name){
			$prep_data = array('backup_dbname'  => $new_db_name,
							   'display_name' 	=> $display_name,
							   'backup_type'	=> 'Yearly',
							   'backup_by' 	=> $userID,
							   'backup_date' 	=> get_date('Y-m-d H:i:s')
							   );
			$this->db->insert(DATABASE_BACKUP, $prep_data);
			$db_id = $this->db->insert_id();
			if($db_id){
				return $db_id;
			}
		}
		return false;
	}
	
	function copyAllTableStructure(){
		$prefs = array(
					//'tables'        => array('table1', 'table2'),   // Array of tables to backup.
					//'ignore'        => array(DAILYTOLL, DAILYTOLLINFO),                     // List of tables to omit from the backup
					'format'        => 'zip',                       // gzip, zip, txt
					'filename'      => 'my_db_backup.sql',          // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
					'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
					'newline'       => "\n"                         // Newline character used in backup file
		);		
		@$backup =& $this->dbutil->backup($prefs); 
		$save = FCPATH.'DB_BACKUP/tmp_database_structure/database_structure.zip';
		write_file($save, $backup);
		return $save;
	}
	
	function importDBStructure($db_file_path, $new_db){
		// Set line to collect lines that wrap
		$templine = '';
		$error = array();
			
		// Read in entire file
		$lines = file($db_file_path); 
		
		// Loop through each line
		foreach ($lines as $line){
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '#')
			continue;
		
			// Add this line to the current templine we are creating
			$templine .= $line;
		
			// If it has a semicolon at the end, it's the end of the query so can process this templine
			if (substr(trim($line), -1, 1) == ';'){
				// Perform the query
				$result = $new_db->query($templine);
				if(!$result){
					$error[] = 'error';
				}
				// Reset temp variable to empty
				$templine = '';
			}
		}
		
		
		if(!empty($error))		
			return false;
		else
			return true;
	}	
	
	function fisherman_closing_balance($fisherman_id = NULL, $MainGroup = NULL){
		$this->db->select('fm.ID, fm.Code, fm.Name, mg.Name as Samiti, 
							fm.products_balance as opening_product_balance,
							fm.wages_balance as opening_wages_balance,
							
							IFNULL(outward_liability,0) as outward_liability, 
							IFNULL(advance_wages,0) as advance_wages, 
							IFNULL(outward_returned_amt,0) as outward_returned_amt, 
							IFNULL(outward_liability_deducted,0) as outward_liability_deducted, 
							IFNULL(advance_wages_deduction,0) as advance_wages_deduction, 
							IFNULL(product_liability,0) as cash_product_liability,
							IFNULL(wages_liability,0) as cash_wages_liability
							
							', false);
		
		$this->db->from(FISHERMAN . ' fm');
		
		$this->db->join(MAINGROUP.' mg', 'mg.ID = fm.MainGroup','LEFT');
		$this->db->join('(SELECT fisherman_id, SUM(grand_total) as outward_liability FROM '.PRODUCT_OUTWARD.' WHERE status="Active" GROUP BY fisherman_id) po', 'po.fisherman_id = fm.ID', 'LEFT');
		$this->db->join('(SELECT Fisherman, SUM(Amount) as advance_wages FROM '.WAGES_ADVANCE.' WHERE status="Active" GROUP BY Fisherman) wa', 'wa.Fisherman = fm.ID', 'LEFT');
		$this->db->join('(SELECT fisherman_id, SUM(total_price) as outward_returned_amt FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' WHERE status="Active" GROUP BY fisherman_id) poir', 'poir.fisherman_id = fm.ID','LEFT');
		
		$this->db->join('(SELECT deducted_for, SUM(amount + advance_deduction) as outward_liability_deducted FROM '.LIABILITY_DEDUCTION.' WHERE status="Active" GROUP BY deducted_for) ld', 'ld.deducted_for = fm.ID','LEFT');
		
		$this->db->join('(SELECT FishermanId, SUM(AdvanceWagesDeduction) as advance_wages_deduction FROM '.WAGESITEM.' WHERE status="Active" GROUP BY FishermanId) wi', 'wi.FishermanId = fm.ID','LEFT');
		
		$this->db->join('(SELECT fisherman_id, SUM(product_liability) as product_liability, SUM(wages_liability) as wages_liability FROM '.CASH_DEPOSITED_PAYMENT.' WHERE status="Active" GROUP BY fisherman_id) cdp', 'cdp.fisherman_id = fm.ID','LEFT');
		
		if($MainGroup !== NULL){
			$this->db->where('fm.MainGroup', $MainGroup);
		}
		
		if($fisherman_id !== NULL){
			$this->db->where('fm.ID', $fisherman_id);
		}		
		$this->db->where('fm.status', 'Active');
		$this->db->group_by('fm.ID');
		$this->db->order_by('fm.Code','ASC');
		
		//$this->db->limit(50);
	  	$result = $this->db->get()->result_array();	
		if($fisherman_id !== NULL){
			return $result[0];
		}	
		return $result;
	
	}
	
	function samiti_closing_balance($samiti_id = NULL, $group_type = NULL){
		$this->db->select('mg.ID, mg.Name as Samiti, 
							mg.product_balance as opening_product_balance,
							mg.wages_balance as opening_wages_balance,
							
							IFNULL(outward_liability,0) as outward_liability, 
							IFNULL(advance_wages,0) as advance_wages, 
							IFNULL(outward_returned_amt,0) as outward_returned_amt, 
							IFNULL(GroupLiabilityDeduction,0) as GroupLiabilityDeduction, 
							IFNULL(AdvanceWagesDeduction,0) as AdvanceWagesDeduction, 
							IFNULL(product_liability,0) as cash_product_liability,
							IFNULL(wages_liability,0) as cash_wages_liability
							
							', false);
		
		$this->db->from(MAINGROUP . ' mg');
		$this->db->join('(SELECT main_group_id, SUM(grand_total) as outward_liability FROM '.PRODUCT_OUTWARD.' WHERE status="Active" AND outward_to = "Group" GROUP BY main_group_id) po', 'po.main_group_id = mg.ID', 'LEFT');
		$this->db->join('(SELECT maingroup_id, SUM(Amount) as advance_wages FROM '.WAGES_ADVANCE.' WHERE status="Active" AND advance_to = "Group" GROUP BY maingroup_id) wa', 'wa.maingroup_id = mg.ID', 'LEFT');
		$this->db->join('(SELECT main_group_id, SUM(total_price) as outward_returned_amt FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' WHERE status="Active" AND outward_to = "Group" GROUP BY main_group_id) poir', 'poir.main_group_id = mg.ID','LEFT');
				
		$this->db->join('(SELECT MainGroup, SUM(GroupLiabilityDeduction) as GroupLiabilityDeduction, SUM(AdvanceWagesDeduction) as AdvanceWagesDeduction FROM '.WAGESITEM.' WHERE status="Active" AND wages_for = "Group" GROUP BY MainGroup) wi', 'wi.MainGroup = mg.ID','LEFT');
		
		$this->db->join('(SELECT maingroup_id, SUM(product_liability) as product_liability, SUM(wages_liability) as wages_liability FROM '.CASH_DEPOSITED_PAYMENT.' WHERE status="Active" AND deposited_by = "Group" GROUP BY maingroup_id) cdp', 'cdp.maingroup_id = mg.ID','LEFT');
		
		if($group_type !== NULL){
			$this->db->where('mg.Type', $group_type);
		}
		
		if($samiti_id !== NULL){
			$this->db->where('mg.ID', $samiti_id);
		}	
		
		$this->db->group_by('mg.ID');
		$this->db->order_by('mg.ID','ASC');
		//$this->db->limit(10);
	  	$result = $this->db->get()->result_array();	
		if($samiti_id !== NULL){
			return $result[0];
		}
		return $result;
	
	}
	
	function getBackupDBInfo($backup_id, $table_name = NULL){
		
		$this->db->select('dbt.file_path, dbt.table_name, db.backup_dbname'); //, tbl.TABLE_ROWS as total_records
		$this->db->from(DATABASE_BACKUP_TABLES . ' dbt');
		$this->db->join(DATABASE_BACKUP . ' db', 'db.backup_id = dbt.backup_id', 'LEFT');
		/*$this->db->join('(SELECT TABLE_NAME, TABLE_ROWS 
							FROM INFORMATION_SCHEMA.TABLES 
						  	WHERE TABLE_SCHEMA = "'.$this->db->database.'") as tbl', 'tbl.TABLE_NAME = dbt.table_name', 'LEFT');*/
		
		if($backup_id != ''){
			$this->db->where(array('dbt.backup_id'=>$backup_id));
		}
		if($table_name != NULL){
			$this->db->where(array('dbt.table_name'=>$table_name));
		}
		$result = $this->db->get()->result_array();
		if($table_name != NULL && !empty($result)){
			return $result[0];
		}				
		return $result;
		
	}
	
	function importSQLFiledata($import_file, $db_name, $import_dst_path){	
		$return_response = array('status'=>'error', 'message' => 'Import db failed!');
		
		$filename = $import_file; //'ericsCompressed.sql';
		$maxRuntime = 20; // less then your max script execution limit	
		$dbInfo = array('hostname' => $this->db->hostname, 
						'username' => $this->db->username, 
						'password' => $this->db->password, 
						'database' => $db_name, 
						'dbdriver' => 'mysqli');
						
		$mysqli = $this->load->database($dbInfo, TRUE);		
		$deadline = time()+$maxRuntime;		
		$progressFilename = $import_dst_path.'_filepointer'; // tmp file for progress
		$errorFilename = $filename.'_error'; // tmp file for erro
		
		$fp = fopen($filename, 'r');
		if(!$fp){
			return $return_response['message'] = 'failed to open file:'.$filename;			
		}
		
		// check for previous error
		if(file_exists($errorFilename) ){			
			return $return_response['message'] = '<pre> previous error: '.file_get_contents($errorFilename);			
		}
		
		// go to previous file position
		$filePosition = 0;
		if(file_exists($progressFilename) ){
			$filePosition = file_get_contents($progressFilename);
			fseek($fp, $filePosition);
		}
		
		$queryCount = 0;
		$query = '';
		while($deadline > time() AND ($line=fgets($fp, 5024000)) ){
			if(substr($line,0,2)=='--' OR trim($line)=='' ){
				continue;
			}
			$query .= $line;			
			if( substr(trim($query),-1)==';' ){
				if( !$mysqli->query($query) ){
					$error = 'Error performing query \'<strong>' . $query . '\': ' . $mysqli->error;
					file_put_contents($errorFilename, $error."\n");
					exit;
				}
				$query = '';
				file_put_contents($progressFilename, ftell($fp)); // save the current file position for 
				$queryCount++;
			}
		}
		
		if(feof($fp)){
			$queryProcessed = ftell($fp).'/'.filesize($filename).' queries processed.';
			$msg = 'Data successfully imported: '.$queryProcessed;			
			$return_response = array('status'=>'success',
										'message'=>$msg, 
										'process'=>'', 
										'status_icon' => '<span class="text-success"><i class="fa fa-check"></i></span>',
										'action_button' => '<button type="button" disabled="disabled" class="btn btn-sm btn-warning">Done</button>');
			
			$progressFilename = $import_dst_path.'_filepointer';
			if(file_exists($progressFilename)){
				 @unlink($progressFilename);
			}			
			if(file_exists($errorFilename)){
			    @unlink($errorFilename);
			}						
		}else{
			$msg = 'Data import in progress! '. "<br/>";
			$msg .= "<small>".ftell($fp).'/'.filesize($filename).' '.(round(ftell($fp)/filesize($filename), 2 )*100).'%'."\n";
			$msg .= $queryCount.' queries processed!'."</small>";
			
			$return_response = array('status'=>'success', 'message'=>$msg,  'process'=>'continue');
		}
	
		return $return_response;
	}
	
	function checkUserExistanceInBackupDB($email, $db_conn){
		
		$db_conn->select("a.*, CONCAT(a.first_name, ' ', a.last_name) as full_name, ag.group_name");
        $db_conn->from(ADMINISTRATOR.' a');
		$db_conn->join(ADMINISTRATOR_GROUP.' ag', 'ag.group_id = a.group_id', 'LEFT');
        $db_conn->where('a.email', $email);
        $result = $db_conn->get()->result_array();
        if(!empty($result)) {
			return $result[0];       
        }
        return false;
	}

}



