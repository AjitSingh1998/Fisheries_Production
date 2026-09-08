<?php
#[AllowDynamicProperties]
class Business_Model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function update_details( $data ){		
	  $files = $_FILES;
	  $same_billing_detail =  (array_key_exists("same_billing_detail",$data))? $data['same_billing_detail'] : 'no' ; 
	  $updData = array( 
					'business_name' 		=>  $data['business_name'],
					'website'  				=>  $data['website'],
					'email'  				=>  $data['email'],
					'business_phone'  		=>  $data['business_phone'],
					'year_established'  	=>  $data['year_established'],
					'payment_method'  		=>  $data['payment_method'],
					'same_billing_detail'   =>  $same_billing_detail,
					'country' 				=>  $data['country'],
					'currency' 				=>  $data['currency'],
					'timezone' 				=>  $data['time_zone'],
					'date_format' 			=>  $data['date_format'],
					'time_format' 			=>  $data['time_format'],
					'business_details' 		=>  $data['business_details'],
					'twitter_url' 			=>  $data['twitter_url'],
					'facebook_url' 			=>  $data['facebook_url'],
					'create_date' 			=>  date('Y-m-d H:i:s')
					);
					
	   $where = array( 'user_id' => $data['business_id'] );
	   $upResp = $this->db->where($where)->update(BUSINESS_DETAIL, $updData);
	  
