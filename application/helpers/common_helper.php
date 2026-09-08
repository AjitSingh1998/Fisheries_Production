<?php
function is_internet_connected()
{
    $connected = @fsockopen("www.simransolvex.com", 80); //website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;
}
/**
 * Function		:	ago
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Unix Timestamp	-	timestamp
 * Return		: 	time
 * Description	: 	This function returns the time difference from current time.
**/
function ago($timestamp = ''){
 if(empty($timestamp)){
  return false;
 }
 // $difference = time() - strtotime($timestamp);
  $difference = time() - $timestamp;
  $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'years', 'decade');
  $lengths = array('60', '60', '24', '7', '4.35', '12', '10');
  for($j = 0; $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];
  $difference = round($difference);
  if($difference != 1) $periods[$j] .= "s";
  return "$difference $periods[$j] ago";
}
/**
 * Function		:	get_time_ago
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Datetime	-	datetime
 * Return		: 	time
 * Description	: 	This function returns the time difference from current time.
**/
function get_time_ago($datetime){
	
	$time = strtotime($datetime);
	$time = time() - $time; // to get the time since that moment	
	$tokens = array (
		31536000 => 'year',
		2592000 => 'month',
		604800 => 'week',
		86400 => 'day',
		3600 => 'hour',
		60 => 'minute',
		1 => 'second'
	);	
	foreach ($tokens as $unit => $text) {
		if ($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
	}
}
/**
 * Function		:	safe_b64encode
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String	-	string
 * Return		: 	data
 * Description	: 	This function encode the given string in base64_encode.
**/
function safe_b64encode($string) {
 
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
}
 
/**
 * Function		:	safe_b64decode
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String	-	string
 * Return		: 	data
 * Description	: 	This function decode the given string in safe_b64decode.
**/
function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
}
/**
 * Function		:	encrypt_data
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Text	-	data
 * Return		: 	encrypted string
 * Description	: 	This function encrypt the give string.
*
function encrypt_data($data){
	  $CI = & get_instance();
	  $CI->load->library('encrypt');	 
	  $key = $CI->config->item('encryption_key');
	  return  $CI->encrypt->encode($data, $key);
}*/
/**
 * Function		:	decrypt_data
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Text	-	value
 * Return		: 	decrypted string
 * Description	: 	This function decrypt the give string.
*	 
function decrypt_data($value){
		$CI = & get_instance();
		$CI->load->library('encrypt');	 
		$key = $CI->config->item('encryption_key');
		return  $CI->encrypt->decode($value, $key);
}*/	

function encrypt_data($data){	  
  	$CI = & get_instance();	  	
	$CI->load->library('encryption');	
	$key = $CI->config->item('encryption_key');	
	$config = array( 'driver' => 'openssl', 'cipher' => 'aes-128', 'mode' => 'cbc', 'key' => $key);
	$CI->encryption->initialize($config);		
	return $value = $CI->encryption->encrypt($data);
}

function decrypt_data($value){
	$CI = & get_instance();		
	$CI->load->library('encryption');	
	$key = $CI->config->item('encryption_key');	
	$config = array( 'driver' => 'openssl', 'cipher' => 'aes-128', 'mode' => 'cbc', 'key' => $key);
	$CI->encryption->initialize($config);	
	return $value = $CI->encryption->decrypt($value);
}




