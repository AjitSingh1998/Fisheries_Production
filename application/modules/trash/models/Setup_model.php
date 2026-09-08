<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Setup_model extends CI_Model {
	
	function get_mg_data($id){
		$data = array();
		$result = $this->db->get_where(MAINGROUP, array('ID'=>$id))->result_array();
		if(!empty($result)){
			$data = $result[0];
		}
		//printr($data);
		return $data;
	}
	
	function get_mgtype_data(){
		$data = array();
		$result = $this->db->get(MAINGROUP_TYPE)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	// Get all maingroup data according to maingroup_type id
	function get_all_mg_data($type){
		$data = array();
		$result = $this->db->get_where(MAINGROUP, array('Type'=>$type))->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
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
				  WHERE (fisherman.MainGroup = ".$maingroup." ) ".(($f_ids != '')?' AND fisherman.ID NOT IN('.$f_ids.')':'').
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
	
	function get_fisherman($q, $f_ids = '', $offset = 0, $limit = 10){
		$data['result1'] = '';
		$data['result2'] = '';
		
		$query = "SELECT
					fisherman.ID AS ID,
					CONCAT(fisherman.Code,' ( ',maingroup.Name,' / ', fisherman.Name, ' )') AS text,
					fisherman.Code AS Code,
					fisherman.group_type_id AS FGroup,
					fisherman.MainGroup AS MainGroup,
					maingroup.Name AS MainGroupName,
					fisherman.Name AS Name,
					fisherman.MajorFee AS MajorFee,
					fisherman.MinorFee AS MinorFee,
					fisherman.SawalFee AS SawalFee,
					group_type.govt_charges AS govt_charges
				  FROM `".FISHERMAN."` as fisherman
				  LEFT JOIN `".MAINGROUP."` as maingroup ON maingroup.ID = fisherman.MainGroup
				  LEFT JOIN `".MAINGROUP_TYPE."` as group_type ON group_type.ID = fisherman.group_type_id
				  WHERE (fisherman.NAME like '%".$q."%' OR fisherman.Code like '%".$q."%') ".(($f_ids != '')?' AND fisherman.ID NOT IN('.$f_ids.')':'').
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
														 'MainGroupName'=> $res['MainGroupName'],
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
	
	function get_samiti(){
		$data = array();
		$result = $this->db->get(MAINGROUP)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	function get_fisher_data($id){
		$data = array();
		$data['pf_data'] = array();
		$data['sf_data'] = array();
		$result = $this->db->get_where(FISHERMAN, array('ID' => $id))->result_array();
		
		if(!empty($result)){
			$data['pf_data'] = $result[0];
			$this->db->select('sf.ID, sf.Secondary, pf.Code, mg.Name as Samiti, pf.Name');
			$this->db->from(SECONDARYFISHERMAN.' sf');
			$this->db->join(FISHERMAN.' pf', 'pf.ID=sf.Secondary', 'LEFT');
			$this->db->join(MAINGROUP.' mg', 'mg.ID=pf.MainGroup', 'LEFT');
			$this->db->where(array('sf.Primary' => $id, 'sf.Secondary !=' => $id, 'sf.group_status' => 'Joined'));
			$sec_data = $this->db->get()->result_array();
			
			if(!empty($sec_data)){
				foreach($sec_data as $sec){
					$data['sf_data']['db_id'][] = $sec['ID'];
					$data['sf_data']['samiti'][] = $sec['Samiti'];
					$data['sf_data']['code'][]   = $sec['Code'];
					$data['sf_data']['name'][] = $sec['Name'];
					$data['sf_data']['id'][] = $sec['Secondary'];
				}
			}
		}
		//printr($data);
		return $data;
	}
	
	function get_fpoint_data($fp_id){
		$data = array();
		$result = $this->db->get_where(FISHINGPOINTS, array('ID'=>$fp_id))->result_array();
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
	
	//Get data for transfer liability
	function get_fisherman_data($id){
		$data = array();
		$data['pf_data'] = array();
		$data['sf_data'] = array();
		$this->db->select('fm.*,
						  (SELECT SUM(po.cash_received) FROM '.PRODUCT_OUTWARD.' po WHERE po.fisherman_id=fm.ID GROUP BY po.fisherman_id) as total_cash_received,						  
						  (SELECT SUM(poi.total_price) FROM '.PRODUCT_OUTWARD_ITEM.' poi WHERE poi.fisherman_id=fm.ID AND poi.returnable = "Yes" GROUP BY poi.fisherman_id) as prod_liability,
						  (SELECT SUM(poir.total_price) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.fisherman_id=fm.ID GROUP BY poir.fisherman_id) as returned_amt,
						  (SELECT SUM(wi.GroupLiabilityDeduction) FROM '.WAGESITEM.' wi WHERE wi.FishermanId=fm.ID GROUP BY wi.FishermanId) as deposit_amt
						  ');
		$result = $this->db->get_where(FISHERMAN.' fm', array('ID' => $id))->result_array();
		
		if(!empty($result)){
			$data['pf_data'] = $result[0];
			$this->db->select('sf.ID, sf.Secondary, pf.Code, mg.Name as Samiti, pf.Name, tl.amount');
			$this->db->from(SECONDARYFISHERMAN.' sf');
			$this->db->join(FISHERMAN.' pf', 'pf.ID=sf.Secondary', 'LEFT');
			$this->db->join(MAINGROUP.' mg', 'mg.ID=pf.MainGroup', 'LEFT');
			$this->db->join(TRANSFERRED_LIABILITY.' tl', 'tl.secondary_id = sf.Secondary AND tl.primary_id='.$id, 'LEFT');
			$this->db->where(array('sf.Primary' => $id, 'sf.Secondary !=' => $id, 'sf.group_status' => 'Joined'));
			$sec_data = $this->db->get()->result_array();
			
			if(!empty($sec_data)){
				foreach($sec_data as $sec){
					$data['sf_data']['db_id'][] = $sec['ID'];
					$data['sf_data']['samiti'][] = $sec['Samiti'];
					$data['sf_data']['code'][]   = $sec['Code'];
					$data['sf_data']['name'][] = $sec['Name'];
					$data['sf_data']['id'][] = $sec['Secondary'];
					$data['sf_data']['amount'][] = $sec['amount'];
				}
			}
		}
		//printr($data);
		return $data;
	}
	
	//Get outward items data by id
	function get_liability_data($id){
		$data = array();
		//$result = $this->db->order_by('ID', 'ASC')->get_where(PRODUCT_OUTWARD_ITEM, array('outward_id'=>$id))->result_array();
		
		$this->db->select('poir.*, p.Name as prod_name, pt.Name as type_name, pc.Name as cat_name');
		$this->db->from(PRODUCT_OUTWARD_ITEM.' poir');
		$this->db->join(PRODUCT.' p', 'poir.product_id=p.ID', 'LEFT');
		$this->db->join(PRODUCT_TYPE.' pt', 'poir.product_type=pt.ID', 'LEFT');
		$this->db->join(PRODUCT_CATEGORY.' pc', 'poir.product_category=pc.ID', 'LEFT');
		$this->db->where(array('poir.fisherman_id'=>$id));
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
		$this->db->where(array('poir.fisherman_id'=>$id));
		$this->db->order_by('poir.ID', 'ASC');
		$result = $this->db->get()->result_array();
		
		if(!empty($result)){
			$data = $result;
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
	
}


