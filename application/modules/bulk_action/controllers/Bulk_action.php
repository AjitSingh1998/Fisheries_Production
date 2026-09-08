<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Bulk_action extends MY_Controller {
	var $userID, $userInfo, $datetime;
    public function __construct() {
        parent::__construct();
		$this->userID = checkUserLogin();
		$this->userInfo = loginUserInfo();
		$this->datetime = get_datetime('l, d M, Y h:i A');
    }
	
	public function action(){
		$postData = $this->input->post();						
		$action = $this->uri->segment(3);
		$table_name = $this->input->post("table_name", TRUE);
		$field_name = $this->input->post("column_name", TRUE);
		$primary_key = $this->input->post("primary_key", TRUE);	
		$id_array = $this->input->post("items", TRUE);
		//$items = rtrim($this->input->post("items", TRUE), '|');
		//$id_array = ($items) ? explode("|", $items) : '';
		
		if($id_array != '' && $table_name !='' && $primary_key !=''){
			switch($action){
				case 'delete':
					$this->db->where_in($primary_key, $id_array);
					$this->db->delete($table_name);					
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully deleted!');
				break;
				case 'publish':
					$this->db->where_in($primary_key, $id_array);
					$this->db->update($table_name, array($field_name => 'Publish'));					
					$result = array('success'=>'true', 'success_message'=>  count($id_array).' Items successfully published!');
				break;
				case 'unpublish':
					$this->db->where_in($primary_key, $id_array);
					$this->db->update($table_name, array($field_name => 'Unpublish'));
					$result = array('success'=>'true', 'success_message'=>  count($id_array).' Items successfully unpublished!');
				break;
				case 'active':
					$this->db->where_in($primary_key, $id_array);
					$this->db->update($table_name, array($field_name => 'Active'));
					
					if($table_name == DAILYTOLL){
						$this->db->where_in('DailytollId', $id_array);
						$this->db->update(DAILYTOLLINFO, array('status' => 'Active'));
					}
					if($table_name == WAGES){	
						$this->db->where_in('wages_id', $id_array);
						$this->db->update(WAGESITEM, array('status' => 'Active'));
						
						$this->db->where_in('wages_id', $id_array);
						$this->db->update(LIABILITY_DEDUCTION, array('status' => 'Active'));
					}
					if($table_name == PRODUCT_OUTWARD){	
						$this->db->where_in('outward_id', $id_array)->update(PRODUCT_OUTWARD_ITEM, array('status' => 'Active'));
						$this->db->where_in('outward_id', $id_array)->update(PRODUCT_OUTWARD_ITEM_RETURN, array('status' => 'Active'));
					}
					
					$result = array('success'=>'true', 'success_message'=>  count($id_array).' Items successfully Activated!');
				break;
				case 'inactive':
					$this->db->where_in($primary_key, $id_array)->update($table_name, array($field_name => 'Inactive'));					
					$result = array('success'=>'true', 'success_message'=>  count($id_array).' Items successfully Inactivated!');
				break;
				case 'activate':
					$this->db->where_in($primary_key, $id_array)->update($table_name, array($field_name => 'Activate'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully Activated!');
				break;
				case 'deactivate':
					$this->db->where_in($primary_key, $id_array)->update($table_name, array($field_name => 'Deactivate'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully Deactivated!');
				break;
				case 'mark_delete':
					$this->db->where_in($primary_key, $id_array)->update($table_name, array($field_name => 'Deleted'));
					
					if($table_name == DAILYTOLL){
						$this->db->where_in('DailytollId', $id_array)->update(DAILYTOLLINFO, array('status' => 'Deleted'));
						
						foreach($id_array as $key => $value){
							$userActivityLogArray = array(
								'activity_by'			=> $this->userID,
								'activity' 				=> 'Mark Deleted',
								'table_name'			=> 'DAILYTOLL,DAILYTOLLINFO',
								'data_id'				=> $value,
								'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' mark deleted dailytoll data on '.$this->datetime,
								'activity_url'			=> 'bulk_action/action/mark_delete',
								'activity_date'			=> get_datetime('Y-m-d H:i:s')
							 );
							$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
						}
					}
					if($table_name == WAGES){
						$this->db->where_in('wages_id', $id_array)->update(WAGESITEM, array('status' => 'Deleted'));
						$this->db->where_in('wages_id', $id_array)->update(LIABILITY_DEDUCTION, array('status' => 'Deleted'));
						
						foreach($id_array as $key => $value){
							$userActivityLogArray = array(
								'activity_by'			=> $this->userID,
								'activity' 				=> 'Mark Deleted',
								'table_name'			=> 'WAGES,WAGESITEM,LIABILITY_DEDUCTION',
								'data_id'				=> $value,
								'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' mark deleted wages data on '.$this->datetime,
								'activity_url'			=> 'bulk_action/action/mark_delete',
								'activity_date'			=> get_datetime('Y-m-d H:i:s')
							 );
							$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
						}
					}
					if($table_name == PRODUCT_OUTWARD){
						$this->db->where_in('outward_id', $id_array)->update(PRODUCT_OUTWARD_ITEM, array('status' => 'Deleted'));
						$this->db->where_in('outward_id', $id_array)->update(PRODUCT_OUTWARD_ITEM_RETURN, array('status' => 'Deleted'));
						
						foreach($id_array as $key => $value){
							$userActivityLogArray = array(
								'activity_by'			=> $this->userID,
								'activity' 				=> 'Mark Deleted',
								'table_name'			=> 'PRODUCT_OUTWARD,PRODUCT_OUTWARD_ITEM,PRODUCT_OUTWARD_ITEM_RETURN',
								'data_id'				=> $value,
								'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' mark deleted outward data on '.$this->datetime,
								'activity_url'			=> 'bulk_action/action/mark_delete',
								'activity_date'			=> get_datetime('Y-m-d H:i:s')
							 );
							$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
						}
					}
					
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully Deactivated!');
				break;
				case 'delete_fisherman':
					$this->db->where_in($primary_key, $id_array)->update($table_name, array('status'=>'Deleted'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully deleted!');
				break;
				case 'lock':
					$this->db->where_in($primary_key, $id_array)->update($table_name, array($field_name => 'Lock'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Data successfully locked!');
				break;
				case 'unlock':
					$this->db->where_in($primary_key, $id_array)->update($table_name, array($field_name => 'Unlock'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Data successfully unlocked!');
				break;
				case 'delete_dailytoll':
					$result = $this->deleteDailytoll($postData);
				break;
				case 'delete_outward_product':
					$result = $this->deleteOutwardProduct($postData);
				break;
				case 'delete_inward_product':
					$result = $this->deleteInwardProduct($postData);
				break;
				case 'delete_return_inward':
					$result = $this->deleteReturnInward($postData);
				break;
				case 'delete_wages_advance_fisherman':
					$result = $this->deleteWagesAdvanceFisherman($postData);
				break;
				case 'delete_wages_sheetprint':
					$result = $this->deleteWagesSheetprint($postData);
				break;
				case 'delete_cash_deposited_payment':
					$result = $this->delete_cash_deposited_payment($postData);
				break;
			}
			echo json_encode($result);
		}else{
		   echo 'Kindly Select Atleast One Item!';
		}
	   die();
	}


	/* 
	** FOR BELOW ALL METHODS **
	* check user permission
	* check row status
	* check lock unlock
	* check dependencies from other data
	
	* delete main 
	* delete dependency / related data 		
   */
	   
	function deleteDailytoll($data = array()){
		$result = array('success'=>'true', 'success_message'=> 'Data operation perform successfully.');
		if(!empty($data)){
			$table_name  = $data['table_name'];		
			$primary_key = $data['primary_key'];
			$id_array    = $data['items'];			
			if(!empty($id_array)){			
				$this->db->select('ID,editable');
				$this->db->from($table_name);			
				$this->db->where_in($primary_key, $id_array);
				$RESULTS = $this->db->get()->result_array();
				// check results existence
				if(!empty($RESULTS)){
					$count = 0;
					foreach( $RESULTS as $rInfo ){
						// check editable unlock existence
						if( $rInfo['editable'] == 'Unlock' ){
							// delete dailly toll info
							$this->db->delete(DAILYTOLLINFO, array('DailytollId'=>$rInfo['ID']));	
							// delete dailly toll
							$this->db->delete(DAILYTOLL, array('ID' => $rInfo['ID']));
							$count++;
							
							$userActivityLogArray = array(
								'activity_by'			=> $this->userID,
								'activity' 				=> 'Delete',
								'table_name'			=> 'DAILYTOLL,DAILYTOLLINFO',
								'data_id'				=> $rInfo['ID'],
								'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' delete dailytoll data on '.$this->datetime,
								'activity_url'			=> 'bulk_action/action/delete_dailytoll',
								'activity_date'			=> get_datetime('Y-m-d H:i:s')
							 );
							$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
						}
					}
					$result = array('success'=>'true', 'success_message'=> $count . ' Dailytoll Items and related data successfully deleted.');	
				}
			}
		}
		return $result;
	} 
	
	function deleteOutwardProduct($data){
		$result = array('success'=>'true', 'success_message'=> 'Data operation perform successfully.');
		if(!empty($data)){
			$table_name  = $data['table_name'];		
			$primary_key = $data['primary_key'];
			$id_array    = $data['items'];			
			if(!empty($id_array)){			
				$this->db->select('ID,editable');
				$this->db->from($table_name);			
				$this->db->where_in($primary_key, $id_array);
				$RESULTS = $this->db->get()->result_array();				
				// check results existence
				if(!empty($RESULTS)){
					$count = 0;
					foreach( $RESULTS as $rInfo ){
						// check editable unlock existence
						if( $rInfo['editable'] == 'Unlock' ){						
							// delete PRODUCT_OUTWARD_ITEM
							 $this->db->delete(PRODUCT_OUTWARD_ITEM, array('outward_id'=>$rInfo['ID']));	
							// delete PRODUCT_OUTWARD 
							 $this->db->delete(PRODUCT_OUTWARD, array('ID' => $rInfo['ID']));
							$count++;
						}
					}
					$result = array('success'=>'true', 'success_message'=> $count . ' Outward product and related items data successfully deleted.');	
				}
			}
		}
		return $result;
	}
	  	
	function deleteInwardProduct($data){
		$result = array('success'=>'true', 'success_message'=> 'Data operation perform successfully.');
		if(!empty($data)){
			$table_name  = $data['table_name'];		
			$primary_key = $data['primary_key'];
			$id_array    = $data['items'];			
			if(!empty($id_array)){			
				$this->db->select('ID,editable');
				$this->db->from($table_name);			
				$this->db->where_in($primary_key, $id_array);
				$RESULTS = $this->db->get()->result_array();				
				// check results existence
				if(!empty($RESULTS)){
					$count = 0;			
					foreach( $RESULTS as $rInfo ){
						// check editable unlock existence
						if( $rInfo['editable'] == 'Unlock' ){									
							// delete PRODUCT_INWARD 
							 $this->db->delete(PRODUCT_INWARD, array('ID' => $rInfo['ID']));
							$count++;
						}
					}
					$result = array('success'=>'true', 'success_message'=> $count . ' Inward products data successfully deleted.');	
				}
			}
		}
		return $result;
	}  
	 	
	function deleteReturnInward($data){
		$result = array('success'=>'true', 'success_message'=> 'Data operation perform successfully.');
		if(!empty($data)){
			$table_name  = $data['table_name'];		
			$primary_key = $data['primary_key'];
			$id_array    = $data['items'];			
			if(!empty($id_array)){			
				$this->db->select('ID,editable');
				$this->db->from($table_name);			
				$this->db->where_in($primary_key, $id_array);
				$RESULTS = $this->db->get()->result_array();				
				// check results existence
				if(!empty($RESULTS)){
					$count = 0;			
					foreach( $RESULTS as $rInfo ){
						// check editable unlock existence
						if( $rInfo['editable'] == 'Unlock' ){									
							// delete PRODUCT_INWARD_RETURN 
							 $this->db->delete(PRODUCT_INWARD_RETURN, array('ID' => $rInfo['ID']));
							$count++;
						}
					}
					$result = array('success'=>'true', 'success_message'=> $count . ' Return inward data successfully deleted.');	
				}
			}
		}
		return $result;
	} 
	  
	function deleteWagesAdvanceFisherman($data){
		$result = array('success'=>'true', 'success_message'=> 'Data operation perform successfully.');
		if(!empty($data)){
			$table_name  = $data['table_name'];		
			$primary_key = $data['primary_key'];
			$id_array    = $data['items'];			
			if(!empty($id_array)){			
				$this->db->select('ID,editable');
				$this->db->from($table_name);			
				$this->db->where_in($primary_key, $id_array);
				$RESULTS = $this->db->get()->result_array();				
				// check results existence
				if(!empty($RESULTS)){
					$count = 0;			
					foreach( $RESULTS as $rInfo ){
						// check editable unlock existence
						if( $rInfo['editable'] == 'Unlock' ){									
							// delete WAGES_ADVANCE 
							 $this->db->delete(WAGES_ADVANCE, array('ID' => $rInfo['ID']));
							$count++;
						}
					}
					$result = array('success'=>'true', 'success_message'=> $count . ' Wages advance to fisherman data successfully deleted.');	
				}
			}
		}
		return $result;
	} 
	  
	function deleteWagesSheetprint($data){
		$result = array('success'=>'false', 'success_message'=> 'Operation failed.');
		//printr($data);
		if(!empty($data)){
			$table_name  = $data['table_name'];		
			$primary_key = $data['primary_key'];
			$id_array    = $data['items'];			
			if(!empty($id_array)){			
				$this->db->select('ID,editable');
				$this->db->from($table_name);			
				$this->db->where_in($primary_key, $id_array);
				$RESULTS = $this->db->get()->result_array();	
				//printr($RESULTS);			
				// check results existence
				if(!empty($RESULTS)){
					$count = 0;			
					foreach( $RESULTS as $rInfo ){
						// check editable unlock existence
						if( $rInfo['editable'] == 'Unlock' ){
							// delete WAGES Items liability deduction
							$this->db->delete(LIABILITY_DEDUCTION, array('wages_id' => $rInfo['ID']));
							// delete WAGESITEM 
							$this->db->delete(WAGESITEM, array('wages_id' => $rInfo['ID']));
							// delete WAGES 
							$this->db->delete(WAGES, array('ID' => $rInfo['ID']));
							$count++;
							
							$userActivityLogArray = array(
								'activity_by'			=> $this->userID,
								'activity' 				=> 'Delete',
								'table_name'			=> 'WAGES,WAGESITEM,LIABILITY_DEDUCTION',
								'data_id'				=> $rInfo['ID'],
								'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' delete wages data on '.$this->datetime,
								'activity_url'			=> 'bulk_action/action/delete_wages_sheetprint',
								'activity_date'			=> get_datetime('Y-m-d H:i:s')
							 );
							$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
						}
					}
					$result = array('success'=>'true', 'success_message'=> $count . ' Wages successfully deleted.');	
				}
			}
		}
		return $result;
	}
	
	function delete_cash_deposited_payment($data){
		$result = array('success'=>'false', 'success_message'=> 'Operation failed.');
		//printr($data);
		if(!empty($data)){
			$table_name  = $data['table_name'];		
			$primary_key = $data['primary_key'];
			$id_array    = $data['items'];			
			if(!empty($id_array)){			
				$this->db->select('deposit_id,editable');
				$this->db->from($table_name);			
				$this->db->where_in($primary_key, $id_array);
				$RESULTS = $this->db->get()->result_array();	
				//printr($RESULTS);			
				// check results existence
				if(!empty($RESULTS)){
					$count = 0;			
					foreach( $RESULTS as $rInfo ){
						// check editable unlock existence
						if( $rInfo['editable'] == 'Unlock' ){
							// delete CASH_DEPOSITED_PAYMENT
							$this->db->delete(CASH_DEPOSITED_PAYMENT, array('deposit_id' => $rInfo['deposit_id']));
							$count++;
							
							$userActivityLogArray = array(
								'activity_by'			=> $this->userID,
								'activity' 				=> 'Delete',
								'table_name'			=> 'CASH_DEPOSITED_PAYMENT',
								'data_id'				=> $rInfo['deposit_id'],
								'description' 			=> $this->userInfo['first_name'].' '.$this->userInfo['last_name'].' delete cash deposite payment data on '.$this->datetime,
								'activity_url'			=> 'bulk_action/action/delete_cash_deposited_payment',
								'activity_date'			=> get_datetime('Y-m-d H:i:s')
							 );
							$this->db->insert(USERS_ACTIVITY_LOG, $userActivityLogArray);
						}
					}
					$result = array('success'=>'true', 'success_message'=> $count . ' data successfully deleted.');	
				}
			}
		}
		return $result;
	}
}