/**
 * Function		:	resize_images
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	url		-	path
 					Numeric	-	rs_width
					Numeric	-	rs_height
					Text	-	destinationFolder
 * Return		: 	NULL
 * Description	: 	This function resize the image.
**/	
function resize_images($path, $rs_width, $rs_height, $destinationFolder='') {
	$folder_path = dirname($path);
	$thumb_folder = $destinationFolder;
	$percent = 0.5;
	
	if (!is_dir($thumb_folder)) {
		mkdir($thumb_folder, 0777, true);
	}
	
	$name = basename($path);
	$x = getimagesize($path);            
	$width  = $x['0'];
	$height = $x['1'];
	
	switch ($x['mime']){
		case "image/gif":
			$img = imagecreatefromgif($path);
		break;
		case "image/jpeg":
			$img = imagecreatefromjpeg($path);
		break;
		case "image/jpg":
			$img = imagecreatefromjpeg($path);
		break;
		case "image/png":
			$img = imagecreatefrompng($path);
		break;
	}
    $img_base = imagecreatetruecolor($rs_width, $rs_height);
    $white = imagecolorallocate($img_base, 255, 255, 255);
    imagefill($img_base, 0, 0, $white);
    
	imagecopyresized($img_base, $img, 0, 0, 0, 0, $rs_width, $rs_height, $width, $height);
    imagecopyresampled($img_base, $img, 0, 0, 0, 0, $rs_width, $rs_height, $width, $height);
    $path_info = pathinfo($path);   
    $dest = $thumb_folder.$name;
	switch ($path_info['extension']) {
	  case "gif":
		 imagegif($img_base, $dest);  
		 break;
	  case "jpg":
		 return imagejpeg($img_base, $dest);  
		 break;
	  case "jpeg":
		 return imagejpeg($img_base, $dest);  
		 break;
	  case "png":
		return imagepng($img_base, $dest);  
		 break;
	}
}
/**
 * Function		:	getTimeZoneList
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String $selected_timezone
 * Return		: 	$option dropdown for time zone
 * Description	: 	This function return dropdown listing for the timezone.
**/
function getTimeZoneList($selected_timezone = ''){
	$timezones =  DateTimeZone::listIdentifiers(DateTimeZone::ALL);	
	$options = '<option value="">--Select Timezone--</option>'."\n";
	if(count($timezones) > 0){
		foreach($timezones as $timezone){
			$selected = ($selected_timezone == $timezone) ? 'selected="selected"':'';			
			$options .= '<option '.$selected.'  value="'.$timezone.'">'.$timezone.'</option>'."\n";
		}
	}
	return $options;
}
/**
 * Function		:	displayDate
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Date 	- $date
 					String	- $format
 * Return		: 	$value
 * Description	: 	This function return date in given format.
**/
function displayDate($date='', $format='d M, Y h:i A'){	
	$value = $date;
	if($value != '' && $value != '0000-00-00 00:00:00'){
		$timezone = date_default_timezone_get();
		$defaultTimeZone = DEFAULT_TIME_ZONE;
		$userTimeZone = loginUserInfo('time_zone');
		
		$datetime = new DateTime($value, new DateTimeZone($timezone));
		
		if(isset($userTimeZone) && !empty($userTimeZone))
			$datetime->setTimezone(new DateTimeZone($userTimeZone));
		else if(isset($defaultTimeZone) && !empty($defaultTimeZone))
			$datetime->setTimezone(new DateTimeZone($defaultTimeZone));
		else
			$datetime->setTimezone(new DateTimeZone($timezone));
			
		$value = $datetime->format($format);
		
	}else{
		$value = '';
	}
	
	return $value;	
}
/**
 * Function		:	getCountry
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String 		- $country_code
 * Return		: 	country list dropdown
 * Description	: 	This function generate the dropdown of country list.
**/
function getCountry($country_code=''){
	$CI = & get_instance();	
	$country_list = $CI->db->where('status', 'Active')->get($CI->db->dbprefix.'country')->result_array();
	$options = '<option value="">--Select Country--</option>'."\n";
	if(count($country_list) > 0){
		foreach($country_list as $country){
			$selected = ($country_code == $country['country_code']) ? 'selected="selected"':'';			
			$options .= '<option '.$selected.'  value="'.$country['country_code'].'">'.$country['country_name'].'</option>'."\n";
		}
	}
	return $options;
}
/**
 * Function		:	getStates
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String 		- $country_code
 					String 		- $region_code
 * Return		: 	state list dropdown
 * Description	: 	This function generate the dropdown of state list.
**/
function getStates($countryid='', $regionid='' ,$valuesIn = 'id'){
	$CI = & get_instance();
	if($countryid !=''){
		$state_list = $CI->db->where(array('countryid' => $countryid, 'status'=>'Active'))->get($CI->db->dbprefix.'country_regions')->result_array();
		$options = '<option value="">--Select State--</option>'."\n";
		if(count($state_list) > 0){
			foreach($state_list as $states){
				$selected = ($regionid == $states['regionid']) ? 'selected="selected"':'';
				if($valuesIn == 'name') {
					$options .= '<option '.$selected.'  value="'.$states['region'].'">'.$states['region'].'</option>'."\n";	
				}else{
					$options .= '<option '.$selected.'  value="'.$states['regionid'].'">'.$states['region'].'</option>'."\n";
				}			
			}
		}
		return $options;
	}else{
		return $options = '<option value="">-- Please Select --</option>'."\n";
	}
}
/**
 * Function		:	getCities
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String 		- $country_code
 					String 		- $region_code
 					Integer 	- $city_id
 * Return		: 	state list dropdown
 * Description	: 	This function generate the dropdown of cities list.
**/	
function getCities($countryid='', $regionid='', $city_id='',$valuesIn = 'id'){
	$CI = & get_instance();
	if($regionid !=''){
		$city_list = $CI->db->where(array('countryid'=>$countryid, 'regionid' => $regionid, 'status'=>'Active'))->get($CI->db->dbprefix.'country_region_cities')->result_array();
		$options = '<option value="">--Select City--</option>'."\n";
		if(count($city_list) > 0){
			foreach($city_list as $city){
				$selected = ($city_id == $city['cityId']) ? 'selected="selected"':'';
				if($valuesIn == 'name') {
					$options .= '<option '.$selected.'  value="'.$city['city'].'">'.$city['city'].'</option>'."\n";	
				}else{
					$options .= '<option '.$selected.'  value="'.$city['cityId'].'">'.$city['city'].'</option>'."\n";
				}			
			}
		}
		return $options;
	}else{
		return $options = '<option value="">--Please Select State--</option>'."\n";
	}
}
/**
 * Function		:	checkUserLogin
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String	- check
 * Return		: 	user_id
 * Description	: 	This function checks the user login.
**/
function checkUserLogin(){
	$CI =& get_instance();

	$userdata = $CI->session->userdata(SESSION_NAME . '_loginUserInfo');
	$admin_id = @$userdata['admin_id'];
	if($admin_id==''){
		$CI->messageci->set('Required login to access this page!', 'error');
		redirect(site_url('account/login'));
	}
	return $admin_id;
}
/**
 * Function		:	loginUserInfo
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String	- parameter
 * Return		: 	loginUserInfo
 * Description	: 	This function return the info of login user.
**/
function loginUserInfo($parameter = ''){
	$CI =& get_instance();
	
	$loginUserInfo = $CI->session->userdata(SESSION_NAME . '_loginUserInfo');
	
	if(isset($loginUserInfo) && !empty($loginUserInfo)){
		if($parameter != ''){
			if(!array_key_exists($parameter, $loginUserInfo)){
				//echo $parameter.' This key is not exists!';
				//die;
				return '';
			}else{
				return @$loginUserInfo[$parameter];
			}			
		}else{
			return $loginUserInfo;
		}	
	}else{
		return '';
	}
}
/**
 * Function		:	loginCompanyInfo
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String	- parameter
 * Return		: 	logincompanyInfo
 * Description	: 	This function return the info of login company.
**/
function loginCompanyInfo($parameter = ''){
	$CI =& get_instance();
	$logincompanyInfo = $CI->session->userdata(SESSION_NAME . '_loginCompanyInfo');
	if(isset($logincompanyInfo) && count($logincompanyInfo) > 0){
		if($parameter != ''){
			if(!array_key_exists($parameter, $logincompanyInfo)){
				return '';
				die;
			}else{
				return @$logincompanyInfo[$parameter];
			}			
		}else{
			return $logincompanyInfo;
		}	
	}else{
		return ''; 
		die;
	}	
}
/**
 * Function		:	CheckProfilestatus
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	NULL
 * Return		: 	NULL
 * Description	: 	This function check the company profile status.
**/
function CheckProfilestatus(){
	$CI =& get_instance();
	$result = $CI->db->get_where($CI->db->dbprefix.'company_info',array('company_name!='=>'','email!='=>'','zipcode!='=>'','country!='=>'','state!='=>'','city!='=>0,'address!='=>'','phone_number!='=>'','company_id'=>loginUserInfo('application_id')))->result_array();
	//echo "<pre>"; print_r($result); die; 
	if(empty($result)){
		$CI->messageci->set('You have to fill all mandetory fields!','error');
		redirect('settings/company_details');
	}else{
		return 'AutoLogin';
	}
}
/**
 * Function		:	checkUserPermission
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String	- $method
 					String	- $action
 * Return		: 	NULL
 * Description	: 	This function check the permission for invalid access.
**/
function checkUserPermission($method='', $action=''){
	$CI = & get_instance();
	$menuArray = $CI->session->userdata(SESSION_NAME . '_permission');
	$referer_url = @$_SERVER['HTTP_REFERER']; //$CI->input->server('http_referer');
	$self_url = @$_SERVER['PHP_SELF'];
	if(is_array($menuArray) && array_key_exists($method, $menuArray)){
		$actions = !empty($menuArray[$method]) ? explode('|', $menuArray[$method]) : array();
		$action = str_replace(array('success'), array(''), (string)$action);
		if($action != '' && !$CI->input->is_ajax_request()){
			if(!in_array($action, $actions)){				
				$CI->messageci->set('You are not authorise to '.$action.' data!', 'error');				
				if($referer_url != $self_url){
					redirect($referer_url);
				}
			}
		}
		return $actions;
	}
	$CI->messageci->set('You are not authorise to access this page!', 'error');
	if($referer_url != $self_url){
		redirect($referer_url);
	}
}
/**
 * Function		:	printr
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Array	- $value
 * Return		: 	NULL
 * Description	: 	This function print the giver array.
**/
function printr($value){
 echo '<pre>';
 print_r($value);
 echo '</pre>';
 die;
}
/**
 * Function		:	display_price
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Numeric	- $price
 					String	- $type
 * Return		: 	NULL
 * Description	: 	This function display the price with icon.
**/
function display_price($price, $type='icon'){
 $CI =& get_instance();
 $icon = $CI->config->item('default_currency_icon');
 $c_code = $CI->config->item('default_currency_code');
 if($type == 'icon') {
  $data = (isset($icon) && !empty($icon))?'<i class="fa '.$icon.'"></i>'.' '.$price:$price;
 }else{
  $data = (isset($c_code) && !empty($c_code))?$price.' '.$c_code:$price;
 }
 return $data;
}
/**
 * Function		:	displayImage
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	URL	- $main_image_path
 					URL	- $default_image_path
 * Return		: 	image url
 * Description	: 	This function display the image.
**/
function displayImage($main_image_path='', $default_image_path=''){
	$image = @getimagesize($main_image_path);
	if(is_array($image)){
		return $main_image_path;
	}else{
		return $default_image_path;
	}	
}
/**
 * Function		:	getFileInfo
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Text	-	file
 					Array	-	returned_values
 * Return		: 	fileinfo
 * Description	: 	This function get the property of file.
**/
function getFileInfo($file, $returned_values = array('name', 'server_path', 'size', 'date')){
	if ( ! file_exists($file) && !is_file($file))
	{
		return FALSE;
	}
	if (is_string($returned_values))
	{
		$returned_values = explode(',', $returned_values);
	}
	foreach ($returned_values as $key)
	{
		switch ($key)
		{
			case 'name':
				$fileinfo['name'] = basename($file);
				break;
			case 'server_path':
				$fileinfo['server_path'] = $file;
				break;
			case 'size':
				$fileinfo['size'] = filesize($file);
				break;
			case 'date':
				$fileinfo['date'] = filemtime($file);
				break;
			case 'readable':
				$fileinfo['readable'] = is_readable($file);
				break;
			case 'writable':
				$fileinfo['writable'] = is_really_writable($file);
				break;
			case 'executable':
				$fileinfo['executable'] = is_executable($file);
				break;
			case 'fileperms':
				$fileinfo['fileperms'] = fileperms($file);
				break;
		}
	}
	return $fileinfo;
}
/**
 * Function		:	getFileDetail
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	URL	-	filepath
 * Return		: 	fileInfo array
 * Description	: 	This function return the attached file info.
**/
function getFileDetail($filepath){
	
	if(isset($filepath) && !empty($filepath) && file_exists(FCPATH.$filepath) && is_file(FCPATH.$filepath)){
		
		$name = pathinfo($filepath, PATHINFO_BASENAME);
		$extn = pathinfo($filepath, PATHINFO_EXTENSION );
		$size = filesize($filepath);
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$type = finfo_file($finfo, $filepath);
		finfo_close($finfo);
		
		$content = file_get_contents($filepath);
								
		if(!function_exists('get_magic_quotes_gpc') || !get_magic_quotes_gpc())
		{
			$name = addslashes($name);
		}
		
		return $attachment = array('name' => $name, 'type' => $type, 'size' => $size = format_size($size), 'content'=>$content);
		
	}else{
		return false;
	}
		
}
/**
 * Function		:	format_size
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Numeric	-	size
 * Return		: 	size string
 * Description	: 	This function format the size.
**/
function format_size($size) {
	$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	if ($size == 0) { return('n/a'); } else {
	return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
}
/**
 * Function		:	parse_string
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String	-	$string
 					Array	-	$data
					String	-	$output
 * Return		: 	parsed string
 * Description	: 	This function parse the given string with data array.
**/
function parse_string($string='', $data=array(), $output= TRUE){
	$CI = & get_instance();
	//$CI->load->library('parser');
	$string = $CI->parser->parse_string($string, $data, $output);
	return $string;
}
/**
 * Function		:	parse_string
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	URL		-	$template_path
 					Array	-	$data
					String	-	$output
 * Return		: 	parsed string
 * Description	: 	This function parse the given template with data array.
**/
function parse_template($template_path='', $data=array(), $output= TRUE){
	$CI = & get_instance();
	//$CI->load->library('parser');
	$string = $CI->parser->parse($template_path, $data, $output);
	return $string;
}
/**
 * Function		:	sendEmail
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * params		:
		 			$template_name	 string, unique name of mail template to fetch template content from database,
					$content_data 	 associative array('to_name'=>'', to_email=>'', 'from_name'=>'', 'from_email'=>''), 
		 			$subject 	string, 
					$body 		string, 
					$attachment array
 * Return		: 	TRUE/FALSE
 * Description	: 	This function sends email to recepients.
*/
function sendEmail($data = array(), $template_name=''){
	$CI = & get_instance();
	$CI->load->library('email');
	$subject = '';
	$message = '';
	$result = '';
	
	if($data['to_email'] == '' && empty($data['to_email'])){
		$CI->messageci->set('E-mail sending failed, the recipient email id is missing!', 'error');
		return false;			
	}	
	
	if($template_name !=''){
		$CI->db->where('unique_name', $template_name);
		$templateResult = $CI->db->get('email_templates')->result_array();
		foreach($templateResult as $template){
			$subject = parse_string($template['subject'], $data);
			$message = parse_string($template['message'], $data);
		}
	}else{
		$subject = parse_string($data['subject'], $data);
		$message = parse_string($data['message'], $data);
	}
	
	$CI->email->from(NO_REPLY_EMAIL_ID, SITE_NAME);	
	$CI->email->to($data['to_email']);
	$CI->email->subject($subject);
	$CI->email->message($message);
	$CI->email->set_mailtype('html');
	if(!empty($data['attachments']))
	{
		foreach($data['attachments'] as $attachment){
			$CI->email->attach(base_url($attachment));
		}
	}		
	
	$result = $CI->email->send();
	
	return $result;
}
/**
 * Function		:	sendEmailCI
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	$to				can be string, array or comma saparated emailid, 
				 	$from 			array('name'=>'', email=>'') or string, 
					$reply_to 		array('name'=>'', email=>'') or string, 
				 	$subject 		string, 
					$message 		string, 
				 	$attachments 	filename array
 * Return		: 	TRUE/FALSE
 * Description	: 	This function sends email to recepients.
**/
function sendEmailCI($dataArray = array()){
	$CI = & get_instance();
	$CI->load->library('email');	
	$CI->email->set_mailtype('html');
	if(array_key_exists('from', $dataArray) && is_array($dataArray['from'])){
		$CI->email->from($dataArray['from']['email'], $dataArray['from']['name']);
	}else if(array_key_exists('from', $dataArray) && !is_array($dataArray['from']) && !empty($dataArray['from'])){
		$CI->email->from($dataArray['from']);
	}else{
		$CI->email->from('service@techlect.com', 'Form Duniya');
	}
	if(array_key_exists('to', $dataArray) && is_array($dataArray['to'])){
		$CI->email->to($dataArray['to']['email'], $dataArray['to']['name']);
	}else{
		$CI->email->to($dataArray['to']);
	}
	if(array_key_exists('reply_to', $dataArray) && isset($dataArray['reply_to']) && is_array($dataArray['reply_to'])){
		$CI->email->reply_to($dataArray['reply_to']['email'], $dataArray['reply_to']['name']);
	}else if(array_key_exists('reply_to', $dataArray) && !is_array($dataArray['reply_to']) && !empty($dataArray['reply_to'])){
		$CI->email->reply_to($dataArray['reply_to']);
	}
	if(array_key_exists('subject', $dataArray) && isset($dataArray['subject']) && !empty($dataArray['subject']) && !is_array($dataArray['subject'])){
		$CI->email->subject($dataArray['subject']);
	}
	if(array_key_exists('message', $dataArray) && isset($dataArray['message']) && !empty($dataArray['subject']) && !is_array($dataArray['subject'])){
		$CI->email->message($dataArray['message']);
	}	
    if(array_key_exists('attachments', $dataArray) && !empty($dataArray['attachments']) && isset($dataArray['attachments']))
	{
		foreach($dataArray['attachments'] as $attachment){
			$CI->email->attach(config_item('site_url').$attachment);
		}
	}
	$send = $CI->email->send();	
	//echo $CI->email->print_debugger();
	return $send;
}
/**
 * Function		:	formatingAttachmentFile
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	File	-	 $files
 					URL		-	 $path
					String  -	 $style
 * Return		: 	fileData
 * Description	: 	This function format the attached file.
**/
function formatingAttachmentFile($files, $path='', $style=''){
	$icon = '<i class="clip-attachment"></i>';
	$fileData = '';	
	$files = explode(',', $files);
	if(is_array($files)){
		for($i=0; $i < count($files); $i++){
			$filePath = $files[$i];
			$fileInfo = pathinfo(FCPATH.$filePath);
			$fileName = $fileInfo['basename'];
			$url = base_url($filePath);
			if($style !='icon')
				$fileName =  ' '.$fileName;
			
			$fileData .= '<a target="_blank" href="'.$url.'">'.$icon.$fileName.'</a> &nbsp;';
					
		}	
	}
	return $fileData;
}
/**
 * Function		:	slugify
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	String  -	 $text
 * Return		: 	text
 * Description	: 	This function format the text.
**/
function slugify($text){
	// replace non letter or digits by -
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);
	// transliterate
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);
	// trim
	$text = trim($text, '-');
	// remove duplicate -
	$text = preg_replace('~-+~', '-', $text);
	// lowercase
	$text = strtolower($text);
	if (empty($text)) {
		return 'n-a';
	} 
	return $text;
}
/**
 * Function		:	getMonthDateList
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Numeric	-	selected
 * Return		: 	opt
 * Description	: 	This function returns the list on month dates.
**/
function getMonthDateList($selected=''){
	$opt='';
	for($i=1; $i<=31; $i++){
		if($i<=9){$i='0'.$i;}
		$sel=($selected==$i)?'selected="selected"':'';
		$opt.='<option '.$sel.' value="'.$i.'">'.$i.'</option>';
	}
	return $opt;
}
/**
 * Function		:	getMonthList
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Numeric	-	selected
 * Return		: 	opt
 * Description	: 	This function returns the list on month names.
**/
function getMonthList($selected=''){
	$month = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
	$opt='';
	foreach($month as $key => $value){
		$sel=($selected == $key) ? 'selected="selected"' : '';
		$opt.='<option '.$sel.' value="'.$key.'">'.$value.'('.$key.')</option>';
	}
	return $opt;
}
/**
 * Function		:	getYearList
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Numeric	-	selected
 * Return		: 	opt
 * Description	: 	This function returns the list on Year.
**/
function getYearList($selected=''){
	$opt='';
	for($i=1970; $i<=2005; $i++){
		$sel=($selected==$i)?'selected="selected"':'';
		$opt.='<option '.$sel.' value="'.$i.'">'.$i.'</option>';
	}
	return $opt;
}
/**
 * Function		:	getLastDate
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Date - last_date
 					Text - class
 * Return		: 	option
 * Description	: 	This function checks the last day from today.
**/
function getLastDate($last_date, $class = ''){
	$now = time(); // or your date as well
	$your_date = strtotime($last_date);
	$datediff = $your_date - $now;	
	$diff = floor($datediff / (60 * 60 * 24));
	if($class == ''){
		if($diff >= 30)
			return '<span class="badge badge-success">'.date('d M, Y',strtotime($last_date)).'</span>';
		else if($diff < 30 && $diff >= 20)
			return '<span class="badge badge-info">'.date('d M, Y',strtotime($last_date)).'</span>';
		else if($diff < 20 && $diff >= 10)
			return '<span class="badge badge-warning">'.date('d M, Y',strtotime($last_date)).'</span>';
		else if($diff < 10)
			return '<span class="badge badge-danger">'.date('d M, Y',strtotime($last_date)).'</span>';
	}else{
		return '<span class="badge '.$class.'">'.date('d M, Y',strtotime($last_date)).'</span>';
	}
}
/**
 * Function		:	buildPermissionMenuList
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Text 	- $parent
 					Text 	- $menu
					Integer - $level
					Text 	- $group_menu_list
 * Return		: 	html
 * Description	: 	This function generate menu perssions list.
**/
function buildPermissionMenuList($parent, $menu, $level = '0', $group_menu_list = ''){
	//echo '<pre>';print_r($group_menu_list);die;
   $html = "";
   if (isset($menu['parents'][$parent]))
   {
	  if($level == 0)
		$html .= '<ul class="treeview">'."\n";
		
	   foreach ($menu['parents'][$parent] as $itemId)
	   {
		   // check assigned menu to group
		  $group_actions = array(); $selected = '';
		  $page_menu_id = $menu['items'][$itemId]['page_menu_id'];
		  $groupMenu = (is_array($group_menu_list) && count($group_menu_list) > 0) ? @$group_menu_list[$page_menu_id] : '';
		  if(isset($groupMenu) && !empty($groupMenu)){
		  	$group_actions = !empty($groupMenu['actions']) ? explode('|', $groupMenu['actions']) : array();
			$selected = ' checked=checked';
		  }
		  
		  if(!isset($menu['parents'][$itemId]))
		  {
			
			$menuValue = $menu['items'][$itemId]['parent_id'].'_'.$menu['items'][$itemId]['page_menu_id'];
			$html .= '<li><a class="plus" href="javascript:void(0)"><span class="glyphicon glyphicon-plus-sign"></span></a><label>
								<input '.$selected.' type="checkbox" value="'.$menuValue.'" name="menu[name][]">
								'.$menu['items'][$itemId]['menu_title'].'
							</label>';
			if($menu['items'][$itemId]['menu_actions'] != ''){
				 $actions = explode('|', $menu['items'][$itemId]['menu_actions']);
				 $html .= '<ul class="treeview_actions">';
				 for($i=0; $i< sizeof($actions); $i++){					 
					 $selectAct = in_array($actions[$i], $group_actions) ? ' checked=checked' : '';
					 $actionValue = $menu['items'][$itemId]['page_menu_id'].'_'.$menu['items'][$itemId]['page_menu_id'];
					 $label = $actions[$i];
					 if($actions[$i] == 'read'){
						 $label = 'View';
					 }
					 $html .= '<li><label><input '.$selectAct.' type="checkbox" value="'.$actions[$i].'" name="menu['.$menuValue.'][]"> '.ucwords($label).'</label></li>';
				 }
				 $html .= '</ul>';
			}
			$html .= '</li>';					
		  }
		  
		  if(isset($menu['parents'][$itemId]))
		  {
			 $menuValue = $menu['items'][$itemId]['parent_id'].'_'.$menu['items'][$itemId]['page_menu_id'];
			 $html .= '<li><a class="plus" href="javascript:void(0)"><span class="glyphicon glyphicon-plus-sign"></span></a>';
			 $html .= '<label>
							<input '.$selected.' type="checkbox" value="'.$menuValue.'" name="menu[name][]">
							'.$menu['items'][$itemId]['menu_title'].'
						</label>';				 
			 $html .= '<ul>';
			 $html .=  buildPermissionMenuList($itemId, $menu, '1', $group_menu_list);
			 $html .= "</li>";
		  }
	   }
	   $html .= "</ul>";
   }
   return $html;
}
/**
 * Function		:	buildLeftNavigationBar
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Text 	- $parent
 					Text 	- $menu
					Integer - $level
					Text 	- $parent_menu
					Text 	- $child_menu
 * Return		: 	html
 * Description	: 	This function generate left navigation menu list.
**/
function buildLeftNavigationBar($parent, $menu, $level = '0' , $parent_menu='', $child_menu='', $schild_menu = '', $type='letter'){
   $html = "";
   if (isset($menu['parents'][$parent]))
   {
	  if($level == 0)
		$html .= "<ul class=\"main-navigation-menu\">\n";
		
	   foreach ($menu['parents'][$parent] as $itemId)
	   {
		  if(!isset($menu['parents'][$itemId]))
		  {
				$pmenu = explode('/', $menu['items'][$itemId]['menu_url']);
				$menu_level = $menu['items'][$itemId]['menu_level'];
				if(array_key_exists($menu_level, $pmenu)){
				$pmenu = $pmenu[$menu_level];
				}					
			  if($menu_level == 0){ 
				$html .= '<li'. ($parent_menu==$pmenu ? ' class="active open"':'').'>
							<a href="'.site_url($menu['items'][$itemId]['menu_url']).'">							
							<div class="item-content">
								<div class="item-media">';
				
				if($type == 'letter'){			
					$html .=  '<div class="lettericon" data-text="'.$menu['items'][$itemId]['menu_title'].'" data-size="sm" data-char-count="2"></div>';
				}
				if($type == 'icon'){			
					$html .=  '<i class="'.$menu['items'][$itemId]['icon_class'].'"></i>';
				}
				
				$html .= 		'</div>
								<div class="item-inner">
									<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span>
								</div>
							</div> 
							</a>
						</li>';	
			  }else{
				$html .= '<li'. ($schild_menu==$pmenu ? ' class="active"':'').'>
							<a href="'.site_url($menu['items'][$itemId]['menu_url']).'">
								<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span>
							</a>
						</li>';
			  }
		  }
		  
		  if(isset($menu['parents'][$itemId]))
		  {
			 $pmenu = explode('/', $menu['items'][$itemId]['menu_url']);
			 $menu_level = $menu['items'][$itemId]['menu_level'];
			 if(array_key_exists($menu_level, $pmenu)){
			 	$pmenu = $pmenu[$menu_level];
			 }
			 $display = '';
			 if($menu_level == 1){
			 $display = ($child_menu==$pmenu) ?  'style="display:block;"' : '';
			 $html .= '<li'. ($child_menu==$pmenu ? ' class="active open"':'').'>';
			 }else if($menu_level == 2){
			 $html .= '<li'. ($schild_menu==$pmenu ? ' class="active open"':'').'>';
			 $display = ($schild_menu==$pmenu) ?  'style="display:block;"' : '';
			 }else{
			 $html .= '<li'. ($parent_menu==$pmenu ? ' class="active open"':'').'>';
			 }			 
			 
			 if($menu_level == 0){ 				
				$html .='<a href="javascript:void(0);">
							<div class="item-content">
								<div class="item-media">';
								
								if($type == 'letter'){			
									$html .=  '<div class="lettericon" data-text="'.$menu['items'][$itemId]['menu_title'].'" data-size="sm" data-char-count="2"></div>';
								}
								if($type == 'icon'){		
									$html .=  '<i class="'.$menu['items'][$itemId]['icon_class'].'"></i>';
								}				
				$html .= '</div>										
								<div class="item-inner">
									<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span><i class="icon-arrow"></i>
								</div>
							</div>
						</a>';
			 }else{				
			 	$html .= '<a href="javascript:void(0);"> <span>'.$menu['items'][$itemId]['menu_title'].'</span> <i class="icon-arrow"></i> </a>';
			 }
			 $html .= '<ul class="sub-menu"'.$display.'>';
			 $level++;
			 $html .= buildLeftNavigationBar($itemId, $menu, $level, $parent_menu, $child_menu, $schild_menu, $type);
			 $html .= "</li>";				 
		  }
	   }
	   $html .= "</ul>";
   }
   return $html;
}
function buildHorizontalNavigationBar($parent, $menu, $level = '0' , $parent_menu='', $child_menu='', $schild_menu = '', $type='letter'){
	//printr($menu);
   $html = "";
   if (isset($menu['parents'][$parent]))
   {
	  if($level == 0)
		$html .= "<ul class=\"nav navbar-nav no-border\">\n";
		
	   foreach ($menu['parents'][$parent] as $itemId)
	   {
		  if(!isset($menu['parents'][$itemId]))
		  {
				$pmenu = explode('/', $menu['items'][$itemId]['menu_url']);
				$menu_level = $menu['items'][$itemId]['menu_level'];
				if(array_key_exists($menu_level, $pmenu)){
				$pmenu = $pmenu[$menu_level];
				}					
			  if($menu_level == 0){ 
				$html .= '<li'. ($parent_menu==$pmenu ? ' class="active open"':'').'>
							<a href="'.site_url($menu['items'][$itemId]['menu_url']).'">							
							';
				
				if($type == 'letter'){			
					$html .=  '<div class="lettericon" data-text="'.$menu['items'][$itemId]['menu_title'].'" data-size="sm" data-char-count="2" data-color="auto"></div>';
				}
				if($type == 'icon'){			
					$html .=  '<i class="'.$menu['items'][$itemId]['icon_class'].'"></i>';
				}
				
				$html .= 		'<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span> 
							</a>
						</li>';	
			  }else{
				$html .= '<li'. ($schild_menu==$pmenu ? ' class="active"':'').'>
							<a href="'.site_url($menu['items'][$itemId]['menu_url']).'">
								<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span>
							</a>
						</li>';
			  }
		  }
		  
		  if(isset($menu['parents'][$itemId]))
		  {
			 
			 $pmenu = explode('/', $menu['items'][$itemId]['menu_url']);
			 $menu_level = $menu['items'][$itemId]['menu_level'];
			 if(array_key_exists($menu_level, $pmenu)){
			 	$pmenu = $pmenu[$menu_level];
			 }
			 $display = '';
			 if($menu_level == 1){
			 $display = ($child_menu==$pmenu) ?  'style="display:block;"' : '';
			 $html .= '<li class="dropdown-submenu'. ($child_menu==$pmenu ? ' active open"':'').'">';
			 }else if($menu_level == 2){
			 $html .= '<li class="dropdown-submenu'. ($schild_menu==$pmenu ? ' active open':'').'">';
			 $display = ($schild_menu==$pmenu) ?  'style="display:block;"' : '';
			 }else{
			 $html .= '<li class="dropdown'. ($parent_menu==$pmenu ? '  active open':'').'">';
			 }			 
			 
			 if($menu_level == 0){ 				
				$html .='<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">';								
								if($type == 'letter'){			
									$html .=  '<div class="lettericon" data-text="'.$menu['items'][$itemId]['menu_title'].'" data-size="sm" data-char-count="2" data-color="auto"></div>';
								}
								if($type == 'icon'){		
									$html .=  '<i class="'.$menu['items'][$itemId]['icon_class'].'"></i>';
								}				
				$html .= '<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span><span class="caret"></span>
						</a>';
			 }else{				
			 	$html .= '<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"> <span>'.$menu['items'][$itemId]['menu_title'].'</span> <i class="icon-arrow"></i> </a>';
			 }
			 $html .= '<ul class="dropdown-menu"'.$display.'>';
			 $level++;
			 $html .= buildSimpleHorizontalNavigationBar($itemId, $menu, $level, $parent_menu, $child_menu, $schild_menu, $type);
			 $html .= "</li>";				 
		  }
	   }
	   $html .= "</ul>";
   }
   return $html;
	
}
function buildSimpleHorizontalNavigationBar($parent, $menu, $level = '0' , $parent_menu='', $child_menu='', $schild_menu = '', $type='letter'){
   $html = "";
   if (isset($menu['parents'][$parent]))
   {
	  if($level == 0)
		$html .= "<ul class=\"nav navbar-left\">\n";
		
	   foreach ($menu['parents'][$parent] as $itemId)
	   {
		  if(!isset($menu['parents'][$itemId]))
		  {
				$pmenu = explode('/', $menu['items'][$itemId]['menu_url']);
				$menu_level = $menu['items'][$itemId]['menu_level'];
				if(array_key_exists($menu_level, $pmenu)){
				$pmenu = $pmenu[$menu_level];
				}					
			  if($menu_level == 0){ 
				$html .= '<li'. ($parent_menu==$pmenu ? ' class="active"':'').'>
							<a href="'.site_url($menu['items'][$itemId]['menu_url']).'">';				
				if(isset($menu['items'][$itemId]['icon_class'])){			
				$html .= '<i class="'.$menu['items'][$itemId]['icon_class'].'"></i>';
				}
				$html .= '<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span> 
							</a>
						</li>';	
			  }else if($menu_level == 1){
				$html .= '<li'. ($child_menu==$pmenu ? ' class="active"':'').'>
							<a href="'.site_url($menu['items'][$itemId]['menu_url']).'">
								<span class="title"> '.$menu['items'][$itemId]['menu_title'].'</span>
							</a>
						</li>';
			  }else{
				 $html .= '<li'. ($schild_menu==$pmenu ? ' class="active"':'').'>
							<a href="'.site_url($menu['items'][$itemId]['menu_url']).'">
								<span class="title"> '.$menu['items'][$itemId]['menu_title'].'</span>
							</a>
						</li>';
			
			  }
		  }
		  
		  if(isset($menu['parents'][$itemId]))
		  {
			 
			 $pmenu = explode('/', $menu['items'][$itemId]['menu_url']);
			 $menu_level = $menu['items'][$itemId]['menu_level'];
			 if(array_key_exists($menu_level, $pmenu)){
			 	$pmenu = $pmenu[$menu_level];
			 }
			 $display = '';
			 if($menu_level == 1){
			 $display = ($child_menu==$pmenu) ?  '' : '';
			 $html .= '<li class="dropdown-submenu'. ($child_menu==$pmenu ? ' active"':'').'">';
			 }else if($menu_level == 2){
			 $html .= '<li class="dropdown-submenu'. ($schild_menu==$pmenu ? ' active':'').'">';
			 $display = ($schild_menu==$pmenu) ?  '' : '';
			 }else{
			 $html .= '<li class="dropdown'. ($parent_menu==$pmenu ? ' active':'').'">';
			 }			 
			 
			 if($menu_level == 0){ 				
				$html .='<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">';								
				if(isset($menu['items'][$itemId]['icon_class'])){			
					$html .= '<i class="'.$menu['items'][$itemId]['icon_class'].'"></i>';
				}												
				$html .= '<span class="title"> '.$menu['items'][$itemId]['menu_title'].' </span><span class="caret"></span>
						</a>';
			 }else{				
			 	$html .= '<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"> <span>'.$menu['items'][$itemId]['menu_title'].'</span> <i class="icon-arrow"></i> </a>';
			 }
			 $html .= '<ul class="dropdown-menu"'.$display.'>';
			 $level++;
			 $html .= buildSimpleHorizontalNavigationBar($itemId, $menu, $level, $parent_menu, $child_menu, $schild_menu, $type);
			 $html .= "</li>";				 
		  }
	   }
	   $html .= "</ul>";
   }
   return $html;
	
}

