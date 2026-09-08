<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Wages_model extends CI_Model {
	/*
	# Advance to fisherman functions
	*/
	
	// Same as Dailytoll get_fisherman() model
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
	
	// Get all maingroup_type data
	function get_mgtype_data(){
		$data = array();
		$result = $this->db->get(MAINGROUP_TYPE)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	// Get maingroup data according to maingroup_type id
	function get_mg_data($type){
		$data = array();
		$result = $this->db->get_where(MAINGROUP, array('Type'=>$type))->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	function get_mg_fisherman($maingroup, $f_ids = ''){
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
	
	//Get outward data by id
	function get_advance_wages($id){
		$data = array();
		
		
		$this->db->select('wa.*, CONCAT(fm.Code,"-",fm.Name) AS f_cn');
		$this->db->from(WAGES_ADVANCE.' wa');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=wa.Fisherman', 'LEFT');
		$this->db->where(array('wa.ID'=>$id));
		$result = $this->db->get()->result_array();
		
		if(!empty($result)){
			$data = $result[0];
		}
		//printr($data);
		return $data;
	}
	
	/*
	# Wages Sheet functions
	*/
	function get_all_maingroup_data(){
		$data = array();
		$result = $this->db->get(MAINGROUP)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	function get_wages_items($from_date, $to_date, $maingroup, $wage_id=NULL){
		$data = array();
		
		$this->db->select('dti.group_type as f_group_type, 
						   dti.Samiti as f_maingroup, 
						   dti.CompanyId as f_id, 
						   fm.Name as f_name,
						  
						   
						   SUM((dti.Tmwt * dti.MajorFee)) as total_major,
						   SUM((dti.Localminor * dti.MinorFee)) as total_minor,
						   SUM((dti.Swt * dti.SawalFee)) as total_sawal,
						   
						   SUM(((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
						   
						   SUM(((dti.Tmwt + dti.Localminor + dti.Swt) * dti.govt_charges)) as govt_deduction,
						   
						   SUM((((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - ((dti.Tmwt + dti.Localminor + dti.Swt) * dti.govt_charges))) as net_amount,
						   
						   dti.Samiti,
						   
						   wi.ID as wage_item_id,
						   wi.GroupLiabilityDeduction,
						   wi.AdvanceWagesDeduction,
						   
						   mg.Name as samiti_name, 
						   (SELECT SUM(tl.amount) FROM '.TRANSFERRED_LIABILITY.' tl WHERE tl.secondary_id=dti.CompanyId GROUP BY tl.secondary_id) as transferred_liability,
						   (SELECT SUM(poi.total_price) FROM '.PRODUCT_OUTWARD_ITEM.' poi WHERE poi.fisherman_id=dti.CompanyId GROUP BY poi.fisherman_id) as prod_liability,
						   (SELECT SUM(po.cash_received) FROM '.PRODUCT_OUTWARD.' po WHERE po.fisherman_id=dti.CompanyId GROUP BY po.fisherman_id) as total_cash_received,
						   (SELECT SUM(poir.total_price) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.fisherman_id=dti.CompanyId GROUP BY poir.fisherman_id) as returned_amt,
						   (SELECT SUM(wa.Amount) FROM '.WAGES_ADVANCE.' wa WHERE wa.Fisherman=dti.CompanyId GROUP BY wa.Fisherman) as advance_amt,
						   (SELECT CONCAT(SUM(wi.GroupLiabilityDeduction)," / ",SUM(wi.AdvanceWagesDeduction)) FROM '.WAGESITEM.' wi WHERE wi.FishermanId=dti.CompanyId GROUP BY wi.FishermanId) as deposit_amt
						  ');
		$this->db->from(DAILYTOLL. ' dt');
		$this->db->join(DAILYTOLLINFO.' dti', 'dti.DailytollId=dt.ID', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->join(WAGESITEM.' wi', 'wi.FishermanId=dti.CompanyId AND wi.wages_id="'.$wage_id.'"', 'LEFT');
		$this->db->where('dt.Date >=', $from_date);
		$this->db->where('dt.Date <=', $to_date);
		$this->db->where('dti.Samiti', $maingroup);
		//$this->db->where('dti.CompanyId', '628');
		$this->db->group_by('dti.CompanyId');
		$result = $this->db->get()->result_array();
		
		//echo $this->db->last_query(); echo '<br>';
		//printr($result);
		
		if(!empty($result)){
			$data = $result;
		}
		return $data;
	}
	
	function get_wages_data($wage_id){
		$data = array('wage_info' => '', 'wage_items' => '');
		$result = $this->db->get_where(WAGES, array('ID'=>$wage_id))->result_array();
		if(!empty($result)){
			$data['wage_info'] = $result[0];
			//$data['wage_items'] = $this->db->select('wi.*, fm.Name')->from(WAGESITEM.' wi')->join(FISHERMAN.' fm', 'fm.ID=wi.FishermanId', 'LEFT')->where(array('wi.wages_id'=>$wage_id))->get()->result_array();
		}
		
		return $data;
	}
	
	function get_fisherman_dti($fid, $fdt, $tdt, $mg){
		$result = array();
		$this->db->select('dti.*, fm.Code, fm.Name as fName, mg.Name as Samiti_name, dt.Date');
		$this->db->from(DAILYTOLLINFO.' dti');
		$this->db->join(DAILYTOLL.' dt', 'dt.ID=dti.DailytollId', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->where(array('dti.CompanyId'=>$fid, 'dti.Samiti'=>$mg, 'dt.Date >='=>$fdt, 'dt.Date <='=>$tdt, ));
		$result = $this->db->get()->result_array();	
		return $result;
	}
	
}

