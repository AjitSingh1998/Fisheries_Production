<?php
#[AllowDynamicProperties]
class Excel_import_model extends CI_Model
{
	var $userID;
	public function __construct()
	{
		parent::__construct();
		$this->userID = checkUserLogin();
	}
	
	function validate_excel_data($head_element, $worksheet, $errors, $production_type, $allFishType, $allFishCode, $mp_code, $current_box_number, $allClientCode){
		$client = $box = $carret_weight = $box_weight = '';
		$prepare_data = array();
		foreach($head_element as $key => $name){
			$bdata = $worksheet->getCellByColumnAndRow($key, $row)->getValue();
			if($name == 'production_type'){
				if($bdata !== $production_type){
					$errors[] = $bdata.' is not valid production type at line number '.$row.' there should be '.$production_type;
				}
			}else if($name == 'fish_type'){
				if(!in_array($bdata, $allFishType)){
					$errors[] = $bdata.' is not valid <strong>fish type</strong> at line number '.$row;
				}
				$prepare_data['fish_type'] = $bdata;		
			}else if($name == 'quantity'){
				if (!preg_match('/^[0-9]+$/', $bdata)) {
					$errors[] = $bdata.' is not valid <strong>fish quantity</strong> at line number '.$row. ($bdata==''?' there should be 0 instead of blank':'');
				}						
				$prepare_data['carret_quantity'] = $bdata;									
			}else if($name == 'fish_code'){
				if(!in_array($bdata, $allFishCode)){
					$errors[] = $bdata.' is not valid <strong>fish code</strong> at line number '.$row;
				}
				$prepare_data['fish_code'] = $bdata;
			}else if($name == 'carret_weight'){
				if(is_float($bdata) || is_numeric($bdata)){
					$carret_weight = $bdata;
				}else{
					$errors[] = $bdata.' is not valid <strong>carret weight</strong> at line number '.$row;
				}
				$prepare_data['carret_weight'] = $bdata;
			}else if($name == 'box_weight'){
				if(is_float($bdata) || is_numeric($bdata)){
					$box_weight = $bdata;
				}else{
					$errors[] = $bdata.' is not valid <strong>box weight</strong> at line number '.$row;
				}						
				$prepare_data['box_weight'] = $bdata;
			}else if($name == 'box_number'){
				$box_number = $mp_code.$current_box_number;
				if($bdata != ''){
					if($bdata !== $box_number){
						$errors[] = $bdata.' is not valid <strong>box number</strong> at line number '.$row.' there should be '.$box_number;
					}
					$current_box_number++;
					$box = $box_number;
				}
				$prepare_data['box_number'] = $box_number;
			}else if($name == 'client_code'){
				if($bdata != ''){
					if(!in_array($bdata, $allClientCode)){
						$errors[] = $bdata.' is not valid <strong>client code</strong> at line number '.$row;
					}
					$client = $bdata;
				}
				$prepare_data['client_id'] = ($bdata=='' ? 0 : $bdata);
			}									
		}
		
		if($box != '' && $client != ''){
			$errors[] = 'You must have to make either <strong>box</strong> or <strong>sale localy</strong> to this client <strong>'.$client.'</strong> at line number '.$row;
		}
		
		if(($carret_weight < $box_weight) || ($carret_weight - $box_weight) > 1){
			$errors[] = 'The <strong>Box weight('.$box_weight.')</strong> should always less <strong>0.5KG or 1KG</strong> than <strong>carret weight('.$carret_weight.')</strong> at line number '.$row;
		}
		
		return array('data'=>$prepare_data, 'errors'=>$errors);
	
	}
	
	function generate_carret($prepare_data){
		$prepare_carret_data = $prepare_data;
		unset($prepare_carret_data['box_weight'], $prepare_carret_data['fish_code']);
		$this->db->insert(PRODUCTION_CARRET, $prepare_carret_data);
		$carret_number = $this->db->insert_id();
		if($carret_number){
			unset($prepare_carret_data['carret_quantity'], $prepare_carret_data['carret_weight']);
			$prepare_carret_item_data = $prepare_carret_data;
			$prepare_carret_item_data['carret_number'] = $carret_number;
			$prepare_carret_item_data['fish_code'] = $prepare_data['fish_code'];
			$prepare_carret_item_data['fish_qty'] = $prepare_data['carret_quantity'];
			$prepare_carret_item_data['fish_wt'] = $prepare_data['carret_weight'];
			$prepare_carret_item_data['box_wt'] = $prepare_data['box_weight'];				
			$this->db->insert(PRODUCTION_CARRET_ITEMS, $prepare_carret_item_data);
		}
		return $carret_number;
	}
	
