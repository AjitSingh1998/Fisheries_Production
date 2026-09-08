<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Template extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();		
	}
	
	function set($key, $value)
	{
		if(!empty($key) && !empty($value)){
			$this->setting[$key] = $value;
		}
	}
	
	function layout($data = NULL, $template = NULL)
	{
		if($template != NULL){
			
			$this->template_name = $template;
			$this->template_path = base_url() . $this->setting['template_path'] . $this->template_name.'/';
			
			$this->load->view('template/'.$this->template_name, $data);
			
		}else{
			$this->load->view('template/'.$this->template_name, $data);
		}
	}
}
