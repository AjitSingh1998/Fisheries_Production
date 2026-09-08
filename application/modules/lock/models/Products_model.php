<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Products_model extends CI_Model {
	
	// Get all prduct_type data
	function get_prtype_data(){
		$data = array();
		$result = $this->db->get_where(PRODUCT_TYPE, array('status'=>'Active'))->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	// Product category data
	function get_pc_data($id){
		$data = array();
		$result = $this->db->get_where(PRODUCT_CATEGORY, array('ID'=>$id))->result_array();
		if(!empty($result)){
			$data = $result[0];
		}
		//printr($data);
		return $data;
	}
	
	// Product category data
	function get_all_pr_cat($type){
		$data = array();
		$result = $this->db->get_where(PRODUCT_CATEGORY, array('Type_id'=>$type))->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	function get_product_data($id){
		$data = array();
		$result = $this->db->get_where(PRODUCT, array('ID'=>$id))->result_array();
		if(!empty($result)){
			$data = $result[0];
		}
		//printr($data);
		return $data;
	}
	
	//Jaal product data
	function get_jp_data($id){
		$data = array();
		$result = $this->db->get_where(JAALPRODUCT, array('ID'=>$id))->result_array();
		if(!empty($result)){
			$data = $result[0];
		}
		//printr($data);
		return $data;
	}
	
	//Jaal product data
	function get_np_data($id){
		$data = array();
		$result = $this->db->get_where(NAV, array('ID'=>$id))->result_array();
		if(!empty($result)){
			$data = $result[0];
		}
		//printr($data);
		return $data;
	}
	
}


