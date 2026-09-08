<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Dailytoll_model extends CI_Model {
	
	function get_dailytoll($dt_id){
		$data = array();
		$this->db->select('dt.*, fp.Name, CONCAT(a.first_name, " ", a.last_name) as operator_name');
		$this->db->from(DAILYTOLL. ' dt');
		$this->db->join(FISHINGPOINTS. ' fp', 'fp.ID=dt.Point', 'LEFT');
		$this->db->join(ADMINISTRATOR. ' a', 'a.admin_id = dt.added_by', 'LEFT');
		$this->db->where(array('dt.ID'=>$dt_id, 'dt.editable'=>'Unlock'));
		$result = $this->db->get()->result_array();
		if(!empty($result)){
			$data = $result[0];
		}
		return $data;
	}
	
	function get_dailytoll_info($dt_id=NULL){
		$result = array();
		if($dt_id != NULL){
			$this->db->select('dti.*, fm.Code, mg.Name as Samiti_name');
			$this->db->from(DAILYTOLLINFO. ' dti');
			$this->db->join(MAINGROUP. ' mg', 'mg.ID=dti.Samiti', 'LEFT');
			$this->db->join(FISHERMAN. ' fm', 'fm.ID=dti.CompanyId', 'LEFT');
			$this->db->where(array('dti.DailytollId'=>$dt_id));
			$result = $this->db->get()->result_array();		
		}
		//printr($result);
		return $result;
	}
	
	function get_fishing_points(){
		$data = array();
		$result = $this->db->get(FISHINGPOINTS)->result_array();
		if(!empty($result)){
			$data = $result;
		}
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
	
	function insertDailytoll($post, $userID){
		$data = array('status' => "danger", "msg" => "Invalid request.");
		
		$mode			= $post['mode'];
		$fisherman_id 	= $post['fisherman_id'];
		$dtollid		= $post['dtollid'];
		$dtiid			= $post['dtiid'];
		
		$toll_date 		= get_date('Y-m-d', str_replace('/', '-', $post['toll_date']));
		$point 			= $post['point'];
		
		$gtqty		= $post['gtqty'];
		$gtwt		= $post['gtwt'];
		
		if(!empty($dtollid)){
			$result = $this->db->select('ID')->get_where(DAILYTOLL, array('ID'=>$dtollid))->result_array();
			if(!empty($result)){
				$dtollid = $result[0]['ID'];
			}else{
				$data = array('status' => "danger", "msg" => "Error: Dailytoll ID is missing, 
															  please refresh the page select valid toll date and point.");
			}
		}else{
			$prepData = array('Date'=>$toll_date, 'Point'=>$point, 'added_by' => $userID, 'added_date' => get_date('Y-m-d H:i:s'), 'action_microtime' => microtime(true));
			$retval = $this->db->insert(DAILYTOLL, $prepData);
			$dtollid = $this->db->insert_id();
			$this->db->where(array('ID'=>$dtollid))->update(DAILYTOLL, array('page_code'=>$dtollid));
		}
		
		if(empty($dtollid)){			
			$data = array('status' => "danger", "msg" => "Inserting daily toll point failed.");
		}else{
			$data_array = array('toll_date' 	=> $toll_date,
								'Point' 		=> $point,
								'DailytollId' 	=> $dtollid,
								'group_type' 	=> $post['f_group'],
								'Samiti' 		=> $post['f_maingroup'],
								'CompanyId' 	=> $fisherman_id, //fisherman id
								'Name' 			=> $post['f_name'],
								'govt_charges' 	=> $post['govt_charges'],
								'MajorFee' 		=> $post['f_MajorFee'],
								'MinorFee' 		=> $post['f_MinorFee'],
								'SawalFee' 		=> $post['f_SawalFee'],
								'Cqty' 			=> $post['cqty'],
								'Cwt' 			=> $post['cwt'],
								'Rqty' 			=> $post['rqty'],
								'Rwt' 			=> $post['rwt'],
								'Mqty' 			=> $post['mqty'],
								'Mwt' 			=> $post['mwt'],
								'Kqty' 			=> $post['kqty'],
								'Kwt' 			=> $post['kwt'],
								'Aqty' 			=> $post['aqty'],
								'Awt' 			=> $post['awt'],
								'Sqty' 			=> $post['sqty'],
								'Swt' 			=> $post['swt'],
								'Lqty' 			=> $post['lqty'],
								'Lwt' 			=> $post['lwt'],
								'Localminor' 	=> $post['localminor'],
								'Tmqty' 		=> $post['tmqty'],
								'Tmwt' 			=> $post['tmwt'],
								'Tqty' 			=> $post['tqty'],
								'Twt' 			=> $post['twt'],
								'added_by' 		=> $userID,
								'added_date' 	=> get_date('Y-m-d H:i:s'),
								'action_microtime' => microtime(true),
								);
			$toll_info_id = '';
			
			$this->db->where(array('ID'=>$dtollid))->update(DAILYTOLL, array('total_qty'=>$gtqty, 'total_wt'=>$gtwt));
			
			if($mode == 'edit' && !empty($fisherman_id) && !empty($dtiid)){
				$DailytollInfoId = url_encryptor("decrypt", $dtiid);
				$where = array('ID'=>$DailytollInfoId, 'DailytollId'=>$dtollid, 'CompanyId'=>$fisherman_id);
				$result = $this->db->select('ID')->get_where(DAILYTOLLINFO, $where)->result_array();
				if(!empty($result)){
					unset($data_array['added_by']); 
					unset($data_array['added_date']);
					$data_array['updated_by'] =  $userID;
					$data_array['updated_date'] =  get_date('Y-m-d H:i:s');
					//printr($data_array);
					$update = $this->db->where($where)->update(DAILYTOLLINFO, $data_array);
					if($update){
						$data = array('status' => "success", "msg" => "Daily Toll detail updated successfully.", "dti_id" => $dtiid, "dtollid" => $dtollid);
					}else{
						$data = array('status' => "danger", "msg" => "Daily Toll updation failed, Please try again.", "dti_id" => $dtiid, "dtollid" => $dtollid);
					}
				}else{
					$data = array('status' => "danger", "msg" => "Error: Invailid Daily Toll Info ID, Please reload the page and try again to update toll info.", "dti_id" => $dtiid, "dtollid" => $dtollid);
				}
			}elseif($mode == 'add' && !empty($fisherman_id)){
				$retvalLog = $this->db->insert(DAILYTOLLINFO, $data_array);
				$toll_info_id = $this->db->insert_id();
				if($toll_info_id ){
					$data = array('status' => "success", "msg" => "Daily Toll detail inserted successfully.", "dti_id" => url_encryptor("encrypt", $toll_info_id),  "dtollid" => $dtollid);
				}else{
					$data = array('status' => "danger", "msg" => "Daily Toll insertion failed, Please try again.", "dtollid" => $dtollid);
				}
			}else{
				$data = array('status' => "danger", "msg" => "Error: This is an invalid request, to performing action with these data:.");
			}
		}
		
		return $data;	
	}
	
}