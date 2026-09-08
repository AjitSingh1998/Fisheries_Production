<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

#[AllowDynamicProperties]
class Error_handler extends CI_Controller {
	
    public function __construct() 
    {
        parent::__construct();
		
		//$this->load->database();
		//$this->load->helper(array('url','common'));
		//$this->load->library(array('session', 'form_validation','grocery_CRUD','ajax_grocery_crud'));
    } 

    public function index() 
    { 
        $this->output->set_status_header('404'); 
        $data['page_title'] = 'Error-404'; // View name 
        $this->load->view('errors/html/error_404', $data);//loading in my template 
		
    } 
} 
