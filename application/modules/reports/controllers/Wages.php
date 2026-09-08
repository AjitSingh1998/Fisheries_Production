<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Wages extends MY_Controller {
	var $userID;
	var $stylesheet_array;
	var $scriptsrc_array;
	var $datatable_scripts;
	
	function __construct(){
		parent::__construct();
		$this->userID = checkUserLogin();
		$this->load->model('reports_model', 'RM');
		$this->stylesheet_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
										 base_url('assets/plugins/select2/dist/css/select2.min.css'),
										 base_url('assets/plugins/DataTables/media/css/jquery.dataTables.min.css'),
										 );
		$this->scriptsrc_array = array(base_url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
										base_url('assets/plugins/select2/dist/js/select2.full.min.js'),
										site_url('reports/assets/js/jquery.table2excel.min.js'),
										site_url('reports/assets/js/jQuery.print.min.js'),
										base_url('assets/plugins/DataTables/media/js/jquery.dataTables.min.js'),
										site_url('reports/assets/js/reports.js'),
										site_url('reports/assets/js/extra.js')
										);
		$this->datatable_scripts = '<style> 
									table.dataTable thead th, table.dataTable thead td { padding: 8px !important;}
									table.dataTable tbody th, table.dataTable tbody td { padding: 1px 5px !important;}
									</style>';											
	}
	
	function index(){
		redirect(site_url('reports/wages/weekly'));
	}
	
	//1. Wages Weekly Report
	function weekly(){
		$data['content_view'] = 'reports/wages/report_wages_weekly_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Wages Weekly Report');
		$this->template->layout($data);
	}
	
	function ajax_weekly(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('weekly');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
			
			//Preparing Header Start
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			$i=0;
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("d-M");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			//Preparing Header End
			
			$tbody = '';
			$tfoot = '';
			$total_value = array();
			$report_data = $this->RM->get_wages_weekly($from_date, $to_date, $group_type);
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){					
					$samiti = explode('__', $key);
					$tbody .= '<tr>';
					$tbody .= '<td>'.@$samiti[1].'</td>';
					$total_wages = $govt_deduction = $net_amount = $adv_dec = $grp_ded = 0;
					$j=0;
					foreach($daterange as $date){
						$toll_date = $date->format("Y-m-d");			
						if(array_key_exists($toll_date, $r_data)){											
							$report = $r_data[$toll_date];
							$report['Twt'] = ($report['Twt'] != '') ? $report['Twt'] : 0 ;
							
							$tbody .= '<td>'.$report['Twt'].'</td>';
							$total_wages += ($report['total_wages'] != '') ? $report['total_wages'] : 0 ;
							$govt_deduction += ($report['govt_deduction'] != '') ? $report['govt_deduction'] : 0 ;
							$net_amount += ($report['net_amount'] != '') ? $report['net_amount'] : 0 ;
							
							$grp_lib_cash_dep = $adv_wage_cash_dep = 0;
							if(!empty($report['cash_deposited']) && $report['cash_deposited'] != ''){
								$cash_deposited = explode('|', $report['cash_deposited']);
								$grp_lib_cash_dep = ($cash_deposited[0] != '') ? $cash_deposited[0] : 0 ;
								$adv_wage_cash_dep = ($cash_deposited[1] != '') ? $cash_deposited[1] : 0 ;
							}
							
							$grp_ded = $grp_lib_cash_dep + ($report['group_liability_deducted'] != '') ? $report['group_liability_deducted'] : 0 ;
							$adv_dec = $adv_wage_cash_dep + ($report['AdvanceWagesDeduction'] != '') ? $report['AdvanceWagesDeduction'] : 0 ;
							
							$total_value[$j] = $report['Twt'] + (isset($total_value[$j]) ? $total_value[$j] : 0);
													
						}else{
							$tbody .= '	<td>0</td>';
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['tw'] 	= $total_wages + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  = $govt_deduction + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] = $grp_ded + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']	= $adv_dec + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $net_amount + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$tbody .= '<td>'.$total_wages.'</td>';
					$tbody .= '<td>'.$govt_deduction.'</td>';
					$tbody .= '<td>'.$grp_ded.'</td>';
					$tbody .= '<td>'.$adv_dec.'</td>';
					$tbody .= '<td>'.($net_amount-($grp_ded+$adv_dec)).'</td>';
					
					$tbody .= '</tr>';
					
					$i++;
					
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th>Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '</tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//2. Wages Monthly Report
	function monthly(){
						
		$data['content_view'] = 'reports/wages/report_wages_monthly_v';
		
		$data['group_type'] = $this->RM->get_all_group_type();
		//$data['all_maingroup'] = $this->RM->get_all_maingroups();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Wages Monthly Report');
		$this->template->layout($data);
	}
		
	function ajax_monthly(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_monthly');
		if($form_validation){
			
			$month = $this->input->post('month');
			$group_type = $this->input->post('group_type');
						
			$date_period[] = array('from'=>date('Y-m-01', strtotime($month)), 'to'=>date('Y-m-15', strtotime($month)));
			//$date_period[] = array('from'=>date('Y-m-08', strtotime($month)), 'to'=>date('Y-m-15', strtotime($month)));
			//$date_period[] = array('from'=>date('Y-m-16', strtotime($month)), 'to'=>date('Y-m-23', strtotime($month)));
			$date_period[] = array('from'=>date('Y-m-16', strtotime($month)), 'to'=>date('Y-m-t', strtotime($month)));

			
			$report_data = $this->RM->get_wages_monthly($group_type, $date_period);
			$search_date = $this->input->post('month');
			
			$all_date = '';
			$i=0;
			$colspan = 0;			
			foreach($date_period as $date){
				$toll_date = get_date('d/m/Y', $date['from']).' - '.get_date('d/m/Y', $date['to']);
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			$tbody = '';
			$tfoot = '';
			
			$total_value = array();
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key2 => $r_data){
					$i++;				
					$samiti = explode('__', $key2);
					$tbody .= '<tr>';
					$tbody .= '<td>'.$i.'</td>';
					$tbody .= '<td>'.@$samiti[1].'</td>';
					$total_wages = $govt_deduction = $net_amount = $adv_dec = $grp_ded = 0;
					$j=0;
					foreach($date_period as $key => $date){
						//$toll_date = $date->format("W");			
						
						if(array_key_exists($key, $r_data)){											
							$report = $r_data[$key];
							$report['Twt'] = ($report['Twt'] == '') ? 0 : $report['Twt'];
							
							$tbody .= '<td>'.$report['Twt']. '</td>';
							
							$total_wages += ($report['total_wages'] == '') ? 0 : $report['total_wages'];
							$govt_deduction += ($report['govt_deduction'] == '') ? 0 : $report['govt_deduction'];
							$net_amount += ($report['net_amount'] == '') ? 0 : $report['net_amount'];
							
							$grp_lib_cash_dep = $adv_wage_cash_dep = 0;
							if(!empty($report['cash_deposited']) && $report['cash_deposited'] != ''){
								$cash_deposited = explode('|', $report['cash_deposited']);
								$grp_lib_cash_dep = ($cash_deposited[0] != '') ? $cash_deposited[0] : 0 ;
								$adv_wage_cash_dep = ($cash_deposited[1] != '') ? $cash_deposited[1] : 0 ;
							}
							
							$grp_ded = $grp_lib_cash_dep + ($report['group_liability_deducted'] != '') ? $report['group_liability_deducted'] : 0 ;
							$adv_dec = $adv_wage_cash_dep + ($report['AdvanceWagesDeduction'] != '') ? $report['AdvanceWagesDeduction'] : 0 ;
							
							$total_value[$j] = $report['Twt'] + (isset($total_value[$j]) ? $total_value[$j] : 0);
			
													
						}else{
							$tbody .= '	<td>0</td>';
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['tw'] 	= $total_wages + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  = $govt_deduction + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] 		= $grp_ded + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']			= $adv_dec + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $net_amount + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$tbody .= '<td>'.$total_wages.'</td>';
					$tbody .= '<td>'.$govt_deduction.'</td>';
					$tbody .= '<td>'.$grp_ded.'</td>';
					$tbody .= '<td>'.$adv_dec.'</td>';
					$tbody .= '<td>'.($net_amount-($grp_ded+$adv_dec)).'</td>';
					$tbody .= '</tr>';
					
					
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="2">Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '</tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	//3. Wages Quarterly Report
	function quarterly(){
		$data['content_view'] = 'reports/wages/report_wages_quarterly_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['page_title'] = 'Wages Yearly Report';
		$data['data_url'] = 'reports/wages/ajax_quarterly';
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function ajax_quarterly(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('ajax_quarterly');
		if($form_validation){
			$from_date = get_date('Y-m-d', $this->input->post('from_date'));
			$to_date = get_date('Y-m-t', $this->input->post('to_date'));
			$group_type = $this->input->post('group_type');
			$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
			
			//Preparing Header Start
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1M');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("M-y");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			//Preparing Header End
			
			
			$tbody = '';
			$tfoot = '';
			$total_value = array();
			$report_data = $this->RM->get_wages_month_wise($from_date, $to_date, $group_type);
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){
					$i++;					
					$samiti = explode('__', $key);
					$tbody .= '<tr>';
					$tbody .= '<td>'.$i.'</td>';
					$tbody .= '<td>'.@$samiti[1].'</td>';
					$total_wages = $govt_deduction = $net_amount = $adv_dec = $grp_ded = 0;
					$j=0;
					foreach($daterange as $date){
						$toll_date = $date->format("n");			
						if(array_key_exists($toll_date, $r_data)){											
							$report = $r_data[$toll_date];
							$report['Twt'] = ($report['Twt'] != '') ? $report['Twt'] : 0 ;
							
							$tbody .= '<td>'.$report['Twt'].'</td>';
							$total_wages += ($report['total_wages'] != '') ? $report['total_wages'] : 0 ;
							$govt_deduction += ($report['govt_deduction'] != '') ? $report['govt_deduction'] : 0 ;
							$net_amount += ($report['net_amount'] != '') ? $report['net_amount'] : 0 ;
							
							$grp_lib_cash_dep = $adv_wage_cash_dep = 0;
							if(!empty($report['cash_deposited']) && $report['cash_deposited'] != ''){
								$cash_deposited = explode('|', $report['cash_deposited']);
								$grp_lib_cash_dep = ($cash_deposited[0] != '') ? $cash_deposited[0] : 0 ;
								$adv_wage_cash_dep = ($cash_deposited[1] != '') ? $cash_deposited[1] : 0 ;
							}
							
							$grp_ded = $grp_lib_cash_dep + ($report['group_liability_deducted'] != '') ? $report['group_liability_deducted'] : 0 ;
							$adv_dec = $adv_wage_cash_dep + ($report['AdvanceWagesDeduction'] != '') ? $report['AdvanceWagesDeduction'] : 0 ;
							
							$total_value[$j] = $report['Twt'] + (isset($total_value[$j]) ? $total_value[$j] : 0);
			
													
						}else{
							$tbody .= '	<td>0</td>';
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['tw'] 	= $total_wages + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  = $govt_deduction + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] 		= $grp_ded + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']			= $adv_dec + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $net_amount + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$tbody .= '<td>'.$total_wages.'</td>';
					$tbody .= '<td>'.$govt_deduction.'</td>';
					$tbody .= '<td>'.$grp_ded.'</td>';
					$tbody .= '<td>'.$adv_dec.'</td>';
					$tbody .= '<td>'.($net_amount-($grp_ded+$adv_dec)).'</td>';
					
					$tbody .= '</tr>';
					
					
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="2">Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '</tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//4. 
	function account(){
		$data['content_view'] = 'reports/wages/report_wages_weekly_with_account_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['maingroup'] = $this->RM->get_all_maingroups();
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Samiti wise weekly report with account');
		$this->template->layout($data);
	}
		
	function ajax_account(){
		
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_account');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			
			$report_data = $this->RM->get_wages_weekly_with_account($from_date, $to_date, $maingroup, $group_type);
			$search_date  = $this->input->post('from_date').' - '.$this->input->post('to_date');
			$maingroup_name = 'Samiti Name: ';
			if($maingroup > 0){
				$greslt = $this->db->get_where(MAINGROUP, array('ID' => $maingroup))->result_array();
				$maingroup_name .= $greslt['0']['Name'];
			}else{
				$maingroup_name .= 'All';
			}
			
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			$i=0;
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("d-M");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			$tbody = '';
			$tfoot = '';
			
			$total_value = array();
			
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){					
					
					$total_wages = $govt_deduction = $net_amount = $adv_dec = $grp_ded = $total_tmwt = $total_mnr = $total_swl = 0;
					$j=0;
					$oneday_Twt = $oneday_Tmwt = $oneday_Localminor = $oneday_Swt = $weight = $rate = 0;
					foreach($daterange as $date){
						$toll_date = $date->format("Y-m-d");			
						if(array_key_exists($toll_date, $r_data)){										
							$report = $r_data[$toll_date];
							
							$weight = $weight +  $report['Twt'];
							
							$MajorFee = $report['MajorFee'];
							$MinorFee = $report['MinorFee'];
							$SawalFee = $report['SawalFee'];
							
							$total_tmwt = $total_tmwt +  $report['Tmwt'];
							$total_mnr = $total_mnr +  $report['Localminor'];
							$total_swl = $total_swl +  $report['Swt'];
							
							$oneday_Twt 		.= '<th>'.$report['Twt'].'</th>';
							$oneday_Tmwt 		.= '<td>'.$report['Tmwt'].'</td>';
							$oneday_Localminor  .= '<td>'.$report['Localminor'].'</td>';
							$oneday_Swt 		.= '<td>'.$report['Swt'].'</td>';
							
							
							$grp_lib_cash_dep = $report['cash_product_liability'];
							$adv_wage_cash_dep = $report['cash_wages_liability'];
							
							/*if(!empty($report['cash_deposited']) && $report['cash_deposited'] != ''){
								$cash_deposited = explode('|', $report['cash_deposited']);
								$grp_lib_cash_dep = ($cash_deposited[0] != '') ? $cash_deposited[0] : 0 ;
								$adv_wage_cash_dep = ($cash_deposited[1] != '') ? $cash_deposited[1] : 0 ;
							}*/
							
							$GroupLiabilityDeduction = $report['GroupLiabilityDeduction'];
							$AdvanceWagesDeduction = $report['AdvanceWagesDeduction'];
							
							/*if(!empty($report['wages_deduction'])){
								$wages_deduction = explode('|', $report['wages_deduction']);						
								$GroupLiabilityDeduction = ($wages_deduction[0] != '') ? $wages_deduction[0] : 0.00;  //credit
								$AdvanceWagesDeduction = ($wages_deduction[1] != '') ? $wages_deduction[1] : 0.00;  //debit
							}*/
							
							//$GroupLiabilityDeduction = is_null($report['group_liability_deduction']) ? 0 : $report['group_liability_deduction'];
							
							$total_wages += $report['total_wages'];
							$govt_deduction += $report['govt_deduction'];
							$net_amount += $report['net_amount'];
							
							$grp_ded = $grp_lib_cash_dep + $GroupLiabilityDeduction;
							$adv_dec = $adv_wage_cash_dep + $AdvanceWagesDeduction;
							
							$total_value[$j] = $report['Twt'] + (isset($total_value[$j]) ? $total_value[$j] : 0);
			
													
						}else{
							$oneday_Twt .= '<th>0</th>';
							$oneday_Tmwt .= '<td>0</td>';
							$oneday_Localminor .= '<td>0</td>';
							$oneday_Swt .= '<td>0</td>';							
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['weight']  = $weight + (isset($total_value['weight']) ? $total_value['weight'] : 0);
					$total_value['rate']  = '';
					$total_value['tw'] 	= $total_wages + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  = $govt_deduction + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] 		= $grp_ded + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']			= $adv_dec + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= ($net_amount-($grp_ded+$adv_dec)) + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$mso = 'mso-number-format:"\@"';
					
					$samiti = explode('__', $key);
					$fname = str_replace('/',' / ', @$samiti[1]);
					$tbody .= '<tr>';
					$tbody .=   '<td rowspan="3">'.$fname.'</td>';
					$tbody .=   '<td rowspan="3">'.@$samiti[2].'</td>';
					$tbody .=   '<td rowspan="3" style='.$mso.' class="no-print">'.@$samiti[3].'</td>';
					$tbody .=   '<td rowspan="3" style='.$mso.' class="no-print">'.@$samiti[4].'</td>';
					$tbody .=   $oneday_Tmwt;					
					$tbody .=   '<th>'.$total_tmwt.'</th>';
					$tbody .=   '<td>'.$MajorFee.'</td>';
					$tbody .=   '<td rowspan="3">'.$total_wages.'</td>';
					$tbody .=   '<td rowspan="3">'.$govt_deduction.'</td>';
					$tbody .=   '<td rowspan="3">'.$grp_ded.'</td>';
					$tbody .=   '<td rowspan="3">'.$adv_dec.'</td>';
					$tbody .=   '<td rowspan="3">'.($net_amount-($grp_ded+$adv_dec)).'</td>';
					$tbody .=   '<td rowspan="3"></td>';
					$tbody .=   '</tr>';
					$tbody .=   '<tr>';
					$tbody .=   $oneday_Localminor;
					$tbody .=   '<th>'.$total_mnr.'</th>';
					$tbody .=   '<td>'.$MinorFee.'</td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .= $oneday_Swt;
					$tbody .= '<th>'.$total_swl.'</th>';
					$tbody .= '<td>'.$SawalFee.'</td>';
					$tbody .= '</tr>';
					
					$i++;
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="2">Summary </th>';
				$tfoot .= '<th class="no-print"> </th>';
				$tfoot .= '<th class="no-print"> </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '<th></th></tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$maingroup_colspan = 13+$colspan;			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan, 'maingroup_name'=>$maingroup_name, 'maingroup_colspan'=>$maingroup_colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	//5. Wages Account Report
	function samiti_account(){
		$data['content_view'] = 'reports/wages/report_wages_weekly_with_samiti_account_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Samiti wise weekly report with account');
		$this->template->layout($data);
	}
	
	function ajax_samiti_account_OLD(){
		
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_samiti_account');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$report_data = $this->RM->get_wages_weekly_with_samiti_account($from_date, $to_date, $group_type);
			$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
			
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			$i=0;
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("d-M");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			$tbody = '';
			$tfoot = '';
			
			$total_value = array();
			
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){			
					$samiti = explode('__', $key);
					$mso = 'mso-number-format:"\@"';
					$tbody .= '<tr>';
					$tbody .= '<td>'.@$samiti[1].'</td>';
					$tbody .= '<td>'.@$samiti[2].'</td>';
					$tbody .= '<td style='.$mso.'>'.@$samiti[3].'</td>';
					$tbody .= '<td style='.$mso.'>'.@$samiti[4].'</td>';
					$total_wages = $govt_deduction = $net_amount = $adv_dec = $grp_ded = 0;
					$j=0;
					foreach($daterange as $date){
						$toll_date = $date->format("Y-m-d");			
						if(array_key_exists($toll_date, $r_data)){											
							$report = $r_data[$toll_date];
														
							$tbody .= '<td>'.$report['Twt'].'</td>';
							$total_wages += $report['total_wages'];
							$govt_deduction += $report['govt_deduction'];
							$net_amount += $report['net_amount'];
							
							$grp_lib_cash_dep = $adv_wage_cash_dep = 0;
							if(!empty($report['cash_deposited']) && $report['cash_deposited'] != ''){
								$cash_deposited = explode('|', $report['cash_deposited']);
								$grp_lib_cash_dep = ($cash_deposited[0] != '') ? $cash_deposited[0] : 0 ;
								$adv_wage_cash_dep = ($cash_deposited[1] != '') ? $cash_deposited[1] : 0 ;
							}
							
							$GroupLiabilityDeduction = $AdvanceWagesDeduction = 0.00;
							if(!empty($report['wages_deduction'])){
								$wages_deduction = explode('|', $report['wages_deduction']);						
								$GroupLiabilityDeduction = ($wages_deduction[0] != '') ? $wages_deduction[0] : 0.00;  //credit
								$AdvanceWagesDeduction = ($wages_deduction[1] != '') ? $wages_deduction[1] : 0.00;  //debit
							}
							
							//$GroupLiabilityDeduction = is_null($report['group_liability_deduction']) ? 0 : $report['group_liability_deduction'];

							
							$grp_ded = $grp_lib_cash_dep + $GroupLiabilityDeduction;
							$adv_dec = $adv_wage_cash_dep + $AdvanceWagesDeduction;
							
							$total_value[$j] = $report['Twt'] + (isset($total_value[$j]) ? $total_value[$j] : 0);
			
													
						}else{
							$tbody .= '	<td>0</td>';
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['tw'] 	= $total_wages + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  = $govt_deduction + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] 		= $grp_ded + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']			= $adv_dec + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= ($net_amount-($grp_ded+$adv_dec)) + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$tbody .= '<td>'.$total_wages.'</td>';
					$tbody .= '<td>'.$govt_deduction.'</td>';
					$tbody .= '<td>'.$grp_ded.'</td>';
					$tbody .= '<td>'.$adv_dec.'</td>';
					$tbody .= '<td>'.($net_amount-($grp_ded+$adv_dec)).'</td>';
					$tbody .= '<td></td>';
					$tbody .= '</tr>';
					$i++;
					
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="4">Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '<th></th></tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	function ajax_samiti_account_OLD2(){
		
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_samiti_account');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$report_data = $this->RM->get_wages_weekly_with_samiti_account($from_date, $to_date, $group_type);
			$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
			
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			$i=0;
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("d-M");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			$tbody = '';
			$tfoot = '';
			
			$total_value = array();
			
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){					
					
					$j=0;
					$oneday_Twt = $oneday_Tmwt = $oneday_Localminor = $oneday_Swt = $weight = $rate = '';
										
					$toll_details = explode('|', $r_data['dailytollinfo']);
					
					$tollInfo = array();
					if(count($toll_details) > 0 && !empty($toll_details)){
						foreach($toll_details as $toll){							
							$oneday_toll = explode(', ', $toll);
							$tollDate = @$oneday_toll[0];
							$major = @$oneday_toll[1];
							$minor = @$oneday_toll[2];
							$sawal = @$oneday_toll[3];
							$tollInfo[$tollDate][] = array('Tmwt' => $major, 'Localminor' => $minor, 'Swt' => $sawal); 							
						}
						
					}
					$row_total_major_wt = $row_total_minor_wt = $row_total_sawal_wt = 0;
					foreach($daterange as $date){
						$toll_date = $date->format("Y-m-d");
						if(array_key_exists($toll_date, $tollInfo)){															
							$reportData = $tollInfo[$toll_date];
							
							$dt_major_wt = $dt_minor_wt = $dt_sawal_wt = 0;
							foreach($reportData as $report){
								$dt_major_wt += $report['Tmwt'];
								$dt_minor_wt += $report['Localminor'];
								$dt_sawal_wt += $report['Swt'];
							}
														
							$row_total_major_wt += $dt_major_wt;
							$row_total_minor_wt += $dt_minor_wt;
							$row_total_sawal_wt += $dt_sawal_wt;
							
							$oneday_wt = ($dt_major_wt + $dt_minor_wt + $dt_sawal_wt);
							
							//$oneday_Twt 		.= '<th>'.$oneday_wt.'</th>';
							$oneday_Tmwt 		.= '<td>'.$dt_major_wt.'</td>';
							$oneday_Localminor  .= '<td>'.$dt_minor_wt.'</td>';
							$oneday_Swt 		.= '<td>'.$dt_sawal_wt.'</td>';

							$total_value[$j] = $oneday_wt + (isset($total_value[$j]) ? $total_value[$j] : 0);			
													
						}else{
							//$oneday_Twt .= '<th>0</th>';
							$oneday_Tmwt .= '<td>0</td>';
							$oneday_Localminor .= '<td>0</td>';
							$oneday_Swt .= '<td>0</td>';							
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['weight']  = $r_data['total_wt'] + (isset($total_value['weight']) ? $total_value['weight'] : 0);
					//$total_value['rate']  	= '';
					$total_value['tw'] 		= $r_data['total_wages'] + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  	= $r_data['govt_deduction'] + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] = $r_data['GroupLiabilityDeduction'] + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']	= $r_data['AdvanceWagesDeduction'] + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $r_data['final_wages'] + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$mso = 'mso-number-format:"\@"';
									
					$tbody .= '<tr>';
					$tbody .=   '<td rowspan="3">'.$r_data['maingroup_name'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['bank_name'].'</td>';
					$tbody .=   '<td rowspan="3" style='.$mso.'>'.$r_data['ifsc_code'].'</td>';
					$tbody .=   '<td rowspan="3" style='.$mso.'>'.$r_data['account_number'].'</td>';
					$tbody .=   $oneday_Tmwt;					
					$tbody .=   '<th>'.$row_total_major_wt.'</th>';
					//$tbody .=   '<td>'.$r_data['MajorFee'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['total_wages'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['govt_deduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['GroupLiabilityDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['AdvanceWagesDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['final_wages'].'</td>';
					$tbody .=   '<td rowspan="3"></td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=   $oneday_Localminor;
					$tbody .=   '<th>'.$row_total_minor_wt.'</th>';
					//$tbody .=   '<td>'.$r_data['MinorFee'].'</td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=    $oneday_Swt;
					$tbody .= '  <th>'.$row_total_sawal_wt.'</th>';
					//$tbody .= '  <td>'.$r_data['SawalFee'].'</td>';
					$tbody .= '</tr>';
					
					$i++;
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="4">Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '<th></th></tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	function ajax_samiti_account(){
		
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_samiti_account');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$report_data = $this->RM->get_wages_weekly_with_samiti_account($from_date, $to_date, $group_type);
			$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
			
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			$i=0;
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("d-M");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			$tbody = '';
			$tfoot = '';
			
			$total_value = array();
			
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){					
					
					$j=0;
					$oneday_Twt = $oneday_Tmwt = $oneday_Localminor = $oneday_Swt = $weight = $rate = '';
										
					
					$tollInfo = array();
					if(isset($r_data['dailytollinfo']) > 0 && !empty($r_data['dailytollinfo'])){
						foreach($r_data['dailytollinfo'] as $toll){							
							
							$tollDate = $toll['toll_date'];
							$major = $toll['Tmwt'];
							$minor = $toll['Localminor'];
							$sawal = $toll['Swt'];
							$tollInfo[$tollDate][] = array('Tmwt' => $major, 'Localminor' => $minor, 'Swt' => $sawal); 							
						}
					}
					
					
					$row_total_major_wt = $row_total_minor_wt = $row_total_sawal_wt = 0;
					foreach($daterange as $date){
						$toll_date = $date->format("Y-m-d");
						if(array_key_exists($toll_date, $tollInfo)){															
							$reportData = $tollInfo[$toll_date];
							
							$dt_major_wt = $dt_minor_wt = $dt_sawal_wt = 0;
							foreach($reportData as $report){
								$dt_major_wt += $report['Tmwt'];
								$dt_minor_wt += $report['Localminor'];
								$dt_sawal_wt += $report['Swt'];
							}
														
							$row_total_major_wt += $dt_major_wt;
							$row_total_minor_wt += $dt_minor_wt;
							$row_total_sawal_wt += $dt_sawal_wt;
							
							$oneday_wt = ($dt_major_wt + $dt_minor_wt + $dt_sawal_wt);
							
							//$oneday_Twt 		.= '<th>'.$oneday_wt.'</th>';
							$oneday_Tmwt 		.= '<td>'.$dt_major_wt.'</td>';
							$oneday_Localminor  .= '<td>'.$dt_minor_wt.'</td>';
							$oneday_Swt 		.= '<td>'.$dt_sawal_wt.'</td>';

							$total_value[$j] = $oneday_wt + (isset($total_value[$j]) ? $total_value[$j] : 0);			
													
						}else{
							//$oneday_Twt .= '<th>0</th>';
							$oneday_Tmwt .= '<td>0</td>';
							$oneday_Localminor .= '<td>0</td>';
							$oneday_Swt .= '<td>0</td>';							
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['weight']  = $r_data['total_wt'] + (isset($total_value['weight']) ? $total_value['weight'] : 0);
					//$total_value['rate']  	= '';
					$total_value['tw'] 		= $r_data['total_wages'] + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  	= $r_data['govt_deduction'] + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] = $r_data['GroupLiabilityDeduction'] + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']	= $r_data['AdvanceWagesDeduction'] + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $r_data['final_wages'] + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$mso = 'mso-number-format:"\@"';
									
					$tbody .= '<tr>';
					$tbody .=   '<td rowspan="3">'.$r_data['maingroup_name'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['bank_name'].'</td>';
					$tbody .=   '<td rowspan="3" style='.$mso.'>'.$r_data['ifsc_code'].'</td>';
					$tbody .=   '<td rowspan="3" style='.$mso.'>'.$r_data['account_number'].'</td>';
					$tbody .=   $oneday_Tmwt;					
					$tbody .=   '<th>'.$row_total_major_wt.'</th>';
					//$tbody .=   '<td>'.$r_data['MajorFee'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['total_wages'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['govt_deduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['GroupLiabilityDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['AdvanceWagesDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['final_wages'].'</td>';
					$tbody .=   '<td rowspan="3"></td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=   $oneday_Localminor;
					$tbody .=   '<th>'.$row_total_minor_wt.'</th>';
					//$tbody .=   '<td>'.$r_data['MinorFee'].'</td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=    $oneday_Swt;
					$tbody .= '  <th>'.$row_total_sawal_wt.'</th>';
					//$tbody .= '  <td>'.$r_data['SawalFee'].'</td>';
					$tbody .= '</tr>';
					
					$i++;
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="4">Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '<th></th></tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	//6. 
	function fisherman_wages(){
		
		$data['content_view'] = 'reports/wages/report_fisherman_wages_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['maingroup'] = $this->RM->get_all_maingroups();
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Samiti wise weekly report with account');
		$this->template->layout($data);
	}
	
	function ajax_fisherman_wages_OLD(){
		
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_account');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$maingroup = $this->input->post('maingroup');
			$group_type = $this->input->post('group_type');
			$report_data = $this->RM->get_fisherman_wages_with_account($from_date, $to_date, $maingroup, $group_type);
			$search_date  = $this->input->post('from_date').' - '.$this->input->post('to_date');
			$maingroup_name = 'Samiti Name: ' . ($maingroup > 0 ? $report_data['0']['maingroup_name'] : 'All');
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			$i=0;
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("d-M");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			
			$tbody = '';
			$tfoot = '';
			
			$total_value = array();
			
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){					
					
					$j=0;
					$oneday_Twt = $oneday_Tmwt = $oneday_Localminor = $oneday_Swt = $weight = $rate = '';
										
					$toll_details = explode('|', $r_data['dailytollinfo']);
					
					$tollInfo = array();
					if(count($toll_details) > 0 && !empty($toll_details)){
						foreach($toll_details as $toll){							
							$oneday_toll = explode(', ', $toll);
							$tollDate = @$oneday_toll[0];
							$major = @$oneday_toll[1];
							$minor = @$oneday_toll[2];
							$sawal = @$oneday_toll[3];
							$tollInfo[$tollDate][] = array('Tmwt' => $major, 'Localminor' => $minor, 'Swt' => $sawal); 							
						}
						
					}
					$row_total_major_wt = $row_total_minor_wt = $row_total_sawal_wt = 0;
					foreach($daterange as $date){
						$toll_date = $date->format("Y-m-d");
						if(array_key_exists($toll_date, $tollInfo)){															
							$reportData = $tollInfo[$toll_date];
							
							$dt_major_wt = $dt_minor_wt = $dt_sawal_wt = 0;
							foreach($reportData as $report){
								$dt_major_wt += $report['Tmwt'];
								$dt_minor_wt += $report['Localminor'];
								$dt_sawal_wt += $report['Swt'];
							}
														
							$row_total_major_wt += $dt_major_wt;
							$row_total_minor_wt += $dt_minor_wt;
							$row_total_sawal_wt += $dt_sawal_wt;
							
							$oneday_wt = ($dt_major_wt + $dt_minor_wt + $dt_sawal_wt);
							
							//$oneday_Twt 		.= '<th>'.$oneday_wt.'</th>';
							$oneday_Tmwt 		.= '<td>'.$dt_major_wt.'</td>';
							$oneday_Localminor  .= '<td>'.$dt_minor_wt.'</td>';
							$oneday_Swt 		.= '<td>'.$dt_sawal_wt.'</td>';

							$total_value[$j] = $oneday_wt + (isset($total_value[$j]) ? $total_value[$j] : 0);			
													
						}else{
							$oneday_Twt .= '<th>0</th>';
							$oneday_Tmwt .= '<td>0</td>';
							$oneday_Localminor .= '<td>0</td>';
							$oneday_Swt .= '<td>0</td>';							
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['weight']  = $r_data['total_wt'] + (isset($total_value['weight']) ? $total_value['weight'] : 0);
					$total_value['rate']  	= '';
					$total_value['tw'] 		= $r_data['total_wages'] + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  	= $r_data['govt_deduction'] + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] = $r_data['GroupLiabilityDeduction'] + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']	= $r_data['AdvanceWagesDeduction'] + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $r_data['final_wages'] + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$mso = 'mso-number-format:"\@"';
									
					$tbody .= '<tr>';
					$tbody .=   '<td rowspan="3">'.$r_data['fisher_name'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['fisher_code'].'</td>';
					$tbody .=   '<td rowspan="3" class="no-print" style='.$mso.'>'.$r_data['IfscCode'].'</td>';
					$tbody .=   '<td rowspan="3" class="no-print" style='.$mso.'>'.$r_data['AccountNo'].'</td>';
					$tbody .=   $oneday_Tmwt;					
					$tbody .=   '<th>'.$row_total_major_wt.'</th>';
					$tbody .=   '<td>'.$r_data['MajorFee'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['total_wages'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['govt_deduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['GroupLiabilityDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['AdvanceWagesDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['final_wages'].'</td>';
					$tbody .=   '<td rowspan="3"></td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=   $oneday_Localminor;
					$tbody .=   '<th>'.$row_total_minor_wt.'</th>';
					$tbody .=   '<td>'.$r_data['MinorFee'].'</td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=    $oneday_Swt;
					$tbody .= '  <th>'.$row_total_sawal_wt.'</th>';
					$tbody .= '  <td>'.$r_data['SawalFee'].'</td>';
					$tbody .= '</tr>';
					
					$i++;
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr>
							   <th colspan="2">Summary </th>
							   <th class="no-print"></th>
							   <th class="no-print"></th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '<th></th></tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$maingroup_colspan = 13+$colspan;
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan, 'maingroup_name'=>$maingroup_name, 'maingroup_colspan'=>$maingroup_colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	function ajax_fisherman_wages(){
		
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_account');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$maingroup = $this->input->post('maingroup');
			$group_type = $this->input->post('group_type');
			$report_data = $this->RM->get_fisherman_wages_with_account($from_date, $to_date, $maingroup, $group_type);
			$search_date  = $this->input->post('from_date').' - '.$this->input->post('to_date');
			$maingroup_name = 'Samiti Name: ' . ($maingroup > 0 ? $report_data['0']['maingroup_name'] : 'All');
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			$i=0;
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("d-M");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			
			$tbody = '';
			$tfoot = '';
			
			$total_value = array();
			
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){					
					
					$j=0;
					$oneday_Twt = $oneday_Tmwt = $oneday_Localminor = $oneday_Swt = $weight = $rate = '';
					
					$tollInfo = array();
					if(isset($r_data['dailytollinfo']) > 0 && !empty($r_data['dailytollinfo'])){
						foreach($r_data['dailytollinfo'] as $toll){							
							
							$tollDate = $toll['toll_date'];
							$major = $toll['Tmwt'];
							$minor = $toll['Localminor'];
							$sawal = $toll['Swt'];
							$tollInfo[$tollDate][] = array('Tmwt' => $major, 'Localminor' => $minor, 'Swt' => $sawal); 							
						}
					}
					
					
					$row_total_major_wt = $row_total_minor_wt = $row_total_sawal_wt = 0;
					foreach($daterange as $date){
						$toll_date = $date->format("Y-m-d");
						if(array_key_exists($toll_date, $tollInfo)){															
							$reportData = $tollInfo[$toll_date];
							
							$dt_major_wt = $dt_minor_wt = $dt_sawal_wt = 0;
							foreach($reportData as $report){
								$dt_major_wt += $report['Tmwt'];
								$dt_minor_wt += $report['Localminor'];
								$dt_sawal_wt += $report['Swt'];
							}
														
							$row_total_major_wt += $dt_major_wt;
							$row_total_minor_wt += $dt_minor_wt;
							$row_total_sawal_wt += $dt_sawal_wt;
							
							$oneday_wt = ($dt_major_wt + $dt_minor_wt + $dt_sawal_wt);
							
							//$oneday_Twt 		.= '<th>'.$oneday_wt.'</th>';
							$oneday_Tmwt 		.= '<td>'.$dt_major_wt.'</td>';
							$oneday_Localminor  .= '<td>'.$dt_minor_wt.'</td>';
							$oneday_Swt 		.= '<td>'.$dt_sawal_wt.'</td>';

							$total_value[$j] = $oneday_wt + (isset($total_value[$j]) ? $total_value[$j] : 0);			
													
						}else{
							$oneday_Twt .= '<th>0</th>';
							$oneday_Tmwt .= '<td>0</td>';
							$oneday_Localminor .= '<td>0</td>';
							$oneday_Swt .= '<td>0</td>';							
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['weight']  = $r_data['total_wt'] + (isset($total_value['weight']) ? $total_value['weight'] : 0);
					$total_value['rate']  	= '';
					$total_value['tw'] 		= $r_data['total_wages'] + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  	= $r_data['govt_deduction'] + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] = $r_data['GroupLiabilityDeduction'] + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']	= $r_data['AdvanceWagesDeduction'] + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $r_data['final_wages'] + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$mso = 'mso-number-format:"\@"';
									
					$tbody .= '<tr>';
					$tbody .=   '<td rowspan="3">'.$r_data['fisher_name'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['fisher_code'].'</td>';
					$tbody .=   '<td rowspan="3" class="no-print" style='.$mso.'>'.$r_data['IfscCode'].'</td>';
					$tbody .=   '<td rowspan="3" class="no-print" style='.$mso.'>'.$r_data['AccountNo'].'</td>';
					$tbody .=   $oneday_Tmwt;					
					$tbody .=   '<th>'.$row_total_major_wt.'</th>';
					$tbody .=   '<td>'.$r_data['MajorFee'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['total_wages'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['govt_deduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['GroupLiabilityDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['AdvanceWagesDeduction'].'</td>';
					$tbody .=   '<td rowspan="3">'.$r_data['final_wages'].'</td>';
					$tbody .=   '<td rowspan="3"></td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=   $oneday_Localminor;
					$tbody .=   '<th>'.$row_total_minor_wt.'</th>';
					$tbody .=   '<td>'.$r_data['MinorFee'].'</td>';
					$tbody .= '</tr>';
					$tbody .= '<tr>';
					$tbody .=    $oneday_Swt;
					$tbody .= '  <th>'.$row_total_sawal_wt.'</th>';
					$tbody .= '  <td>'.$r_data['SawalFee'].'</td>';
					$tbody .= '</tr>';
					
					$i++;
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr>
							   <th colspan="2">Summary </th>
							   <th class="no-print"></th>
							   <th class="no-print"></th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '<th></th></tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$maingroup_colspan = 13+$colspan;
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan, 'maingroup_name'=>$maingroup_name, 'maingroup_colspan'=>$maingroup_colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	
	}
	
	//7. Samiti Report
	function samiti(){
		$data['content_view'] = 'reports/wages/report_wages_samiti_v';
		$data['all_maingroup'] = $this->RM->get_all_maingroups();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Wages Report');
		$this->template->layout($data);
	}
	
	function ajax_samiti(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('samiti');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$maingroup = $this->input->post('maingroup');
			
			$report_data = $this->RM->get_wages_samiti($from_date, $to_date, $maingroup);
			
			if(!empty($report_data)){
				$tbody = '';
				$total_wages = $group_liability = $adv_liability = $gov_deduction = $group_deduction = $adv_deduction = $net_wages = 0; //tfoot
				foreach($report_data as $report){
					$tbody .= '<tr>';
					$tbody .= '	<td>'.$report['f_name'].'</td>';
					$tbody .= '	<td>'.get_date('d/m/Y', $report['from_date']).'</td>';
					$tbody .= '	<td>'.get_date('d/m/Y', $report['to_date']).'</td>';
					$tbody .= '	<td>'.$report['TotalWage'].'</td>';
					$tbody .= '	<td>'.$report['group_liability'].'</td>';
					$tbody .= '	<td>'.$report['advance_wages'].'</td>';
					$tbody .= '	<td>'.$report['GovDeduction'].'</td>';
					$tbody .= '	<td>'.$report['GroupLiabilityDeduction'].'</td>';
					$tbody .= '	<td>'.$report['AdvanceWagesDeduction'].'</td>';
					$tbody .= '	<td>'.$report['final_wages'].'</td>';
					$tbody .= '</tr>';
					
					$total_wages 	 += $report['TotalWage'];
					$group_liability += $report['group_liability'];
					$adv_liability   += $report['advance_wages'];
					$gov_deduction   += $report['GovDeduction'];
					$group_deduction += $report['GroupLiabilityDeduction'];
					$adv_deduction 	 += $report['AdvanceWagesDeduction'];
					$net_wages 		 += $report['final_wages'];
				}
				
				 $tfoot  = '<tr>';
            	 $tfoot .= ' <th colspan="3">Summary :</th>';
            	 $tfoot .= ' <th width="10%">'.$total_wages.'</th>';
				 $tfoot .= ' <th width="10%">'.$group_liability.'</th>';
				 $tfoot .= ' <th width="10%">'.$adv_liability.'</th>';
				 $tfoot .= ' <th width="10%">'.$gov_deduction.'</th>';
				 $tfoot .= ' <th width="10%">'.$group_deduction.'</th>';
				 $tfoot .= ' <th width="10%">'.$adv_deduction.'</th>';
				 $tfoot .= ' <th width="10%">'.$net_wages.'</th>';
          		 $tfoot .= '</tr>';
				 $tfoot .= $this->datatable_scripts;
				 
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>'', 'search_date'=>'');
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	
	//8. overall_fisherman Report
	function overall_fisherman(){
		$data['content_view'] = 'reports/wages/report_wages_overall_fisherman_v';
		$data['data_url'] = 'reports/wages/ajax_overall_fisherman';
		$data['all_maingroup'] = $this->RM->get_all_maingroups();
		$data['page_title'] = 'Overall Fisherman Report';
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function ajax_overall_fisherman(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('overall_fisherman');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$maingroup = $this->input->post('maingroup');
			
			//$group_opening_balance = $this->RM->get_group_opening_balance($maingroup, $from_date);
			$group_opening_balance = $this->RM->get_samiti_opening_balance($maingroup, $from_date);
			$report_data = $this->RM->get_overall_fisherman($from_date, $to_date, $maingroup);
			$group_advance_payment = $this->RM->get_group_advance_payment($from_date, $to_date, $maingroup);
			
			$search_key = 'Group: '.(empty($maingroup) ?  'All' : $group_opening_balance['open_group_name']);
			$search_date = 'Date: '.$this->input->post('from_date').' - '.$this->input->post('to_date');
			$tbody = '';
			$tfoot = '';
			
			if(!empty($report_data)){
				$total_net_pending = 0;
				$total_major_wt = $total_cash_deposit = $total_opening_bal = $total_minor_wt = $total_sawal_wt = $total_total_wt = $total_total_amount = $total_present_days = $total_advance_wages = 0; //tfoot
				foreach($report_data as $report){
					$net_pending = ($report['cash_deposit'] + $report['total_amount']) - ($report['advance_wages'] + $report['opening_balance']);
					
					$tbody .= '<tr>';
					$tbody .= '	<td><a href="'.site_url('wages/dailytoll_info?fid='.$report['f_id'].'&fdt='.$from_date.'&tdt='.$to_date.'&mg='.$maingroup).'" class="loadDailyTollInfo text-danger">'.$report['f_name'].'</a></td>';
					$tbody .= '	<td>'.$report['major_wt'].'</td>';
					$tbody .= '	<td>'.$report['minor_wt'].'</td>';
					$tbody .= '	<td>'.$report['sawal_wt'].'</td>';
					$tbody .= '	<td>'.$report['total_wt'].'</td>';
					$tbody .= '	<td>'.$report['total_amount'].'</td>';
					$tbody .= '	<td>'.$report['present_days'].'</td>';
					$tbody .= '	<td>'.$report['opening_balance'].'</td>';
					$tbody .= '	<td>'.$report['advance_wages'].'</td>';
					$tbody .= '	<td>'.$report['cash_deposit'].'</td>';
					$tbody .= '	<td>'.($net_pending < 0 ? '<span class="text-danger">'.$net_pending.'</span>' : $net_pending).'</td>';
					$tbody .= '</tr>';
					
					$total_major_wt += $report['major_wt'];
					$total_minor_wt += $report['minor_wt'];
					$total_sawal_wt += $report['sawal_wt'];
					$total_total_wt += $report['total_wt'];
					$total_total_amount += $report['total_amount'];
					$total_present_days += $report['present_days'];
					$total_opening_bal += $report['opening_balance'];
					$total_advance_wages += $report['advance_wages'];
					$total_cash_deposit += $report['cash_deposit'];
					$total_net_pending += $net_pending;
				}
				
				 $tfoot  = '<tr>';
            	 $tfoot .= ' <th>Summary :</th>';
            	 $tfoot .= ' <th>'.$total_major_wt.'</th>';
				 $tfoot .= ' <th>'.$total_minor_wt.'</th>';
				 $tfoot .= ' <th>'.$total_sawal_wt.'</th>';
				 $tfoot .= ' <th>'.$total_total_wt.'</th>';
				 $tfoot .= ' <th>'.$total_total_amount.'</th>';
				 $tfoot .= ' <th>'.$total_present_days.'</th>';
				 $tfoot .= ' <th>'.$total_opening_bal.'</th>';
				 $tfoot .= ' <th>'.$total_advance_wages.'</th>';
				 $tfoot .= ' <th>'.$total_cash_deposit.'</th>';
				 $tfoot .= ' <th>'.$total_net_pending.'</th>';
          		 $tfoot .= '</tr>';
				 
				 $tfoot .= '<tr>';
            	 $tfoot .= ' <th colspan="10" class="text-right">Opening Balance</th>';
				 $tfoot .= ' <th>'.$group_opening_balance['opening_balance'].' (+)</th>';
          		 $tfoot .= '</tr>';
				 
				 $total_advance = $total_cash_deposit = 0;
				 if(!empty($group_advance_payment)){
					 foreach($group_advance_payment as $advance){
						 if($advance['debit'] == '0' && $advance['credit'] != '0'){
						 	$amount = $advance['credit'].' (-)';
							$total_cash_deposit += $advance['credit'];
						 }elseif($advance['debit'] != '0' && $advance['credit'] == '0'){
						 	$amount = $advance['debit'].' (+)';
							$total_advance += $advance['debit'];
						 }else{
						 	$amount = '0';
						 }
						 
						 
						 $tfoot .= '<tr>';
						 $tfoot .= ' <th>'.get_date('d/m/Y',$advance['date']).'</th>';
						 $tfoot .= ' <th colspan="9" class="text-right">'.$advance['particulars'].'</th>';
						 $tfoot .= ' <th>'.$amount.'</th>';
						 $tfoot .= '</tr>';
						 
					 }
				 }
				 
				 $tfoot .= '<tr>';
            	 $tfoot .= ' <th colspan="10" class="text-right">Total Wages</th>';
				 $tfoot .= ' <th>'.$total_total_amount.' (-)</th>';
          		 $tfoot .= '</tr>';
				 
				 $total_total_amount = $total_total_amount + $total_cash_deposit;
				 $current_balance = ($group_opening_balance['opening_balance'] + $total_advance) - $total_total_amount;
				 
				 $tfoot .= '<tr>';
            	 $tfoot .= ' <th colspan="10" class="text-right">Current Balance</th>';
				 $tfoot .= ' <th>'.($current_balance < 0 ? '<span class="text-danger">'.$current_balance.'</span>' : $current_balance).'</th>';
          		 $tfoot .= '</tr>';
				 
				 $tfoot .= $this->datatable_scripts;
				
			}
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
		}else{
			$data = array('status'=>'danger', 'message'=>validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//9. overall_group Report
	function overall_samiti(){
		$data['content_view'] = 'reports/wages/report_wages_overall_samiti_v';
		$data['data_url'] = 'reports/wages/ajax_overall_samiti';
		$data['group_type'] = $this->RM->get_all_group_type();
		//$data['all_maingroup'] = $this->RM->get_all_maingroups();
		$data['page_title'] = 'Overall Samiti Report';
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function ajax_overall_samiti(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('overall_samiti');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			
			if(is_array($maingroup) && !empty($maingroup)){
				foreach($maingroup as $key => $mgid){
					if($mgid === '0'){
						unset($maingroup[$key]);
					}	
				}				
			}
			
			$report_data = $this->RM->get_overall_samiti($from_date, $to_date, $maingroup, $group_type);
			
			$search_key = '';
			$search_date = 'Date: '.$this->input->post('from_date').' - '.$this->input->post('to_date');
			$tbody = '';
			$tfoot = '';
			
			if(!empty($report_data)){
				$total_net_pending = $tTotal_outward = $tOutward_deduction = $tAdvance_deduction = 0;
				$total_major_wt = $total_cash_deposit = $total_opening_bal = $total_minor_wt = $total_sawal_wt = $total_total_wt = $total_total_amount = $total_present_days = $total_advance_wages = 0; //tfoot
				foreach($report_data as $report){
					
					$cash_received 	= 0;
					$total_outward 	= 0;
					
					if(!empty($report['product_outward'])){
						$outward_data = explode('/',$report['product_outward']);
						$cash_received 	= ($outward_data[0] != '') ? $outward_data[0] : '0'; //credit
						$total_outward 	= ($outward_data[1] != '') ? $outward_data[1] : '0'; //debit
					}
					
					$product_liability = 0;
					$wages_liability = 0;
					if(!empty($report['cash_deposited'])){
						$cash_deposited = explode('/',$report['cash_deposited']);
						$product_liability 	= ($cash_deposited[0] != '') ? $cash_deposited[0] : '0'; //credit
						$wages_liability 	= ($cash_deposited[1] != '') ? $cash_deposited[1] : '0'; //credit
					}
					
					$cash_received 	= $cash_received + $report['returned_amt'] + $product_liability; //credit
					
					$GroupLiabilityDeduction = 0;
					$AdvanceWagesDeduction = 0;
					if(!empty($report['liability_deduction'])){
						$wages_data = explode('/',$report['liability_deduction']);
						$GroupLiabilityDeduction 	= ($wages_data[0] != '') ? $wages_data[0] : '0'; //credit
						$AdvanceWagesDeduction 		= ($wages_data[1] != '') ? $wages_data[1] : '0'; //credit
					}
					
					$AdvanceWagesDeduction 	= $AdvanceWagesDeduction + $wages_liability; //credit
					
					$total_debit = $total_outward + $report['advance_wages'] + $report['opening_balance'];
					//$total_credit = $report['total_amount'] + $cash_received + $GroupLiabilityDeduction + $AdvanceWagesDeduction;
					$total_credit = $cash_received + $GroupLiabilityDeduction + $AdvanceWagesDeduction;
					
					$net_pending = $total_debit - $total_credit;
															
					$tbody .= '<tr>';
					$tbody .= '	<td>'.$report['group_name'].'</td>';
					$tbody .= '	<td>'.$report['major_wt'].'</td>';
					$tbody .= '	<td>'.$report['minor_wt'].'</td>';
					$tbody .= '	<td>'.$report['sawal_wt'].'</td>';
					$tbody .= '	<td>'.$report['total_wt'].'</td>';
					$tbody .= '	<td>'.$report['total_amount'].'</td>';
					$tbody .= '	<td>'.$report['present_days'].'</td>';
					$tbody .= '	<td>'.$report['opening_balance'].'</td>';
					$tbody .= '	<td>'.$total_outward.'</td>';
					$tbody .= '	<td>'.$cash_received.'</td>';
					$tbody .= '	<td>'.$GroupLiabilityDeduction.'</td>';
					$tbody .= '	<td>'.$report['advance_wages'].'</td>';
					$tbody .= '	<td>'.$AdvanceWagesDeduction.'</td>';
					$tbody .= '	<td>'.($net_pending < 0 ? '<span class="text-danger">'.$net_pending.'</span>' : $net_pending).'</td>';
					$tbody .= '</tr>';
					
					$total_major_wt += $report['major_wt'];
					$total_minor_wt += $report['minor_wt'];
					$total_sawal_wt += $report['sawal_wt'];
					$total_total_wt += $report['total_wt'];
					
					$total_total_amount += $report['total_amount'];
					$total_present_days += $report['present_days'];
					$total_opening_bal += $report['opening_balance'];
					
					$tTotal_outward 		= $tTotal_outward + $total_outward;
					$total_cash_deposit 	= $total_cash_deposit + $cash_received;
					$tOutward_deduction		= $tOutward_deduction + $GroupLiabilityDeduction;
					$total_advance_wages 	= $total_advance_wages + $report['advance_wages'];
					$tAdvance_deduction 	= $tAdvance_deduction + $AdvanceWagesDeduction;
					
					$total_net_pending += $net_pending;
				}
				
				 $tfoot  = '<tr>';
            	 $tfoot .= ' <th>Summary :</th>';
            	 $tfoot .= ' <th>'.$total_major_wt.'</th>';
				 $tfoot .= ' <th>'.$total_minor_wt.'</th>';
				 $tfoot .= ' <th>'.$total_sawal_wt.'</th>';
				 $tfoot .= ' <th>'.$total_total_wt.'</th>';
				 $tfoot .= ' <th>'.$total_total_amount.'</th>';
				 $tfoot .= ' <th>'.$total_present_days.'</th>';
				 $tfoot .= ' <th>'.$total_opening_bal.'</th>';
				 $tfoot .= ' <th>'.$tTotal_outward.'</th>';
				 $tfoot .= ' <th>'.$total_cash_deposit.'</th>';
				 $tfoot .= ' <th>'.$tOutward_deduction.'</th>';
				 $tfoot .= ' <th>'.$total_advance_wages.'</th>';
				 $tfoot .= ' <th>'.$tAdvance_deduction.'</th>';
				 $tfoot .= ' <th>'.$total_net_pending.'</th>';
          		 $tfoot .= '</tr>';
				 
				 $tfoot .= $this->datatable_scripts;
				
			}
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
		}else{
			$data = array('status'=>'danger', 'message'=>validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	
	//Wages Half Year Report
	function half_yearly(){
		$data['content_view'] = 'reports/wages/report_wages_half_year_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Wages Half Yearly Report');
		$this->template->layout($data);
	}
	
	function ajax_half_yearly(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('ajax_half_yearly');
		if($form_validation){
			$from_date = get_date('Y-m-d', $this->input->post('from_date'));
			$to_date = get_date('Y-m-t', $this->input->post('to_date'));
			$group_type = $this->input->post('group_type');
			$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
			
			//Preparing Header Start
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1M');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("M-y");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			//Preparing Header End
			
			
			$tbody = '';
			$tfoot = '';
			$total_value = array();
			$report_data = $this->RM->get_wages_month_wise($from_date, $to_date, $group_type);
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){					
					$i++;					
					$samiti = explode('__', $key);
					$tbody .= '<tr>';
					$tbody .= '<td>'.$i.'</td>';
					$tbody .= '<td>'.@$samiti[1].'</td>';
					$total_wages = $govt_deduction = $net_amount = $adv_dec = $grp_ded = 0;
					$j=0;
					foreach($daterange as $date){
						$toll_date = $date->format("n");			
						if(array_key_exists($toll_date, $r_data)){											
							$report = $r_data[$toll_date];
							$report['Twt'] = ($report['Twt'] != '') ? $report['Twt'] : 0 ;
							
							$tbody .= '<td>'.$report['Twt'].'</td>';
							$total_wages += ($report['total_wages'] != '') ? $report['total_wages'] : 0 ;
							$govt_deduction += ($report['govt_deduction'] != '') ? $report['govt_deduction'] : 0 ;
							$net_amount += ($report['net_amount'] != '') ? $report['net_amount'] : 0 ;
							
							if(!empty($report['deposit_amt']) && $report['deposit_amt'] != ''){
								$deposit_amt = explode('|', $report['deposit_amt']);
								$grp_ded = ($deposit_amt[0] != '') ? $deposit_amt[0] : 0 ;
								$adv_dec = ($deposit_amt[1] != '') ? $deposit_amt[1] : 0 ;
							}
							
							$total_value[$j] = $report['Twt'] + (isset($total_value[$j]) ? $total_value[$j] : 0);
			
													
						}else{
							$tbody .= '	<td>0</td>';
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						
						$j++;
					}
					
					$total_value['tw'] 	= $total_wages + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  = $govt_deduction + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] 		= $grp_ded + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']			= $adv_dec + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $net_amount + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$tbody .= '<td>'.$total_wages.'</td>';
					$tbody .= '<td>'.$govt_deduction.'</td>';
					$tbody .= '<td>'.$grp_ded.'</td>';
					$tbody .= '<td>'.$adv_dec.'</td>';
					$tbody .= '<td>'.($net_amount-($grp_ded+$adv_dec)).'</td>';
					
					$tbody .= '</tr>';
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="2">Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '</tr>';
				$tfoot .= $this->datatable_scripts;
				
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Wages Yearly Report
	function yearly(){
		$data['content_view'] = 'reports/wages/report_wages_yearly_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Wages Yearly Report');
		$this->template->layout($data);
	}
	
	function ajax_yearly(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('ajax_yearly');
		if($form_validation){
			
			$from_date = date('Y-01-01', strtotime($this->input->post('date')));
			$to_date = date('Y-12-31', strtotime($this->input->post('date')));
			$group_type = $this->input->post('group_type');
			$search_date = 'Year - '.$this->input->post('date');
			
			//Preparing Header Start
			$all_date = '';
			$begin = new DateTime( $from_date );
			$end = new DateTime( $to_date );
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1M');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			$colspan = 0;			
			foreach($daterange as $date){
				$toll_date = $date->format("M-y");
				$all_date .= '<th>'.$toll_date.'</th>';
				$colspan++;
			}
			//Preparing Header End
			
			
			$tbody = '';
			$tfoot = '';
			$total_value = array();
			$report_data = $this->RM->get_wages_month_wise($from_date, $to_date, $group_type);
			if(!empty($report_data)){
				$i=0;
				foreach($report_data as $key => $r_data){
					$i++;			
					$samiti = explode('__', $key);
					$tbody .= '<tr>';
					$tbody .= '<td>'.$i.'</td>';
					$tbody .= '<td>'.@$samiti[1].'</td>';
					$total_wages = $govt_deduction = $net_amount = $adv_dec = $grp_ded = 0;
					$j=0;
					foreach($daterange as $date){
						$toll_date = $date->format("n");			
						if(array_key_exists($toll_date, $r_data)){											
							$report = $r_data[$toll_date];
							$report['Twt'] = ($report['Twt'] != '') ? $report['Twt'] : 0 ;
							
							$tbody .= '<td>'.$report['Twt'].'</td>';
							$total_wages += ($report['total_wages'] != '') ? $report['total_wages'] : 0 ;
							$govt_deduction += ($report['govt_deduction'] != '') ? $report['govt_deduction'] : 0 ;
							$net_amount += ($report['net_amount'] != '') ? $report['net_amount'] : 0 ;
							
							if(!empty($report['deposit_amt']) && $report['deposit_amt'] != ''){
								$deposit_amt = explode('|', $report['deposit_amt']);
								$grp_ded = ($deposit_amt[0] != '') ? $deposit_amt[0] : 0 ;
								$adv_dec = ($deposit_amt[1] != '') ? $deposit_amt[1] : 0 ;
							}
							$total_value[$j] = $report['Twt'] + (isset($total_value[$j]) ? $total_value[$j] : 0);
						}else{
							$tbody .= '	<td>0</td>';
							$total_value[$j] = (isset($total_value[$j]) ? $total_value[$j] : 0);
						}
						$j++;
					}
					
					$total_value['tw'] 	= $total_wages + (isset($total_value['tw']) ? $total_value['tw'] : 0);
					$total_value['gd']  = $govt_deduction + (isset($total_value['gd']) ? $total_value['gd'] : 0);					
					$total_value['grp_ded'] 		= $grp_ded + (isset($total_value['grp_ded']) ? $total_value['grp_ded'] : 0);
					$total_value['adv_dec']			= $adv_dec + (isset($total_value['adv_dec']) ? $total_value['adv_dec'] : 0);
					$total_value['na'] 		= $net_amount + (isset($total_value['na']) ? $total_value['na'] : 0);
					
					$tbody .= '<td>'.$total_wages.'</td>';
					$tbody .= '<td>'.$govt_deduction.'</td>';
					$tbody .= '<td>'.$grp_ded.'</td>';
					$tbody .= '<td>'.$adv_dec.'</td>';
					$tbody .= '<td>'.($net_amount-($grp_ded+$adv_dec)).'</td>';
					
					$tbody .= '</tr>';					
				}
				
				$summary = array_values($total_value);
				$tfoot = '<tr><th colspan="2">Summary </th>';
				foreach($summary as $value){
					$tfoot .= '<th>'.$value.'</th>'; 
				}
				$tfoot .= '</tr>';
				$tfoot .= $this->datatable_scripts;
			}
			
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$all_date, 'search_date'=>$search_date, 'colspan'=>$colspan);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'samiti':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				//$this->form_validation->set_rules('maingroup', 'Maingroup', 'trim|required|min_length[1]');
			break;
			
			case 'weekly':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));			
			break;
			
			case 'ajax_samiti_account':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));			
			break;
			
			case 'ajax_account':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				//$this->form_validation->set_rules('maingroup', 'Main Group', 'trim|required|min_length[1]');
			break;
			
			case 'ajax_monthly':
				$this->form_validation->set_rules('month', 'Month', 'trim|required|min_length[1]');
			break;
			
			case 'ajax_quarterly':
				$this->form_validation->set_rules('from_date', 'From Month', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Month', 'trim|required|min_length[1]|callback_validate_date2', array('validate_date2' => 'To Month must be greater than From Month.'));			
			break;
			
			case 'ajax_half_yearly':
				$this->form_validation->set_rules('from_date', 'From Month', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Month', 'trim|required|min_length[1]|callback_validate_date2', array('validate_date2' => 'To Month must be greater than From Month.'));			
			break;
			
			case 'ajax_yearly':
				$this->form_validation->set_rules('date', 'Year', 'trim|required|min_length[1]');
			break;
			
			case 'overall_fisherman':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				$this->form_validation->set_rules('maingroup', 'Maingroup', 'trim|required|min_length[1]');
			break;
			
			case 'overall_samiti':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
				$this->form_validation->set_rules('maingroup[]', 'Maingroup', 'trim|required|min_length[1]');
			break;
			
			
		}
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert_msg margin-5 padding-5"><button data-dismiss="alert" class="close">×</button><i class="fa fa-times-circle"></i> ', '</div>');
		return $this->form_validation->run($this);
	}
	
	//Form rule validation callback functions
	//For full dates
	function validate_date(){
		$data = FALSE;
		$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
		$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
		if(strtotime($to_date) >= strtotime($from_date)){
			$data = TRUE;
		}
		return $data;
	}
	
	//Form rule validation callback functions
	//For months
	function validate_date2(){
		$data = FALSE;
		$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
		$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
		if(strtotime($to_date) > strtotime($from_date)){
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
	
	//ajax onchange of group type
	function ajax_get_maingroup(){
		$data = array('status'=>'danger', 'msg'=>'Maingroup data not found', 'data'=>'<option value="">No group found</option>');
		$mg_type = $this->input->post('mg_type');
		if(!empty($mg_type)){
			$result = $this->RM->get_all_maingroups($mg_type);
			$maingroups = '<option value="0" selected="selected">All</option>';
			if(!empty($result)){
				foreach($result as $res){
					$maingroups .= '<option value="'.$res['ID'].'">'.$res['Name'].'</option>';
				}
				$data = array('status'=>'success', 'msg'=>'Maingroup data.', 'data'=>$maingroups);
			}
		}		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
}

