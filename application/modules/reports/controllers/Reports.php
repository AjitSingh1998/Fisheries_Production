<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Reports extends MY_Controller {
	var $userID;
	var $stylesheet_array;
	var $scriptsrc_array;
	var $datatable_scripts;
	var $datatable_script_body;
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('reports_model', 'RM');
		//$this->load->library('HtmlExcel');
		$this->stylesheet_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
												 base_url('assets/plugins/select2/dist/css/select2.min.css'),
												 base_url('assets/plugins/DataTables/media/css/jquery.dataTables.min.css'),
												 );
		$this->scriptsrc_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
												base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
												site_url('reports/assets/js/jquery.table2excel.min.js'),
												site_url('reports/assets/js/jQuery.print.min.js'),
												base_url('assets/plugins/DataTables/media/js/jquery.dataTables.js'),
												site_url('reports/assets/js/reports.js'),
												site_url('reports/assets/js/extra.js')
												);
												
		 //dtTable.destroy(); retrieve: true,	  destroy: true,	.fnDestroy()								
		$this->datatable_scripts = '<style> 
									table.dataTable thead th, table.dataTable thead td { padding: 8px !important;}
									table.dataTable tbody th, table.dataTable tbody td { padding: 1px 5px !important;}
									</style>';
	}
		
	function index(){
		redirect(site_url('reports/p1'));
	}
	
	//1. P1 Report (reports/p1)
	function p1(){
		$data['content_view'] = 'reports/report/report_p1_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		//$data['all_maingroup'] = $this->RM->get_all_maingroups();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Reports P1');
		$this->template->layout($data);
	}
	
	function ajax_p1_report(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('p1_report');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			$report_data = $this->RM->get_p1_report($from_date, $to_date, $maingroup, $group_type);
			
			if(!empty($report_data)){
							$tbody = '';
							$tCqty = $tCwt = $tRqty = $tRwt = $tMqty = $tMwt = $tKqty = $tKwt = $tAqty = $tAwt = $tSqty = $tSwt = $tLqty = $tLwt = $tLocalminor = $tTqty = $tTwt =  0;
							$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
							$search_key = ($maingroup > 0) ? $report_data[0]['Samiti_name'] : 'All';
							foreach($report_data as $report){
								
								$tbody .='<tr>';
								$tbody .='    <td>'.$report['f_name'].'</td>';
								$tbody .='    <td>'.$report['Cqty'].'</td>';
								$tbody .='    <td>'.$report['Cwt'].'</td>';
								$tbody .='    <td>'.$report['Rqty'].'</td>';
								$tbody .='    <td>'.$report['Rwt'].'</td>';
								$tbody .='    <td>'.$report['Mqty'].'</td>';
								$tbody .='    <td>'.$report['Mwt'].'</td>';
								$tbody .='    <td>'.$report['Kqty'].'</td>';
								$tbody .='    <td>'.$report['Kwt'].'</td>';
								$tbody .='    <td>'.$report['Aqty'].'</td>';
								$tbody .='    <td>'.$report['Awt'].'</td>';
								$tbody .='    <td>'.$report['Sqty'].'</td>';
								$tbody .='    <td>'.$report['Swt'].'</td>';
								$tbody .='    <td>'.$report['Lqty'].'</td>';
								$tbody .='    <td>'.$report['Lwt'].'</td>';
								$tbody .='    <td>'.$report['Localminor'].'</td>';
								$tbody .='    <td>'.$report['Tmqty'].'</td>';
								$tbody .='    <td>'.$report['Tmwt'].'</td>';
								$tbody .='    <td>'.$report['Tqty'].'</td>';
								$tbody .='    <td>'.$report['Twt'].'</td>';
								$tbody .='</tr>';
								
								
								$tCqty += $report['Cqty'];
								$tCwt +=  $report['Cwt'];
								$tRqty +=  $report['Rqty'];
								$tRwt +=  $report['Rwt'];
								$tMqty +=  $report['Mqty'];
								$tMwt +=  $report['Mwt'];
								$tKqty +=  $report['Kqty'];
								$tKwt +=  $report['Kwt'];
								$tAqty +=  $report['Aqty'];
								$tAwt +=  $report['Awt'];
								$tSqty +=  $report['Sqty'];
								$tSwt +=  $report['Swt'];
								$tLqty +=  $report['Lqty'];
								$tLwt +=  $report['Lwt'];
								$tLocalminor +=  $report['Localminor'];
								$tTqty +=  $report['Tqty'];
								$tTwt +=  $report['Twt'];
							}
							
							$tfoot ='<th width="5%">Total :</th>';
							$tfoot .='<th class="cqty">'.$tCqty.'</th>';
							$tfoot .='<th class="cwt">'.$tCwt.'</th>';
							$tfoot .='<th class="rqty">'.$tRqty.'</th>';
							$tfoot .='<th class="rwt">'.$tRwt.'</th>';
							$tfoot .='<th class="mqty">'.$tMqty.'</th>';
							$tfoot .='<th class="mwt">'.$tMwt.'</th>';
							$tfoot .='<th class="kqty">'.$tKqty.'</th>';
							$tfoot .='<th class="kwt">'.$tKwt.'</th>';
							$tfoot .='<th class="aqty">'.$tAqty.'</th>';
							$tfoot .='<th class="awt">'.$tAwt.'</th>';
							$tfoot .='<th class="sqty">'.$tSqty.'</th>';
							$tfoot .='<th class="swt">'.$tSwt.'</th>';
							$tfoot .='<th class="lqty">'.$tLqty.'</th>';
							$tfoot .='<th class="lwt">'.$tLwt.'</th>';
							$tfoot .='<th class="localminor">'.$tLocalminor.'</th>';
							$tfoot .='<th class=""></th>';
							$tfoot .='<th class=""></th>';
							$tfoot .='<th class="gtqty">'.$tTqty.'</th>';
							$tfoot .='<th class="gtwt">'.$tTwt.'</th>';
							$tfoot .= $this->datatable_scripts;
							
							$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
							$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
							
						}
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//2. P2 Report (get_p2_report)
	function p2(){
		$data['content_view'] = 'reports/report/report_p2_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['fishingpoints']  = $this->RM->get_fishing_points();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Reports P2');
		$this->template->layout($data);
	}
	
	function ajax_p2_report(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('p2_report');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$point = $this->input->post('point');
			$group_type = $this->input->post('group_type');
		
			$report_data = $this->RM->get_p2_report($from_date, $to_date, $point, $group_type);
			if(!empty($report_data)){
				//printr($report_data);
				$tbody = '';
				$tCqty = $tCwt = $tRqty = $tRwt = $tMqty = $tMwt = $tKqty = $tKwt = $tAqty = $tAwt = $tSqty = $tSwt = $tLqty = $tLwt = $tLocalminor = $tTqty = $tTwt =  0;
				$search_date = $this->input->post('from_date').' To '.$this->input->post('to_date');
				
				$search_key = ($point > 0 ? $report_data[0]['point_name']:'All');
				$search_key .= ', &nbsp;&nbsp;&nbsp; Group Name: '.($group_type > 0 ? $report_data[0]['group_type_name'] : 'All');
				
				foreach($report_data as $report){
					
					$tbody .='<tr>';
                    $tbody .='    <td>'.$report['Samiti_name'].'</td>';
					$tbody .='    <td>'.$report['total_members'].'</td>';
                    $tbody .='    <td>'.$report['Cqty'].'</td>';
                    $tbody .='    <td>'.$report['Cwt'].'</td>';
                    $tbody .='    <td>'.$report['Rqty'].'</td>';
                    $tbody .='    <td>'.$report['Rwt'].'</td>';
                    $tbody .='    <td>'.$report['Mqty'].'</td>';
                    $tbody .='    <td>'.$report['Mwt'].'</td>';
                    $tbody .='    <td>'.$report['Kqty'].'</td>';
                    $tbody .='    <td>'.$report['Kwt'].'</td>';
                    $tbody .='    <td>'.$report['Aqty'].'</td>';
                    $tbody .='    <td>'.$report['Awt'].'</td>';
                    $tbody .='    <td>'.$report['Sqty'].'</td>';
                    $tbody .='    <td>'.$report['Swt'].'</td>';
                    $tbody .='    <td>'.$report['Lqty'].'</td>';
                    $tbody .='    <td>'.$report['Lwt'].'</td>';
                    $tbody .='    <td>'.$report['Localminor'].'</td>';
                    $tbody .='    <td>'.$report['Tmqty'].'</td>';
                    $tbody .='    <td>'.$report['Tmwt'].'</td>';
					$tbody .='    <td>'.$report['Tqty'].'</td>';
                    $tbody .='    <td>'.$report['Twt'].'</td>';
                    $tbody .='</tr>';
					
					
					$tCqty += $report['Cqty'];
					$tCwt +=  $report['Cwt'];
					$tRqty +=  $report['Rqty'];
					$tRwt +=  $report['Rwt'];
					$tMqty +=  $report['Mqty'];
					$tMwt +=  $report['Mwt'];
					$tKqty +=  $report['Kqty'];
					$tKwt +=  $report['Kwt'];
					$tAqty +=  $report['Aqty'];
					$tAwt +=  $report['Awt'];
					$tSqty +=  $report['Sqty'];
					$tSwt +=  $report['Swt'];
					$tLqty +=  $report['Lqty'];
					$tLwt +=  $report['Lwt'];
					$tLocalminor +=  $report['Localminor'];
					$tTqty +=  $report['Tqty'];
					$tTwt +=  $report['Twt'];
				}
				
				$tfoot ='<th colspan="2" width="5%">Total :</th>';
				$tfoot .='<th class="cqty">'.$tCqty.'</th>';
                $tfoot .='<th class="cwt">'.$tCwt.'</th>';
                $tfoot .='<th class="rqty">'.$tRqty.'</th>';
                $tfoot .='<th class="rwt">'.$tRwt.'</th>';
                $tfoot .='<th class="mqty">'.$tMqty.'</th>';
                $tfoot .='<th class="mwt">'.$tMwt.'</th>';
                $tfoot .='<th class="kqty">'.$tKqty.'</th>';
                $tfoot .='<th class="kwt">'.$tKwt.'</th>';
                $tfoot .='<th class="aqty">'.$tAqty.'</th>';
                $tfoot .='<th class="awt">'.$tAwt.'</th>';
                $tfoot .='<th class="sqty">'.$tSqty.'</th>';
                $tfoot .='<th class="swt">'.$tSwt.'</th>';
                $tfoot .='<th class="lqty">'.$tLqty.'</th>';
                $tfoot .='<th class="lwt">'.$tLwt.'</th>';
                $tfoot .='<th class="localminor">'.$tLocalminor.'</th>';
                $tfoot .='<th class=""></th>';
                $tfoot .='<th class=""></th>';
                $tfoot .='<th class="gtqty">'.$tTqty.'</th>';
                $tfoot .='<th class="gtwt">'.$tTwt.'</th>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot,'search_key'=>$search_key,'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
				
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//3. P1 Report with fisherman code
	function p1_withcode(){
		$data['content_view'] = 'reports/report/report_p1_with_fcode_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['all_maingroup'] = $this->RM->get_all_maingroups();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Report P1 with Fisherman Code');
		$this->template->layout($data);
	}
	
	function ajax_p1_withcode(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('p1_report');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$maingroup = $this->input->post('maingroup');
			$report_data = $this->RM->get_p1_report($from_date, $to_date, $maingroup);
			if(!empty($report_data)){
				$tbody = '';
				$tCqty = $tCwt = $tRqty = $tRwt = $tMqty = $tMwt = $tKqty = $tKwt = $tAqty = $tAwt = $tSqty = $tSwt = $tLqty = $tLwt = $tLocalminor = $tTqty = $tTwt =  0;
				$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
				$search_key = ($maingroup > 0 ? $report_data[0]['Samiti_name'] : 'All');
				foreach($report_data as $report){
					
					$tbody .='<tr>';
                    $tbody .='    <td>'.$report['f_name'].'</td>';
					$tbody .='    <td>'.$report['f_code'].'</td>';
					$tbody .='    <td>'.$report['page_code'].'</td>';
                    $tbody .='    <td>'.$report['Cqty'].'</td>';
                    $tbody .='    <td>'.$report['Cwt'].'</td>';
                    $tbody .='    <td>'.$report['Rqty'].'</td>';
                    $tbody .='    <td>'.$report['Rwt'].'</td>';
                    $tbody .='    <td>'.$report['Mqty'].'</td>';
                    $tbody .='    <td>'.$report['Mwt'].'</td>';
                    $tbody .='    <td>'.$report['Kqty'].'</td>';
                    $tbody .='    <td>'.$report['Kwt'].'</td>';
                    $tbody .='    <td>'.$report['Aqty'].'</td>';
                    $tbody .='    <td>'.$report['Awt'].'</td>';
                    $tbody .='    <td>'.$report['Sqty'].'</td>';
                    $tbody .='    <td>'.$report['Swt'].'</td>';
                    $tbody .='    <td>'.$report['Lqty'].'</td>';
                    $tbody .='    <td>'.$report['Lwt'].'</td>';
                    $tbody .='    <td>'.$report['Localminor'].'</td>';
                    $tbody .='    <td>'.$report['Tmqty'].'</td>';
                    $tbody .='    <td>'.$report['Tmwt'].'</td>';
					$tbody .='    <td>'.$report['Tqty'].'</td>';
                    $tbody .='    <td>'.$report['Twt'].'</td>';
                    $tbody .='</tr>';
					
					
					$tCqty += $report['Cqty'];
					$tCwt +=  $report['Cwt'];
					$tRqty +=  $report['Rqty'];
					$tRwt +=  $report['Rwt'];
					$tMqty +=  $report['Mqty'];
					$tMwt +=  $report['Mwt'];
					$tKqty +=  $report['Kqty'];
					$tKwt +=  $report['Kwt'];
					$tAqty +=  $report['Aqty'];
					$tAwt +=  $report['Awt'];
					$tSqty +=  $report['Sqty'];
					$tSwt +=  $report['Swt'];
					$tLqty +=  $report['Lqty'];
					$tLwt +=  $report['Lwt'];
					$tLocalminor +=  $report['Localminor'];
					$tTqty +=  $report['Tqty'];
					$tTwt +=  $report['Twt'];
				}
				
				$tfoot ='<th colspan="3" width="5%">Total :</th>';
				$tfoot .='<th class="cqty">'.$tCqty.'</th>';
                $tfoot .='<th class="cwt">'.$tCwt.'</th>';
                $tfoot .='<th class="rqty">'.$tRqty.'</th>';
                $tfoot .='<th class="rwt">'.$tRwt.'</th>';
                $tfoot .='<th class="mqty">'.$tMqty.'</th>';
                $tfoot .='<th class="mwt">'.$tMwt.'</th>';
                $tfoot .='<th class="kqty">'.$tKqty.'</th>';
                $tfoot .='<th class="kwt">'.$tKwt.'</th>';
                $tfoot .='<th class="aqty">'.$tAqty.'</th>';
                $tfoot .='<th class="awt">'.$tAwt.'</th>';
                $tfoot .='<th class="sqty">'.$tSqty.'</th>';
                $tfoot .='<th class="swt">'.$tSwt.'</th>';
                $tfoot .='<th class="lqty">'.$tLqty.'</th>';
                $tfoot .='<th class="lwt">'.$tLwt.'</th>';
                $tfoot .='<th class="localminor">'.$tLocalminor.'</th>';
                $tfoot .='<th class=""></th>';
                $tfoot .='<th class=""></th>';
                $tfoot .='<th class="gtqty">'.$tTqty.'</th>';
                $tfoot .='<th class="gtwt">'.$tTwt.'</th>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot,'search_key'=>$search_key,'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
				
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//4. Fish Percent Report
	function fish_percent(){
		$data['content_view'] = 'reports/report/report_fish_percent_v';
		$data['all_maingroup'] = $this->RM->get_all_maingroups();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Fish Percent Report');
		$this->template->layout($data);
	}
	
	function ajax_fish_percent(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('fish_percent');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			
			$report_data = $this->RM->get_fish_percent($from_date, $to_date);
			if(!empty($report_data)){
				$tbody = '';
				$tCqty = $tCwt = $tRqty = $tRwt = $tMqty = $tMwt = $tKqty = $tKwt = $tAqty = $tAwt = $tSqty = $tSwt = $tLqty = $tLwt = $tLocalminor = $tTqty = $tTwt =  0;
				$search_date = $this->input->post('from_date').' To '.$this->input->post('to_date');
				$search_key = $report_data[0]['Samiti_name'];
				
				foreach($report_data as $report){
					
					$tbody .='<tr>';
                    $tbody .='    <td>'.get_date('', $report['Date']).'</td>';
                    $tbody .='    <td>'.$report['tCwt'].'</td>';
                    $tbody .='    <td>'.$report['tRwt'].'</td>';
                    $tbody .='    <td>'.$report['tMwt'].'</td>';
                    $tbody .='    <td>'.$report['tKwt'].'</td>';
                    $tbody .='    <td>'.$report['tAwt'].'</td>';
                    $tbody .='    <td>'.$report['tSwt'].'</td>';
                    $tbody .='    <td>'.$report['tLwt'].'</td>';
                    $tbody .='    <td>'.$report['tLocalminor'].'</td>';
                    $tbody .='    <td>'.$report['tTwt'].'</td>';
                    $tbody .='</tr>';
					
					
					$tCwt +=  $report['tCwt'];
					$tRwt +=  $report['tRwt'];
					$tMwt +=  $report['tMwt'];
					$tKwt +=  $report['tKwt'];
					$tAwt +=  $report['tAwt'];
					$tSwt +=  $report['tSwt'];
					$tLwt +=  $report['tLwt'];
					$tLocalminor +=  $report['tLocalminor'];
					$tTwt +=  $report['tTwt'];
				}
				
				$tfoot ='<tr>';
				$tfoot .='<th>Total :</th>';
                $tfoot .='<th>'.$tCwt.'</th>';
                $tfoot .='<th>'.$tRwt.'</th>';
                $tfoot .='<th>'.$tMwt.'</th>';
                $tfoot .='<th>'.$tKwt.'</th>';
                $tfoot .='<th>'.$tAwt.'</th>';
                $tfoot .='<th>'.$tSwt.'</th>';
                $tfoot .='<th>'.$tLwt.'</th>';
                $tfoot .='<th>'.$tLocalminor.'</th>';
                $tfoot .='<th>'.$tTwt.'</th>';
				$tfoot .='</tr>';
				
				$tfoot .='<tr>';
				$tfoot .='<th>Percent :</th>';
                $tfoot .='<th>'.number_format((($tCwt/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tRwt/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tMwt/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tKwt/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tAwt/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tSwt/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tLwt/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tLocalminor/$tTwt)*100), 2).' %</th>';
                $tfoot .='<th>'.number_format((($tTwt/$tTwt)*100), 2).' %</th>';
				$tfoot .='</tr>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
				
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//5. Fisherman Ledger Report (ajax_fisherman_ledger)
	function fisherman_ledger(){
		$data['content_view'] = 'reports/report/report_fisherman_ledger_v';	
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Fisherman Ledger Report');
		$this->template->layout($data);
	}
	
	function ajax_fisherman_ledger(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('fisherman_ledger');
		if($form_validation){
			$from_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('from_date'))));
			$to_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('to_date'))));
			$fisherman = $this->input->post('fisherman');
			
			//$data['outward'] = $this->RM->get_product_outward($from_date, $to_date, $fisherman);
			//$data['outward_return'] = $this->RM->get_product_outward_return($from_date, $to_date, $fisherman);
			//$data['transferred_liability'] = $this->RM->get_transferred_liability($from_date, $to_date, $fisherman);
			//$data['wages_sheet'] = $this->RM->get_wages_sheet($from_date, $to_date, $fisherman);
			//$data['wages_advance'] = $this->RM->get_wages_advance($from_date, $to_date, $fisherman);
			
			//$report_data = array_merge_recursive($data['outward_return'], $data['wages_sheet'], $data['wages_advance'], $data['outward'], $data['transferred_liability']);
			
			$sql = "
					SELECT CONCAT(wa.ID, ' - Advance Wages to fisherman') as particulars, wa.Amount as debit, '0' as credit, wa.Date as date
						FROM ".WAGES_ADVANCE." wa 
						WHERE wa.status='Active' AND wa.advance_to='Fisherman' AND wa.Date >= '".$from_date."' AND wa.Date <= '".$to_date."' AND wa.Fisherman = '". $fisherman."'
					
					UNION ALL 
					
					SELECT CONCAT(cdp.deposit_id, ' - Cash deposit for product liability') as particulars, '0' as debit, cdp.product_liability as credit, cdp.deposit_date as date 
						FROM ".CASH_DEPOSITED_PAYMENT." cdp 
						WHERE cdp.status = 'Active' AND cdp.deposit_date >= '".$from_date."' AND cdp.deposit_date <= '".$to_date."' AND cdp.fisherman_id = '". $fisherman."' AND cdp.deposited_by = 'Fisherman'
					
					UNION ALL
					
					SELECT CONCAT(cdp.deposit_id, ' - Cash deposit for advance wages') as particulars, '0' as debit, cdp.wages_liability as credit, cdp.deposit_date as date 
						FROM ".CASH_DEPOSITED_PAYMENT." cdp 
						WHERE cdp.status = 'Active' AND cdp.deposit_date >= '".$from_date."' AND cdp.deposit_date <= '".$to_date."' AND cdp.fisherman_id = '". $fisherman."' AND cdp.deposited_by = 'Fisherman'
					
					UNION ALL
					
					SELECT CONCAT(wi.ID, ' - Net Wages Payable') as particulars, wi.final_wages as debit, wi.final_wages as credit, wi.to_date as date
						FROM ".WAGESITEM." wi 
						WHERE wi.status = 'Active' AND wi.wages_for='Fisherman' AND wi.from_date >= '".$from_date."' AND wi.to_date <= '".$to_date."' AND wi.FishermanId = '". $fisherman."' 
					UNION ALL 
					
					SELECT CONCAT(ld.wages_item_id, ' - Personal Liability Deduction') as particulars, '0' as debit, (ld.amount+ld.advance_deduction) as credit, ld.to_date as date
						FROM ".LIABILITY_DEDUCTION." ld 
						WHERE ld.status = 'Active' AND ld.from_date >= '".$from_date."' AND ld.to_date <= '".$to_date."' AND ld.deducted_by = '". $fisherman."' AND ld.deducted_for = '". $fisherman."' 
							
					UNION ALL 
					
					SELECT CONCAT(ld.wages_item_id, ' - Group Liability Deduction for ', fm.Name) as particulars, (ld.amount+ld.advance_deduction) as debit, (ld.amount+ld.advance_deduction) as credit, ld.to_date as date
						FROM ".LIABILITY_DEDUCTION." ld LEFT JOIN ".FISHERMAN." fm ON(fm.ID = ld.deducted_for)
						WHERE ld.status = 'Active' AND ld.from_date >= '".$from_date."' AND ld.to_date <= '".$to_date."' AND ld.deducted_by = '". $fisherman."' AND ld.deducted_for != '". $fisherman."'
					UNION ALL 
					
					SELECT CONCAT(ld.wages_item_id, ' - Group Liability Deduction by ', fm.Name) as particulars, '0' as debit, (ld.amount+ld.advance_deduction) as credit, ld.to_date as date
						FROM ".LIABILITY_DEDUCTION." ld LEFT JOIN ".FISHERMAN." fm ON(fm.ID = ld.deducted_by)
						WHERE ld.status = 'Active' AND ld.from_date >= '".$from_date."' AND ld.to_date <= '".$to_date."' AND ld.deducted_by != '". $fisherman."' AND ld.deducted_for = '". $fisherman."'
					UNION ALL 
					
					SELECT CONCAT(wi.ID, ' - Advance Wages Deduction') as particulars, '0' as debit, wi.AdvanceWagesDeduction as credit, wi.to_date as date
						FROM ".WAGESITEM." wi 
						WHERE wi.status = 'Active' AND wi.wages_for='Fisherman' AND wi.from_date >= '".$from_date."' AND wi.to_date <= '".$to_date."' AND wi.FishermanId = '". $fisherman."' 												
					
					UNION ALL 					 
					
					SELECT  CONCAT(po.ID, ' - Product Outward Liability') as particulars, po.sub_total as debit, po.cash_received as credit, po.outward_date as date 
						FROM ".PRODUCT_OUTWARD." po
						WHERE po.status = 'Active' AND po.outward_to = 'Fisherman' AND po.outward_date >= '".$from_date."' AND po.outward_date <= '".$to_date."' AND po.fisherman_id = '". $fisherman."' 												
					
					UNION ALL
					
					SELECT CONCAT(por.ID, ' - Product Outward Return') as particulars, '0' as debit, por.total_price as credit, por.return_date as date
						FROM ".PRODUCT_OUTWARD_ITEM_RETURN." por 
						WHERE por.status = 'Active' AND por.outward_to = 'Fisherman' AND por.return_date >= '".$from_date."' AND por.return_date <= '".$to_date."' AND por.fisherman_id = '". $fisherman."' 
					
					UNION ALL 
					 
					SELECT CONCAT(trl.ID, ' - Transferred Outward liability') as particulars, trl.amount as debit, '0' as credit, trl.added_date as date
						FROM ".TRANSFERRED_LIABILITY." trl 
						WHERE trl.added_date >= '".$from_date."' AND trl.added_date <= '".$to_date."' AND trl.secondary_id = '". $fisherman."' 
									
					ORDER BY date ASC
					";
			
			/*"
			SELECT CONCAT(wi.ID, ' - Group Liability Deduction') as particulars, '0' as debit, wi.GroupLiabilityDeduction as credit, wi.to_date as date
					FROM ".WAGESITEM." wi 
					WHERE wi.from_date >= '".$from_date."' AND wi.to_date <= '".$to_date."' AND wi.status = 'Active' AND wi.FishermanId = '". $fisherman."' 
			UNION ALL 
			
			SELECT CONCAT(wi.ID, ' - Grovernment Charges Deduction') as particulars, wi.GovDeduction as debit, '0' as credit, wi.to_date as date
							FROM ".WAGESITEM." wi 
							WHERE wi.from_date >= '".$from_date."' AND wi.to_date <= '".$to_date."' AND wi.status = 'Active' AND wi.FishermanId = '". $fisherman."' 												
					
					UNION ALL";*/
			
			
			$report_data = $this->db->query($sql)->result_array();
			//printr($report_data);			
			$tbody = '';
			$debit = $credit = 0.00;
			if(!empty($report_data)){
				$i = 0;				
				foreach($report_data as $report){
					$date = get_date('d/m/Y', $report['date']);
					
					if($report['debit'] != '0' || $report['credit'] != '0'){ $i++;
						$tbody .='<tr>';
						$tbody .='    <td>'.$i.'</td>';
						$tbody .='    <td>'.$date.'</td>';
						$tbody .='    <td>'.$report['particulars'].'</td>';
						$tbody .='    <td>'.($report['debit'] != '0' ? $report['debit'] : '').'</td>';
						$tbody .='    <td>'.($report['credit'] != '0' ? $report['credit'] : '').'</td>';
						$tbody .='</tr>';
						
						$debit += !empty($report['debit']) ? $report['debit'] : 0;
						$credit += !empty($report['credit']) ? $report['credit'] : 0;
					}					
					
				}		
			}
			$opening_balance = $this->RM->get_fisherman_opening_balance($fisherman, $from_date);
			$opening_balance = $opening_balance['remaining_liability'];
			$current_balance = ($debit-$credit);
			$remaing_balance = $opening_balance + $current_balance;
			
			$closing_balance = $remaing_balance > 0 ? $remaing_balance : '<span class="text-danger">'.($remaing_balance).'</span>';
						
			$tfoot  ='<tr>';
			$tfoot .='    <th colspan="3" class="text-right"><strong>Summary</strong> </th>';
			$tfoot .='    <th>'.$debit.'</th>';
			$tfoot .='    <th>'.$credit.'</th>';
			$tfoot .='</tr>';
			
			$tfoot .='<tr>';
			$tfoot .='    <th colspan="4" class="text-right"><strong>Opening Balance</strong> </th>';
			$tfoot .='    <th>'.($opening_balance).'</th>';
			$tfoot .='</tr>';
			$tfoot .='<tr>';
			$tfoot .='    <th colspan="4" class="text-right"><strong>Current Balance</strong> </th>';
			$tfoot .='    <th>'.($current_balance).'</th>';
			$tfoot .='</tr>';			
			$tfoot .='<tr>';
			$tfoot .='    <th colspan="4" class="text-right"><strong>Closing Balance</strong> </th>';
			$tfoot .='    <th>'.($closing_balance).'</th>';
			$tfoot .='</tr>';
			$tfoot .= $this->datatable_scripts;
			
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'p1_report':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				$this->form_validation->set_rules('maingroup', 'Maingroup', 'trim|required|min_length[1]');
			break;
			
			case 'p2_report':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				$this->form_validation->set_rules('point', 'Point', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('group_type', 'Group Type', 'trim|required|min_length[1]');
			break;
			
			case 'fish_percent':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
			break;
			
			case'fisherman_ledger':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				$this->form_validation->set_rules('fisherman', 'Fisherman', 'trim|required|min_length[1]');
			break;
			
			case 'outward_fisherman':
				$this->form_validation->set_rules('maingroup', 'Maingroup', 'trim|required|min_length[1]');
			break;
			
			case 'wages_report':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				$this->form_validation->set_rules('maingroup', 'Maingroup', 'trim|required|min_length[1]');
			break;
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
	
	//Form rule validation callback functions
	function validate_date(){
		$data = FALSE;
		$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
		$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
		if(strtotime($to_date) >= strtotime($from_date)){
			$data = TRUE;
		}
		return $data;
	}
	
	//Select fisherman ajax request
	function ajax_fisherman(){
		$data['result1'] = '';
		$q = $this->input->get('q');
		if(!empty($q)){
			$data = $this->RM->get_fisherman($q);
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
}


