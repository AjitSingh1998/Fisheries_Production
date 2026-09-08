<?php
(defined('BASEPATH')) or exit('No direct script access allowed');

#[AllowDynamicProperties]
class MY_Controller extends MX_Controller {
	public $setting;
	public $language;
	public $currency;
	public $currency_code;
	public $currency_icon;
	public $timezone;
	public $template_name;
	public $template_style;
	public $template_path;
	
	function __construct(){
		parent::__construct();
		
		$this->load->database(DEFAULT_DB_NAME);
		
		$this->setting = $this->config->item('default_setting');
		
		// code start here to switch multiple language
		
		/*$currentLang = $this->input->get('lang'); // fetching language name from url	
		if(isset($currentLang) && !empty($currentLang)){
			// prepare language data to set in cookie
			$cookie = array('name' => 'language', 'value' => $currentLang, 'expire' => time()+86400);
			$this->input->set_cookie($cookie);
			$this->language = $currentLang;
		}else{
			// check language name exists in cookie
			$cookieLang = $this->input->cookie('language');
			if(isset($cookieLang) && !empty($cookieLang)){
				$this->language = $cookieLang;
			}else{
				$defaultLang = empty(loginUserInfo('language')) ? $this->config->item('language') : loginUserInfo('language');
				$this->language = $defaultLang;
				$cookie = array('name' => 'language', 'value' => $defaultLang, 'expire' => time()+86400);
				$this->input->set_cookie($cookie);
			}
		}*/
		
		$defaultLang = empty(loginUserInfo('language')) ? $this->config->item('language') : loginUserInfo('language');
		$this->language = $defaultLang;
		
		// code start here to switch multiple Currency
		$currentCurrency = $this->input->get('crr'); // fetching Currency name from url
		if(isset($currentCurrency) && !empty($currentCurrency)){
			// prepare Currency data to set in cookie
			$cookie = array('name' => 'currency', 'value' => $currentCurrency, 'expire' => time()+86400);
			$this->input->set_cookie($cookie);
			$this->currency = $currentCurrency;
			
			$currency_code = empty(loginUserInfo('currency_code')) ? $this->setting['currency_code'] : loginUserInfo('currency_code');
			$cookie = array('name' => 'currency_code', 'value' => $currency_code, 'expire' => time()+86400);
			$this->input->set_cookie($cookie);
			$this->currency_code = $currency_code;
			
			$currency_icon = empty(loginUserInfo('currency_icon')) ? $this->setting['currency_icon'] : loginUserInfo('currency_icon');
			$cookie = array('name' => 'currency_icon', 'value' => $currency_icon, 'expire' => time()+86400);
			$this->input->set_cookie($cookie);
			$this->currency_icon = $currency_icon;
			
			
		}else{
			// check Currency name exists in cookie
			$cookieCurrency = $this->input->cookie('currency');
			$currency_code = $this->input->cookie('currency_code');
			$currency_icon = $this->input->cookie('currency_icon');
			if(isset($cookieCurrency) && !empty($cookieCurrency)){
				$this->currency = $cookieCurrency;
				$this->currency_code = $currency_code;
				$this->currency_icon = $currency_icon;
			}else{
				
				$defaultCurrency = empty(loginUserInfo('currency')) ? $this->setting['currency_code'] : loginUserInfo('currency');
				$currency_code = empty(loginUserInfo('currency_code')) ? $this->setting['currency_code'] : loginUserInfo('currency_code');
				$currency_icon = empty(loginUserInfo('currency_icon')) ? $this->setting['currency_icon'] : loginUserInfo('currency_icon');
				
				$cookie = array('name' => 'currency', 'value' => $defaultCurrency, 'expire' => time()+86400);
				$this->input->set_cookie($cookie);
				$cookie = array('name' => 'currency_code', 'value' => $currency_code, 'expire' => time()+86400);
				$this->input->set_cookie($cookie);
				$cookie = array('name' => 'currency_icon', 'value' => $currency_icon, 'expire' => time()+86400);
				$this->input->set_cookie($cookie);
				$this->currency = $defaultCurrency;
				$this->currency_code = $currency_code;
				$this->currency_icon = $currency_icon;
			}
		}
		
		// code start here to switch multiple timezone
		$currentTimeZone = $this->input->post('tz'); // fetching timezone name from url	
		if(isset($currentTimeZone) && !empty($currentTimeZone)){
			// prepare timezone data to set in cookie
			$cookie = array('name' => 'timezone', 'value' => $currentTimeZone, 'expire' => time()+86400);
			$this->input->set_cookie($cookie);
			$this->timezone = $currentTimeZone;
		}else{
			// check timezone name exists in cookie
			$cookieTimeZone = $this->input->cookie('timezone');
			if(isset($cookieTimeZone) && !empty($cookieTimeZone)){
				$this->timezone = $cookieTimeZone;
			}else{
				$defaultTimeZone = empty(loginUserInfo('timezone')) ? $this->setting['timezone'] : loginUserInfo('timezone');
				$cookie = array('name' => 'timezone', 'value' => $defaultTimeZone, 'expire' => time()+86400);
				$this->input->set_cookie($cookie);				
				$this->timezone = $defaultTimeZone;
			}
		}
		
		// code start here to switch multiple template
		$currentTP = $this->input->post('tp'); // fetching template name from url		
		if(isset($currentTP) && !empty($currentTP)){
			// prepare template data to set in cookie
			$cookie = array('name' => 'template', 'value' => $currentTP, 'expire' => time()+86400);
			$this->input->set_cookie($cookie);
			$this->template_name = $currentTP;
			$this->template_style = $this->setting['template_style'];
		}else{
			// check template name exists in cookie
			$cookieTP = $this->input->cookie('template');
			if(isset($cookieTP) && !empty($cookieTP)){
				$this->template_name = $cookieTP;
				$this->template_style = $this->setting['template_style'];
			}else{
						
				$defaultTP = empty(loginUserInfo('template')) ? $this->setting['template_name'] : loginUserInfo('template');
				$cookie = array('name' => 'template', 'value' => $defaultTP, 'expire' => time()+86400);
				$this->input->set_cookie($cookie);
				$this->template_name = $defaultTP;
				$this->template_style = $this->setting['template_style'];
			}
		}
		
		$checkUser = loginUserInfo();
		if(empty($checkUser)){
			$this->template_name = $this->setting['guest_template'];
			$this->template_style = $this->setting['guest_template_style'];
		}		
		
		$this->template_path = base_url() . $this->setting['template_path'] . $this->template_name.'/';
		
		$this->load->module('template');
	}
	
}