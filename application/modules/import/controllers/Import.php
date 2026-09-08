<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Import extends MY_Controller {
	var $old;
	function __construct(){
		parent::__construct();
		$this->group_type = array('Samiti'=>'1','DGroup'=>'2','DSamiti'=>'3','Samuha'=>'4');
	
	}
	
	function dailytoll(){
		$result = $this->db->get('dailytoll')->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			foreach($result as $res){
				if($i == 300){
					$i=0;
					$z++;
				};
				
				$data_set[$z][] = array('ID' 		=> $res['ID'],
										'Date' 		=> get_date('Y-m-d', str_replace('/', '-', $res['Date'])),
										'Point' 	=> $res['Point'],
										'page_code' => $res['ID'],
										'added_by' 	=> 1,
										'added_date' => get_date('Y-m-d H:i:s'),
										'action_microtime' => microtime(true)
										);
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(DAILYTOLL, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}
	
	function dailytoll_info(){
		$result = $this->old->get('dailytollinfo')->result_array();
		
		$this->old->select('dti.*, fm.MajorFee, fm.MinorFee, fm.SawalFee, mg.Type, mg.ID as samiti_id');
		$this->old->from('dailytollinfo dti');
		$this->old->join('fisherman fm', 'fm.ID=dti.CompanyId', 'LEFT');
		$this->old->join('maingroup mg', 'mg.Name=dti.Samiti', 'LEFT');
		//$this->old->limit(10);
		$result = $this->old->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			$group = array('Samiti'=>'1','DGroup'=>'2','DSamiti'=>'3','Samuha'=>'4');
			foreach($result as $res){
				if($i == 100){
					$i=0;
					$z++;
				};
				
				$gov_charges = 0;
				if($res['Type'] == 'Samiti'){ $gov_charges = 4;}
				
				$data_set[$z][] = array('ID' 			=> $res['ID'],
										'DailytollId' 	=> $res['DailytollId'],
										'group_type' 	=> @$group[$res['Type']],
										'govt_charges' 	=> $gov_charges,
										'MajorFee' 		=> $res['MajorFee'],
										'MinorFee' 		=> $res['MinorFee'],
										'SawalFee' 		=> $res['SawalFee'],
										'Samiti' 		=> $res['samiti_id'],
										'CompanyId' 	=> $res['CompanyId'],
										'Name' 			=> $res['Name'],
										'Cqty' 			=> $res['Cqty'],
										'Cwt' 			=> $res['Cwt'],
										'Rqty' 			=> $res['Rqty'],
										'Rwt' 			=> $res['Rwt'],
										'Mqty' 			=> $res['Mqty'],
										'Mwt' 			=> $res['Mwt'],
										'Kqty' 			=> $res['Kqty'],
										'Kwt' 			=> $res['Kwt'],
										'Aqty' 			=> $res['Aqty'],
										'Awt' 			=> $res['Awt'],
										'Sqty' 			=> $res['Sqty'],
										'Swt' 			=> $res['Swt'],
										'Lqty' 			=> $res['Lqty'],
										'Lwt' 			=> $res['Lwt'],
										'Localminor' 	=> $res['Localminor'],
										'Tmqty' 		=> $res['Tmqty'],
										'Tmwt' 			=> $res['Tmwt'],
										'Tqty' 			=> $res['Tqty'],
										'Twt' 			=> $res['Twt'],
										'added_by' 		=> 1,
										'added_date' 	=> get_date('Y-m-d H:i:s'),
										'action_microtime' => microtime(true),
										);
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 0;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(DAILYTOLLINFO, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}
	
	function update_dailyinfo(){
		$this->db->select('fm.ID, fm.MajorFee, fm.MinorFee, fm.SawalFee');
		$this->db->from(FISHERMAN.' fm');
		$result = $this->db->get()->result_array();
		//printr($result);
		if(!empty($result) && count($result)>0){
			$query = '';
			foreach($result as $res){
				$query = "UPDATE `psac_dailytollinfo` SET `MajorFee` = '".$res['MajorFee']."', `MinorFee` = '".$res['MinorFee']."', `SawalFee` = '".$res['SawalFee']."' WHERE `psac_dailytollinfo`.`CompanyId` = ".$res['ID'];
				//$this->db->query($query);
				echo $query."<br>";
			}
			
			//$this->db->query($query); DAILYTOLLINFO
		}
	}
	
	function fisherman(){
		$this->old->select('fm.*,mg.Type');
		$this->old->from('fisherman fm');
		$this->old->join('maingroup mg', 'mg.ID=fm.MainGroup', 'LEFT');
		$result = $this->old->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			$group = array('Samiti'=>'1','DGroup'=>'2','DSamiti'=>'3','Samuha'=>'4');
			foreach($result as $res){
				if($i == 300){
					$i=0;
					$z++;
				};
				
				$data_set[$z][] = array('Code'			=> $res['Code'],
									  'Name'			=> $res['Name'],
									  'Contact'			=> $res['Contact'],
									  'doc_name'		=> 'Adhaar',
									  'doc_number'		=> $res['Adhaar'],
									  'AccountNo'		=> $res['AccountNo'],
									  'IfscCode'		=> $res['IfscCode'],
									  'Bank'			=> $res['Bank'],
									  'Branch'			=> $res['Branch'],
									  'Village'			=> $res['Village'],
									  'MajorFee'		=> $res['MajorFee'],
									  'MinorFee'		=> $res['MinorFee'],
									  'SawalFee'		=> $res['SawalFee'],
									  'group_type_id'	=> $group[$res['Type']],
									  'MainGroup'		=> $res['MainGroup'],
									  'Remark'			=> $res['Remark'],
									  'added_by'		=> 1,
									  'added_date'		=> get_datetime('Y-m-d H:i:s'),
									  'action_microtime'=> microtime(true),
									  );
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(FISHERMAN, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}
	
	function secondaryfisherman(){
		
		$this->old->select('sfm.*, fm.MainGroup, mg.Type as GTYPE');
		$this->old->from('secondaryfisherman sfm');
		$this->old->join('fisherman fm', 'fm.ID = sfm.Primary', 'LEFT');
		$this->old->join('maingroup mg', 'mg.ID = fm.MainGroup', 'LEFT');
		$result = $this->old->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 300){
					$i=0;
					$z++;
				};
				
				$data_set[$z][] = array(  'ID'			  => $res['ID'],
										  'group_type_id' => @$this->group_type[$res['GTYPE']],
										  'maingroup_id'  =>  $res['MainGroup'],
				
										  'Primary'			=> $res['Primary'],
										  'Secondary'		=> $res['Secondary'],
										  'Type'			=> $res['Type'],
										  'added_by'		=> 1,
										  'added_date'		=> get_datetime('Y-m-d H:i:s'),
										  'action_microtime'=> microtime(true),
									  );
				$i++;					
			}
		}
		printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(SECONDARYFISHERMAN, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	
	}
	
	function product_invard(){
		$result = $this->old->get('inward')->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 300){
					$i=0;
					$z++;
				};
				
				$data_set[$z][] = array(  'PID' 			=> $res['JaalProduct'],
										  'PType' 			=> 'Jaal',
										  'inward_date'		=> get_date('Y-m-d', str_replace('/', '-',$res['Date'])),
										  'quantity' 		=> $res['Quantity'],
										  'added_by'		=> 1,
										  'added_date'		=> get_datetime('Y-m-d H:i:s'),
										  'action_microtime'=> microtime(true),
										  );
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(PRODUCT_INWARD, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}
	
	function product_invard_update(){
		$this->db->select('pi.*, p.ID as p_id, p.type, p.Category_Id');
		$this->db->from(PRODUCT_INWARD.' pi');
		$this->db->join(PRODUCT.' p', 'p.current_id=pi.PID AND p.type=2', 'LEFT');
		$result = $this->db->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 300){
					$i=0;
					$z++;
				};
				
				echo "<br>".$sql = "UPDATE psac_product_inward SET product_type ='".$res['type']."', product_category ='".$res['Category_Id']."', product_id ='".$res['p_id']."' WHERE ID = '".$res['ID']."';";
				
				$data_set[$z][] = array( 
										  'product_type' 	=> $res['type'],
										  'product_category'=> $res['Category_Id'],
										  'product_id' 		=> $res['p_id']
										  );
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(PRODUCT_INWARD, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}
	
	function product_outward_JAAL(){
		$this->old->select('o.*, mg.Type ');
		$this->old->from('outward o');
		$this->old->join('maingroup mg', 'mg.ID = o.Samiti', 'LEFT');
		$result = $this->old->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
								
				$data_set[$z][] = array(  'ID'				 => $res['ID'],
										  'receipt_number' 	 => $res['ReceiptNo'],
										  'group_type'		 => @$this->group_type[$res['Type']],
										  'main_group_id' 	 => $res['Samiti'],
										  'fisherman_id' 	 => $res['Fisherman'],
										  'sub_total' 		 => $res['Total'],
										  'cash_received' 	 => $res['CashRecieved'],
										  'grand_total' 	 => ($res['Total'] - $res['CashRecieved']),
										  'outward_date' 	 => get_date('Y-m-d', str_replace('/', '-', $res['Date'])),
										  'remark' 		     => $res['Remark'],
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(PRODUCT_OUTWARD, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}

	function product_outward_item(){
		$this->old->select('oi.*, fm.MainGroup, mg.Type ');
		$this->old->from('outwardnavitem oi');
		$this->old->join('fisherman fm', 'fm.ID = oi.Fisherman', 'LEFT');
		$this->old->join('maingroup mg', 'mg.ID = fm.MainGroup', 'LEFT');
		$result = $this->old->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
								
				$data_set[$z][] = array(  'outwardnav_id' 	 => $res['OutwardNavId'],
										  'group_type'		 => @$this->group_type[$res['Type']],
										  'main_group_id' 	 => $res['MainGroup'],
										  'fisherman_id' 	 => $res['Fisherman'],
										  'product_type'	 => 1,
										  'product_category' => '',
										  'product_id'		 => $res['NavProduct'],
										  'quantity'		=> $res['Quantity'],
										  'rate'			=> $res['Rate'],
										  'returnable'		=> ($res['Returns']==='1')? 'No' : 'Yes',
										  'total_price' 	=> $res['Total'],										 
										 
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(PRODUCT_OUTWARD_ITEM, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}

	function product_outward_item_update(){
		$this->db->select('oi.ID, p.ID as productID, p.Category_Id as CategoryID');
		$this->db->from(PRODUCT_OUTWARD_ITEM .' oi');
		$this->db->join(PRODUCT .' p', 'p.current_id = oi.product_id AND type=1', 'LEFT');
		$this->db->where(array('oi.product_type' => '1'));
		$result = $this->db->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
				
				echo "<br>"."UPDATE psac_product_outward_item SET product_category = '".$res['CategoryID']."', product_id = '".$res['productID']."' WHERE ID='".$res['ID']."'; ";
				
				/*$data_set[$z][] = array(  'outwardnav_id' 	 => $res['OutwardNavId'],
										  'group_type'		 => @$this->group_type[$res['Type']],
										  'main_group_id' 	 => $res['MainGroup'],
										  'fisherman_id' 	 => $res['Fisherman'],
										  'product_type'	 => 1,
										  'product_category' => '',
										  'product_id'		 => $res['NavProduct'],
										  'quantity'		=> $res['Quantity'],
										  'rate'			=> $res['Rate'],
										  'returnable'		=> ($res['Returns']==='1')? 'No' : 'Yes',
										  'total_price' 	=> $res['Total'],										 
										 
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );*/
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(PRODUCT_OUTWARD_ITEM, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}

	function product_outward_item_NAV_update(){
		$this->db->select('poi.ID, po.ID as outwardID');
		$this->db->from(PRODUCT_OUTWARD_ITEM .' poi');
		$this->db->join(PRODUCT_OUTWARD .' po', 'po.outwardnav_id = poi.outwardnav_id', 'LEFT');
		$this->db->where(array('poi.product_type' => '1'));
		$result = $this->db->get()->result_array();
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
				
				echo "<br>"."UPDATE psac_product_outward_item SET outward_id = '".$res['outwardID']."' WHERE ID='".$res['ID']."'; ";
				
				/*$data_set[$z][] = array(  'outwardnav_id' 	 => $res['OutwardNavId'],
										  'group_type'		 => @$this->group_type[$res['Type']],
										  'main_group_id' 	 => $res['MainGroup'],
										  'fisherman_id' 	 => $res['Fisherman'],
										  'product_type'	 => 1,
										  'product_category' => '',
										  'product_id'		 => $res['NavProduct'],
										  'quantity'		=> $res['Quantity'],
										  'rate'			=> $res['Rate'],
										  'returnable'		=> ($res['Returns']==='1')? 'No' : 'Yes',
										  'total_price' 	=> $res['Total'],										 
										 
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );*/
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(PRODUCT_OUTWARD_ITEM, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}

	function wagesitem(){
		
		$this->old->select('w.*, mg.Type as GTYPE');
		$this->old->from('wagesitem w');
		$this->old->join('maingroup mg', 'mg.ID = w.MainGroup', 'LEFT');
		$result = $this->old->get()->result_array();
		
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
				
				$Minor = $res['Minor'] * $res['MinorFee'];
				$Major = $res['Major'] * $res['MajorFee'];
				$Sawal = $res['Sawal'] * $res['SawalFee'];

				$TotalWageAmount = $res['TotalWageAmount'];

				$date = explode('-', $res['Date']); // 24/08/2017-31/08/2017;
				$from_date = get_date('Y-m-d', str_replace('/','-',$date[0]));
				$to_date = get_date('Y-m-d', str_replace('/','-',$date[1]));
				
				$data_set[$z][] = array(  'from_date' 	 			=> $from_date,
										  'to_date'		 			=> $to_date,
										  'group_type_id' 	 		=> @$this->group_type[$res['GTYPE']],
										  'MainGroup' 	 			=> $res['MainGroup'],
										  'FishermanId'	 			=> $res['FishermanId'],
										  'total_major' 			=> $Major,
										  'total_minor'				=> $Minor,
										  'total_sawal'				=> $Sawal,
										  'TotalWage'				=> $TotalWageAmount,
										  'group_liability'			=> $res['Liability'],
										  'advance_wages' 			=> $res['Advance'],										 
										  'GovDeduction'			=> $res['GovDeduction'],	
										  'GroupLiabilityDeduction'	=> $res['GroupLiability'],	
										  'AdvanceWagesDeduction'	=> $res['AdvanceLiability'],	
										  'final_wages'				=> $res['NetAmount'],	
										  
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(WAGESITEM, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}

	function wages(){
		
		$result = $this->db->group_by(array('from_date', 'to_date', 'MainGroup'))->get(WAGESITEM)->result_array();
		//echo count($result);
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
								
				$data_set[$z][] = array(  'from_date' 	 			=> $res['from_date'],
										  'to_date'		 			=> $res['to_date'],
										  'MainGroup' 	 			=> $res['MainGroup'],
										  
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );
				$i++;					
			}
		}
		//printr($data_set);
		
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(WAGES, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}
	
	function wagesitem_update(){
		
		$result = $this->db->get(WAGES)->result_array();
		//echo count($result);
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
				
				echo "<br>"."UPDATE ".WAGESITEM." SET wages_id = '".$res['ID']."' WHERE from_date='".$res['from_date']."' AND to_date='".$res['to_date']."' AND MainGroup='".$res['MainGroup']."'; ";
				
				/*$data_set[$z][] = array(  'from_date' 	 			=> $res['from_date'],
										  'to_date'		 			=> $res['to_date'],
										  'MainGroup' 	 			=> $res['MainGroup'],
										  
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );*/
				$i++;					
			}
		}
		//printr($data_set);
		
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(WAGES, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}

	function update_product_outward_item_date(){
		$this->db->select('poi.ID, poi.outward_id, po.outward_date');
		$this->db->from(PRODUCT_OUTWARD_ITEM .' poi');
		$this->db->join(PRODUCT_OUTWARD .' po', 'po.ID = poi.outward_id', 'LEFT');
		$this->db->where(array('poi.outward_date' => '0000-00-00'));
		$result = $this->db->get()->result_array();
		//printr($result);
		$data_set = array();
		if(!empty($result)){
			$i = 0;
			$z = 0;
			
			foreach($result as $res){
				if($i == 200){
					$i=0;
					$z++;
				};
				
				echo "<br>"."UPDATE ".PRODUCT_OUTWARD_ITEM." SET outward_date = '".$res['outward_date']."' WHERE ID = '".$res['ID']."'; ";
				
				/*$data_set[$z][] = array(  'outwardnav_id' 	 => $res['OutwardNavId'],
										  'group_type'		 => @$this->group_type[$res['Type']],
										  'main_group_id' 	 => $res['MainGroup'],
										  'fisherman_id' 	 => $res['Fisherman'],
										  'product_type'	 => 1,
										  'product_category' => '',
										  'product_id'		 => $res['NavProduct'],
										  'quantity'		=> $res['Quantity'],
										  'rate'			=> $res['Rate'],
										  'returnable'		=> ($res['Returns']==='1')? 'No' : 'Yes',
										  'total_price' 	=> $res['Total'],										 
										 
										  'added_by' 		 => 1,
										  'added_date' 		 => get_datetime('Y-m-d H:i:s'),
										  'action_microtime' => microtime(true)
										  );*/
				$i++;					
			}
		}
		//printr($data_set);
		if(!empty($data_set)){
			$i = 1;
			foreach($data_set as $data){
				if(is_array($data) && !empty($data)){
					//$this->db->insert_batch(PRODUCT_OUTWARD_ITEM, $data);
				}
				
				echo '<br>'.$i++;
			}
		}
	}


}