	function generate_box($prepare_data, $carret_number){
		$prepare_box_data = $prepare_data;
		$prepare_box_data['carret_number'] = $carret_number;
		$prepare_box_data['box_quantity'] = $prepare_data['carret_quantity'];
		unset($prepare_box_data['carret_quantity'], $prepare_box_data['fish_code'], $prepare_box_data['client_id'], $prepare_box_data['production_type']);
		$this->db->insert(PRODUCT_BOX, $prepare_box_data);
		$bxnumber = $this->db->insert_id();
		if($bxnumber){
			$prepare_box_item_data = $prepare_data;
			$prepare_box_item_data['carret_number'] = $carret_number;
			$prepare_box_item_data['fish_qty'] = $prepare_data['carret_quantity'];
			$prepare_box_item_data['fish_wt'] = $prepare_data['carret_weight'];
			$prepare_box_item_data['box_wt'] = $prepare_data['box_weight'];
			unset($prepare_box_item_data['production_type'], $prepare_box_item_data['client_id'], 
				  $prepare_box_item_data['carret_quantity'], $prepare_box_item_data['carret_weight'],$prepare_box_item_data['box_weight']);
			$this->db->insert(PRODUCT_BOX_ITEMS, $prepare_box_item_data);
		}
		return true;	
	}
	
	function generate_sale($prepare_data, $mp_code, $carret_number, $allClientCode){
		$sale_id = '';
		$where = array('market_id'=>$prepare_data['depot_id'], 'client_id'=>$prepare_data['client_id'], 'DATE(sale_date)'=>$prepare_data['packing_date'], 'status'=>'Active');
		$checkPreSale = $this->db->select('sale_id')->get_where(SALE, $where)->result_array();
		if(!empty($checkPreSale)){
			$sale_id = $checkPreSale[0]['sale_id'];
		}		
		if(empty($sale_id)){
		  
		  $currentNumber = $this->db->select('MAX(inv_number) as inv_number')->get_where(SALE, array('market_id'=>$prepare_data['depot_id']))->result_array();
			
			$inv_number = @$currentNumber[0]['inv_number']+1;
			$invoice_number = $mp_code.$inv_number;
			
			$prepare_sale_data = array();
			$prepare_sale_data['market_type'] 		= '1';
			$prepare_sale_data['market_category'] 	= '2';
			$prepare_sale_data['market_id'] 		= $prepare_data['depot_id'];
			$prepare_sale_data['mp_code'] 			= $mp_code;
			$prepare_sale_data['invoice_number'] 	= $invoice_number;
			$prepare_sale_data['inv_number'] 		= $inv_number;
			
			$prepare_sale_data['sale_date'] 		= get_datetime('Y-m-d H:i:s', $prepare_data['packing_date']);
			$prepare_sale_data['client_id'] 		= $prepare_data['client_id'];
			$prepare_sale_data['client_name'] 		= @$allClientCode['info'][$prepare_data['client_id']]['name'];
			$prepare_sale_data['client_mobile'] 	= @$allClientCode['info'][$prepare_data['client_id']]['number'];
			$prepare_sale_data['client_email'] 		= @$allClientCode['info'][$prepare_data['client_id']]['email'];
			
			$prepare_sale_data['added_by'] 			= $this->userID;
			$prepare_sale_data['added_date'] 		= get_datetime('Y-m-d H:i:s');
			$prepare_sale_data['status'] 			= 'Active';
			$prepare_sale_data['action_microtime'] 	= microtime(true);
			$this->db->insert(SALE, $prepare_sale_data);
			$sale_id = $this->db->insert_id();
		}
		
		if(!empty($sale_id)){
			
			$stock_type = '';
			if($prepare_data['production_type'] == 'Point'){
				$stock_type = 'Current';
			}else if($prepare_data['production_type'] == 'Stock'){
				$stock_type = 'Opening';
			}else if($prepare_data['production_type'] == 'Bachat'){
				$stock_type = 'Bachat';
			}else{
				$stock_type = 'Current';
			}
			 
			$prepare_sale_item_data = array();
			$prepare_sale_item_data['sale_id'] 				= $sale_id;
			$prepare_sale_item_data['carret_number'] 		= $carret_number;
			$prepare_sale_item_data['client_id'] 			= $prepare_data['client_id'];
			$prepare_sale_item_data['sale_date'] 			= get_datetime('Y-m-d H:i:s', $prepare_data['packing_date']);
			$prepare_sale_item_data['market_type'] 			= 1;
			$prepare_sale_item_data['market_category'] 		= 2;
			$prepare_sale_item_data['market_id'] 			= $prepare_data['depot_id'];
			$prepare_sale_item_data['fish_code'] 			= $prepare_data['fish_code'];
			$prepare_sale_item_data['fish_quantity'] 		= $prepare_data['carret_quantity'];
			$prepare_sale_item_data['fish_weight'] 			= $prepare_data['carret_weight'];
			$prepare_sale_item_data['stock_type'] 			= $stock_type;			
			$prepare_sale_item_data['added_by'] 			= $this->userID;
			$prepare_sale_item_data['added_date'] 			= get_datetime('Y-m-d H:i:s');
			$prepare_sale_item_data['status'] 				= 'Active';
			$prepare_sale_item_data['action_microtime'] 	= microtime(true);			
			$this->db->insert(SALE_ITEMS, $prepare_sale_item_data);
		}
		return $sale_id;
	}
	
