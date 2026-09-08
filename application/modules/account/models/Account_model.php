<?php
#[AllowDynamicProperties]
class Account_model extends CI_Model {
    var $details;
	
	/**
	 * Function		:	validate_user
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	Email	-	$email
	 					String	-	$password
	 * Return		: 	TRUE/FALSE
	 * Description	: 	This function validate user email and password on login.
	**/
	
	function validate_user($email, $password){
		$this->db->select("a.*, CONCAT(a.first_name, ' ', a.last_name) as full_name, ag.group_name");
        $this->db->from(ADMINISTRATOR.' a');
		$this->db->join(ADMINISTRATOR_GROUP.' ag', 'ag.group_id = a.group_id', 'LEFT');
        $this->db->where('a.email', $email);
        $result = $this->db->get();
		$records = $result->num_rows();
		$recordsData = $result->result_array();
		$result->free_result();
		
        if($records > 0) {					
            // check password with stored password	
		//echo decrypt_data($recordsData[0]['password']) . '===='. trim($password);
		//echo $records['password'];
		//die;
			if(decrypt_data($recordsData[0]['password']) == trim($password)){		
				$this->set_session($recordsData[0]);
				$this->set_session_companyData($recordsData[0]['business_id']);		
				return true;
			}         
        }
        return false;
    }
    
	/**
	 * Function		:	set_session
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	Array	-	$userData
	 * Return		: 	NULL
	 * Description	: 	This function sets user info on session.
	**/
	function set_session($userData = '') {		
		$this->session->set_userdata(array(SESSION_NAME . '_loginUserInfo' => $userData));										
		$this->updateUserLoginStatus($userData['admin_id']);		
	}
	
	/**
	 * Function		:	set_session_companyData
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	Integer	-	$application_id
	 * Return		: 	NULL
	 * Description	: 	This function sets company info on session.
	**/
	function set_session_companyData($client_id = ''){ 
	
		$result = $this->db->select('bd.*')
				->from(BUSINESS_DETAIL . ' bd')
				//->join(USERS.' u', 'u.user_id = bd.user_id','left')
				//->join(USERS_GROUP.' ug', 'ug.group_id = u.group_id','left')
				->where( array('bd.user_id'=> $client_id ))->get()->result_array();					
		if(count($result) > 0){
			$result = $result[0];
			$result['business_id'] = $client_id;
		}else{
			$result = array();
		}
		$this->session->set_userdata( array(SESSION_NAME . '_loginCompanyInfo' => $result) );		
	}
	
	/**
	 * Function		:	updateUserLoginStatus
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	Integer	-	$admin_id
	 * Return		: 	NULL
	 * Description	: 	This function update user last login on database table.
	**/
	function updateUserLoginStatus($admin_id=''){
		if($admin_id !=''){
			$dataArray = array('lastLoginDate' => date('Y-m-d h:i:s'));
			$this->db->update(ADMINISTRATOR, $dataArray, array('admin_id' => $admin_id));
		}
	}	
	/**
	 * Function		:	uploadProfileImage
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	String $files
	 					String $dstPath
						String $fileBodyName
						Integer $size
						Integer userId
	 * Return		: 	file_name
	 * Description	: 	This function upload the profile image .
 	**/
	
	function uploadProfileImage($files, $dstPath, $fileBodyName='', $size=200, $userId=''){
		
		if(!empty($files['name'])){			
			$ext = @pathinfo($files['name'], PATHINFO_EXTENSION);				
			$file_name   = 'admin_'.$userId.'.'.$ext;
			if(!file_exists($dstPath) && !is_dir($dstPath)){
				 mkdir($dstPath, 0755, true);				
			}
			$file_path = $dstPath.'/'.$file_name;		
			@move_uploaded_file($files['tmp_name'], $file_path);		
			//$img = resize_images($file_path,271,221, $dstPath.'/thumb/');		
			$img = resize_images($file_path, 150, 150, $dstPath.'/150150/');		
			//resize_images		
		}
		return $file_name;				
	}
	
	/**
	 * Function		:	updateProfileImage
	 * Author		: 	Tarun Malviya
	 * Author Email	: 	tarun.malviya@techlect.com
	 * Params		: 	Integer	- $userId
	 * Return		: 	true
	 * Description	: 	This function update the profile image .
 	**/
	
