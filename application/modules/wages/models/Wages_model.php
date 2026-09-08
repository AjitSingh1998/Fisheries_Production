<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Wages_model extends CI_Model {
	/*
	# Advance to fisherman functions
	*/
	
	// Same as Dailytoll get_fisherman() model
	function get_fisherman($q, $f_ids = '', $offset=0, $limit=10){
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
				  WHERE (fisherman.NAME like '%".$q."%' OR fisherman.Code like '%".$q."%') ".(($f_ids != '')?' AND fisherman.ID NOT IN('.$f_ids.')':'').
				  " ORDER BY fisherman.Code ASC
				  LIMIT 0, 50";
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
	function get_mg_data($type=''){
		$data = array();
		if(!empty($type)){
			$this->db->where(array('Type'=>$type));
		}
		$result = $this->db->get(MAINGROUP)->result_array();
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
				  WHERE (fisherman.MainGroup = ".$maingroup." ) ".(($f_ids != '') ?' AND fisherman.ID NOT IN('.$f_ids.')':'').
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
		$result = $this->db->order_by('Name')->get(MAINGROUP)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	function get_wages_items_OLD_07June2018($from_date, $to_date, $maingroup, $wage_id=NULL){
		$data = array();
		$this->db->select('dti.group_type as f_group_type, 
						   dti.Samiti as f_maingroup, 
						   dti.CompanyId as f_id, 
						   
						   SUM(dti.Tmwt) as major_wt,
						   SUM(dti.Localminor) as minor_wt,
						   SUM(dti.Swt) as sawal_wt,
						   
						   fm.Name as f_name,
						   fm.AccountNo as AccountNo,
						   fm.IfscCode as IfscCode,
						   dti.MajorFee,
						   dti.MinorFee,
						   dti.SawalFee,
						   
						   wi.ID as wage_item_id,
						   wi.GroupLiabilityDeduction,
						   wi.AdvanceWagesDeduction,
						   
						   mg.Name as samiti_name,
						   
						   SUM((dti.Tmwt * dti.MajorFee)) as major_wage,
						   SUM((dti.Localminor * dti.MinorFee)) as minor_wage,
						   SUM((dti.Swt * dti.SawalFee)) as sawal_wage,
						   
						   SUM(((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
						   
						   SUM((dti.Twt * dti.govt_charges)) as govt_deduction,
						   
						   SUM(((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - ((dti.Twt * dti.govt_charges))) as net_amount
						   
						  ');
		
						   
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->join(WAGESITEM.' wi', 'wi.status="Active" AND wi.wages_for="Fisherman" AND wi.FishermanId=dti.CompanyId AND wi.wages_id="'.$wage_id.'"', 'LEFT');
		$this->db->where('dti.toll_date >=', $from_date);
		$this->db->where('dti.toll_date <=', $to_date);
		$this->db->where('dti.Samiti', $maingroup);
		$this->db->where('dti.status != ', 'Deleted');
		$this->db->group_by('dti.CompanyId');
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';
		//printr($result);
		if(!empty($result)){
			
			foreach($result as $v){
				
$sql = 'SELECT sf.Primary, sf.Secondary, sf.group_type_id, sf.maingroup_id,
		
		(SELECT CONCAT(IFNULL(fm.products_balance, 0.00), "/", IFNULL(fm.wages_balance, 0.00)) FROM '.FISHERMAN.' fm WHERE fm.status="Active" AND fm.ID = sf.Primary) as opening_account_balance,
		
		(SELECT IFNULL(SUM(po.grand_total), 0.00) FROM '.PRODUCT_OUTWARD.' po WHERE po.status="Active" AND po.fisherman_id = sf.Primary AND po.outward_date<="'.$to_date.'") as group_liability,
		
		(SELECT IFNULL(SUM(poir.total_price), 0.00) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.status="Active" AND poir.fisherman_id = sf.Primary AND poir.return_date<="'.$to_date.'" ) as returned_amt,
		
		(SELECT SUM(wa.Amount) FROM '.WAGES_ADVANCE.' wa WHERE wa.status="Active" AND wa.Fisherman=sf.Primary AND wa.Date<="'.$to_date.'") as advance_wages,
		
		(SELECT SUM(wi.AdvanceWagesDeduction) FROM '.WAGESITEM.' wi WHERE wi.status="Active" AND wi.FishermanId=sf.Primary AND wi.to_date <="'.$to_date.'") as advance_wages_deduction,
		
		(SELECT CONCAT(SUM(cdp.product_liability), "/", SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status = "Active" AND cdp.fisherman_id=sf.Primary AND cdp.deposited_by = "Fisherman" AND cdp.deposit_date <="'.$to_date.'") as cash_deposited,
		
		(SELECT IFNULL((SUM(ld.amount) + SUM(ld.advance_deduction)), 0.00) 
		 FROM '.LIABILITY_DEDUCTION.' ld WHERE ld.status = "Active" AND ld.deducted_for = sf.Primary AND ld.to_date <="'.$to_date.'") as group_liability_deducted
		
		FROM '.SECONDARYFISHERMAN.' sf 
		WHERE sf.Secondary = "'.$v['f_id'].'" AND sf.group_status = "Joined" GROUP BY sf.Primary, sf.Secondary ORDER BY sf.ID ASC, sf.join_date ASC';
		
			//echo $sql;die; '.$v['f_id'].'
			  $cal_result = $this->db->query($sql)->result_array();
			  //printr($cal_result);
			 /* echo '<pre>';
			  print_r($cal_result);
			  echo '</pre>';*/
			  //die;
			  $extra = array('group_liability'=>0, 'group_liability_deducted' => 0, 'returned_amt' => 0, 'advance_wages' =>0, 'advance_wages_deduction'=>0);
			  if(!empty($cal_result)){
				foreach($cal_result as $res){
					$opening_account_balance = explode('/', $res['opening_account_balance']);
					$products_balance = is_null(@$opening_account_balance[0]) ? 0 : @$opening_account_balance[0];
					$wages_balance = is_null(@$opening_account_balance[1]) ? 0 : @$opening_account_balance[1];
					
					$cash_deposited = explode('/', $res['cash_deposited']);
					$cash_product_liability_deposited = is_null(@$cash_deposited[0]) ? 0 : @$cash_deposited[0];
					$cash_wages_liability_deposited = is_null(@$cash_deposited[1]) ? 0 : @$cash_deposited[1];
					
					$outstanding = ($products_balance + $res['group_liability'])-($res['returned_amt'] + $res['group_liability_deducted'] + $cash_product_liability_deposited);
					if($outstanding < 0 && $v['f_id'] == $res['Primary']){						
						$extra['group_liability'] += $outstanding;						
					}else if($outstanding > 0){
						$extra['group_liability'] += $outstanding;
					}
					//$extra['advance_wages'] += $res['advance_wages'];
					$advance_wages = (($wages_balance + $res['advance_wages'])  - ($res['advance_wages_deduction'] + $cash_product_liability_deposited));
					if($advance_wages < 0 && $v['f_id'] == $res['Primary']){						
						$extra['advance_wages'] += $res['advance_wages'];
						$extra['advance_wages_deduction'] += $res['advance_wages_deduction'];						
					}else if($advance_wages > 0){
						$extra['advance_wages'] += $res['advance_wages'];
						$extra['advance_wages_deduction'] += $res['advance_wages_deduction'];
					}
					//$extra['group_liability'] += $res['products_balance'];
				}
				//printr($extra);
			  	$data[] = array_merge($v + $extra);
			  }else{
			  	$data[] = array_merge($v + $extra);
			  }
			}
			//$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	function get_wages_items($from_date, $to_date, $maingroup, $wage_id=NULL){
		$data = array();
		$this->db->select('ANY_VALUE(dti.group_type) as f_group_type, 
						   ANY_VALUE(dti.Samiti) as f_maingroup, 
						   fm.ID as f_id, ANY_VALUE(fm.Code) as f_code,
						   
						   IFNULL(SUM(dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt),0) as major_wt,
						   IFNULL(SUM(dti.Localminor),0) as minor_wt,
						   IFNULL(SUM(dti.Swt),0) as sawal_wt,
						   
						   ANY_VALUE(fm.Name) as f_name,
						   ANY_VALUE(fm.AccountNo) as AccountNo,
						   ANY_VALUE(fm.IfscCode) as IfscCode,
						   ANY_VALUE(IFNULL(dti.MajorFee,0)) as MajorFee,
						   ANY_VALUE(IFNULL(dti.MinorFee,0)) as MinorFee,
						   ANY_VALUE(IFNULL(dti.SawalFee,0)) as SawalFee,
						   
						   ANY_VALUE(wi.ID) as wage_item_id,
						   ANY_VALUE(wi.GroupLiabilityDeduction) as GroupLiabilityDeduction,
						   ANY_VALUE(wi.AdvanceWagesDeduction) as AdvanceWagesDeduction,
						   
						   ANY_VALUE(mg.Name) as samiti_name,                 
						   
						   SUM(IFNULL((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee, 0)) as major_wage,
						   SUM(IFNULL(dti.Localminor * dti.MinorFee, 0)) as minor_wage,
						   SUM(IFNULL(dti.Swt * dti.SawalFee, 0)) as sawal_wage,
						   
						   SUM(IFNULL(((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee), 0)) as total_wages,
						   
						   SUM(IFNULL(dti.Twt * dti.govt_charges, 0)) as govt_deduction,
						   
						   SUM(IFNULL(((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee) - (dti.Twt * dti.govt_charges), 0)) as net_amount
						  ');
		
		$this->db->from(FISHERMAN.' fm');
		$this->db->join(DAILYTOLLINFO.' dti', 'dti.CompanyId=fm.ID AND dti.Samiti = "'.$maingroup.'" AND dti.status = "Active" AND dti.toll_date >="'.$from_date.'" AND dti.toll_date <="'.$to_date.'"', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=fm.MainGroup', 'LEFT');
		$this->db->join(WAGESITEM.' wi', 'wi.status="Active" AND wi.wages_for="Fisherman" AND wi.FishermanId=fm.ID AND wi.wages_id="'.$wage_id.'"', 'LEFT');		
		$this->db->where(array('fm.MainGroup' => $maingroup));
		//$this->db->where(array('fm.ID' => 685));
		$this->db->group_by('fm.ID');
		$this->db->order_by('fm.ID', 'ASC');
		
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';
		//printr($result);
		if(!empty($result)){
			
			foreach($result as $v){
				
$sql = 'SELECT sf.Primary, sf.Secondary, sf.group_type_id, sf.maingroup_id,
		
		(SELECT CONCAT(IFNULL(fm.products_balance, 0.00), "/", IFNULL(fm.wages_balance, 0.00)) FROM '.FISHERMAN.' fm WHERE fm.status="Active" AND fm.ID = sf.Primary) as opening_account_balance,
		
		(SELECT IFNULL(SUM(po.grand_total), 0.00) FROM '.PRODUCT_OUTWARD.' po WHERE po.status="Active" AND po.fisherman_id = sf.Primary AND po.outward_date<="'.$to_date.'") as group_liability,
		
		(SELECT IFNULL(SUM(poir.total_price), 0.00) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.status="Active" AND poir.fisherman_id = sf.Primary AND poir.return_date<="'.$to_date.'" ) as returned_amt,
		
		(SELECT SUM(wa.Amount) FROM '.WAGES_ADVANCE.' wa WHERE wa.status="Active" AND wa.Fisherman=sf.Primary AND wa.Date<="'.$to_date.'") as advance_wages,
		
		(SELECT SUM(wi.AdvanceWagesDeduction) FROM '.WAGESITEM.' wi WHERE wi.status="Active" AND wi.FishermanId=sf.Primary AND wi.to_date <="'.$to_date.'") as advance_wages_deduction,
		
		(SELECT CONCAT(SUM(cdp.product_liability), "/", SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status = "Active" AND cdp.fisherman_id=sf.Primary AND cdp.deposited_by = "Fisherman" AND cdp.deposit_date <="'.$to_date.'") as cash_deposited,
		
		(SELECT IFNULL((SUM(ld.amount) + SUM(ld.advance_deduction)), 0.00) 
		 FROM '.LIABILITY_DEDUCTION.' ld WHERE ld.status = "Active" AND ld.deducted_for = sf.Primary AND ld.to_date <="'.$to_date.'") as group_liability_deducted
		
		FROM '.SECONDARYFISHERMAN.' sf 
		WHERE sf.Secondary = "'.$v['f_id'].'" AND sf.group_status = "Joined" GROUP BY sf.Primary, sf.Secondary ORDER BY sf.ID ASC, sf.join_date ASC';
		
			  //echo $sql;die; 
			  //echo $v['f_id'];
			  $cal_result = $this->db->query($sql)->result_array();
			  //printr($cal_result);
			 /* echo '<pre>';
			  print_r($cal_result);
			  echo '</pre>';*/
			  //die;
			  $extra = array('personal_liability'=>0,'group_liability'=>0, 'group_liability_deducted' => 0, 'returned_amt' => 0, 'advance_wages' =>0, 'advance_wages_deduction'=>0);
			  if(!empty($cal_result)){
				foreach($cal_result as $res){
					$opening_account_balance = explode('/', $res['opening_account_balance']);
					$products_balance = is_null(@$opening_account_balance[0]) ? 0 : @$opening_account_balance[0];
					$wages_balance = is_null(@$opening_account_balance[1]) ? 0 : @$opening_account_balance[1];
					
					$cash_deposited = explode('/', $res['cash_deposited']);
					$cash_product_liability_deposited = is_null(@$cash_deposited[0]) ? 0 : @$cash_deposited[0];
					$cash_wages_liability_deposited = is_null(@$cash_deposited[1]) ? 0 : @$cash_deposited[1];
					
					$products_balance = $products_balance ? $products_balance : 0 ;
					$cash_product_liability_deposited = $cash_product_liability_deposited ? $cash_product_liability_deposited : 0 ;
					
					$outstanding = ($products_balance + $res['group_liability'])-($res['returned_amt'] + $res['group_liability_deducted'] + $cash_product_liability_deposited);
					if($outstanding < 0 && $v['f_id'] == $res['Primary']){						
						$extra['group_liability'] += $outstanding;						
					}else if($outstanding > 0){
						$extra['group_liability'] += $outstanding;
					}
					//$extra['advance_wages'] += $res['advance_wages'];
					$advance_wages = (($wages_balance + $res['advance_wages'])  - ($res['advance_wages_deduction'] + $cash_wages_liability_deposited));
					
				
					if($advance_wages < 0 && $v['f_id'] == $res['Primary']){
						$extra['advance_wages'] += $advance_wages; //$res['advance_wages'];
						$extra['advance_wages_deduction'] += $res['advance_wages_deduction'];						
					}else if($advance_wages > 0){						
						$extra['advance_wages'] += $advance_wages; //$res['advance_wages'];
						$extra['advance_wages_deduction'] += $res['advance_wages_deduction'];
					}
										
					//$extra['group_liability'] += $res['products_balance'];
					if($res['Primary'] == $v['f_id'] && $res['Secondary'] == $v['f_id']){
						$extra['personal_liability'] = $outstanding;
					}
				}
				//printr($extra);
			  	$data[] = array_merge($v + $extra);
			  }else{
			  	$data[] = array_merge($v + $extra);
			  }
			}
			//$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	function get_samiti_wages_items_OLD_06June2018($from_date, $to_date, $group_type, $wage_id=NULL){
		$data = array();
				
		$this->db->select('dti.group_type as f_group_type, 
						   dti.Samiti as f_maingroup, 
						   dti.CompanyId as f_id, 
						   
						   SUM(dti.Tmwt) as major_wt,
						   SUM(dti.Localminor) as minor_wt,
						   SUM(dti.Swt) as sawal_wt,
						   
						   mg.Name as samiti_name,
						   mg.account_number as AccountNo,
						   mg.ifsc_code as IfscCode,
						   dti.MajorFee as MajorFee,
						   dti.MinorFee as MinorFee,
						   dti.SawalFee as SawalFee,
						   mg.product_balance,
						   mg.wages_balance,
						   
						   wi.ID as wage_item_id,
						   wi.GroupLiabilityDeduction as GroupLiabilityDeduction,
						   wi.AdvanceWagesDeduction as AdvanceWagesDeduction,
						   						   
						   SUM((dti.Tmwt * dti.MajorFee)) as major_wage,
						   SUM((dti.Localminor * dti.MinorFee)) as minor_wage,
						   SUM((dti.Swt * dti.SawalFee)) as sawal_wage,
						   
						   SUM(((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
						   
						   SUM((dti.Twt * dti.govt_charges)) as govt_deduction,
						   
						   SUM(((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - ((dti.Twt * dti.govt_charges))) as net_amount,
						   
						   (SELECT SUM(po.grand_total) FROM '.PRODUCT_OUTWARD.' po WHERE po.status="Active" AND po.outward_to="Group" AND po.main_group_id = dti.Samiti) as group_liability,
							(SELECT SUM(poir.total_price) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.status="Active" AND poir.outward_to="Group" AND poir.main_group_id = dti.Samiti ) as returned_amt,
							
							(SELECT SUM(wa.Amount) FROM '.WAGES_ADVANCE.' wa WHERE wa.advance_to="Group" AND wa.status="Active" AND wa.maingroup_id = dti.Samiti) as advance_wages,
							
							(SELECT CONCAT(SUM(cdp.product_liability), "/", SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.maingroup_id = dti.Samiti AND cdp.deposited_by = "Group" AND cdp.status = "Active") as cash_deposited,
							
							(SELECT CONCAT(SUM(wit.GroupLiabilityDeduction), "/", SUM(wit.AdvanceWagesDeduction)) FROM '.WAGESITEM.' wit WHERE wit.status="Active" AND wit.wages_for="Group" AND wit.MainGroup=dti.Samiti) as liability_deduction
						   
						  ');
					   
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(WAGESITEM.' wi', 'wi.status="Active" AND wi.wages_for="Group" AND wi.MainGroup=dti.Samiti AND wi.wages_id="'.$wage_id.'"', 'LEFT');
		$this->db->where(array('dti.status' => 'Active', 'dti.toll_date >=' => $from_date, 'dti.toll_date <=' => $to_date, 'dti.group_type' => $group_type));
		$this->db->group_by('dti.Samiti');
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';
		//printr($result);
		if(!empty($result)){			
			$data = $result;
		}
		return $data;
	}
	
	function get_samiti_wages_items($from_date, $to_date, $group_type, $wage_id=NULL){
		$data = array();
		
		$this->db->select('
						   mg.ID as f_maingroup,
						   ANY_VALUE(mg.Name) as samiti_name,
						   ANY_VALUE(mg.account_number) as AccountNo,
						   ANY_VALUE(mg.ifsc_code) as IfscCode,
						   
						   ANY_VALUE(mg.product_balance) as product_balance,
						   ANY_VALUE(mg.wages_balance) as wages_balance,
						   
						   ANY_VALUE(dti.group_type) as f_group_type,
						   ANY_VALUE(dti.CompanyId) as f_id,
						   
						   IFNULL(SUM(dti.Tmwt), 0) as major_wt,
						   IFNULL(SUM(dti.Localminor), 0) as minor_wt,
						   IFNULL(SUM(dti.Swt), 0) as sawal_wt,
						   
						   ANY_VALUE(dti.MajorFee) as MajorFee,
						   ANY_VALUE(dti.MinorFee) as MinorFee,
						   ANY_VALUE(dti.SawalFee) as SawalFee,
						   
						   SUM(IFNULL(dti.Tmwt * dti.MajorFee, 0)) as major_wage,
						   SUM(IFNULL(dti.Localminor * dti.MinorFee, 0)) as minor_wage,
						   SUM(IFNULL(dti.Swt * dti.SawalFee, 0)) as sawal_wage,
						   
						   SUM(IFNULL((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee), 0)) as total_wages,
						   
						   SUM(IFNULL(dti.Twt * dti.govt_charges, 0)) as govt_deduction,
						   
						   SUM(IFNULL(((dti.Tmwt * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - (dti.Twt * dti.govt_charges), 0)) as net_amount,
						   
						   ANY_VALUE(wi.ID) as wage_item_id,
						   ANY_VALUE(wi.GroupLiabilityDeduction) as GroupLiabilityDeduction,
						   ANY_VALUE(wi.AdvanceWagesDeduction) as AdvanceWagesDeduction,
						   
						   (SELECT SUM(po.grand_total) FROM '.PRODUCT_OUTWARD.' po WHERE po.status="Active" AND po.outward_to="Group" AND po.main_group_id = mg.ID) as group_liability,
						   (SELECT SUM(poir.total_price) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.status="Active" AND poir.outward_to="Group" AND poir.main_group_id = mg.ID ) as returned_amt,
						   
						   (SELECT SUM(wa.Amount) FROM '.WAGES_ADVANCE.' wa WHERE wa.advance_to="Group" AND wa.status="Active" AND wa.maingroup_id = mg.ID) as advance_wages,
						   
						   (SELECT CONCAT(SUM(cdp.product_liability), "/", SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.maingroup_id = mg.ID AND cdp.deposited_by = "Group" AND cdp.status = "Active") as cash_deposited,
						   
						   (SELECT CONCAT(SUM(wit.GroupLiabilityDeduction), "/", SUM(wit.AdvanceWagesDeduction)) FROM '.WAGESITEM.' wit WHERE wit.status="Active" AND wit.wages_for="Group" AND wit.MainGroup=mg.ID) as liability_deduction
						  ');
		
		$this->db->from(MAINGROUP.' mg');
		
		$this->db->join(DAILYTOLLINFO.' dti', 'dti.Samiti=mg.ID AND dti.status = "Active" AND dti.toll_date >= "'.$from_date.'" AND dti.toll_date <= "'.$to_date.'" ', 'LEFT');
		
		$this->db->join(WAGESITEM.' wi', 'wi.status="Active" AND wi.wages_for="Group" AND wi.MainGroup=mg.ID AND wi.wages_id="'.$wage_id.'"', 'LEFT');
		//$this->db->where(array('dti.status' => 'Active', 'dti.toll_date >=' => $from_date, 'dti.toll_date <=' => $to_date, 'mg.Type' => $group_type));
		$this->db->where(array('mg.Type' => $group_type));
		$this->db->group_by('mg.ID');
		$this->db->order_by('mg.ID', 'ASC');
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';
		
		//echo count($result);
		//die;
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
		$this->db->select('dti.*, fm.Code, fm.Name as fName, mg.Name as Samiti_name, dt.Date');
		$this->db->from(DAILYTOLLINFO.' dti');
		$this->db->join(DAILYTOLL.' dt', 'dt.ID=dti.DailytollId', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->where(array('dti.CompanyId'=>$fid, 'dti.Samiti'=>$mg, 'dti.toll_date >='=>$fdt, 'dti.toll_date <='=>$tdt, 'dti.status'=>'Active'));
		$this->db->order_by('dti.toll_date', 'ASC');
		$result = $this->db->get()->result_array();	
		return $result;
	}
	
	function get_deposited_cash_payment($deposit_id){
		$data = array();
		$this->db->select('cdp.*');
		$this->db->from(CASH_DEPOSITED_PAYMENT.' cdp');
		$this->db->where(array('cdp.deposit_id'=>$deposit_id));
		$result = $this->db->get()->result_array();
		if(!empty($result) && count($result)>0){
			$data = $result[0];
		}
		return $data;
	}
	
	function get_group_liability($fid, $fdt, $to_date, $mg){
		$sql = 'SELECT 
					 sf.Primary, sf.Type, fm.Name, fm.Code, sf.Secondary, sf.group_type_id, sf.maingroup_id,
					(SELECT CONCAT(IFNULL(fm.products_balance, 0.00), "/", IFNULL(fm.wages_balance, 0.00)) 
						FROM '.FISHERMAN.' fm 
						WHERE fm.status="Active" AND fm.ID = sf.Primary) as opening_account_balance,
					
					(SELECT IFNULL(SUM(po.grand_total), 0.00) 
						FROM '.PRODUCT_OUTWARD.' po 
						WHERE po.status="Active" AND po.fisherman_id = sf.Primary AND po.outward_date<="'.$to_date.'") as outward_liability,
					
					(SELECT IFNULL(SUM(poir.total_price), 0.00) 
						FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
						WHERE poir.status="Active" AND poir.fisherman_id = sf.Primary AND poir.return_date<="'.$to_date.'" ) as outward_returned_amt,
					
					(SELECT IFNULL(SUM(wa.Amount),0) 
						FROM '.WAGES_ADVANCE.' wa 
						WHERE wa.status="Active" AND wa.Fisherman=sf.Primary AND wa.Date<="'.$to_date.'") as advance_wages,
					
					(SELECT IFNULL(SUM(wi.AdvanceWagesDeduction),0) 
						FROM '.WAGESITEM.' wi 
						WHERE wi.status="Active" AND wi.FishermanId=sf.Primary AND wi.to_date <="'.$to_date.'") as advance_wages_deduction,
					
					(SELECT CONCAT(IFNULL(SUM(cdp.product_liability),0), "/", IFNULL(SUM(cdp.wages_liability),0)) 
						FROM '.CASH_DEPOSITED_PAYMENT.' cdp 
						WHERE cdp.status = "Active" AND cdp.fisherman_id=sf.Primary AND 
							  cdp.deposited_by = "Fisherman" AND cdp.deposit_date <="'.$to_date.'") as cash_deposited,
					
					(SELECT IFNULL((SUM(ld.amount) + SUM(ld.advance_deduction)), 0.00) 
						FROM '.LIABILITY_DEDUCTION.' ld 
						WHERE ld.status = "Active" AND ld.deducted_for = sf.Primary AND ld.to_date <="'.$to_date.'") as outward_deducted
				
				FROM '.SECONDARYFISHERMAN.' sf 
				LEFT JOIN '.FISHERMAN.' fm ON fm.ID=sf.Primary
				WHERE sf.Secondary = "'.$fid.'" AND sf.group_status = "Joined" GROUP BY sf.Primary, sf.Secondary ORDER BY sf.ID ASC, sf.join_date ASC';
		$result = $this->db->query($sql)->result_array();
		
		if(!empty($result)){
			return $result;
		}
		return false;
	}
}