function dropdownMenuList($parent, $menu, $selected_menu ){
   $html = "";
   if (isset($menu['parents'][$parent]) && !empty($menu['parents'][$parent]))
   {	
	   foreach ($menu['parents'][$parent] as $itemId)
	   {
		   		  
		$selected = ($selected_menu == $menu['items'][$itemId]['page_menu_id']) ? 'selected="selected"' : '';
		$extraSpace = '';
		for($i = 0; $i < ($menu['items'][$itemId]['menu_level'] * 3); $i++){
			$extraSpace .= '-';
		}
		$menuValue = $menu['items'][$itemId]['page_menu_id'].'_'.$menu['items'][$itemId]['menu_level'];
		$menu_title = $menu['items'][$itemId]['menu_title'];
		$html .= '<option '.$selected.' value="'.$menuValue.'">'.$extraSpace.' '.$menu_title.'</option>';
		  
		if(isset($menu['parents'][$itemId]))
		{
			$html .=  dropdownMenuList($itemId, $menu, $selected_menu);
		}
		
	   }
   }
   return $html;
}
/**
 * Function		:	load_setting_view_page
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Text 	- $output
 * Return		: 	html
 * Description	: 	This function load settings page.
**/
function load_setting_view_page($output = NULL) {
	$CI = & get_instance();
	$output = ($output==NULL) ? (object) array('output' => '' , 'js_files' => array() , 'css_files' => array()) : $output;
	$CI->load->view('setting' , $output);
}
/**
 * Function		:	generateStrongPassword
 * Author		: 	Tarun Malviya
 * Author Email	: 	tarun.malviya@techlect.com
 * Params		: 	Integer	- $length
 * Return		: 	$dash_str
 * Description	: 	This function generate new random password.
**/
function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds' , $prefix = ''){
	
	$sets = array();
	if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
	if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';
	$all = '';
	$password = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}
	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];
	$password = str_shuffle($password);
	if(!$add_dashes)
		return $password;
	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while(strlen($password) > $dash_len)
	{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $prefix.$dash_str;
}
/*************************
Author : Manoj Kumat (manoj.kumar@techlect.com)
Function Name : current_page_url();
Paramete : $unset_parameter : this parameter required to unset query string from current page url. The parameter support array and string.
		   $set_parameter : This is parameter use to set add in current page url. this is support associative array only.
Return type : string with current url.
*/ 
function current_page_url($unset_parameter = '', $set_parameters = ''){
	$url = current_url();
	$parameters = $_GET;
	if(isset($parameters) && !empty($parameters)){	
		if(!is_array($unset_parameter)){
			if (($key = array_search($unset_parameter, $parameters)) !== false) {
				unset($parameters[$key]);
			}
		}elseif(is_array($unset_parameter)){
			foreach($unset_parameter as $value){
				if (($key = array_search($value, $parameters)) !== false) {
					unset($parameters[$key]);
				}
			}			
		}
	}
	
	if(isset($set_parameters) && !empty($set_parameters)){	
		if(is_array($set_parameters)){
			$parameters = array_merge($parameters, $set_parameters);
		}
	}
	
	$parameters = http_build_query($parameters);	
	return $url .(!empty($parameters) ? '?'. $parameters : '');
}
	function getFdApiData($api_method = '', $parameter = '', $request_method = 1){ 
		if(is_array($parameter) && !empty($parameter)){
			$parameter = http_build_query($parameter);
		}
		
		if($request_method === 'get' || $request_method === 'GET'){
			$request_method = 0;
		}else if($request_method === 'post' || $request_method === 'POST'){
			$request_method = 1;
		}else{
			$request_method = 1;
		}
		
		$token = 'sakjddkjsa32kj232kjdn32k';
		$base_url = "http://techlect.net/formduniya/coaching/index.php/fdapi/".$api_method."?token=".$token;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $base_url);
		curl_setopt($ch, CURLOPT_POST, $request_method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
		
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		$server_output = curl_exec($ch);
		curl_close($ch);
		
		$server_output = json_decode($server_output, true);
		return $server_output;
	}
