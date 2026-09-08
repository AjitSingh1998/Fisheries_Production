<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Dashboard extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->userID = checkUserLogin();
		//$this->load->language('dashboard', $this->language);		
	}
	
	function index()
	{
		$data['page_title'] = 'Dashboard';
		$data['breadcrumb'] = 'Dashboard';
		$data['content_view'] = 'dashboard/dashboard_v';
		
		$data['total_fishermen'] = $this->db->count_all('psac_fisherman');
		$data['total_products'] = $this->db->count_all('psac_product');
		$data['total_groups'] = $this->db->count_all('psac_maingroup');
		$data['total_dailytoll'] = $this->db->count_all('psac_dailytoll');

		$this->template->set('document_title', 'Dashboard - Bansagar System');
		$this->template->layout($data);
	}
}
