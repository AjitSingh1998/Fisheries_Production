<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Stocks_model extends CI_Model {
	
	// Dailytoll get_fisherman() model
	function get_fisherman($q, $f_ids = '', $offset = 0, $limit = 10){
		$data['result1'] = '';
		$data['result2'] = '';
		
		$query = "SELECT
					fisherman.ID AS ID,
					CONCAT(fisherman.Code,' ( ',maingroup.Name,' / ', fisherman.Name, ' )') AS text,
					fisherman.Code AS Code,
					fisherman.group_type_id AS FGroup,
					fisherman.MainGroup AS MainGroup,
					fisherman.Name AS Name,
					fisherman.MajorFee AS MajorFee,
					fisherman.MinorFee AS MinorFee,
					fisherman.SawalFee AS SawalFee,
					group_type.govt_charges AS govt_charges
				  FROM `".FISHERMAN."` as fisherman
				  LEFT JOIN `".MAINGROUP."` as maingroup ON maingroup.ID = fisherman.MainGroup
				  LEFT JOIN `".MAINGROUP_TYPE."` as group_type ON group_type.ID = fisherman.group_type_id
				  LEFT JOIN `".SECONDARYFISHERMAN."` as sf ON sf.Primary = fisherman.ID
				  WHERE sf.Primary = fisherman.ID AND sf.Secondary = fisherman.ID AND sf.group_status = 'Joined' AND (fisherman.NAME like '%".$q."%' OR fisherman.Code like '%".$q."%') ".(($f_ids != '')?' AND fisherman.ID NOT IN('.$f_ids.')':'').
				  " ORDER BY fisherman.Code ASC
				  LIMIT 0, 10";
		//echo $query; die;
		$result = $this->db->query($query)->result_array();
		//printr($result);
		if(!empty($result)){
			$new_data = array();
			foreach($result as $res){
				$new_data['result1'][] = array('id'=>$res['ID'], 'text'=>$res['text']);
				$new_data['result2'][$res['ID']] = array('ID' 			=> $res['ID'],
														 'Code' 		=> $res['Code'],
														 'Group' 		=> $res['FGroup'],
														 'MainGroup' 	=> $res['MainGroup'],
														 'Name' 		=> $res['Name'],
														 'govt_charges' => $res['govt_charges'],
														 'MajorFee' 	=> $res['MajorFee'],
														 'MinorFee' 	=> $res['MinorFee'],
														 'SawalFee' 	=> $res['SawalFee']
														 );
			}
			//printr($result);
			$data = $new_data;
		}
		//printr($data);
		return $data;
	}
	
	 // Get all group_type data
	function get_mgtype_data(){
		$data = array();
		$result = $this->db->get(MAINGROUP_TYPE)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	// Get all maingroup data by group_type id
	function get_mg_data($type){
		$data = array();
		$result = $this->db->get_where(MAINGROUP, array('Type'=>$type))->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	//get all fisherman's by maingroup id
	function get_all_mg_fisherman($maingroup, $f_ids = ''){
		$data['result1'] = '';
		$data['result2'] = '';
		
		$query = "SELECT
					fisherman.ID AS ID,
					CONCAT(fisherman.Code,'-',fisherman.Name) AS text,
					fisherman.Code AS Code,
					maingroup.Name AS MainGroup,
					fisherman.Name AS Name
				  FROM `".FISHERMAN."` as fisherman
				  LEFT JOIN `".MAINGROUP."` as maingroup ON maingroup.ID = fisherman.MainGroup
				  LEFT JOIN `".SECONDARYFISHERMAN."` as sf ON sf.Primary = fisherman.ID
				  WHERE sf.Primary = fisherman.ID AND sf.Secondary = fisherman.ID AND sf.group_status = 'Joined' AND (fisherman.MainGroup = ".$maingroup." ) ".(($f_ids != '')?' AND fisherman.ID NOT IN('.$f_ids.')':'').
				  " ORDER BY fisherman.Code ASC";
		$result = $this->db->query($query)->result_array();
		if(!empty($result)){
			$new_data = array();
			foreach($result as $res){
				$new_data['result1'][] = array('id'=>$res['ID'], 'text'=>$res['text']);
				$new_data['result2'][$res['ID']] = array('ID'=>$res['ID'], 'Code'=>$res['Code'], 'MainGroup'=>$res['MainGroup'], 'Name'=>$res['Name']);
			}
			$data = $new_data;
		}
		//printr($data);
		return $data;
	}
	
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
	
	// Get all prduct itmes according to cateogry
	function get_pr_items($cat_id){
		$data = array();
		$result = $this->db->get_where(PRODUCT, array('Category_Id'=>$cat_id))->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	//Get product category according to product type
	function get_pr_cat($ptype){
		$data = array();
		$result = $this->db->get_where(PRODUCT_CATEGORY, array('Type_id'=>$ptype))->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	//Get outward data by id
	function get_outward_data($id){
		$data = array();
		$this->db->select('po.*, CONCAT(fm.Code,"-",fm.Name) AS f_cn, mg.Name as mg_name, mgt.Name as mgt_name');
		$this->db->from(PRODUCT_OUTWARD.' po');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=po.fisherman_id', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=po.main_group_id', 'LEFT');
		$this->db->join(MAINGROUP_TYPE.' mgt', 'mgt.ID=po.group_type', 'LEFT');
		$this->db->where(array('po.ID'=>$id));
		$result = $this->db->get()->result_array();
		
		if(!empty($result)){
			$data = $result[0];
		}
		//printr($data);
		return $data;
	}
	
	//Get outward items data by id
	function get_outward_items($id){
		$data = array();
		//$result = $this->db->order_by('ID', 'ASC')->get_where(PRODUCT_OUTWARD_ITEM, array('outward_id'=>$id))->result_array();
		
		$this->db->select('poir.*, p.Name as prod_name, pt.Name as type_name, pc.Name as cat_name');
		$this->db->from(PRODUCT_OUTWARD_ITEM.' poir');
		$this->db->join(PRODUCT.' p', 'poir.product_id=p.ID', 'LEFT');
		$this->db->join(PRODUCT_TYPE.' pt', 'poir.product_type=pt.ID', 'LEFT');
		$this->db->join(PRODUCT_CATEGORY.' pc', 'poir.product_category=pc.ID', 'LEFT');
		$this->db->where(array('poir.outward_id'=>$id));
		$this->db->order_by('poir.ID', 'ASC');
		$result = $this->db->get()->result_array();
		
		if(!empty($result)){
			foreach($result as $res){
				$data['ID'][] = $res['ID'];
				$data['fid'][] = $res['fisherman_id'];
				$data['outid'][] = $res['outward_id'];
				$data['type'][] = $res['product_type'];
				$data['category'][] = $res['product_category'];
				$data['name'][] = $res['product_id'];
				$data['qty'][] = $res['quantity'];
				$data['rate'][] = $res['rate'];
				$data['return'][] = $res['returnable'];
				$data['total'][] = $res['total_price'];
				$data['cat_name'][] = $res['cat_name'];
				$data['prod_name'][] = $res['prod_name'];
				$data['type_name'][] = $res['type_name'];
			}
		}
		//printr($data);
		return $data;
	}
	
	//Get outward return items data by id
	function get_outward_return_items($id){
		$data = array();
		$this->db->select('poir.*, p.Name as prod_name, pt.Name as type_name, pc.Name as cat_name');
		$this->db->from(PRODUCT_OUTWARD_ITEM_RETURN.' poir');
		$this->db->join(PRODUCT.' p', 'poir.product_id=p.ID', 'LEFT');
		$this->db->join(PRODUCT_TYPE.' pt', 'poir.product_type=pt.ID', 'LEFT');
		$this->db->join(PRODUCT_CATEGORY.' pc', 'poir.product_category=pc.ID', 'LEFT');
		$this->db->where(array('poir.outward_id'=>$id));
		$this->db->order_by('poir.ID', 'ASC');
		$result = $this->db->get()->result_array();
		
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	//Get inward data by id
	function get_inward_data($id){
		
		$dataset = array('info'=>'', 'items'=>'');
		$result = $this->db->get_where(PRODUCT_INWARD, array('ID'=>$id))->result_array();
		if(!empty($result)){
			foreach($result as $res){
				$data['type'][] 	= $res['product_type'];
				$data['category'][] = $res['product_category'];
				$data['name'][] 	= $res['product_id'];
				$data['qty'][] 		= $res['quantity'];
				$data['rate'][] 	= $res['rate'];
				$data['total'][] 	= $res['total_price'];
				$dataset['info'] = $res;
			}
			$dataset['items'] = $data;
		}
		//printr($data);
		return $dataset;
	}
	
	//Get inward return data by id
	function get_inward_return_data($id){
		$dataset = array('info'=>'', 'items'=>'');
		$result = $this->db->get_where(PRODUCT_INWARD_RETURN, array('ID'=>$id))->result_array();
		if(!empty($result)){
			foreach($result as $res){
				$data['type'][] 	= $res['product_type'];
				$data['category'][] = $res['product_category'];
				$data['name'][] 	= $res['product_id'];
				$data['qty'][] 		= $res['quantity'];
				$data['rate'][] 	= $res['rate'];
				$data['total'][] 	= $res['total_price'];
				$dataset['info'] = $res;
			}
			$dataset['items'] = $data;
		}
		//printr($data);
		return $dataset;
	}
}