function getYoutubeVideoId($url){
	$vId = '';
	if(!empty($url)){
		preg_match(
			"/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",
			$url, 
			$matches
			);
		if(isset($matches) && !empty($matches) && !empty($matches[1])){
			$vId = $matches[1];
		}
	}
	return $vId;
}
function uploadfile($source_file, $destination_path, $config = array()){
	$result = array();	
	$CI = & get_instance();
	if(!$CI->load->is_loaded('fileupload')){
		$CI->load->library('fileupload/fileupload');
	}
	$body_name 	= array_key_exists('body_name', $config) ? $config['body_name'] : ''; 
	$extention 	= array_key_exists('extention', $config) ? $config['extention'] : '';
	$overwrite 	= array_key_exists('overwrite', $config) ? $config['overwrite'] : false;
	$maxsize 	= array_key_exists('maxsize', $config) ? $config['maxsize'] : '';
	$thumbnails = array_key_exists('thumbnail', $config) ? $config['thumbnail'] : array(); // array('size'=>200, 'path' => 'www/abcd/dd')
	$allowed_mime_type = array_key_exists('mime_type', $config) ? $config['mime_type'] : array(); //  array('application/pdf','application/msword', 'image/*'); or Simple wildcards are allowed, such as image/* or application/* If there is only one MIME type allowed, then it can be a string instead of an array 
	
	if(isset($source_file) && !empty($source_file)){ 	     
	   $CI->fileupload->upload($source_file);
		if($CI->fileupload->uploaded){			
			if($body_name != '') $CI->fileupload->file_new_name_body   = $body_name;
			if($extention != '') $CI->fileupload->file_new_name_ext	= $extention;		
			if($overwrite) 		 $CI->fileupload->file_overwrite  = $overwrite;
			if($maxsize)		 $CI->fileupload->file_max_size = $maxsize; // '1024' = 1KB
			if(isset($allowed_mime_type) && !empty($allowed_mime_type)) $CI->fileupload->allowed = $allowed_mime_type;			
			$CI->fileupload->process($destination_path);
			$result['originalfile'] = $CI->fileupload->file_dst_pathname;			
			if(isset($thumbnails) && !empty($thumbnails)){
				foreach($thumbnails as $thumb){					
					$CI->fileupload->image_max_width = $thumb['width'];
					$CI->fileupload->process($thumb['path']);
					$result['thumb_'.$thumb['size']] = $CI->fileupload->file_dst_pathname;
				}
			}			
			if ($CI->fileupload->processed){				
				$CI->fileupload->clean(); 
			}
		}
	}
	return $result;
}
function getUserMeta( $user_id = NULL , $meta_key = NULL , $single = false  ){	
		$result = array();	
		$CI = & get_instance();
		if(!empty($user_id) && $user_id != NULL && is_numeric($user_id) ){			
			/* prepare only key data */
			if( !empty($meta_key) && $meta_key != NULL  && $single ){
				$keyWhere  = array('user_id' => $user_id , 'meta_key' => $meta_key );
				$keyResult = $CI->db->select('meta_value')->get_where( USERMETA, $keyWhere )->result_array();
				if(!empty($keyResult) && isset($keyResult[0]) && !empty($keyResult[0]) && !empty($keyResult[0]['meta_value'])){
					return $keyResult[0]['meta_value'];
				} else {
					return '';
				}
			}
			/* prepare all user meta data */
			if( $single == false && empty($meta_key) && $meta_key == NULL){
				$metaWhere  = array( 'user_id' => $user_id );
				$allMeta = $CI->db->select('*')->get_where( USERMETA, $metaWhere )->result_array();
				if(!empty($allMeta)){
					return $allMeta ;
				} else {
					return array();
				}
			}
			/* prepare all user meta data */
		}
		return;		
}
function updateUserMeta( $user_id = NULL , $meta_key = NULL ,  $meta_val = '' ){
		$resp = array();	
		$CI = & get_instance();
		if(!empty($user_id) && $user_id != NULL && is_numeric($user_id) ){
			if(!empty($meta_key) && $meta_key != NULL ){									
				$keyWhere  = array('user_id' => $user_id , 'meta_key' => $meta_key );
				$keyResult = $CI->db->select('meta_key')->get_where( USERMETA, $keyWhere )->result_array();
				if(!empty($keyResult) && isset($keyResult[0]) && !empty($keyResult[0])&& !empty($keyResult[0]['meta_key'])){
					/* prepare update key data if exists */					
					$upWhere =  array('user_id' => $user_id , 'meta_key' => $keyResult[0]['meta_key'] );
					$upData  =  array('meta_value' => $meta_val );
					$upResp  =  $CI->db->where($upWhere)->update( USERMETA , $upData );
					if($upResp){ return true; }
				} else {
					/* prepare update key data if exists */
					$insData = array('user_id' => $user_id , 'meta_key' => $meta_key , 'meta_value' => $meta_val );
					$CI->db->insert( USERMETA , $insData);
					$umetaid = $CI->db->insert_id();
					if(!empty($umetaid)){ return true; }
				}
			}
		}
	return ;
}
function deleteUserMeta( $user_id = NULL , $meta_key = NULL ,  $single = false  , $bulk = '' ){
		$result = array();	
		$CI = & get_instance();
		if(!empty($user_id) && $user_id != NULL && is_numeric($user_id) ){				
			if( !empty($meta_key) && $meta_key != NULL  && $single ){
				/* prepare delete only key data */
				$keyWhere  = array('user_id' => $user_id , 'meta_key' => $meta_key );
				$keyResult = $CI->db->select('meta_value')->get_where( USERMETA, $keyWhere )->result_array();
				if(!empty($keyResult) && isset($keyResult[0]) && !empty($keyResult[0])){
					$delResp = $CI->db->delete( USERMETA , $keyWhere );
					if( $delResp ){ return true; }
				}
			}
			/* prepare all user meta data */
			if( $single == false && empty($meta_key) && $meta_key == NULL && $bulk == 'ALL' ){
				$metaWhere  = array( 'user_id' => $user_id );
				$delResp    = $CI->db->delete( USERMETA , $metaWhere );
				if( $delResp ){ return true; }
			}
			/* prepare all user meta data */
		}
		return;		
		
}
function url_encryptor($action, $string) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		//pls set your unique hashing key
		$secret_key = 'G7#$*ikikdsOOl8vD';
		$secret_iv = 'v&5P+*Ijjhksy7+84Rc';
	
		// hash
		$key = hash('sha256', $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
	
		//do the encyption given text/string/number
		if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		else if( $action == 'decrypt' ){
			//decrypt the given text/string/number
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}	
		return $output;
}
function getLanguage(){	
	$CI = & get_instance();
	$language = $CI->config->item('lang_uri_abbr');
	$langData = array();
	if(!empty($language)){
		foreach($language as $key => $lang){
			$langData[$key] = ucfirst($lang);			
		}
	}
	return $langData;
}
	function get_date($format = NULL, $date = NULL, $timezone = NULL){
		$CI = & get_instance();
		$setting = $CI->config->item('default_setting');
		$format = ($format != NULL) ? $format : $setting['date_format'];
		$timezone = ($timezone != NULL) ? $timezone : $setting['timezone'];
		
		try {
			if(!empty($date) && is_numeric($date)){
				$datetimeob1 = new DateTime(date($format, $date), new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}elseif(!empty($date)){
				$datetimeob1 = new DateTime($date, new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}else{
				$datetimeob1 = new DateTime('now', new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}
		} catch (Exception $e) {
			$datetimeob1 = new DateTime('now', new DateTimeZone($timezone));
			$date = $datetimeob1->format($format);
		}
		return $date;
	}
	
	function get_datetime($format = NULL, $datetime = NULL, $timezone = NULL){
		$CI = & get_instance();
		$setting = $CI->config->item('default_setting');
		$format = ($format != NULL) ? $format : $setting['datetime_format'];
		$timezone = ($timezone != NULL) ? $timezone : $setting['timezone'];
		
		try {
			if(!empty($datetime) && is_numeric($datetime)){
				$datetimeob1 = new DateTime($datetime, new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}elseif(!empty($datetime)){
				$datetimeob1 = new DateTime($datetime, new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}else{
				$datetimeob1 = new DateTime('now', new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}
		} catch (Exception $e) {
			$datetimeob1 = new DateTime('now', new DateTimeZone($timezone));
			$date = $datetimeob1->format($format);
		}
		return $date;
	}
	
	function get_time($format = NULL, $time = NULL, $timezone = NULL){
		$CI = & get_instance();
		$setting = $CI->config->item('default_setting');
		$format = ($format != NULL) ? $format : $setting['time_format'];
		$timezone = ($timezone != NULL) ? $timezone : $setting['timezone'];
		
		try {
			if(!empty($time) && is_numeric($time)){
				$datetimeob1 = new DateTime($time, new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}elseif(!empty($time)){
				$datetimeob1 = new DateTime($time, new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}else{
				$datetimeob1 = new DateTime('now', new DateTimeZone($timezone));
				$date = $datetimeob1->format($format);
			}
		} catch (Exception $e) {
			$datetimeob1 = new DateTime('now', new DateTimeZone($timezone));
			$date = $datetimeob1->format($format);
		}
		return $date;
	}
	
	function printrr($value){
	 echo '<pre>';
	 print_r($value);
	 echo '</pre>';
	}
	
	
	function export_to_excel($data, $filename = NULL){
		$ci = &get_instance();
		$ci->load->library('PHP_Excel');
		
		if($filename != NULL){
			$filename = $filename."-".get_date("d-M-Y_h:i-A").".xlsx";
		}else{
			$filename = "Export-".get_date("d-M-Y_h:i-A").".xlsx";
		}
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Techlect Pvt. Ltd.")
									 ->setLastModifiedBy("Techlect Pvt. Ltd.")
									 ->setTitle("Office 2007 XLSX Report Document")
									 ->setSubject("Office 2007 XLSX Report Document")
									 ->setDescription("Report document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Report");
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		$clmn = 'A';
		foreach($data['columns'] as $column => $label){
			//$objPHPExcel->getActiveSheet()->getColumnDimension($clmn)->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->SetCellValue($clmn.'1', $label);
			$clmn++;
		}		
		$rwn=2;		
		foreach($data['list'] as $num_row => $row){
			$clmn='A';
			foreach($data['columns'] as $field_name => $label){			
				$objPHPExcel->getActiveSheet()->SetCellValue($clmn.$rwn, $this->_trim_export_string($row[$field_name]));
				$clmn++;				
			}
			$rwn++;			
		}
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$filename);
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;

	}
	
	function getDatabaseList(){
				 
		$CI = & get_instance();
		$admin_id = loginUserInfo('admin_id');		
		
		//$current_db = $CI->db->database;
		
		include APPPATH . 'config/database.php';
		//$current_db = $active_group;
		
		$current_db = @$db[$active_group]['database'];
		
		$connInfo = array('hostname' => $db[$active_group]['hostname'],
						  'username' => $db[$active_group]['username'],
						  'password' => $db[$active_group]['password'],
						  'database' => $db[$active_group]['database'],
						  'dbdriver' => $db[$active_group]['dbdriver']);
				
		$dflt_db_conn = $CI->load->database($connInfo, true);				
		$dflt_db_conn->select('ds.db_id, db.backup_dbname as database_name, db.display_name');
		$dflt_db_conn->from(DATABASE_SETTING .' ds');
		$dflt_db_conn->join(DATABASE_BACKUP .' db', 'db.backup_id = ds.db_id', 'LEFT');
		$dflt_db_conn->where(array('admin_id' => $admin_id));
		$dflt_db_conn->order_by('ds.db_id', 'DESC');
		$query = $dflt_db_conn->get();
		$lq = $dflt_db_conn->last_query();
		
		//printr($connInfo);
		//printr($lq);
		
		//printr($query->result_array());
		
		$html = '';
		$active_db = $CI->session->userdata(SESSION_NAME . '_backup_database');
		if($query !== FALSE && $query->num_rows() > 0){
			$html .= '<select class="form-control" id="business_database" data-ddb="'.$current_db.'" style="width:100px;">';
			$html .= '<option value="'.$current_db.'">Current DB</option>';					
			foreach ($query->result_array() as $db){
				$selected = '';
				if($active_db == $db['database_name']){
					$selected = 'selected="selected"';
				}
				$html .= '<option '.$selected.' value="'.$db['database_name'].'">'.$db['display_name'].'</option>';
			}
			$html .= '</select>';
		}
		return $html;	
	}
	
	