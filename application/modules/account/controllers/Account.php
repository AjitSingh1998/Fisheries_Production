<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Account extends MY_Controller {
	var $userID;
	public $CI;
	
	public function __construct(){
		parent::__construct();
		//$this->template_name = 'landing-page';
		$this->userID = loginUserInfo('admin_id');
		$this->load->model('account_model', 'AM');
		$this->form_validation->CI = & $this;
    } 
	
	function display_table(){
		//die('ghgfhgf'); show tables
		$tables = $this->db->query("SELECT t.TABLE_NAME AS myTables FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = 'simaqua_punasa_new'")->result_array();
		//printr($tables);
		foreach($tables as $key => $val) {
			$table = str_replace('psac_','',$val['myTables']);
			$pref = '$db_prefix.';
			echo " defined('".strtoupper($table)."') OR define('".strtoupper($table)."', ".$pref."'".$table."');"."<br>";
		}
	}
	
	public function index(){
		if(isset($this->userID) && !empty($this->userID)){
			redirect(site_url('dashboard'));
		}
		$data['content_view'] = 'account/login_v';
		$this->template->set('document_title', 'Login to Account');
		$this->load->view('account/login_v');			
		//$this->template->layout($data, $this->template_name);
	}
	public function login(){
		$user_id = $this->userID;
		if(isset($user_id) && !empty($user_id)){
			redirect(site_url('dashboard'));
		}
		
		// Grab the email and password from the form POST
		$checkValidation = $this->__setFormRules();
		
		if($checkValidation){
			  
			 $email = $this->input->post('email');
			 $pass  = $this->input->post('password', false); 
			 //printr($this->input->post());
			//Ensure values exist for email and pass, and validate the user's credentials 
			if($this->AM->validate_user($email, $pass)) {	
				redirect('dashboard', 'refresh');		
			} else {
				// Otherwise show the login screen with an error message.
				$this->messageci->set('Incorrect Email-ID and Password','error');
			}	
		}
		
		$data['content_view'] = 'account/login_v';
		$this->template->set('document_title', 'Login to Account');
		$this->load->view('account/login_v');			
		//$this->template->layout($data, $this->template_name);
		
	}
	
	public function register(){
		redirect(site_url('account/login'));
		/*
		$tables=$this->db->query("SELECT t.TABLE_NAME AS myTables FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = 'partners'")->result_array();    
		foreach($tables as $key => $val) {
			$table = str_replace('fd_partner_','',$val['myTables']);
			$table2 = str_replace('fd_','',$val['myTables']);
			$pref = '$db_prefix.';
			echo " defined('".strtoupper($table)."') OR define('".strtoupper($table)."', ".$pref."'".$table2."');"."<br>";
		}
		die;
		*/ 
		
		$user_id = $this->userID;
		if(isset($user_id) && !empty($user_id)){
			redirect(site_url('dashboard'));
		}
		
		$post = $this->input->post();
		$validateForm = $this->__setFormRules('register');
		if( $validateForm && !empty($post)){
			$resp = $this->AM->registerUserData($post);
			if(!empty($resp['aid']) && $resp['status']=='success' ){
				$this->messageci->set( 'User has been successfully saved.' , 'success') ;
				redirect(site_url('account/login' ));
			} else {
				$this->messageci->set( 'Data failed to save.' , 'error');
				redirect(site_url('account/register' ));
			}					
		}	
		
		$data['user_group'] = $this->input->get('group');
		//$data['page_title'] = 'Register Account';
		$data['content_view'] = 'account/register_v';
		
		$this->template->set('document_title', 'Techlect Practice - Register Account');
		$this->template->set('stylesheet', array(base_url('assets/modules/account/css/account.css')));
		$this->template->set('scriptsrc', array(
												base_url('assets/plugins/jquery-validation/dist/jquery.validate.js'),
												site_url('account/assets/js/account-validation.js')	
												)
							);
								
		$this->template->layout($data, $this->template_name);
	}
	
	public function updateProfileImage(){
		$userID = checkUserLogin();
		
		if($_FILES['profile_image']['name']!=''){
			$fileBodyName = strtotime(date('Y-m-d h:i:s'));				
			$imageName = $this->AM->uploadProfileImage($_FILES['profile_image'], USER_IMAGE_DIRPATH, $fileBodyName, '200');
			if($imageName != ''){
				$this->AM->updateProfileImage($this->userID);
			}												
			$imageData = array('profile_image' => $imageName);
			$result = $this->db->update('demo_user', $imageData, array('user_id' => $this->userID));
			if($result){
				$this->message[] = array('success' => 'Your profile picture has been changed!');
			}else{
				$this->message[] = array('error' => 'Your profile picture did not changed!');
			}
			
		}else{			
			$this->message[] = array('error' => 'Kindly select Valid Image!');			
		}		
		$msg = array('message' => $this->message);
		$this->session->set_userdata($msg);
		redirect('mydashboard/profile');		
	}
	
	public function edit_profile(){
		$user_id = $this->userID;
		if(empty($user_id)){
			redirect(site_url('account/login'));
		}
		$data['tab'] = $this->uri->segment(2); 
		$validateProfile = $this->__setFormRules('edit_profile');
		if($validateProfile){	
			$post = $this->input->post();						
			$userData = array(
							 'first_name' 		=> $post['first_name'],
							 'last_name' 		=> $post['last_name'],
							 'email' 			=> $post['email'],
							 'phone_number' 	=> $post['phone_number'],
							 'phone_number2'  	=> $post['phone_number2'],
							 'address' 			=> $post['address'],
							 'postal_code' 		=> $post['postal_code'],
							 'state' 			=> $post['state'],
							 'city' 			=> $post['city'],
							 'gender' 			=> $post['gender']
							 );
			$upResp = $this->db->update(ADMINISTRATOR, $userData, array('admin_id' => $user_id));			
			if($upResp){
				$this->db->select("a.*, CONCAT(a.first_name, ' ', a.last_name) as full_name, ag.group_name");
				$this->db->from(ADMINISTRATOR.' a');
				$this->db->join(ADMINISTRATOR_GROUP.' ag', 'ag.group_id = a.group_id', 'LEFT');
				$this->db->where('a.admin_id', $this->userID);
				$result = $this->db->get()->result_array();	
				if(!empty($result)) {					
					 $this->AM->set_session($result[0]);
				}
				$this->messageci->set('Your account has been updated successfully!', 'success');
				redirect(site_url('account/edit_profile'));
			} else{
				$this->messageci->set('There is problem to update your profile! Please try again later.', 'error' );
			}
		} 
		$this->template->set('scriptsrc', array(
			base_url('assets/plugins/jquery-validation/dist/jquery.validate.js'),
			site_url('account/assets/js/account-validation.js'),											
		));		
		
		$data['userInfo'] = $this->AM->getProfileData($this->userID);
		$data['page_title'] = 'Edit Profile';
		$data['content_view'] = 'account/edit_profile_v';		
		$this->template->set('document_title', 'Edit profile');		
		$this->template->layout($data, $this->template_name);
	}
	
	public function photo(){
		
		$user_id = $this->userID;
		if(empty($user_id)){
			redirect(site_url('account/login'));
		}
		
		$data['tab'] = $this->uri->segment(2); 
		if(isset($_FILES) && !empty($_FILES['profileImage']['name'])){
			$profileImagePath = USER_PROFILE_IMAGE_DIR;
			$imageName = $this->AM->uploadProfileImage($_FILES['profileImage'], $profileImagePath, '', '200', $this->userID);				
			if($imageName != ''){
				$userData = array('profileImage' => $imageName );	
				$upResp = $this->db->update(ADMINISTRATOR, $userData, array('admin_id' => $user_id));			
				$this->db->select("a.*, CONCAT(a.first_name, ' ', a.last_name) as full_name, ag.group_name");
				$this->db->from(ADMINISTRATOR.' a');
				$this->db->join(ADMINISTRATOR_GROUP.' ag', 'ag.group_id = a.group_id', 'LEFT');
				$this->db->where('a.admin_id', $this->userID);
				$result = $this->db->get()->result_array();	
				if(!empty($result)) {					
					 $this->AM->set_session($result[0]);
				}
				$this->messageci->set('Your profile image has been updated successfully!', 'success');
				redirect(site_url('account/photo'));
			} else{
				$this->messageci->set('There is problem to update your profile image! Please try again later.', 'error' );
			}
		}
		
		$data['userInfo'] = loginUserInfo();
		$data['page_title'] = 'Edit profile photo';
		$data['content_view'] = 'account/change_photo_v';		
		$this->template->set('document_title', 'Edit profile photo');	
		$this->template->set('stylesheet', array(base_url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css')));
		$this->template->set('scriptsrc',  array(base_url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js')));
		$this->template->layout($data, $this->template_name);
	}
	
	public function change_password(){
		$this->userID = checkUserLogin();		
		$data['tab'] = $this->uri->segment(2); 
		$passChangeFormValidation = $this->__setFormRules('change_password');
		if($passChangeFormValidation){
		    $postData=$this->input->post();
			$oldpass = $postData['oldpass'];
			$newpass = $postData['newpass'];
			$newPass = array('password' => encrypt_data($newpass));			
			$where = array('admin_id'=> $this->userID);
			
			if($this->db->update(ADMINISTRATOR, $newPass, $where)){				
				//send Email notification start
				/*$parseArray = array('name'=>loginUserInfo('first_name').' '.loginUserInfo('last_name'));
				$message = $this->load->view('mail_template/password_change', $parseArray, TRUE);
				$dataArray = array( 'to' => loginUserInfo('email'),
									'from'=> loginCompanyInfo('email'),
									'subject'=> 'Your profile password has been changed',
									'message'=> $message
									);
				$Emailresult = sendEmailCI($dataArray);*/
				//send Email notification end
				$this->messageci->set('Your Password has been changed successfully!', 'success');
				redirect('account/change_password');
			}else{
				$this->messageci->set('Password changing faild, Please try again!', 'error');
			}
		}
		
		$data['userInfo'] = loginUserInfo();		
		$data['page_title'] = 'Change password';
		$data['content_view'] = 'account/change_password_v';
		
		$this->template->set('document_title', 'Techlect Practice - Change password');
		$this->template->set('scriptsrc', array(base_url('assets/plugins/jquery-validation/dist/jquery.validate.js'),
												site_url('account/assets/js/account-validation.js'),											
												));		
		$this->template->set('scripts', '<script>$(document).ready(function(e){Login.init();});</script>');
		$this->template->layout($data);
	}	
	public function checkoldpass($password){
		$result = $this->db->select('password')->where('admin_id', $this->userID)->get(ADMINISTRATOR)->result_array();
		$oldpass = !empty($result) ? decrypt_data($result[0]['password']) : '';
		if(trim($password) == $oldpass){
			return true;
		}else{
			return false;
		}
	}
	public function logout(){		
		$this->session->sess_destroy();
      	//redirect('login/index','referece');
		$url = site_url();
		echo "<script type='text/javascript'>window.location.href = '".$url."'</script>";
        exit();
	}
	
	function getCitiesAjax(){
		$post = $this->input->post();
		$regionid = $post['regionid'];
		if($regionid !=''){
			$city_list = $this->db->where(array('regionid' => $regionid, 'status'=>'Active'))->get($this->db->dbprefix.'country_region_cities')->result_array();
			$options = '<option value="">--Select City--</option>'."\n";
			if(count($city_list) > 0){
				foreach($city_list as $city){
					$selected = '';
					if($valuesIn == 'name') {
						$options .= '<option '.$selected.'  value="'.$city['city'].'">'.$city['city'].'</option>'."\n";	
					}else{
						$options .= '<option '.$selected.'  value="'.$city['cityId'].'">'.$city['city'].'</option>'."\n";
					}			
				}
			}
		}else{
			$options = '<option value="">--Please Select State--</option>'."\n";
		}
		echo $options;
		die();
	}
	public function uniqueemail($emailVal){		
		$result = $this->db->where(array('email' => $emailVal, 'admin_id !='=>$this->userID))->count_all_results(ADMINISTRATOR);
		if($result > 0 ){
			return false;				
		}else{
			return true;
		}		
	}
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case'change_password':
				$this->form_validation->set_rules('oldpass', 'Old Password', 'trim|required|callback_checkoldpass', array('checkoldpass' => 'The %s is not valid, kindly enter valid one!'));
				$this->form_validation->set_rules('newpass', 'New Password', 'trim|required|min_length[8]|matches[newpassconf]');
				$this->form_validation->set_rules('newpassconf', 'New Password Confirm', 'trim|required');
			break;
			case'changePassword_verify':
				$this->form_validation->set_rules('newpass', 'New Password','trim|required|min_length[6]|matches[newpassconf]');
				$this->form_validation->set_rules('newpassconf', 'New Password Confirm', 'trim|required');
			break;
			case'register':
				$this->form_validation->set_rules('user_group', 'User Group', 'trim|required');
				$this->form_validation->set_rules('full_name', 'Full name', 'trim|required');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[fd_users_administrator.email]');
				$this->form_validation->set_rules('phone', 'Phone', 'trim|required|is_unique[fd_users_administrator.phone_number]');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[fd_user.email]');
				$this->form_validation->set_rules('phone', 'Phone', 'trim|required|is_unique[fd_user.phone_number]');
				$this->form_validation->set_rules('password', 'Password', 'trim|required');
			break;
			case'edit_profile':
				$this->form_validation->set_rules('first_name', 'First name', 'trim|required');
				$this->form_validation->set_rules('email', 'Primary Email', 'trim|required|valid_email|callback_uniqueemail',array('uniqueemail'=>'The %s is already exists, Please enter some other.'));			
				$this->form_validation->set_rules('phone_number', 'Primary phone number', 'trim|required');				
			break;
			default:
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
				$this->form_validation->set_rules('password', 'Password', 'trim|required');
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run();
	}

	public function show_users()
    {
		$offset = $this->input->get('offset');
		$limit = $this->input->get('limit');
		$this->db->select('u.admin_id,  u.email, u.password, u.status, ag.group_name');
		$this->db->from(ADMINISTRATOR.' u');
		$this->db->join(ADMINISTRATOR_GROUP.' ag', 'ag.group_id = u.group_id', 'LEFT');
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$result = $this->db->get()->result_array();
		if(count($result) > 0) {
			foreach($result as $key=>$row){
				$result[$key]['password'] = decrypt_data($row['password']);
			}
			printr($result);
		}else{
			echo 'No data found';
		}
    }
}