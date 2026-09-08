<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Bulk_action extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }
	
	public function action(){
						
		$action = $this->uri->segment(4);
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
					$result = array('success'=>'true', 'success_message'=>  count($id_array).' Items successfully Activated!');
				break;
				case 'inactive':
					$this->db->where_in($primary_key, $id_array);
					$this->db->update($table_name, array($field_name => 'Inactive'));					
					$result = array('success'=>'true', 'success_message'=>  count($id_array).' Items successfully Inactivated!');
				break;
				case 'activate':
					$this->db->where_in($primary_key, $id_array);
					$this->db->update($table_name, array($field_name => 'Activate'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully Activated!');
				break;
				case 'deactivate':
					$this->db->where_in($primary_key, $id_array);
					$this->db->update($table_name, array($field_name => 'Deactivate'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully Deactivated!');
				break;
				case 'mark_delete':
					$this->db->where_in($primary_key, $id_array);
					$this->db->update($table_name, array($field_name => 'Deleted'));
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully Deactivated!');
				break;
				case 'delete_fisherman':
					$data = array('status'=>'Deleted');
					$this->db->where_in($primary_key, $id_array)->update($table_name, $data);
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Items successfully deleted!');
				break;
				case 'lock':
					$data = array($field_name => 'Lock');
					$this->db->where_in($primary_key, $id_array)->update($table_name, $data);
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Data successfully locked!');
				break;
				case 'unlock':
					$data = array($field_name => 'Unlock');
					$this->db->where_in($primary_key, $id_array)->update($table_name, $data);
					$result = array('success'=>'true', 'success_message'=> count($id_array).' Data successfully unlocked!');
				break;
			}
			echo json_encode($result);
		}else{
		   echo 'Kindly Select Atleast One Item!';
		}
	}
    	
}
