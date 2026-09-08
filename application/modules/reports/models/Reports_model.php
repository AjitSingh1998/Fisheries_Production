<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Reports_model extends CI_Model {
	
	// Get all maingroup data
	function get_all_group_type(){
		$data = array();
		$result = $this->db->get_where(MAINGROUP_TYPE)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		return $data;
	}
	
	// Get all maingroup data
	function get_all_maingroups($type = ''){
		$data = array();
		if(!empty($type)){
			$this->db->where(array('Type'=>$type));
		}
		$result = $this->db->get(MAINGROUP)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		return $data;
	}
	
	function get_fishing_points(){
		$data = array();
		$result = $this->db->get(FISHINGPOINTS)->result_array();
		if(!empty($result)){
			$data = $result;
		}
		return $data;
	}
	
	function get_fisherman($q){
		$data['result1'] = '';
		
		$query = "SELECT
					fisherman.ID AS ID,
					CONCAT(fisherman.Code,' ( ',maingroup.Name,' / ', fisherman.Name, ' )') AS text
				  FROM `".FISHERMAN."` as fisherman
				  LEFT JOIN `".MAINGROUP."` as maingroup ON maingroup.ID = fisherman.MainGroup
				  WHERE fisherman.NAME like '%".$q."%' OR fisherman.Code like '%".$q."%'
				  ORDER BY fisherman.Code ASC
				  LIMIT 0, 10";
		$result = $this->db->query($query)->result_array();
		if(!empty($result)){
			$new_data = array();
			foreach($result as $res){
				$new_data['result1'][] = array('id'=>$res['ID'], 'text'=>$res['text']);
			}
			$data = $new_data;
		}
		return $data;
	}
	
	//REPORTS
	//1. Get P1 & P1 with fisherman code Report (get_p1_report)
	function get_p1_report($from_date, $to_date, $maingroup = '', $group_type = ''){
		$this->db->select('SUM(dti.Cqty) as Cqty,
						   SUM(dti.Cwt) as Cwt,
						   SUM(dti.Rqty) as Rqty,
						   SUM(dti.Rwt) as Rwt,
						   SUM(dti.Mqty) as Mqty,
						   SUM(dti.Mwt) as Mwt,
						   SUM(dti.Kqty) as Kqty,
						   SUM(dti.Kwt) as Kwt,
						   SUM(dti.Aqty) as Aqty,
						   SUM(dti.Awt) as Awt,
						   SUM(dti.Sqty) as Sqty,
						   SUM(dti.Swt) as Swt,
						   SUM(dti.Lqty) as Lqty,
						   SUM(dti.Lwt) as Lwt,
						   SUM(dti.Localminor) as Localminor,
						   SUM(dti.Tmqty) as Tmqty,
						   SUM(dti.Tmwt) as Tmwt,
						   SUM(dti.Tqty) as Tqty,
						   SUM(dti.Twt) as Twt,						   
						   GROUP_CONCAT(DISTINCT dti.DailytollId ORDER BY dti.DailytollId SEPARATOR "," ) as page_code,						   
						   fm.Code as f_code,
						   fm.Name as f_name,
						   mg.Name as Samiti_name');
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->where(array('dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'dti.status'=>'Active'));
		if($group_type){
			$this->db->where(array('dti.group_type'=>$group_type));	
		}
		if($maingroup){
			$this->db->where(array('dti.Samiti'=>$maingroup));	
		}
		$this->db->group_by('fm.ID');
		$result = $this->db->get()->result_array();
		return $result;
	}
	
	//2. Get P2 Report (get_p2_report)
	function get_p2_report($from_date, $to_date, $point, $group_type){
		$this->db->select('SUM(dti.Cqty) as Cqty,
						   SUM(dti.Cwt) as Cwt,
						   SUM(dti.Rqty) as Rqty,
						   SUM(dti.Rwt) as Rwt,
						   SUM(dti.Mqty) as Mqty,
						   SUM(dti.Mwt) as Mwt,
						   SUM(dti.Kqty) as Kqty,
						   SUM(dti.Kwt) as Kwt,
						   SUM(dti.Aqty) as Aqty,
						   SUM(dti.Awt) as Awt,
						   SUM(dti.Sqty) as Sqty,
						   SUM(dti.Swt) as Swt,
						   SUM(dti.Lqty) as Lqty,
						   SUM(dti.Lwt) as Lwt,
						   SUM(dti.Localminor) as Localminor,
						   SUM(dti.Tmqty) as Tmqty,
						   SUM(dti.Tmwt) as Tmwt,
						   SUM(dti.Tqty) as Tqty,
						   SUM(dti.Twt) as Twt,
						   fm.Name as f_name, 
						   gt.Name as group_type_name,
						   mg.Name as Samiti_name,
						   fp.Name as point_name,
						   COUNT(DISTINCT(dti.CompanyId)) as total_members');
						  
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(MAINGROUP_TYPE.' gt', 'gt.ID=dti.group_type', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->join(FISHINGPOINTS.' fp', 'fp.ID=dti.Point', 'LEFT');
		$this->db->where(array('dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'dti.status'=>'Active'));
		if($point){
			$this->db->where(array('dti.Point'=>$point));	
		}
		if($group_type){
			$this->db->where(array('dti.group_type'=>$group_type));	
		}
		$this->db->group_by('dti.Samiti');
		$result = $this->db->get()->result_array();
		return $result;
	}
	
	//3. Get Fish Percent Report (fish_percent)
	function get_fish_percent($from_date, $to_date){
		$this->db->select('dti.toll_date as Date,
						   SUM(dti.Cwt) as tCwt,
						   SUM(dti.Rwt) as tRwt,
						   SUM(dti.Mwt) as tMwt,
						   SUM(dti.Kwt) as tKwt,
						   SUM(dti.Awt) as tAwt,
						   SUM(dti.Swt) as tSwt,
						   SUM(dti.Lwt) as tLwt,
						   SUM(dti.Localminor) as tLocalminor,
						   SUM(dti.Twt) as tTwt,
						   fm.Code as f_code, fm.Name as f_name, mg.Name as Samiti_name');
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->where(array('dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'dti.status'=>'Active'));
		$this->db->order_by('dti.toll_date', 'ASC');
		$this->db->group_by('dti.toll_date');
		$result = $this->db->get()->result_array();
		return $result;
	}
	
	//4. Fisherman Ledger Report (fisherman_ledger)
	function get_fisherman_ledger($from_date, $to_date, $fisherman){
		$data = array();
		
		$this->db->select('dti.group_type as f_group_type, 
						   dti.Samiti as f_maingroup, 
						   dti.CompanyId as f_id, 
						   fm.Name as f_name,
						  
						   
						   SUM(( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee)) as total_major,
						   SUM((dti.Localminor * dti.MinorFee)) as total_minor,
						   SUM((dti.Swt * dti.SawalFee)) as total_sawal,
						   
						   SUM((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
						   
						   SUM((dti.Twt * dti.govt_charges)) as govt_deduction,
						   
						   SUM(((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - (dti.Twt * dti.govt_charges))) as net_amount,
						   						   
						   dti.Samiti,
						   
						   wi.ID as wage_item_id,
						   wi.GroupLiabilityDeduction,
						   wi.AdvanceWagesDeduction,
						   
						   mg.Name as samiti_name, 
						   
						   (SELECT SUM(tl.amount) 
						   		FROM '.TRANSFERRED_LIABILITY.' tl 
								WHERE tl.secondary_id=dti.CompanyId 
								GROUP BY tl.secondary_id) as transferred_liability,
						   
						   (SELECT SUM(poi.total_price) 
						   		FROM '.PRODUCT_OUTWARD_ITEM.' poi 
								WHERE poi.fisherman_id=dti.CompanyId 
								GROUP BY poi.fisherman_id) as prod_liability,
						   
						   (SELECT SUM(po.cash_received) 
						   		FROM '.PRODUCT_OUTWARD.' po 
								WHERE po.fisherman_id=dti.CompanyId 
								GROUP BY po.fisherman_id) as total_cash_received,
						   
						   (SELECT SUM(poir.total_price) 
						   		FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
								WHERE poir.fisherman_id=dti.CompanyId 
								GROUP BY poir.fisherman_id) as returned_amt,
						   
						   (SELECT SUM(wa.Amount) 
						   		FROM '.WAGES_ADVANCE.' wa 
								WHERE wa.Fisherman=dti.CompanyId 
								GROUP BY wa.Fisherman) as advance_amt,
						   
						   (SELECT CONCAT(SUM(wi.GroupLiabilityDeduction)," / ",SUM(wi.AdvanceWagesDeduction)) 
						   		FROM '.WAGESITEM.' wi 
								WHERE wi.FishermanId=dti.CompanyId 
								GROUP BY wi.FishermanId) as deposit_amt
						  ');
		$this->db->from(DAILYTOLL. ' dt');
		$this->db->join(DAILYTOLLINFO.' dti', 'dti.DailytollId=dt.ID', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->join(WAGESITEM.' wi', 'wi.FishermanId=dti.CompanyId AND wi.wages_id="'.$wage_id.'"', 'LEFT');
		$this->db->where(array('dt.Date >='=>$from_date, 'dt.Date <=', $to_date, 'dti.Samiti', $maingroup, 'dt.status'=>'Active'));
		$this->db->group_by('dti.CompanyId');
		$result = $this->db->get()->result_array();
		
		if(!empty($result)){
			$data = $result;
		}
		return $data;
	}
	
	
	//PRODUCTION REPORTS
	//1. 
	function get_product_outward($from_date, $to_date, $fisherman_id){
		$result = $this->db->select('ID as outward_id, sub_total as total_outward, cash_received, outward_date as date')->get_where(PRODUCT_OUTWARD, array('outward_date >=' => $from_date, 'outward_date <=' => $to_date, 'status' => 'Active', 'fisherman_id' => $fisherman_id))->result_array();
		$dataSet = array();
		if(!empty($result)){
			foreach($result as $data){
				$date = strtotime($data['date']);
				$dataSet[$date] = $data;
			}		
		}
		return $dataSet;
	}
	
	function get_product_outward_return($from_date, $to_date, $fisherman_id){
		$result = $this->db->select('ID as outward_return_id, total_price as total_return, return_date as date')->get_where(PRODUCT_OUTWARD_ITEM_RETURN, array('return_date >=' => $from_date, 'return_date <=' => $to_date, 'fisherman_id' => $fisherman_id))->result_array();
		$dataSet = array();
		if(!empty($result)){
			foreach($result as $data){
				$date = strtotime($data['date']);
				$dataSet[$date] = $data;
			}		
		}
		return $dataSet;
	}
	
	function get_transferred_liability($from_date, $to_date, $fisherman_id){
		$result = $this->db->select('ID as transferred_id, amount as transferred_liability, added_date as date')->get_where(TRANSFERRED_LIABILITY, array('added_date >=' => $from_date, 'added_date <=' => $to_date, 'secondary_id' => $fisherman_id))->result_array();
		$dataSet = array();
		if(!empty($result)){
			foreach($result as $data){
				$date = strtotime($data['date']);
				$dataSet[$date] = $data;
			}		
		}
		return $dataSet;
	}
	
	function get_wages_sheet($from_date, $to_date, $fisherman){
		$result = $this->db->select('ID as wageitem_id, GroupLiabilityDeduction as po_deduction,  AdvanceWagesDeduction as aw_deduction, to_date as date')->get_where(WAGESITEM, array('from_date >=' => $from_date, 'to_date <=' => $to_date, 'FishermanId' => $fisherman))->result_array();
		$dataSet = array();
		if(!empty($result)){
			foreach($result as $data){
				$date = strtotime($data['date']);
				$dataSet[$date] = $data;
			}		
		}
		return $dataSet;
	}
	
	function get_wages_advance($from_date, $to_date, $fisherman){
		$result = $this->db->select('ID as advance_id, Amount as total_advance, Date as date')->get_where(WAGES_ADVANCE, array('Date >=' => $from_date, 'Date <=' => $to_date, 'Fisherman' => $fisherman))->result_array();
		$dataSet = array();
		if(!empty($result)){
			foreach($result as $data){
				$date = strtotime($data['date']);
				$dataSet[$date] = $data;
			}		
		}
		return $dataSet;
	}
	

	//OUTWARD REPORT FUNCITON START	
	//1. Outward Samiti Report (reports/outward/samiti)
	function get_outward_samiti($from_date, $to_date, $group_type = 0){
		$this->db->select('mg.ID, 
						   mg.Name, 
						   (SELECT 
								CONCAT(SUM(po.cash_received),"/",SUM(po.sub_total)) 
							  FROM '.PRODUCT_OUTWARD.' po 
							  WHERE po.outward_to = "Group" AND 
							  		po.main_group_id=mg.ID AND 
									po.status = "Active" AND 
									po.outward_date >="'.$from_date.'" AND 
									po.outward_date <="'.$to_date.'") as product_outward,
						   
						   (SELECT SUM(poir.total_price) 
						   		FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
								WHERE poir.outward_to = "Group" AND 
									  poir.status="Active" AND 
									  poir.return_date >= "'.$from_date.'" AND 
									  poir.return_date <= "'.$to_date.'" AND 
									  poir.main_group_id = mg.ID) as returned_amt,
						   
						   (SELECT 
						   		CONCAT(SUM(wi.GroupLiabilityDeduction),"/",SUM(wi.AdvanceWagesDeduction)) 
							  FROM '.WAGESITEM.' wi 
							  WHERE wi.wages_for = "Group" AND 
							  		wi.MainGroup=mg.ID AND 
									wi.status = "Active" AND 
									wi.from_date >="'.$from_date.'" AND 
									wi.to_date <="'.$to_date.'" ) as liability_deduction,
							
							(SELECT 
						   		SUM(wa.Amount)
							  FROM '.WAGES_ADVANCE.' wa 
							  WHERE wa.advance_to = "Group" AND 
							  		wa.maingroup_id=mg.ID AND 
									wa.status = "Active" AND 
									wa.Date >="'.$from_date.'" AND 
									wa.Date <="'.$to_date.'") as advance_wages,
							
							(SELECT CONCAT(SUM(cdp.product_liability),"/",SUM(cdp.wages_liability)) 
								FROM '.CASH_DEPOSITED_PAYMENT.' cdp 
								WHERE cdp.status="Active" AND 
									  cdp.deposited_by = "Group" AND 
									  cdp.deposit_date >= "'.$from_date.'" AND 
									  cdp.deposit_date <= "'.$to_date.'" AND 
									  cdp.maingroup_id = mg.ID) as cash_deposited');
		$this->db->from(MAINGROUP.' mg');
		if($group_type){
			$this->db->where(array('mg.Type' => $group_type));
		}
		$this->db->order_by('mg.Name', 'ASC');
		$result = $this->db->get()->result_array();
		$dataset = array();
		if(!empty($result)){
			$samiti_ids = array();
			foreach($result as $res){
				$samiti_ids[] = $res['ID'];
			}
			$samiti_balance = $this->get_samiti_opening_balance($samiti_ids, $from_date);
			
			foreach($result as $res){
				$opening_bal = @$samiti_balance[$res['ID']];
				$data = array_merge($res, $opening_bal);
				$dataset[] = $data;
			}
		}
		return $dataset;
	}

	function get_outward_samiti_fisherman($from_date, $to_date, $group_type=''){
		$this->db->select('mg.ID, 
						   mg.Name, 
						   (SELECT 
								CONCAT(SUM(po.cash_received),"/",SUM(po.sub_total)) 
							  FROM '.PRODUCT_OUTWARD.' po 
							  WHERE po.main_group_id=mg.ID AND 
							  		po.status = "Active" AND 
									po.outward_to="Fisherman" AND 
									po.outward_date >="'.$from_date.'" AND 
									po.outward_date <="'.$to_date.'" 
							  GROUP BY po.main_group_id
							) as product_outward,
							
							(SELECT SUM(poir.total_price) 
							 FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
							 WHERE poir.outward_to = "Fisherman" AND 
							 	   poir.status="Active" AND 
								   poir.return_date >= "'.$from_date.'" AND 
								   poir.return_date <= "'.$to_date.'" AND 
								   poir.main_group_id = mg.ID
							 GROUP BY poir.main_group_id
						    ) as returned_amt,
							
							(SELECT 
						   		SUM(wa.Amount)
							  FROM '.WAGES_ADVANCE.' wa 
							  WHERE wa.maingroup_id=mg.ID AND 
							  		wa.status = "Active" AND 
									wa.advance_to = "Fisherman" AND 
									wa.Date >="'.$from_date.'" AND 
									wa.Date <="'.$to_date.'" 
							  GROUP BY wa.maingroup_id
							) as advance_wages,
							
							(SELECT 
						   		(SUM(ld.amount) + IFNULL(SUM(ld.advance_deduction), 0)) 
							  FROM '.LIABILITY_DEDUCTION.' ld 
							  WHERE ld.maingroup_id = mg.ID AND ld.status = "Active" AND ld.from_date >="'.$from_date.'" AND ld.to_date <="'.$to_date.'" 
							  GROUP BY ld.maingroup_id
							) as group_liability_deduction,
							
						   (SELECT 
						   		CONCAT(SUM(wi.GroupLiabilityDeduction), "|", SUM(wi.AdvanceWagesDeduction))
							  FROM '.WAGESITEM.' wi 
							  WHERE wi.MainGroup=mg.ID AND 
							  		wi.status = "Active" AND 
									wi.wages_for="Fisherman" AND 
									wi.from_date >="'.$from_date.'" AND 
									wi.to_date <="'.$to_date.'" 
							  GROUP BY wi.MainGroup
							) as wages_deduction,
								
							
							(SELECT CONCAT(SUM(cdp.product_liability),"/",SUM(cdp.wages_liability)) 
							 FROM '.CASH_DEPOSITED_PAYMENT.' cdp 
							 WHERE cdp.status="Active" AND 
							 	   cdp.deposited_by = "Fisherman" AND 
								   cdp.deposit_date >= "'.$from_date.'" AND 
								   cdp.deposit_date <= "'.$to_date.'" AND 
								   cdp.maingroup_id = mg.ID
							 ) as cash_deposited
						   ');
		$this->db->from(MAINGROUP.' mg');
		if($group_type){
			$this->db->where('mg.Type', $group_type);
		}
		$this->db->order_by('mg.Name', 'ASC');
		$result = $this->db->get()->result_array();
		$data = array();
		if(!empty($result)){
			foreach($result as $res){
				$group_opening_balance = $this->get_group_opening_balance($res['ID'], $from_date);
				$res['opening_balance'] = $group_opening_balance['remaining_liability'];
				$data[] = $res;
			}
		}
		return $data;
	}
	
	function get_group_opening_balance($maingroup, $from_date = ''){
		if($from_date == ''){
			 $from_date = date('Y-m-d');
		}
		
		$sql = 'SELECT mg.Name as group_name,  
				 
				 (SELECT SUM(po.grand_total) 
					FROM '.PRODUCT_OUTWARD.' po 
					WHERE po.outward_to="Fisherman" AND 
						  po.outward_date < "'.$from_date.'" AND 
						  po.main_group_id = "'.$maingroup.'" AND
						  po.status="Active"
				 ) as group_liability,
						   
				(SELECT SUM(poir.total_price) 
					FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
					WHERE poir.outward_to="Fisherman" AND 
						  poir.return_date < "'.$from_date.'" AND 
						  poir.main_group_id = "'.$maingroup.'" AND
						  poir.status="Active"
				 ) as returned_amt,
				 
				(SELECT SUM(wa.Amount) 
					FROM '.WAGES_ADVANCE.' wa 
					WHERE wa.advance_to="Fisherman" AND 
						  wa.Date < "'.$from_date.'" AND 
						  wa.maingroup_id = "'.$maingroup.'" AND
					 	  wa.status="Active" 
			     ) as advance_wages,
				 
				 (SELECT (SUM(products_balance) + SUM(wages_balance)) 
					FROM '.FISHERMAN.' 
					WHERE MainGroup ="'.$maingroup.'"
				 ) as opening_balance,
				 
				(SELECT (SUM(cdp.product_liability) + SUM(cdp.wages_liability)) 
					FROM '.CASH_DEPOSITED_PAYMENT.' cdp 
					WHERE cdp.deposited_by="Fisherman" AND 
						  cdp.deposit_date < "'.$from_date.'" AND 
						  cdp.maingroup_id ="'.$maingroup.'" AND
						  cdp.status="Active"
				 ) as cash_deposited,
				 
				(SELECT SUM(wi.AdvanceWagesDeduction) 
					FROM '.WAGESITEM.' wi 
					WHERE wi.wages_for="Fisherman" AND 
						  wi.from_date < "'.$from_date.'" AND 
						  wi.MainGroup = "'.$maingroup.'" AND
						  wi.status="Active" 
				 ) as advance_wages_deduction,
				 
				(SELECT (SUM(ld.amount) + SUM(ld.advance_deduction)) 
					FROM '.LIABILITY_DEDUCTION.' ld 
					WHERE ld.status="Active" AND 
						  ld.from_date < "'.$from_date.'" AND 
						  ld.maingroup_id = "'.$maingroup.'"
				 ) as group_liability_deducted
				
				FROM '.MAINGROUP.' mg WHERE mg.ID = "'.$maingroup.'"';
		
	  	$result = $this->db->query($sql)->result_array();
	  	$response_data = array('remaining_liability' => 0);
		if(!empty($result)){
			foreach($result as $res){
				$response_data = $res;
				
				$total_liability = (empty($res['group_liability']) ? 0 : $res['group_liability']) + 
								   (empty($res['advance_wages']) ? 0 : $res['advance_wages']) +
								   (empty($res['opening_balance']) ? 0 : $res['opening_balance']);
									
				$deducted_liability = (empty($res['returned_amt']) ? 0 : $res['returned_amt']) +
									  (empty($res['group_liability_deducted']) ? 0 : $res['group_liability_deducted']) +
									  (empty($res['advance_wages_deduction']) ? 0 : $res['advance_wages_deduction']) +
									  (empty($res['cash_deposited']) ? 0 : $res['cash_deposited']);
									  			
				$remaining_liability = $total_liability - $deducted_liability;				
				
				$response_data['remaining_liability'] = $remaining_liability;
			}
		}  
			
		return $response_data;
	}
	
	function get_samiti_opening_balance($maingroup, $from_date){
		if(is_array($maingroup) && !empty($maingroup)){
			$where = ' mg.ID IN('.implode(',', $maingroup).')';
		}else{
			$where = ' mg.ID = "'.$maingroup.'"';
		}
		
		$sql = 'SELECT
					mg.ID as open_ID, 
					mg.Name as open_group_name, 
					mg.product_balance as open_product_balance, 
					mg.wages_balance as open_wages_balance, 
					
					IFNULL((SELECT SUM(po.grand_total) 
							FROM '.PRODUCT_OUTWARD.' po 
							WHERE  
								  po.status="Active" AND 
								  po.outward_to = "Group" AND
								  po.outward_date < "'.$from_date.'" AND 
								  po.main_group_id = mg.ID), 0) as open_group_liability,
					
					IFNULL((SELECT SUM(poir.total_price) 
							FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
							WHERE 
								  poir.status="Active" AND 
								  poir.outward_to = "Group" AND 
								  poir.return_date < "'.$from_date.'" AND 
								  poir.main_group_id = mg.ID), 0) as open_returned_amt,
				
					IFNULL((SELECT SUM(wa.Amount) 
							FROM '.WAGES_ADVANCE.' wa 
							WHERE 
								  wa.status="Active" AND 
								  wa.advance_to="Group" AND 
								  wa.Date < "'.$from_date.'" AND 
								  wa.maingroup_id = mg.ID), 0) as open_advance_wages_amount,
				
					IFNULL((SELECT SUM(wi.GroupLiabilityDeduction+wi.AdvanceWagesDeduction) 
							FROM '.WAGESITEM.' wi 
							WHERE  
								  wi.status="Active" AND 
								  wi.wages_for = "Group" AND
								  wi.from_date < "'.$from_date.'" 
								  AND wi.MainGroup = mg.ID), 0) as open_liability_deduction,
				
					IFNULL((SELECT SUM(cdp.product_liability + cdp.wages_liability) 
							FROM '.CASH_DEPOSITED_PAYMENT.' cdp 
							WHERE cdp.status="Active" AND 
								  cdp.deposited_by = "Group" AND 
								  cdp.deposit_date < "'.$from_date.'" AND 
								  cdp.maingroup_id = mg.ID), 0) as open_cash_deposited
				
				FROM '.MAINGROUP.' mg WHERE '.$where;
		
	  	$result = $this->db->query($sql)->result_array();
		$data = array();
		if(!empty($result)){
			foreach($result as $res){
				$response_data = $res;
				$total_liability = $res['open_group_liability'] + $res['open_advance_wages_amount'] + $res['open_product_balance'] + $res['open_wages_balance'];
									
				$deducted_liability = $res['open_returned_amt'] + $res['open_liability_deduction'] + $res['open_cash_deposited'];
				$remaining_liability = $total_liability - $deducted_liability;				
				$response_data['opening_balance'] = $remaining_liability;
				if(is_array($maingroup) && !empty($maingroup)){
					$data[$res['open_ID']] = $response_data;
				}else{
					$data = $response_data;
				}
			}
		}
		
		return $data;
	}
	
	//2. Outward Fisherman Report (reports/outward/fisherman)
	function get_outward_fisherman($from_date, $to_date, $maingroup = '', $group_type = ''){
						
		$select = '
				CONCAT(fm.Code," - ",fm.Name) as Name, 
				fm.ID,
				mg.Name as Samiti_name, 
				nav_qty,
				jaal_qty,
				product_outward,
				returned_amt,
				advance_wages,
				
				group_liability_deduction,
				
				wages_deduction,
				
				(SELECT GROUP_CONCAT(CONCAT(sfm.Code," - ",sfm.Name)) 
					FROM psac_fisherman sfm 
					WHERE sfm.ID IN (SELECT sf.Secondary FROM '.SECONDARYFISHERMAN.' sf 
									 WHERE 	sf.Primary=fm.ID AND 
											sf.Secondary != fm.ID AND 
											sf.group_status = "Joined" )
				) as secondary_fisherman,
				
				cash_deposited,
				
				IFNULL(opb_product_outward, 0) as opb_product_outward,
				IFNULL(opb_returned_amt, 0) as opb_returned_amt,
				IFNULL(opb_ld_amount, 0) as opb_ld_amount,
				IFNULL(opb_wa_amount, 0) as opb_wa_amount,
				IFNULL(opb_wi_AdvanceWagesDeduction, 0) as opb_wi_AdvanceWagesDeduction,
				IFNULL(opb_cash_deposited, 0) as opb_cash_deposited,
				(fm.products_balance + fm.wages_balance) as opening_balance
				
				';
		
		$this->db->select($select);
		$this->db->from(FISHERMAN.' fm');
		$this->db->join(MAINGROUP.' mg', 'mg.ID = fm.MainGroup', 'LEFT');
		
		
		$this->db->join('(SELECT 
							poi1.fisherman_id, IFNULL(SUM(poi1.quantity), 0) as nav_qty 
						  FROM '.PRODUCT_OUTWARD_ITEM.' poi1 
						  WHERE poi1.product_type = 2 AND 
								poi1.outward_date >= "'.$from_date.'" AND 
								poi1.outward_date <= "'.$to_date.'" AND 
								poi1.status = "Active"
						  GROUP BY poi1.fisherman_id) poi1', 'poi1.fisherman_id = fm.ID', 'LEFT');
				
		
		$this->db->join('(SELECT 
							poi2.fisherman_id, IFNULL(SUM(poi2.quantity), 0) as jaal_qty 
						  FROM '.PRODUCT_OUTWARD_ITEM.' poi2 
						  WHERE poi2.product_type != 2 AND 
								poi2.outward_date >= "'.$from_date.'" AND 
								poi2.outward_date <= "'.$to_date.'" AND 
								poi2.status = "Active"
						  GROUP BY poi2.fisherman_id) poi2', 'poi2.fisherman_id = fm.ID', 'LEFT');
		
		
		$this->db->join('(SELECT 
							po.fisherman_id, CONCAT(IFNULL(SUM(po.cash_received), 0) ,"|", IFNULL(SUM(po.sub_total),0) ) as product_outward 
						  FROM '.PRODUCT_OUTWARD.' po 
						  WHERE po.outward_date >= "'.$from_date.'" AND 
								po.outward_date <= "'.$to_date.'" AND
								po.status = "Active"
						  GROUP BY po.fisherman_id) po', 'po.fisherman_id = fm.ID', 'LEFT');
		
		
		$this->db->join('(SELECT 
							poir.fisherman_id, IFNULL(SUM(poir.total_price), 0) as returned_amt 
						  FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
						  WHERE poir.return_date >= "'.$from_date.'" AND 
								poir.return_date <= "'.$to_date.'" AND
								poir.status = "Active"
						  GROUP BY poir.fisherman_id) poir', 'poir.fisherman_id = fm.ID', 'LEFT');
		
		
		$this->db->join('(SELECT 
							wa.Fisherman, IFNULL(SUM(wa.Amount), 0) as advance_wages
						  FROM '.WAGES_ADVANCE.' wa 
						  WHERE wa.Date >= "'.$from_date.'" AND 
								wa.Date <= "'.$to_date.'" AND
								wa.status = "Active"
						  GROUP BY wa.Fisherman) wa', 'wa.Fisherman = fm.ID', 'LEFT');
		
		$this->db->join('(SELECT 
							ld.deducted_for, (IFNULL(SUM(ld.amount), 0) + IFNULL(SUM(ld.advance_deduction), 0)) as group_liability_deduction
						  FROM '.LIABILITY_DEDUCTION.' ld 
						  WHERE ld.from_date >= "'.$from_date.'" AND 
								ld.to_date <= "'.$to_date.'" AND
								ld.status = "Active"
						  GROUP BY ld.deducted_for) ld', 'ld.deducted_for = fm.ID', 'LEFT');
						  
		$this->db->join('(SELECT 
							wi.FishermanId, CONCAT(IFNULL(SUM(wi.GroupLiabilityDeduction),0), "|", IFNULL(SUM(wi.AdvanceWagesDeduction),0)) as wages_deduction
						  FROM '.WAGESITEM.' wi 
						  WHERE wi.from_date >= "'.$from_date.'" AND 
								wi.to_date <= "'.$to_date.'" AND
								wi.status = "Active"
						  GROUP BY wi.FishermanId) wi', 'wi.FishermanId = fm.ID', 'LEFT');
		
		
		$this->db->join('(SELECT 
							cdp.fisherman_id, CONCAT(IFNULL(SUM(cdp.product_liability),0) , "|" , IFNULL(SUM(cdp.wages_liability),0)) as cash_deposited
						  FROM '.CASH_DEPOSITED_PAYMENT.' cdp 
						  WHERE cdp.deposited_by = "Fisherman" AND
								cdp.deposit_date >= "'.$from_date.'" AND 
								cdp.deposit_date <= "'.$to_date.'" AND
								cdp.status = "Active"
						  GROUP BY cdp.fisherman_id) cdp', 'cdp.fisherman_id = fm.ID', 'LEFT');
		
		
		
		// for opening balance
		
		$this->db->join('(SELECT 
							temp_obpo.Secondary, SUM(temp_obpo.grand_total) as opb_product_outward 
						  FROM (SELECT sf.Secondary, sf.Primary, po.grand_total 
     							FROM '.SECONDARYFISHERMAN.' sf
								LEFT JOIN (SELECT po.fisherman_id, SUM(po.grand_total) as grand_total 
										   FROM '.PRODUCT_OUTWARD.' po 
										   WHERE po.status="Active" AND po.outward_date < "'.$from_date.'" 
										   GROUP BY po.fisherman_id
								) po ON po.fisherman_id = sf.Primary
						  WHERE sf.group_status = "Joined") temp_obpo GROUP BY temp_obpo.Secondary) temp_opb_po', 
						'temp_opb_po.Secondary = fm.ID', 'LEFT');
		
		$this->db->join('(SELECT 
							temp_poir.Secondary, SUM(temp_poir.total_price) as opb_returned_amt 
						  FROM (SELECT sf.Secondary, sf.Primary, poir.total_price 
     							FROM '.SECONDARYFISHERMAN.' sf
								LEFT JOIN (SELECT poir.fisherman_id, SUM(poir.total_price) as total_price 
										   FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
										   WHERE poir.status="Active" AND poir.return_date < "'.$from_date.'" 
										   GROUP BY poir.fisherman_id
										   ) poir ON poir.fisherman_id = sf.Primary
						  WHERE sf.group_status = "Joined") temp_poir GROUP BY temp_poir.Secondary) temp_poir_2', 
						'temp_poir_2.Secondary = fm.ID', 'LEFT');
		
		$this->db->join('(SELECT 
							temp_ld.Secondary, SUM(temp_ld.amount) as opb_ld_amount 
						  FROM (SELECT sf.Secondary, sf.Primary, ld.amount 
     							FROM '.SECONDARYFISHERMAN.' sf
								LEFT JOIN (SELECT ld.deducted_for, SUM(ld.amount) as amount 
										   FROM '.LIABILITY_DEDUCTION.' ld 
										   WHERE ld.status="Active" AND ld.from_date < "'.$from_date.'" 
										   GROUP BY ld.deducted_for
										   ) ld ON ld.deducted_for = sf.Primary
						  WHERE sf.group_status = "Joined") temp_ld GROUP BY temp_ld.Secondary) temp_ld_2', 
						'temp_ld_2.Secondary = fm.ID', 'LEFT');
		
		$this->db->join('(SELECT 
							wa.Fisherman, SUM(wa.Amount) as opb_wa_amount
						  FROM '.WAGES_ADVANCE.' wa 
						  WHERE wa.Date < "'.$from_date.'" AND 
								wa.status = "Active"
						  GROUP BY wa.Fisherman) temp_wa', 'temp_wa.Fisherman = fm.ID', 'LEFT');
		
		$this->db->join('(SELECT 
							wi.FishermanId, SUM(wi.AdvanceWagesDeduction) as opb_wi_AdvanceWagesDeduction
						  FROM '.WAGESITEM.' wi 
						  WHERE wi.from_date < "'.$from_date.'" AND 
								wi.status = "Active"
						  GROUP BY wi.FishermanId) temp_wi', 'temp_wi.FishermanId = fm.ID', 'LEFT');
		
		$this->db->join('(SELECT 
							temp_cdp.fisherman_id, (SUM(temp_cdp.product_liability) + SUM(temp_cdp.wages_liability)) as opb_cash_deposited
						  FROM '.CASH_DEPOSITED_PAYMENT.' temp_cdp 
						  WHERE temp_cdp.deposit_date < "'.$from_date.'" AND 
								temp_cdp.deposited_by = "Fisherman" AND
								temp_cdp.status = "Active"
						  GROUP BY temp_cdp.fisherman_id) temp_cdp', 'temp_cdp.fisherman_id = fm.ID', 'LEFT');
		
		if($group_type){
			$this->db->where('fm.group_type_id', $group_type);
		}
		if($maingroup){
			$this->db->where('fm.MainGroup', $maingroup);
		}
		$this->db->order_by('fm.Name', 'ASC');
		$result = $this->db->get()->result_array();
		$data = array();
		if(!empty($result)){
			foreach($result as $res){				
				$group_liability = $res['opb_product_outward'];
				$advance_wages = $res['opb_wa_amount'];
				$opening_balance = $res['opening_balance'];
				
				$returned_amt = $res['opb_returned_amt'];
				$group_liability_deducted = $res['opb_ld_amount'];
				$advance_wages_deduction = $res['opb_wi_AdvanceWagesDeduction'];
				$cash_deposited = $res['opb_cash_deposited'];
				
				
				$total_liability = $group_liability + $advance_wages + $opening_balance;
				$deducted_liability = $returned_amt + $group_liability_deducted + $advance_wages_deduction + $cash_deposited;				
				$remaining_liability = $total_liability - $deducted_liability;
				
				$res['opening_balance'] = $remaining_liability;
				$data[] = $res;
			}
		}		
		return $data;
	}
		
	//3. Ledger Outward Fisherman Report (reports/ledger_outward_fisherman)
	function get_ledger_outward_fisherman_report($from_date='', $to_date='', $group_type='', $main_group_id='', $fisherman_id=''){
		
		if(!empty($from_date)){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $from_date));
		}
		if(!empty($to_date)){
			$to_date = get_date('Y-m-d', str_replace('/', '-', $to_date));
		}		
		
		$this->db->select('poi.product_type, poi.product_category, poi.quantity,
						   poi.rate, poi.returnable, poi.total_price, poi.outward_date,
						   po.receipt_number,
						   mg.Name as maingroup, 
						   fm.Name as fisherman, fm.Code, 
						   pt.Name as product_type, 
						   pc.Name as product_category, 
						   p.Name as product');
		$this->db->from(PRODUCT_OUTWARD_ITEM.' poi');
		$this->db->join(PRODUCT_OUTWARD.' po', 'po.ID=poi.outward_id', 'LEFT');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=poi.fisherman_id', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=poi.main_group_id', 'LEFT');
		$this->db->join(PRODUCT_TYPE.' pt', 'pt.ID=poi.product_type', 'LEFT');
		$this->db->join(PRODUCT_CATEGORY.' pc', 'pc.ID=poi.product_category', 'LEFT');
		$this->db->join(PRODUCT.' p', 'p.ID=poi.product_id', 'LEFT');
		if(!empty($from_date)){
			$this->db->where('poi.outward_date >=', $from_date);
		};
		if(!empty($to_date)){
			$this->db->where(array('poi.outward_date <='=>$to_date));
		};
		if($group_type){$this->db->where('poi.group_type', $group_type);};
		if($main_group_id){$this->db->where('poi.main_group_id', $main_group_id);};
		if($fisherman_id){$this->db->where('poi.fisherman_id', $fisherman_id);};
		$this->db->where(array('poi.outward_to'=>'Fisherman','poi.status'=>'Active'));
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query();
		$data = array();
		if(!empty($result)){
			$data = $result;
		}
		//printr($data);
		return $data;
	}
	
	//4. Ledger Outward Samiti Report (reports/ledger_outward_samiti)
	function get_ledger_outward_samiti_report($from_date='', $to_date='', $group_type='', $main_group_id=''){
		
		if(!empty($from_date)){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $from_date));
		}
		if(!empty($to_date)){
			$to_date = get_date('Y-m-d', str_replace('/', '-', $to_date));
		}		
		
		$this->db->select('poi.product_type, poi.product_category, poi.quantity,
						   poi.rate, poi.returnable, poi.total_price, poi.outward_date,
						   po.receipt_number,
						   mg.Name as maingroup, 
						   pt.Name as product_type, 
						   pc.Name as product_category, 
						   p.Name as product');
		$this->db->from(PRODUCT_OUTWARD_ITEM.' poi');
		$this->db->join(PRODUCT_OUTWARD.' po', 'po.ID=poi.outward_id', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=poi.main_group_id', 'LEFT');
		$this->db->join(PRODUCT_TYPE.' pt', 'pt.ID=poi.product_type', 'LEFT');
		$this->db->join(PRODUCT_CATEGORY.' pc', 'pc.ID=poi.product_category', 'LEFT');
		$this->db->join(PRODUCT.' p', 'p.ID=poi.product_id', 'LEFT');
		if(!empty($from_date)){
			$this->db->where('poi.outward_date >=', $from_date);
		};
		if(!empty($to_date)){
			$this->db->where(array('poi.outward_date <='=>$to_date));
		};
		if($group_type){$this->db->where('poi.group_type', $group_type);};
		if($main_group_id){$this->db->where('poi.main_group_id', $main_group_id);};
		$this->db->where(array('poi.outward_to'=>'Group','poi.status'=>'Active'));
		$result = $this->db->get()->result_array();
		$data = array();
		if(!empty($result)){
			$data = $result;
		}
		return $data;
	}
	
	//PRODUCTION REPORT FUNCITON START
	//1. Daily Production Report (reports/production/daily)
	function get_daily_production_report($from_date, $to_date, $group_id){
		$data = array();
		$this->db->select('SUM((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt)) as Tmwt,
						   SUM(dti.Localminor) as Localminor,
						   SUM(dti.Swt) as Swt,
						   SUM(dti.Twt) as Twt,
						   ANY_VALUE(dti.MajorFee) as MajorFee,
						   ANY_VALUE(dti.MinorFee) as MinorFee,
						   ANY_VALUE(dti.SawalFee) as SawalFee,
						   ANY_VALUE(mgt.ID) as group_id,
						   ANY_VALUE(mgt.Name) as group_name');
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(MAINGROUP_TYPE.' mgt', 'mgt.ID=dti.group_type', 'LEFT');
		$this->db->where(array( 'dti.status'=>'Active', 'dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'dti.group_type'=>$group_id));
		$this->db->group_by('dti.group_type');
		$result = $this->db->get()->result_array();
		if(!empty($result)){
			$data = $result;
		}
		return $data;
	}
	
	//2. Monthly Production Report (get_monthly_production_report)
	function get_monthly_production_report($from_date, $to_date){
		$group_data = $this->get_all_group_type();
		$data_set = array();
		foreach($group_data as $group){
			$this->db->select('dti.toll_date,
							   ANY_VALUE(dti.group_type) as group_type,
							   SUM((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt)) as major_wt,
							   SUM(dti.Localminor) as minor_wt,
							   SUM(dti.Swt) as sawal_wt,
							   SUM(dti.Twt) as total_wt');
							   
			$this->db->from(DAILYTOLLINFO. ' dti');
			$this->db->where(array( 'dti.status'=>'Active', 'dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'group_type'=>$group['ID']));
			$this->db->group_by('dti.toll_date');
			$this->db->order_by('dti.toll_date', 'ASC');
			$result = $this->db->get()->result_array();
			if(!empty($result)){
				foreach($result as $res){
					$data_set[$res['toll_date']][$group['ID']] = $res;
				}
			}
		}
		return $data_set;
	}
	
	//3. Yearly Production Report (reports/production/yearly_production)
	function get_yearly_production_report($year){
		$group_data = $this->get_all_group_type();
		$data_set = array();
		foreach($group_data as $group){
			$this->db->select('DATE_FORMAT(dti.toll_date, "%m") as toll_date,
							   ANY_VALUE(dti.group_type) as group_type,
							   SUM((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt)) as major_wt,
							   SUM(dti.Localminor) as minor_wt,
							   SUM(dti.Swt) as sawal_wt,
							   SUM(dti.Twt) as total_wt');
							   
			$this->db->from(DAILYTOLLINFO. ' dti');
			$this->db->where(array('YEAR(dti.toll_date)'=>$year, 'dti.status'=>'Active', 'dti.group_type'=>$group['ID']));
			$this->db->group_by('MONTH(dti.toll_date)');
			$this->db->order_by('dti.toll_date', 'ASC');
			$result = $this->db->get()->result_array();
			if(!empty($result)){
				foreach($result as $res){
					$data_set[$res['toll_date']][$group['ID']] = $res;
				}
			}
		}
		return $data_set;
	}
	
	//4. Samiti Production Report (reports/production/samiti)
	function get_samiti_production_report($from_date, $to_date, $group_type_id){
		$this->db->select('fp.ID, fp.Name,
						   (
						     SELECT  
							  CONCAT(SUM((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt)),"|", SUM(dti.Localminor),"|", SUM(dti.Swt),"|", SUM(dti.Twt))
						   	 FROM '.DAILYTOLLINFO.' dti 
							 WHERE dti.group_type = "'.$group_type_id.'" AND dti.toll_date >= "'.$from_date.'" AND dti.toll_date <= "'.$to_date.'" AND dti.Point = fp.ID AND dti.status = "Active" GROUP BY dti.Point
						   ) as toll_data', false);
						   
		$this->db->from(FISHINGPOINTS.' fp');
		$result = $this->db->get()->result_array();
		return $result;
	}
	
	//5. Inactive Fisherman (reports/production/inactive_fisherman)
	function get_inactive_fisherman_report($from_date, $to_date, $maingroup){
		$mgCondition = "";
		if($maingroup){
			$mgCondition = " AND Samiti = ".$maingroup;
		}
		$query = "SELECT GROUP_CONCAT(DISTINCT(CompanyId)) as active_members 
					FROM  ".DAILYTOLLINFO." 
					WHERE status = 'Active' AND toll_date BETWEEN '".$from_date."' AND '".$to_date."' ".$mgCondition."";
		$result = $this->db->query($query)->result_array();
		$whereNotIn = array();
		if(!empty($result)){
			$whereNotIn = explode(',', $result[0]['active_members']);
		}
		$this->db->select('fm.Name as f_name,
					       fm.ID as f_id,
						   fm.Code as f_code,
					       mg.Name as f_maingroup
					      ');
		$this->db->from(FISHERMAN. ' fm');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=fm.MainGroup', 'LEFT');
		$this->db->join(MAINGROUP_TYPE.' gt', 'gt.ID=fm.group_type_id', 'LEFT');
		if($maingroup){
			$this->db->where(array('fm.MainGroup'=>$maingroup));	
		}
		$this->db->where_not_in('fm.ID',$whereNotIn);
		$result = $this->db->get()->result_array();
		return $result;
	}
	//PRODUCTION REPORT FUNCITON END
	
	
	//WAGES REPORT FUNCTION START
	//1. Day Wise Weekly Report (reports/wages/weekly)
	function get_wages_weekly($from_date, $to_date, $group_type=''){
		$this->db->select('dti.Samiti,
						   dti.toll_date, 
						   SUM(dti.Twt) as Twt,
						   
						   SUM((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
						   
						   SUM((dti.Twt * dti.govt_charges)) as govt_deduction,
						   
						   SUM(((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - (dti.Twt * dti.govt_charges))) as net_amount,
						   IFNULL((SELECT SUM(ld.amount+ld.advance_deduction) FROM '.LIABILITY_DEDUCTION.' ld WHERE ld.status="Active" AND ld.from_date >= "'.$from_date.'" AND ld.to_date <= "'.$to_date.'" AND ld.maingroup_id=dti.Samiti), 0) as group_liability_deducted,
						   
						   IFNULL((SELECT SUM(wi.AdvanceWagesDeduction) FROM '.WAGESITEM.' wi WHERE wi.status="Active" AND wi.MainGroup=dti.Samiti AND wi.from_date >= "'.$from_date.'" AND wi.to_date <= "'.$to_date.'"), 0) as AdvanceWagesDeduction,
						   
						   IFNULL((SELECT CONCAT(SUM(cdp.product_liability) ,"|", SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status="Active" AND cdp.deposit_date >= "'.$from_date.'" AND cdp.deposit_date <= "'.$to_date.'" AND cdp.maingroup_id=dti.Samiti), 0) as cash_deposited,
						   
						   mg.Name as maingroup_name
						   ');
		$this->db->from(DAILYTOLLINFO.' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		
		if(!empty($group_type)){
			$this->db->where(array('dti.group_type'=>$group_type));
		}
		$this->db->where(array('dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'dti.status'=>'Active'));
		
		$this->db->group_by(array('dti.toll_date', 'dti.Samiti'));
		$this->db->order_by('dti.toll_date', 'ASC');
		$result = $this->db->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			foreach($result as $res){
				$data_set[$res['Samiti'].'__'.$res['maingroup_name']][$res['toll_date']] = $res;
			}
		}
		return $data_set;
	}
	
	//2. Week Wise Monthly Report (reports/wages/monthly)
	function get_wages_monthly($group_type='', $date_period=array()){	
		$group_type_condition = '';
		if(!empty($group_type)){
			$group_type_condition = " AND dti.group_type = '".$group_type."' ";
		}
	   $sql = '';
	   
	   foreach($date_period as $key => $date){	
		$sql .= "SELECT dti.Samiti, mg.Name as maingroup_name, '".$key."' as date_peroid,
					SUM(dti.Twt) as Twt,			   
					SUM((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
					SUM((dti.Twt * dti.govt_charges)) as govt_deduction,
			   		SUM(((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - (dti.Twt * dti.govt_charges))) as net_amount,

					IFNULL((SELECT SUM(ld.amount+ld.advance_deduction) FROM ".LIABILITY_DEDUCTION." ld WHERE ld.status='Active' AND ld.from_date >= '".$date['from']."' AND ld.to_date <= '".$date['to']."' AND ld.maingroup_id=dti.Samiti), 0) as group_liability_deducted,
						   
					IFNULL((SELECT SUM(wi.AdvanceWagesDeduction) FROM ".WAGESITEM." wi WHERE wi.status='Active' AND wi.MainGroup=dti.Samiti AND wi.from_date >= '".$date['from']."' AND wi.to_date <= '".$date['to']."'), 0) as AdvanceWagesDeduction,

					IFNULL((SELECT CONCAT(SUM(cdp.product_liability) ,'|', SUM(cdp.wages_liability)) FROM ".CASH_DEPOSITED_PAYMENT." cdp WHERE cdp.status='Active' AND cdp.deposit_date >= '".$date['from']."' AND cdp.deposit_date <= '".$date['to']."' AND cdp.maingroup_id=dti.Samiti), 0) as cash_deposited				
				
				FROM ".DAILYTOLLINFO." dti LEFT JOIN ".MAINGROUP." mg ON(mg.ID = dti.Samiti)
				
				WHERE dti.toll_date >=  '".$date['from']."' AND dti.toll_date <= '".$date['to']."' AND dti.status ='Active' ".$group_type_condition." GROUP BY dti.Samiti";
		
		 $sql .= ($key < (count($date_period)-1)) ? ' UNION ALL ' : '';
		
	    }
		
		$sql .= ' ORDER BY date_peroid ASC'; 
		
		$result = $this->db->query($sql)->result_array();
		$data_set = array();
		if(!empty($result)){
			foreach($result as $res){
			    $data_set[$res['Samiti'].'__'.$res['maingroup_name']][$res['date_peroid']] = $res;
			}
		}
		return $data_set;
	}
		
	//3. Month Wise Yearly Report (reports/wages/quarterly)
	function get_wages_month_wise($from_date, $to_date, $group_type=''){
		$this->db->select('dti.Samiti,
							MONTH(dti.toll_date) as toll_date, 
							SUM(dti.Twt) as Twt,
						   
						   SUM((((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
						   
						   SUM((dti.Twt * dti.govt_charges)) as govt_deduction,
						   
						   SUM(((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - (dti.Twt * dti.govt_charges))) as net_amount,
						   
						   
						   (SELECT SUM(ld.amount+ld.advance_deduction) FROM '.LIABILITY_DEDUCTION.' ld WHERE ld.status="Active" AND ld.from_date >= "'.$from_date.'" AND ld.to_date <= "'.$to_date.'" AND ld.maingroup_id=dti.Samiti) as group_liability_deducted,
						   
							(SELECT SUM(wi.AdvanceWagesDeduction) FROM '.WAGESITEM.' wi WHERE wi.status="Active" AND wi.MainGroup=dti.Samiti AND wi.from_date >= "'.$from_date.'" AND wi.to_date <= "'.$to_date.'") as AdvanceWagesDeduction,

							(SELECT CONCAT(SUM(cdp.product_liability) ,"|", SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status="Active" AND cdp.deposit_date >= "'.$from_date.'" AND cdp.deposit_date <= "'.$to_date.'" AND cdp.maingroup_id=dti.Samiti) as cash_deposited,
						      
						   mg.Name as maingroup_name
						   ');
		$this->db->from(DAILYTOLLINFO.' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		
		if(!empty($group_type)){
			$this->db->where(array('dti.group_type'=>$group_type));
		}
		$this->db->where(array('dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'dti.status'=>'Active'));
		
		$this->db->group_by(array('MONTH(dti.toll_date)', 'dti.Samiti'));
		$this->db->order_by('dti.toll_date', 'ASC');
		$result = $this->db->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			foreach($result as $res){
				$data_set[$res['Samiti'].'__'.$res['maingroup_name']][$res['toll_date']] = $res;
			}
		}
		return $data_set;
	}
		
	//4. Fisherman Account Report (reports/wages/account)
	function get_wages_weekly_with_account($from_date, $to_date, $maingroup='', $group_type=''){
		$this->db->select('dti.CompanyId,
						   dti.Samiti,
						   dti.toll_date,
						   
						   dti.MajorFee,
						   dti.MinorFee,
						   dti.SawalFee,
						   SUM((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt)) as Tmwt,			    
						   
						   SUM(dti.Localminor) as Localminor,
						   SUM(dti.Swt) as Swt,
						   SUM(dti.Twt) as Twt,
						   
						   SUM((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))) as total_wages,
						   
						   SUM((dti.Twt * dti.govt_charges)) as govt_deduction,
						   
						   SUM(((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee)) - (dti.Twt * dti.govt_charges))) as net_amount,
						 
						   fm.Name as fishername, 
						   fm.Code as fishername_code, 
						   fm.Bank, 
						   fm.IfscCode, 
						   fm.AccountNo
						   ');
		$this->db->from(DAILYTOLLINFO.' dti');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');		
		if(!empty($group_type) && $group_type != '0'){
			$this->db->where(array('dti.group_type'=>$group_type));
		}
		if(!empty($maingroup) && $maingroup != '0'){
			$this->db->where(array('dti.Samiti'=>$maingroup));
		}
		$this->db->where(array('dti.toll_date >='=>$from_date, 'dti.toll_date <='=>$to_date, 'dti.status'=>'Active'));
		
		$this->db->group_by(array('dti.toll_date', 'dti.CompanyId'));
		$this->db->order_by('dti.toll_date', 'ASC');
		$result = $this->db->get()->result_array();
		$data_set = array();
		
		$company_ids = array();
		$samiti_ids = array();
		
		if(!empty($result)){
			
			$CompanyId_ids = array();
			foreach($result as $res){
				$CompanyId_ids[] = $res['CompanyId'];
			}			
			
			$this->db->select('deducted_for, (IFNULL(SUM(amount),0) + IFNULL(SUM(advance_deduction),0)) as group_liability_deduction');
			
			$this->db->group_start();
			$CompanyId_ids_chunk = array_chunk($CompanyId_ids,25);
			foreach($CompanyId_ids_chunk as $f_ids)
			{
				$this->db->or_where_in('deducted_for', $f_ids);
			}
			$this->db->group_end();
			
			$this->db->where(array('from_date >='=>$from_date, 'to_date <='=>$to_date, 'status'=>'Active'));
			$this->db->group_by('deducted_for');
			$ld_info = $this->db->get(LIABILITY_DEDUCTION)->result_array();			
			$ld_info_data = array(); 
			foreach($ld_info as $ld){
				$ld_info_data[$ld['deducted_for']] = $ld['group_liability_deduction'];
			}
			
			$this->db->select('FishermanId, IFNULL(SUM(GroupLiabilityDeduction), 0) as GroupLiabilityDeduction, IFNULL(SUM(AdvanceWagesDeduction), 0) as AdvanceWagesDeduction');
			
			$this->db->group_start();
			$CompanyId_ids_chunk = array_chunk($CompanyId_ids,25);
			foreach($CompanyId_ids_chunk as $f_ids)
			{
				$this->db->or_where_in('FishermanId', $f_ids);
			}
			$this->db->group_end();
			
			$this->db->where(array('from_date >='=>$from_date, 'to_date <='=>$to_date, 'status'=>'Active'));
			$this->db->group_by('FishermanId');
			$wi_info = $this->db->get(WAGESITEM)->result_array();			
			$wi_info_data = array();			
			foreach($wi_info as $wi){
			    $fid = (int)$wi['FishermanId'];
			    $wi_info_data[$fid] = $wi;
			}
			
			$this->db->select('fisherman_id, IFNULL(SUM(product_liability),0) as cash_product_liability, IFNULL(SUM(wages_liability),0) as cash_wages_liability');
			
			$this->db->group_start();
			$CompanyId_ids_chunk = array_chunk($CompanyId_ids,25);
			foreach($CompanyId_ids_chunk as $f_ids)
			{
				$this->db->or_where_in('fisherman_id', $f_ids);
			}
			$this->db->group_end();			
			
			$this->db->where(array('deposit_date >='=>$from_date, 'deposit_date <='=>$to_date, 'status'=>'Active'));
			$this->db->group_by('fisherman_id');
			$cdp_info = $this->db->get(CASH_DEPOSITED_PAYMENT)->result_array();			
			$cdp_info_data = '';			
			foreach($cdp_info as $cdp){
				$cdp_info_data[$cdp['fisherman_id']] = $cdp;
			}			
			
			foreach($result as $res){
				$res['group_liability_deduction'] = (isset($ld_info_data[$res['CompanyId']]) ? $ld_info_data[$res['CompanyId']] : 0) ;
					
				$res['GroupLiabilityDeduction'] = (isset($wi_info_data[$res['CompanyId']]['GroupLiabilityDeduction']) ? $wi_info_data[$res['CompanyId']]['GroupLiabilityDeduction'] : 0);	
				$res['AdvanceWagesDeduction'] = (isset($wi_info_data[$res['CompanyId']]['AdvanceWagesDeduction']) ? $wi_info_data[$res['CompanyId']]['AdvanceWagesDeduction']: 0);	
				
				$res['cash_product_liability'] = (isset($cdp_info_data[$res['CompanyId']]['cash_product_liability']) ? $cdp_info_data[$res['CompanyId']]['cash_product_liability'] : 0);	
				$res['cash_wages_liability'] = (isset($cdp_info_data[$res['CompanyId']]['cash_wages_liability']) ? $cdp_info_data[$res['CompanyId']]['cash_wages_liability'] : 0);	
				
				$key = $res['CompanyId'].'__'.$res['fishername'].'__'.$res['fishername_code'].'__'.$res['IfscCode'].'__'.$res['AccountNo'];
				$data_set[$key][$res['toll_date']] = $res;	
			}
						
		}
		return $data_set;
	}
	
	//5. Fisherman Wages Report (reports/wages/fisherman_wages)
	function get_fisherman_wages_with_account($from_date, $to_date, $maingroup='', $group_type = ''){
		$this->db->select('wi.FishermanId, 
						   wi.from_date,
						   wi.to_date,
						   				   
						   wi.MajorFee,
						   wi.MinorFee,
						   wi.SawalFee,
						   						    
						   SUM(wi.major_wt) as major_wt,
						   SUM(wi.minor_wt) as minor_wt,
						   SUM(wi.sawal_wt) as sawal_wt,					   
						   SUM(wi.major_wt + wi.minor_wt + wi.sawal_wt) as total_wt,
						   
						   SUM(wi.TotalWage) as total_wages,					   
						   SUM(wi.GovDeduction) as govt_deduction,
						   
						   SUM(wi.final_wages) as final_wages,
						   
						   SUM(wi.GroupLiabilityDeduction) as GroupLiabilityDeduction,
						   SUM(wi.AdvanceWagesDeduction) as AdvanceWagesDeduction,
						   mg.Name as maingroup_name, fm.Name as fisher_name, fm.Code as fisher_code, fm.Bank, fm.IfscCode, fm.AccountNo
						   ');
		
		$this->db->from(WAGESITEM.' wi');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=wi.FishermanId', 'LEFT');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=wi.MainGroup', 'LEFT');
		if(!empty($group_type) && $group_type != '0'){
			$this->db->where(array('wi.group_type_id'=>$group_type));
		}
			
		if(!empty($maingroup) && $maingroup != '0'){
			$this->db->where(array('wi.MainGroup'=>$maingroup));
		}
		
		$this->db->where(array('wi.status'=>'Active', 'wi.wages_for'=>'Fisherman', 'wi.from_date >='=>$from_date, 'wi.to_date <='=>$to_date));
		
		$this->db->group_by(array('wi.FishermanId'));
		$this->db->order_by('fisher_code', 'ASC');
		$result = $this->db->get()->result_array();
				
		$data_set = array();
		if(!empty($result)){
			$fisherman_ids = array();
			foreach($result as $res){
				$fisherman_ids[] = $res['FishermanId'];
			}
			//(Cwt+Rwt+Mwt+Kwt+Awt+Lwt) as 
			$this->db->select('CompanyId, toll_date, (Cwt+Rwt+Mwt+Kwt+Awt+Lwt) as Tmwt, Localminor, Swt');
			
			$this->db->group_start();
			$fisherman_ids_chunk = array_chunk($fisherman_ids, 25);
			foreach($fisherman_ids_chunk as $f_ids)
			{
				$this->db->or_where_in('CompanyId', $f_ids);
			}
			$this->db->group_end();
			
			$this->db->where(array('status'=>'Active', 'toll_date >='=>$from_date, 'toll_date <='=>$to_date));
			$toll_info = $this->db->get(DAILYTOLLINFO)->result_array();
			//printr($toll_info);
			
			$toll_info_data = array();
			
			foreach($toll_info as $t_info){
			    $CompanyId = (int)$t_info['CompanyId'];
				$toll_info_data[$CompanyId][] = $t_info;
			}
			
			foreach($result as $res){
				$res['dailytollinfo'] = $toll_info_data[$res['FishermanId']];
				$data_set[] = $res;
			}			
		}
		return $data_set;
	}
	
	//6. Samiti Account Report (reports/wages/samiti_account)
	function get_wages_weekly_with_samiti_account($from_date, $to_date, $group_type = ''){
		$this->db->select('wi.MainGroup, 
						   wi.from_date,
						   wi.to_date,
						   				   
						   wi.MajorFee,
						   wi.MinorFee,
						   wi.SawalFee,
						   						    
						   SUM(wi.major_wt) as major_wt,
						   SUM(wi.minor_wt) as minor_wt,
						   SUM(wi.sawal_wt) as sawal_wt,
						   
						   SUM(wi.major_wt + wi.minor_wt + wi.sawal_wt) as total_wt,
						   
						   SUM(wi.TotalWage) as total_wages,					   
						   SUM(wi.GovDeduction) as govt_deduction,
						   
						   SUM(wi.final_wages) as final_wages,
						   
						   SUM(wi.GroupLiabilityDeduction) as GroupLiabilityDeduction,
						   SUM(wi.AdvanceWagesDeduction) as AdvanceWagesDeduction,
						   
						   
							
						   	mg.Name as maingroup_name, 
							mg.bank_name, 
							mg.ifsc_code, 
							mg.account_number
						   ');
							
		$this->db->from(WAGESITEM.' wi');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=wi.MainGroup', 'LEFT');
		
		//$this->db->where('MainGroup', 94);
		
		if(!empty($group_type) && $group_type != '0'){
			$this->db->where(array('wi.group_type_id'=>$group_type));
		}
		
		$this->db->where(array('wi.from_date >='=>$from_date, 
							   'wi.to_date <='=>$to_date, 
							   'wi.status'=>'Active',
							   'wi.wages_for'=>'Fisherman'
							   ));
		
		$this->db->group_by(array('wi.MainGroup'));
		$this->db->order_by('maingroup_name', 'ASC');
		$result = $this->db->get()->result_array();
		
		$data_set = array();
		if(!empty($result)){
			$Samiti_ids = array();
			foreach($result as $res){
				$Samiti_ids[] = $res['MainGroup'];
			}
			
			$this->db->select('Samiti, toll_date, (Cwt+Rwt+Mwt+Kwt+Awt+Lwt) as Tmwt, Localminor, Swt');
			
			$this->db->group_start();
			$Samiti_ids_chunk = array_chunk($Samiti_ids, 25);
			foreach($Samiti_ids_chunk as $s_ids)
			{
				$this->db->or_where_in('Samiti', $s_ids);
			}
			$this->db->group_end();
			
			$this->db->where(array('toll_date >='=>$from_date, 'toll_date <='=>$to_date, 'status'=>'Active'));
			$toll_info = $this->db->get(DAILYTOLLINFO)->result_array();
			
			$toll_info_data = array();
			
			foreach($toll_info as $key => $t_info){
			    $toll_info_data[(int)$t_info['Samiti']][] = $t_info;
			}
			
			foreach($result as $res){
			    //printr($res);
				$res['dailytollinfo'] = $toll_info_data[$res['MainGroup']];
				$data_set[] = $res;
			}
		}		
		return $data_set;
	}
	
	//7. Samiti Report (reports/wages/samiti)
	function get_wages_samiti($from_date, $to_date, $maingroup){
		$this->db->select('wi.from_date, 
						   wi.to_date, 
						   wi.TotalWage, 
						   wi.group_liability, 
						   wi.advance_wages, 
						   wi.GovDeduction, 
						   wi.GroupLiabilityDeduction,
						   wi.AdvanceWagesDeduction,
						   wi.final_wages,
						   fm.Name as f_name
						  ');
		$this->db->from(WAGESITEM.' wi');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=wi.FishermanId', 'LEFT');
		if(!empty($maingroup)){
			$this->db->where(array('wi.MainGroup'=>$maingroup));
		}
		$this->db->where(array('wi.from_date >='=>$from_date, 'wi.to_date <='=>$to_date, 'wi.status'=>'Active'));
		//$this->db->group_by('wi.FishermanId');
		$this->db->order_by('wi.FishermanId', 'ASC');
		$result = $this->db->get()->result_array();
		return $result;
	}

	//8. Overall Fisherman Report (reports/wages/overall_fisherman)
	function get_overall_fisherman($from_date, $to_date, $maingroup){
		$this->db->select('fm.Name as f_name, fm.ID as f_id,
						   IFNULL(SUM((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt)), 0) as major_wt,
						   IFNULL(SUM(dti.Localminor), 0) as minor_wt,
						   IFNULL(SUM(dti.Swt), 0) as sawal_wt,
						   IFNULL(SUM(dti.Twt), 0) as total_wt,
						   IFNULL(SUM((( (dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))), 0) as total_amount,
						   COUNT(dti.CompanyId) as present_days,
						   IFNULL((SELECT SUM(wa.Amount) FROM '.WAGES_ADVANCE.' wa WHERE wa.status="Active" AND wa.Fisherman=dti.CompanyId AND wa.advance_to="Fisherman" AND wa.Date >="'.$from_date.'" AND wa.Date <="'.$to_date.'" AND wa.maingroup_id="'.$maingroup.'" GROUP BY wa.Fisherman), 0) as advance_wages,
						   IFNULL((SELECT (SUM(cdp.product_liability) + SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status="Active" AND cdp.deposited_by="Fisherman" AND cdp.deposit_date >= "'.$from_date.'" AND cdp.deposit_date <= "'.$to_date.'" AND cdp.fisherman_id = dti.CompanyId AND cdp.maingroup_id="'.$maingroup.'"), 0) as cash_deposit
						   
						  ');
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(FISHERMAN.' fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->db->where('dti.status', 'Active');
		$this->db->where('dti.toll_date >=', $from_date);
		$this->db->where('dti.toll_date <=', $to_date);
		$this->db->where('dti.Samiti', $maingroup);
		$this->db->group_by('dti.CompanyId');
		$result = $this->db->get()->result_array();
		$data = array();
		if(!empty($result)){
			foreach($result as $res){
				$group_opening_balance = $this->get_fisherman_opening_balance($res['f_id'], $from_date);
				$res['opening_balance'] = $group_opening_balance['remaining_liability'];
				$data[] = $res;
			}
		}
		return $data;
	}	
	
	function get_fisherman_opening_balance($FishermanId, $from_date = ''){
		if($from_date == ''){
			 $from_date = date('Y-m-d');
		}
		
		$sql = 'select 
				(SELECT SUM(po.grand_total) 
					FROM '.PRODUCT_OUTWARD.' po 
					WHERE po.status="Active" AND 
						  po.outward_to="Fisherman" AND 
						  po.outward_date < "'.$from_date.'" AND 
						  po.fisherman_id IN(SELECT sf.Primary 
											 FROM '.SECONDARYFISHERMAN.' sf 
											 WHERE sf.Secondary = "'.$FishermanId.'" AND 
												   sf.group_status = "Joined")) as group_liability,
				
				(SELECT SUM(ld.amount) FROM '.LIABILITY_DEDUCTION.' ld 
					WHERE ld.from_date < "'.$from_date.'" AND 
						  ld.deducted_for IN(SELECT sf.Primary 
											 FROM '.SECONDARYFISHERMAN.' sf 
											 WHERE sf.Secondary = "'.$FishermanId.'" AND 
												   sf.group_status = "Joined")) as group_liability_deducted,
						   
				(SELECT SUM(poir.total_price) 
					FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir 
					WHERE poir.status="Active" AND 
						  poir.outward_to="Fisherman" AND
						  poir.return_date < "'.$from_date.'" AND 
						  poir.fisherman_id IN(SELECT sf.Primary 
												FROM '.SECONDARYFISHERMAN.' sf 
												WHERE sf.Secondary = "'.$FishermanId.'" AND 
													  sf.group_status = "Joined")) as returned_amt,
				
				(SELECT SUM(wa.Amount) 
					FROM '.WAGES_ADVANCE.' wa 
					WHERE wa.status="Active" AND 
						  wa.advance_to="Fisherman" AND
						  wa.Date < "'.$from_date.'" AND 
						  wa.Fisherman="'.$FishermanId.'") as advance_wages,
				
				(SELECT SUM(wi.AdvanceWagesDeduction) 
					FROM '.WAGESITEM.' wi 
					WHERE wi.status="Active" AND 
						  wi.wages_for="Fisherman" AND
						  wi.from_date < "'.$from_date.'" AND 
						  wi.FishermanId="'.$FishermanId.'") as advance_wages_deduction,
				
				(SELECT (SUM(products_balance) + SUM(wages_balance)) 
					FROM '.FISHERMAN.' 
					WHERE ID ="'.$FishermanId.'") as opening_balance,
				
				(SELECT (SUM(cdp.product_liability) + SUM(cdp.wages_liability)) 
					FROM '.CASH_DEPOSITED_PAYMENT.' cdp 
					WHERE cdp.status="Active" AND 
						  cdp.deposited_by = "Fisherman" AND 
						  cdp.deposit_date < "'.$from_date.'" AND 
						  cdp.fisherman_id ="'.$FishermanId.'") as cash_deposited';
		
	  	$result = $this->db->query($sql)->result_array();
	  	$response_data = array('remaining_liability' => 0);
		if(!empty($result)){
			foreach($result as $res){
				$response_data = $res;
				$total_liability = $res['group_liability'] + $res['advance_wages'] + $res['opening_balance'];
				$deducted_liability = $res['returned_amt'] + $res['group_liability_deducted'] + $res['advance_wages_deduction'] + $res['cash_deposited'];				
				$remaining_liability = $total_liability - $deducted_liability;
				$response_data['remaining_liability'] = $remaining_liability;
			}
		}
		return $response_data;
	}
	
	function get_group_advance_payment($from_date, $to_date, $maingroup){
		$sql = "SELECT 'advance' as type, 
						CASE 
						   	WHEN wa.Remark = '' THEN 'Advance wages to samiti'
							WHEN wa.Remark != '' THEN wa.Remark
						END as particulars, wa.Amount as debit, '0' as credit, wa.Date as date
						FROM ".WAGES_ADVANCE." wa 
						WHERE wa.status = 'Active' AND 
							  wa.Date >= '".$from_date."' AND 
							  wa.Date <= '".$to_date."' AND 
							  wa.maingroup_id = '". $maingroup."' AND 
							  wa.advance_to = 'Group'
					
					UNION ALL 
					
					SELECT 'product_deposit' as type, 
						   CASE 
						   	WHEN cdp.remark = '' THEN 'Cash Deposit For Product Liability'
							WHEN cdp.remark != '' THEN cdp.remark
						   END as particulars, '0' as debit, cdp.product_liability as credit,  cdp.deposit_date as date 
						FROM ".CASH_DEPOSITED_PAYMENT." cdp 
						WHERE cdp.product_liability != '0' AND 
							  cdp.status = 'Active' AND  
							  cdp.deposited_by = 'Group' AND
							  cdp.deposit_date >= '".$from_date."' AND 
							  cdp.deposit_date <= '".$to_date."' AND 
							  cdp.maingroup_id = '". $maingroup."'
					
					UNION ALL 
					
					SELECT 'wages_deposit' as type, 
						   CASE 
						   	WHEN cdp.remark = '' THEN 'Cash Deposit For Advance Wages'
							WHEN cdp.remark != '' THEN cdp.remark
						   END as particulars, '0' as debit, cdp.wages_liability as credit, cdp.deposit_date as date 
						FROM ".CASH_DEPOSITED_PAYMENT." cdp 
						WHERE cdp.wages_liability != '0' AND 
							  cdp.status = 'Active' AND 
							  cdp.deposited_by = 'Group' AND
							  cdp.deposit_date >= '".$from_date."' AND 
							  cdp.deposit_date <= '".$to_date."' AND 
							  cdp.maingroup_id = '". $maingroup."'";
		
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	//9. Overall Samiti Report
	function get_overall_samiti($from_date, $to_date, $maingroup = '', $group_type = ''){
		$this->db->select('mg.Name as group_name,
						   dti.Samiti as maingroup_id,
						   IFNULL(SUM((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt)), 0) as major_wt,
						   IFNULL(SUM(dti.Localminor), 0) as minor_wt,
						   IFNULL(SUM(dti.Swt), 0) as sawal_wt,
						   IFNULL(SUM(dti.Twt), 0) as total_wt,
						   IFNULL(SUM((((dti.Cwt+dti.Rwt+dti.Mwt+dti.Kwt+dti.Awt+dti.Lwt) * dti.MajorFee) + (dti.Localminor * dti.MinorFee) + (dti.Swt * dti.SawalFee))), 0) as total_amount,
						   COUNT(dti.Samiti) as present_days,
						   
						   (SELECT 
								CONCAT(SUM(po.cash_received),"/",SUM(po.sub_total)) 
							  FROM '.PRODUCT_OUTWARD.' po 
							  WHERE po.outward_to = "Group" AND po.main_group_id=dti.Samiti AND po.status = "Active" AND po.outward_date >="'.$from_date.'" AND po.outward_date <="'.$to_date.'") as product_outward,
						   
						   IFNULL((SELECT SUM(poir.total_price) FROM '.PRODUCT_OUTWARD_ITEM_RETURN.' poir WHERE poir.outward_to = "Group" AND poir.status="Active" AND poir.return_date >= "'.$from_date.'" AND poir.return_date <= "'.$to_date.'" AND poir.main_group_id = dti.Samiti), 0) as returned_amt,
						   
						   IFNULL((SELECT 
						   		SUM(wa.Amount)
							  FROM '.WAGES_ADVANCE.' wa 
							  WHERE wa.advance_to = "Group" AND wa.maingroup_id=dti.Samiti AND wa.status = "Active" AND wa.Date >="'.$from_date.'" AND wa.Date <="'.$to_date.'"), 0) as advance_wages,
						   
						   (SELECT 
						   		CONCAT(SUM(wi.GroupLiabilityDeduction),"/",SUM(wi.AdvanceWagesDeduction)) 
							  FROM '.WAGESITEM.' wi 
							  WHERE wi.wages_for = "Group" AND wi.MainGroup=dti.Samiti AND wi.status = "Active" AND wi.from_date >="'.$from_date.'" AND wi.to_date <="'.$to_date.'" ) as liability_deduction,
							
							(SELECT CONCAT(SUM(cdp.product_liability),"/",SUM(cdp.wages_liability)) FROM '.CASH_DEPOSITED_PAYMENT.' cdp WHERE cdp.status="Active" AND cdp.deposited_by = "Group" AND cdp.deposit_date >= "'.$from_date.'" AND cdp.deposit_date <= "'.$to_date.'" AND cdp.maingroup_id = dti.Samiti) as cash_deposited
						   
						   ');
		$this->db->from(DAILYTOLLINFO. ' dti');
		$this->db->join(MAINGROUP.' mg', 'mg.ID=dti.Samiti', 'LEFT');
		$this->db->where('dti.status', 'Active');
		$this->db->where('dti.toll_date >=', $from_date);
		$this->db->where('dti.toll_date <=', $to_date);
		if(is_array($maingroup) && !empty($maingroup)){
			$this->db->where_in('dti.Samiti', $maingroup);
		}
		if(isset($group_type) && !empty($group_type)){
			$this->db->where('dti.group_type', $group_type);
		}
		$this->db->group_by('dti.Samiti');
		$result = $this->db->get()->result_array();
		
		$dataset = array();
		if(!empty($result)){
			$samiti_ids = array();
			foreach($result as $res){
				$samiti_ids[] = $res['maingroup_id'];
			}
			$samiti_balance = $this->get_samiti_opening_balance($samiti_ids, $from_date);
			
			foreach($result as $res){
				$opening_bal = @$samiti_balance[$res['maingroup_id']];
				$data = array_merge($res, $opening_bal);
				$dataset[] = $data;
			}
		}
		return $dataset;
	}

}