	   if($upResp){
		// upload business logo
			$preparedData = array();
			for($i=0; $i<count($data['business_course']);$i++){
				$preparedData[] = array('business_id' => $data['business_id'], 'course_id' => $data['business_course'][$i] );
			}
			if(!empty($preparedData)){
				$this->db->where('business_id',$data['business_id'])->delete(USERS_COURSE);
				$this->db->insert_batch(USERS_COURSE, $preparedData);
			}
		$this->upload_business_logo( $data );
	   }
	   return $upResp;
	}
	
	function get_business_details($business_id){	
		$details = $this->db->where( 'user_id', $business_id )->get(BUSINESS_DETAIL)->result_array();
		if(!empty($details)){
			return $details[0];	   
		}
		return '';		
	}
	
	function upload_business_logo( $data ){
	   $post = $this->input->post(); 
	   if( isset($_FILES) && !empty($_FILES['logo']) && !empty($_FILES['logo']['name'])): 
	 
		   $this->load->library('fileupload/fileupload');	  
		   $this->fileupload->upload($_FILES['logo']);
			if($this->fileupload->uploaded){			
				$this->fileupload->file_new_name_body   = 'business_logo_'.$data['business_id'];
				$this->fileupload->file_overwrite  = true;
				$dst_path = BUSINESS_LOGO_PATH;				
				$this->fileupload->process($dst_path);
				if ($this->fileupload->processed){
					$business_logo = str_replace( $dst_path,"",$this->fileupload->file_dst_pathname);
					$business_logo = trim(str_replace( '\\','',$business_logo));
					$where = array( 'user_id' => $data['business_id'] );
					$this->db->where($where)->update(BUSINESS_DETAIL,array('logo'=>$business_logo));
					$this->fileupload->clean(); 
				}
			}
		endif;
	}
	
	function remove_business_logo($filename){
		$businessID = loginCompanyInfo('business_id');	
		$dst_path = BUSINESS_LOGO_PATH;
		$filePath = $dst_path.$filename;
		
		if(!empty($filename) && !empty($businessID)):
		
			$whereData = array( 'user_id' => $businessID , 'logo' => $filename );
			$where = array_map( 'trim' , $whereData );
			$upRes = $this->db->where($where)->update(BUSINESS_DETAIL,array('logo'=>''));
			if($upRes && @getimagesize($filePath)){
				unlink($filePath);
				return 'success';
			} else {
				return 'error';
			}
		endif;
	}
	
	function get_all_courses(){
		$table = USERS_ARTICLE_KEYWORDS;
		$courses = $this->db->query("select * from ".$table." where status = 'Active'")->result_array();		
		if(!empty($courses)){
			return  $courses ;
		} else {
			return array(); // 'keyword_title'=>'','keyword_id'=>''
		}
   }
   
   	function get_business_courses($business_id){
		$table = USERS_COURSE;		
		$courses = $this->db->select('course_id')->get_where( $table , array('business_id'=>$business_id))->result_array();		
		if(!empty($courses)){
			$data  = array();
			foreach( $courses as $course ){		
				$data[] = $course['course_id'];
			}
			return $data;
		} else {
			return array(); 
		}
   }
      
   function addStaffData07112017($postData){
			$userTable = USERS;
			$associationTable = ASSOCIATIONS;
			$adminTable = ADMINISTRATOR;
			
			$asson_id = '';
			$admin_id = ''; 
			$staff_id = '';

			$files = $_FILES;
			$list_staff_person =  (array_key_exists("list_staff_person",$postData))? $postData['list_staff_person'] : 'no' ;
			/* Create staff as teacher user */		   
			$staffUserData = array(			
						'business_id' 		=> $postData['business_id'],
						'group_id' 			=> 7,						
						'firstName'			=> $postData['first_name'],
						'lastName'			=> $postData['last_name'],
						'phone_number'		=> $postData['phone_number'],
						'zipcode' 			=> $postData['zipcode'],
						'city' 				=> $postData['city'],
						'state' 			=> $postData['state'],
						'email' 			=> $postData['email'],
						'address' 			=> $postData['address']	
					   );
			$this->db->insert($userTable, $staffUserData);
			$staff_id = $this->db->insert_id();
			/* Create staff as teacher user */

				/*  insert staff info as Admin */
				if(!empty($staff_id)){
					$prepAdminData = array(
										'business_id' 		=> $postData['business_id'],
										'user_id' 			=> $staff_id,
										'group_id' 			=> 7,
										'first_name'		=> $postData['first_name'],
										'last_name'			=> $postData['last_name'],
										'email' 			=> $postData['email'],									
										'phone_number' 		=> $postData['phone_number'],
										'password' 			=> encrypt_data(time()),
									);	
					$this->db->insert( $adminTable, $prepAdminData );
					$admin_id = $this->db->insert_id();
					
				    /*  insert staff info as Admin */
					if(!empty($admin_id)){
						/* manage user associations */		   
						$associationsData = array(
									'association_from' 		=> $postData['business_id'],
									'association_to'		=> $staff_id,
									'group_id' 				=> 7,						
									'status' 				=> 'Active',
									'created_date' 			=> date('Y-m-d H:i:s')
												);
						/* manage user associations */	
						$this->db->insert( $associationTable, $associationsData );
						$asson_id = $this->db->insert_id();

						/* Upload photo if required */
						if(!empty($staff_id) && !empty($asson_id)){
								$this->uploadStaffProfileImage( $staff_id, $postData );
						} else {
							/*  Delete user from as bussiness */
							$this->db->delete( $userTable, array( 'user_id' => $staff_id ) );
							/*  Delete user from as bussiness */
							$this->db->delete( $adminTable, array( 'admin_id' => $admin_id ) );
						}	
						/* Upload photo if required */	
					}
				}
			return $staff_id;	
   }
   
   function addStaffData($postData){
			$userTable = USERS;
			$associationTable = ASSOCIATIONS;
			$adminTable = ADMINISTRATOR;
			$staffTable = BUSINESS_STAFFS;
			
			$asson_id = '';
			$admin_id = ''; 
			$staff_id = '';

			$files = $_FILES;
			$list_staff_person =  (array_key_exists("list_staff_person",$postData))? $postData['list_staff_person'] : 'no' ;
			
			/* Create staff as teacher user */		   
			$staffUserData = array(		
						'group_id' 			=> 7,						
						'firstName'			=> $postData['first_name'],
						'lastName'			=> $postData['last_name'],
						'phone_number'		=> $postData['phone_number'],
						'zipcode' 			=> $postData['zipcode'],
						'city' 				=> $postData['city'],
						'state' 			=> $postData['state'],
						'email' 			=> $postData['email'],
						'address' 			=> $postData['address']	
					   );
			$this->db->insert($userTable, $staffUserData);
			$staff_id = $this->db->insert_id();
			/* Create staff as teacher user */
			
			/* Add staff user data in STAFF table */ 
			$staffData = array(
						'business_id' 		=> $postData['business_id'],
						'user_id'			=> $staff_id,
						'group_id'			=> 7,
						'firstName'			=> $postData['first_name'],
						'lastName'			=> $postData['last_name'],
						'phone_number'		=> $postData['phone_number'],
						'job_title'			=> $postData['job_title'],
						'sms_number' 		=> $postData['sms_number'],
						'email' 			=> $postData['email'],
						'address' 			=> $postData['address'],
						'zipcode' 			=> $postData['zipcode'],
						'city' 				=> $postData['city'],
						'state' 			=> $postData['state'],
						'list_staff_person' => $list_staff_person,
						'nickname' 			=> $postData['nickname'],
						'personal_phone'	=> $postData['personal_phone'],
						'personal_email' 	=> $postData['personal_email'],
						'biography' 		=> $postData['biography'],
						'created_date' 		=> date('Y-m-d H:i:s')
					   );
			$this->db->insert( BUSINESS_STAFFS, $staffData );
			$staff_user_id = $this->db->insert_id();	
			/* Add staff user data in STAFF table */ 

				/*  insert staff info as Admin */
				if(!empty($staff_id)){
					$prepAdminData = array(
										'business_id' 		=> $postData['business_id'],										
										'group_id' 			=> 7,
										'first_name'		=> $postData['first_name'],
										'last_name'			=> $postData['last_name'],
										'email' 			=> $postData['email'],									
										'phone_number' 		=> $postData['phone_number'],
										'password' 			=> encrypt_data(time()),
									);	
					$this->db->insert( $adminTable, $prepAdminData );
					$admin_id = $this->db->insert_id();
					
				    /*  insert staff info as Admin */
					if(!empty($admin_id)){
						/* manage user associations */		   
						$associationsData = array(
									'association_from' 		=> $postData['business_id'],
									'association_to'		=> $staff_id,
									'group_id' 				=> 7,						
									'status' 				=> 'Active',
									'created_date' 			=> date('Y-m-d H:i:s')
												);
						/* manage user associations */	
						$this->db->insert( $associationTable, $associationsData );
						$asson_id = $this->db->insert_id();

						/* Upload photo if required */
						if(!empty($staff_id) && !empty($asson_id) && !empty($admin_id)){
								$this->uploadStaffProfileImage( $staff_user_id, $postData );
						} else {
							/*  Delete as user */
							$this->db->delete( $userTable, array( 'user_id' => $staff_id ) );
							/*  Delete as admin */
							$this->db->delete( $adminTable, array( 'admin_id' => $admin_id ) );
							/*  Delete as staff */
							$this->db->delete( $staffTable, array( 'staff_id' => $staff_user_id ) );
							/*  Delete staff as association */
							$this->db->delete( $associationTable, array( 'association_id' => $asson_id ) );
						}	
						/* Upload photo if required */	
					}
				}
			return $staff_id;	
   }   
   
   function editStaffData07112017($postData){				
			$userTable = USERS;
			$associationTable = ASSOCIATIONS;
			$adminTable = ADMINISTRATOR;
			
			$asson_id = '';
			$admin_id = ''; 
			$staff_id = '';

			$files = $_FILES;
			$list_staff_person =  (array_key_exists("list_staff_person",$postData))? $postData['list_staff_person'] : 'no' ;
			/* Create staff as teacher user */		   
			$staffUserData = array(			
						'firstName'			=> $postData['first_name'],
						'lastName'			=> $postData['last_name'],						
						'zipcode' 			=> $postData['zipcode'],
						'city' 				=> $postData['city'],
						'state' 			=> $postData['state'],						
						'address' 			=> $postData['address']	
					   );
			$where = array( 'user_id' => $postData['staff_id'] ,'business_id' => $postData['business_id'],'group_id' => 7 );		   
			$upResp = $this->db->where($where)->update( $userTable , $staffUserData);	
			if($upResp){
				$this->uploadStaffProfileImage( $postData['staff_id'], $postData );
				return true;
			} else {				
				return false;
			}			
   }
   
   function editStaffData($postData){				
			$userTable = USERS;
			$associationTable = ASSOCIATIONS;
			$adminTable = ADMINISTRATOR;
			$staffTable = BUSINESS_STAFFS;
			
			$asson_id = '';
			$admin_id = ''; 
			$staff_id = '';

			$files = $_FILES;
			$list_staff_person =  (array_key_exists("list_staff_person",$postData))? $postData['list_staff_person'] : 'no' ;
			/* Create staff as teacher user */		   
			$staffUserData = array(			
						'firstName'			=> $postData['first_name'],
						'lastName'			=> $postData['last_name'],						
						'zipcode' 			=> $postData['zipcode'],
						'city' 				=> $postData['city'],
						'state' 			=> $postData['state'],						
						'address' 			=> $postData['address']	
					   );
		    $staffStaffData = array(
						'firstName'			=> $postData['first_name'],
						'lastName'			=> $postData['last_name'],						
						'job_title'			=> $postData['job_title'],
						'sms_number' 		=> $postData['sms_number'],						
						'address' 			=> $postData['address'],
						'zipcode' 			=> $postData['zipcode'],
						'city' 				=> $postData['city'],
						'state' 			=> $postData['state'],
						'list_staff_person' => $list_staff_person,
						'nickname' 			=> $postData['nickname'],
						'personal_phone'	=> $postData['personal_phone'],
						'personal_email' 	=> $postData['personal_email'],
						'biography' 		=> $postData['biography'],
						);
			$whereUser = array( 'user_id' => $postData['staff_id'], 'group_id' => 7 );		   
			$whereStaff = array( 'staff_id' => $postData['staff_id'] ,'business_id' => $postData['business_id'],'group_id' => 7 );		   
			
			$upResp = $this->db->where($whereUser)->update( $userTable , $staffUserData);	
			$upRespStaff = $this->db->where($whereStaff)->update( $staffTable , $staffStaffData);
			
			if($upResp && $upRespStaff){
				$this->uploadStaffProfileImage( $postData['staff_id'], $postData );
				return true;
			} else {				
				return false;
			}			
   }

   function uploadStaffProfileImage( $staff_id , $data ){
	   $post = $this->input->post(); 
	   if( isset($_FILES) && !empty($_FILES['profile_image']) && !empty($_FILES['profile_image']['name'])):	
		   $this->load->library('fileupload/fileupload');	  
		   $this->fileupload->upload($_FILES['profile_image']);
			if($this->fileupload->uploaded){			
				$this->fileupload->file_new_name_body   = 'staff_profile_image_'.$data['business_id'].'_'.$staff_id;
				$this->fileupload->file_overwrite  = true;
				$dst_path = STAFF_PROFILE_IMAGE_PATH;				
				$this->fileupload->process($dst_path);
				if ($this->fileupload->processed){
					$business_logo = str_replace( $dst_path,"",$this->fileupload->file_dst_pathname);
					$business_logo = trim(str_replace( '\\','',$business_logo));					
					$whereStaff = array( 'staff_id' => $staff_id );
					$whereUser = array( 'user_id' => $staff_id );					
					$this->db->where($whereStaff)->update(BUSINESS_STAFFS,array('profile_image'=>$business_logo));
					$this->db->where($whereUser)->update(USERS,array('profile_image'=>$business_logo));
					
					$this->fileupload->clean(); 
				}
			}
		endif;
   }
   
   function getBusinessStaffs(){		
		$businessStaffs = $this->db->select("a.*, CONCAT(u.firstName, ' ', u.lastName) as full_name")
				->from(ASSOCIATIONS.' a')
				->join(USERS.' u', 'u.user_id=a.association_to', 'LEFT')
				->where(array('a.association_from'=>loginCompanyInfo('business_id'),'a.group_id' => 7 , 'a.status'=>'Active' ))
				->order_by('a.association_to','desc' )
				->get()->result_array();							
		if(!empty($businessStaffs)){
			return $businessStaffs;
		} else {
			return array(); 
		}
   }
   
   
   
   
   	function get_staff_list($business_id){
		$table = USERS;		
		$staffs = $this->db->get_where( $table ,array('business_id'=>$business_id,'status'=>'Active'))->result_array();
		if(!empty($staffs)){
			return $staffs;
		} else {
			return array(); 
		}
   }
   
   	function get_staff_details( $business_id, $staff_id ){
		$table = BUSINESS_STAFFS;		
		$staff = $this->db->get_where( $table ,array('business_id'=>$business_id,'user_id' => $staff_id , 'status'=>'Active'))->result_array();
		if(isset($staff) && !empty($staff) && !empty($staff[0])){
			return $staff[0];
		} else {
			return array(); 
		}
   }
   
   function get_business_locations($business_id){
	   $table = BUSINESS_LOCATIONS ;
	   $branchs = $this->db->get_where( $table ,array( 'business_id'=>$business_id, 'status'=>'Active'))->result_array();
		if(isset($branchs) && !empty($branchs)){
			return $branchs;
		} else {
			return array(); 
		}
   }
   
   function addBatchData($postData,$timezone){	   	
				$business_id  = loginCompanyInfo('business_id');
				$prepareData = array();

				$weekdays = weekdays();
				for($w=0; $w<count($weekdays); $w++){
					$dayname = $weekdays[$w];
					
					if(isset($postData[$dayname.'_active'])){
						$day = $postData[$dayname];
						
						$breakArray = array();
						if(!empty($day)){
							$from_time 	= $day['from_hour'].':'.$day['from_min'].' '.$day['from_ampm'];
							$to_time 	= $day['to_hour'].':'.$day['to_min'].' '.$day['to_ampm'];
							
							//Prepare day data start
							/* $prepareData[] = array('business_id'	=> $business_id,
												   'branch_id'		=> $business_id,
												   'staff_id'		=> '',
												   'break_name'		=> '',
												   'course'			=> '',
												   'day_name'		=> $dayname,
												   'from_time'		=> date('H:i:s',strtotime($from_time)),
												   'to_time'		=> date('H:i:s',strtotime($to_time)),
												   'day_status'		=> 'Available',
												   'created_date'	=> date('Y-m-d H:i:s'),
												   'updated_date'	=> date('Y-m-d H:i:s'),
												   'time_zone'		=> $timezone
												   ); */
							
							//Prepare day data end
							
							//Prepare break data start
							if(isset($day['break'])){
								$break = $day['break'];
								//printr($break);
								for($i=0; $i<count($break['name']); $i++){
									$break_from = $break['from_hour'][$i].':'.$break['from_min'][$i].' '.$break['from_ampm'][$i];
									$break_to 	= $break['to_hour'][$i].':'.$break['to_min'][$i].' '.$break['to_ampm'][$i];
									$course = $break['course'][$i];
									$prepareData[] = array('business_id'	=> $business_id,
												   		   'branch_id'		=> $break['location'][$i],
														   'staff_id'		=> '',
														   'break_name'		=> $break['name'][$i],
														   'course'			=> $course,
														   'day_name'		=> $dayname,
														   'from_time'		=> date('H:i:s',strtotime($break_from)),
														   'to_time'		=> date('H:i:s',strtotime($break_to)),
														   'day_status'		=> 'Break',
														   'created_date'	=> date('Y-m-d H:i:s'),
														   'updated_date'	=> date('Y-m-d H:i:s'),
														   'time_zone'		=> $timezone,
														   );
									
									
								}
							}
							//Prepare break data end
						}
					}else{
					/*	$prepareData[] = array('business_id'	=> $business_id,
											   'branch_id'		=> $business_id,
											   'staff_id'		=> '',
											   'break_name'		=> '',
											   'course'			=> '',
											   'day_name'		=> $dayname,
											   'from_time'		=> '',
											   'to_time'		=> '',
											   'day_status'		=> 'Busy',
											   'created_date'	=> date('Y-m-d H:i:s'),
											   'updated_date'	=> date('Y-m-d H:i:s'),
											   'time_zone'		=> $timezone,
											  ); 
					*/
					}
				}
				// printr($prepareData);
				if(!empty($prepareData)){
					//no error in days breaks
					$this->db->insert_batch(WORKING_HOURS, $prepareData);
					$this->session->set_flashdata('message_success', 'Batch added successfully.');
					redirect(site_url('settings/business/batchs'));
				}else{
					//error in days breaks
					$this->db->delete(WORKING_HOURS, array('location_id' => $location_id));
					//print_r($isFormOK);print_r($isBreakOk);
					//redirect(site_url('settings/business/locations'));
					$this->session->set_flashdata('message_failed', 'Adding Batch failed, There is some problem in hours timing.');
				}
	 }	
   
   function getBatchData($business_id,$batch_id){
		$table = WORKING_HOURS;		
		$batch = $this->db->get_where( $table ,array('business_id'=>$business_id,'hour_id' => $batch_id ))->result_array();
		if(isset($batch) && !empty($batch) && !empty($batch[0])){
			return $batch[0];
		} else {
			return array(); 
		}
   }
   
   function updateBatchData($postData,$timezone){
		$resp = array();
		$business_id  = loginCompanyInfo('business_id');
		$prepareData = array();
		$table =  WORKING_HOURS;
		 if(!empty($postData)){			 	
				$from_time = $postData['from_hour'].':'.$postData['from_min'].' '.$postData['from_ampm'];
				$to_time 	= $postData['to_hour'].':'.$postData['to_min'].' '.$postData['to_ampm'];
				$where = array( 'business_id'=> $business_id, 'hour_id' => $postData['batch_id'] );
				$prepareData = array(
									'branch_id'  	=> $postData['branch_name'],
									'course'  		=> $postData['course_name'],
									'break_name'  	=> $postData['batch_name'],
									'day_name'  	=> $postData['day_name'],
									'from_time'  	=> date('H:i:s',strtotime($from_time)),
									'to_time'  		=> date('H:i:s',strtotime($to_time)),
									'updated_date'	=> date('Y-m-d H:i:s'),
									'time_zone'		=> $timezone									
									);
			 $upResp = $this->db->where($where)->update($table, $prepareData);
			 if($upResp){ return true; } 
			 else { return false; }
		 }  
	   
   }
}