	function updateProfileImage($userId){
		$table= USERS;
		
		$this->db->select('profileImage');
		$this->db->from($table);		
		$this->db->where(array('userId'=>$userId));
		$query = $this->db->get();
		foreach ($query->result_array() as $resultData){
			
			$userMainImage = USER_IMAGE_DIRPATH.DIRECTORY_SEPARATOR.$resultData['profileImage'];
			//$userThumbImage = USER_IMAGE_DIRPATH.DIRECTORY_SEPARATOR.'thumb'.DIRECTORY_SEPARATOR.$resultData['profileImage'];
			$userThumb150150 = USER_IMAGE_DIRPATH.DIRECTORY_SEPARATOR.'150150'.DIRECTORY_SEPARATOR.$resultData['profileImage'];
			
			if($resultData['profileImage'] !='' && is_file($userMainImage) && file_exists($userMainImage)){
				@unlink($userMainImage);
			}
			/*if($resultData['profileImage'] !='' && is_file($userThumbImage) && file_exists($userThumbImage)){
				@unlink($userThumbImage);
			}*/
			if($resultData['profileImage'] !='' && is_file($userThumb150150) && file_exists($userThumb150150)){
				@unlink($userThumb150150);
			}								  
			
		} // CLose foreach loop
		return true;
	}
	
	function registerUserData($data){
		$response = array('action'=>'inserted', 'uid' => '', 'bid' => '', 'aid' => '', ''=> 'message' ,'status'=>'error');
		$admin_id = ''; 
		$bid = '';
		$user_id = '';
		if(!empty($data)){
			$bussinessTable = BUSINESS_DETAIL;
			$adminTable = ADMINISTRATOR;						
			/*  insert user info as user */			
			$nameArr = explode(" ",$data['full_name']);
			
			$firstName  = ( isset($nameArr) && !empty($nameArr) && isset($nameArr[0]) && !empty($nameArr[0]) ) ? $nameArr[0] : '';
			$middleName = ( isset($nameArr) && !empty($nameArr) && isset($nameArr[1]) && !empty($nameArr[1]) ) ? $nameArr[1] : '';
			$lastName   = ( isset($nameArr) && !empty($nameArr) && isset($nameArr[2]) && !empty($nameArr[2]) ) ? $nameArr[2] : '';
			
			$prepUserData = array(
							'group_id' 		=> $data['user_group'],
							'displayName' 	=> $data['full_name'],									
							'firstName' 	=> $firstName,									
							'middleName' 	=> $middleName,									
							'lastName' 		=> $lastName,									
							'email' 		=> trim($data['email']),
							'phone_number' 	=> $data['phone'],
							'password' 		=> encrypt_data($data['password'])
							);			 
			$this->db->insert(USERS, $prepUserData);
			$user_id = $this->db->insert_id();
			/*  insert user info as user */
			/*  insert user info as bussiness */
			if(!empty($user_id)){ 
				$prepBusinessData = array(
									'user_id' 			=> $user_id,
									'business_name' 	=> $data['full_name'],
									'email' 			=> trim($data['email']),									
									'business_phone' 	=> $data['phone'],
									'create_date' 		=> date('Y-m-d H:i:s')
								);	
				$this->db->insert( $bussinessTable, $prepBusinessData );
				$bid = $this->db->insert_id();
			/*  insert user info as bussiness */
			/*  insert user info as Admin */
				if(!empty($bid)){					
						/*  insert user info as Admin */
						$prepAdminData = array(
											'business_id' 		=> $user_id,
											'group_id' 			=> $data['user_group'],
											'first_name' 		=> $firstName,
											'last_name' 		=> $lastName,
											'email' 			=> $data['email'],									
											'email' 			=> $data['email'],									
											'phone_number' 		=> $data['phone'],
											'password' 			=> encrypt_data($data['password']),
										);	
						$this->db->insert( $adminTable, $prepAdminData);
						$admin_id = $this->db->insert_id();
						/*  insert user info as Admin */
						if(!empty($admin_id) &&  $admin_id !=''){	
							$response['uid']    = $user_id;				
							$response['bid']    = $bid;				
							$response['aid']    = $admin_id;				
							$response['status'] = 'success';
						} else {
							/*  Delete user from as users */
							$this->db->delete( USERS, array( 'user_id' => $user_id ) );
							/*  Delete user from as bussiness */
							$this->db->delete( $bussinessTable, array( 'business_id' => $bid ) );
						}
					}
			/*  Prepare Response */
			}			  	
		}	
		return $response;
	}
	
	function getProfileData($admin_id){	
		$this->db->select("a.*, CONCAT(a.first_name, ' ', a.last_name) as full_name, ag.group_name");
        $this->db->from(ADMINISTRATOR.' a');
		$this->db->join(ADMINISTRATOR_GROUP.' ag', 'ag.group_id = a.group_id', 'LEFT');
        $this->db->where('a.admin_id', $admin_id);
        $result = $this->db->get()->result_array();
        if(!empty($result)) {					
            return $result[0];     
        }
        return false;
    }
}