	function getAllFishCode(){
		$allFishCodeSql = $this->db->select('code')->get_where(FISH, array('status'=>'Active'))->result_array();
		$allFishCode = array();
		if(!empty($allFishCodeSql)){
			foreach($allFishCodeSql as $code){
				$allFishCode[] = $code['code'];
			}
		}
		return $allFishCode;
	}
	
	function getAllCLientInfo(){
		$allClientCodeSql = $this->db->select('ID, company_name, contact_number, email')->get_where(CLIENT, array('status'=>'Active'))->result_array();
		$allClientCode = array();
		if(!empty($allClientCodeSql)){
			foreach($allClientCodeSql as $code){
				$allClientCode[] = $code['ID'];
				$allClientInfo[$code['ID']] = array('name'=>$code['company_name'], 'number'=>$code['contact_number'], 'email'=>$code['email']);
			}
			$allClientCode = array('code'=>$allClientCode, 'info'=>$allClientInfo);
		}
		return $allClientCode;
	}
	
	function getProductionInfo($production_id){
		$productionInfo = $this->db->select(PRODUCTION.'.*, ' . MARKET_PLACE.'.code, IFNULL(max(number),0) as box_number')
											->join(MARKET_PLACE, 'ID = depot_id')
											->join(BOX_NUMBER, 'mp_code = code')
											->get_where(PRODUCTION, array('production_id' => $production_id))->result_array();
		return $productionInfo;
	
	}
	
	function getSaleInfo($sale_id){
		$saleInfo = $this->db->get_where(SALE, array('sale_id' => $sale_id))->result_array();
		if(!empty($saleInfo)){
			return $saleInfo[0];
		}
		return false;	
	}
	
	function getFisherManCode(){
	    $result = $this->db->select('ID, Code, group_type_id, MainGroup, Name, MajorFee, MinorFee, SawalFee')->get(FISHERMAN)->result_array();
	    if(isset($result) && !empty($result)){
	        $data = [];
	        foreach($result as $val){
	            $data[$val['Code']] = $val;
	        }
	        $codes = array_column($result, 'Code');
	        $MainGroup = array_column($result, 'MainGroup');
	        
	        return array('data' => $data, 'Codes' => $codes, 'MainGroup' => $MainGroup);
	    }
	    return array();
	}
	
	function getGroupType(){
	    $result = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
	    if(isset($result) && !empty($result)){
	        $data = array();
	        foreach($result as $val){
	            $data[$val['Name']] = $val['ID']; 
	        }
	        return array('data' => $data, 'name' => array_column($result, 'Name'));
	    }
	    return array();
	}
	
	function getMainGroup(){
	    $result = $this->db->select('ID, Name')->get(MAINGROUP)->result_array();
	    if(isset($result) && !empty($result)){
	        $data = array();
	        foreach($result as $val){
	            $data[$val['Name']] = $val['ID']; 
	        }
	        return array('data' => $data, 'name' => array_column($result, 'Name'));
	    }
	    return array();
	}
}
