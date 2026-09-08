<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Outward extends MY_Controller {
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
		redirect(site_url('reports/outward/samiti'));
	}
	
	//1. Outward Samiti Report (get_outward_samiti)
	function samiti(){
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['content_view'] = 'reports/outward/report_outward_samiti_v';
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		$this->template->set('document_title', 'Outward Samiti Report');
		$this->template->layout($data);
	}
	
	function ajax_samiti(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('outward_samiti');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			
			$report_data = $this->RM->get_outward_samiti($from_date, $to_date, $group_type);
			
			if(!empty($report_data)){
				$tbody = '';
				$search_date = $this->input->post('from_date').' To '.$this->input->post('to_date');
				$tOpBal = $tTotal = $tCashReceived = $tGroup = $tAdvance_wages = $tAdvance = $tPending = 0.00;
				
				foreach($report_data as $data){
					//Calculating Row Data
					$Name 			= $data['Name'];
					$cash_received 	= 0.00;
					$total_outward 	= 0.00;
					
					if(!empty($data['product_outward'])){
						$outward_data = explode('/',$data['product_outward']);
						$cash_received 	= ($outward_data[0] != '') ? $outward_data[0] : '0'; //credit
						$total_outward 	= ($outward_data[1] != '') ? $outward_data[1] : '0'; //debit
					}
					
					$cash_outward_deposit = 0.00;
					$cash_wages_deposit = 0.00;
					if(!empty($data['cash_deposited'])){
						$cash_deposited = explode('/',$data['cash_deposited']);
						$cash_outward_deposit 	= ($cash_deposited[0] != '') ? $cash_deposited[0] : '0'; //credit
						$cash_wages_deposit 	= ($cash_deposited[1] != '') ? $cash_deposited[1] : '0'; //credit
					}
					
					$cash_received 	= $cash_received + $data['returned_amt'] + $cash_outward_deposit; //credit
					
					$GroupLiabilityDeduction = 0.00;
					$AdvanceWagesDeduction = 0.00;
					if(!empty($data['liability_deduction'])){
						$wages_data = explode('/',$data['liability_deduction']);
						$GroupLiabilityDeduction 	= ($wages_data[0] != '') ? $wages_data[0] : '0'; //credit
						$AdvanceWagesDeduction 		= ($wages_data[1] != '') ? $wages_data[1] : '0'; //credit
					}
					
					$AdvanceWagesDeduction 	= $AdvanceWagesDeduction + $cash_wages_deposit; //credit
					
					$advance_wages = ($data['advance_wages'] != '') ? $data['advance_wages'] : '0'; //debit
					
					$total_debited = $data['opening_balance'] + $total_outward + $advance_wages;
					$total_credited = $cash_received + $GroupLiabilityDeduction + $AdvanceWagesDeduction;
					
					//$Pending = ($total_outward - ($cash_received + $GroupLiabilityDeduction)) + ($advance_wages - $AdvanceWagesDeduction);
					$Pending = $total_debited - $total_credited;
					
					
					//$product_outward = 
					$tbody .= '<tr>';
					$tbody .= '	<td>'.$Name.'</td>';
					$tbody .= '    <td>'.$data['opening_balance'].'</td>';
					$tbody .= '    <td>'.$total_outward.'</td>';
					$tbody .= '    <td>'.$cash_received.'</td>';					
					$tbody .= '    <td>'.$GroupLiabilityDeduction.'</td>';
					$tbody .= '    <td>'.$advance_wages.'</td>';
					$tbody .= '    <td>'.$AdvanceWagesDeduction.'</td>';
					$tbody .= '    <td>'.$Pending.'</td>';
					$tbody .= '</tr>';
					
					
					//Calculating column data
					$tOpBal			= $tOpBal + $data['opening_balance'];
					$tTotal 		= $tTotal + $total_outward;
					$tCashReceived 	= $tCashReceived + $cash_received;
					$tGroup			= $tGroup + $GroupLiabilityDeduction;
					$tAdvance_wages	= $tAdvance_wages + $advance_wages;
					$tAdvance 		= $tAdvance + $AdvanceWagesDeduction;
					$tPending 		= $tPending + $Pending;
					
				}
			
				$tfoot = '<tr>';
				$tfoot .= '    <th>Total Summary </th>';
				$tfoot .= '    <th>'.$tOpBal.'</th>';
				$tfoot .= '    <th>'.$tTotal.'</th>';
				$tfoot .= '    <th>'.$tCashReceived.'</th>';
				$tfoot .= '    <th>'.$tGroup.'</th>';
				$tfoot .= '    <th>'.$tAdvance_wages.'</th>';
				$tfoot .= '    <th>'.$tAdvance.'</th>';
				$tfoot .= '    <th>'.$tPending.'</th>';
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
	
	//2. Outward Fisherman Report (get_outward_fisherman)
	function fisherman(){
		$data['page_title'] = 'Outward Fisherman Report';
		$data['ajax_url'] = 'reports/outward/ajax_fisherman';		
		
		$data['content_view'] = 'reports/outward/report_outward_fisherman_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		//$data['all_maingroup'] = $this->RM->get_all_maingroups();
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Outward Fisherman Report');
		$this->template->layout($data);
	}
	
	function ajax_fisherman_OLD(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('outward_fisherman');
		if($form_validation){
			$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			$report_data = $this->RM->get_outward_fisherman($from_date, $to_date, $maingroup, $group_type);
			
			if(!empty($report_data)){
				$tbody = '';
				$t_nav_qty = $t_jaal_qty = $t_outward_amt = $t_cash_paid = $t_adjustment = $t_AdvanceWagesDeduction =  $t_advance_wages =  $t_pending =  0;
				$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
				$search_key = 'All';
				if($maingroup){
					$search_key = $report_data[0]['Samiti_name'];
				}
				
				foreach($report_data as $report){
					$nav_qty = ($report['nav_qty'] != '') ? $report['nav_qty'] : 0.00;
					$jaal_qty = ($report['jaal_qty'] != '') ? $report['jaal_qty'] : 0.00;
					$adjustment = ($report['adjustment'] != '') ? $report['adjustment'] : 0.00;
					$advance_wages = ($report['advance_wages'] != '') ? $report['advance_wages'] : 0.00;
					$AdvanceWagesDeduction = ($report['AdvanceWagesDeduction'] != '') ? $report['AdvanceWagesDeduction'] : 0.00;
					
					$outward_amt = $cash_paid = 0.00;
					if(!empty($report['product_outward'])){
						$product_outward = explode('|', $report['product_outward']);
						$cash_paid = ($product_outward[0] != '') ? $product_outward[0] : 0.00;
						$outward_amt = ($product_outward[1] != '') ? $product_outward[1] : 0.00;
					}
					$pending_amount = (($outward_amt-$cash_paid) - $adjustment) + ($advance_wages - $AdvanceWagesDeduction);
					
					
					$tbody .='<tr>';
					$tbody .='    <td>'.$report['Name'].'</td>';
					$tbody .='    <td>'.$report['secondary_fisherman'].'</td>';
					$tbody .='    <td>'.$jaal_qty.'</td>';
					$tbody .='    <td>'.$nav_qty.'</td>';
					$tbody .='    <td>'.$outward_amt.'</td>';
					$tbody .='    <td>'.$cash_paid.'</td>';
					$tbody .='    <td>'.$adjustment.'</td>';
					$tbody .='    <td>'.$advance_wages.'</td>';
					$tbody .='    <td>'.$AdvanceWagesDeduction.'</td>';
					
					$tbody .='    <td>'.$pending_amount.'</td>';
					$tbody .='</tr>';
					
					$t_nav_qty 					+= $report['nav_qty'];
					$t_jaal_qty 				+= $report['jaal_qty'];
					$t_outward_amt 				+= $outward_amt;
					$t_cash_paid 				+= $cash_paid;
					$t_adjustment 				+= $adjustment;
					$t_advance_wages 			+= $advance_wages;
					$t_AdvanceWagesDeduction 	+= $AdvanceWagesDeduction;
					$t_pending 					+= $pending_amount;
				}
				
				$tfoot ='<tr><th colspan="2">Total :</th>';
				$tfoot .='<th>'.number_format($t_jaal_qty, 2).'</th>';
				$tfoot .='<th>'.number_format($t_nav_qty, 2).'</th>';
				$tfoot .='<th>'.number_format($t_outward_amt, 2).'</th>';
				$tfoot .='<th>'.number_format($t_cash_paid, 2).'</th>';
				$tfoot .='<th>'.number_format($t_adjustment, 2).'</th>';
				$tfoot .='<th>'.number_format($t_advance_wages, 2).'</th>';
				$tfoot .='<th>'.number_format($t_AdvanceWagesDeduction, 2).'</th>';
				$tfoot .='<th>'.number_format($t_pending, 2).'</th></tr>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
				
			}
			
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($data));
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function ajax_fisherman_1_12_2018(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('outward_fisherman');
		if($form_validation){
			$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			$report_data = $this->RM->get_outward_fisherman($from_date, $to_date, $maingroup, $group_type);
			
			if(!empty($report_data)){
				$tbody = '';
				$t_nav_qty = $t_jaal_qty = $t_outward_amt = $t_cash_paid = $t_adjustment = $t_AdvanceWagesDeduction =  $t_advance_wages =  $t_pending =  0;
				$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
				$search_key = 'All';
				if($maingroup){
					$search_key = $report_data[0]['Samiti_name'];
				}
				
				foreach($report_data as $report){
					$nav_qty = ($report['nav_qty'] != '') ? $report['nav_qty'] : 0.00;
					$jaal_qty = ($report['jaal_qty'] != '') ? $report['jaal_qty'] : 0.00;
					
					//1.
					$cash_paid = $outward_amt = 0.00;
					if(!empty($report['product_outward'])){
						$product_outward = explode('|', $report['product_outward']);
						$cash_paid = ($product_outward[0] != '') ? $product_outward[0] : 0.00; //credit
						$outward_amt = ($product_outward[1] != '') ? $product_outward[1] : 0.00; //debit
					}
					
					//2.
					$returned_amt = ($report['returned_amt'] != '') ? $report['returned_amt'] : 0.00;  //credit
					
					//3.
					$advance_wages = ($report['advance_wages'] != '') ? $report['advance_wages'] : 0.00; //debit
					
					//4.
					$adjustment = ($report['group_liability_deduction'] != '') ? $report['group_liability_deduction'] : 0.00; //credit
					
					//5.
					$AdvanceWagesDeduction = ($report['AdvanceWagesDeduction'] != '') ? $report['AdvanceWagesDeduction'] : 0.00; //credit
					
					//6.
					$product_liability_dep = $wages_liability_dep = 0.00;
					if(!empty($report['cash_deposited'])){
						$cash_deposited = explode('|', $report['cash_deposited']);
						$product_liability_dep = ($cash_deposited[0] != '') ? $cash_deposited[0] : 0.00;  //credit
						$wages_liability_dep = ($cash_deposited[1] != '') ? $cash_deposited[1] : 0.00;  //credit
					}
					
					$total_debit = $outward_amt + $advance_wages;
					$total_credit = $cash_paid + $returned_amt + $adjustment + $AdvanceWagesDeduction + $product_liability_dep + $wages_liability_dep;
					
					//$pending_amount = (($outward_amt-$cash_paid) - $adjustment) + ($advance_wages - $AdvanceWagesDeduction);
					$pending_amount = $total_debit - $total_credit;
					
					$tbody .='<tr>';
					$tbody .='    <td>'.$report['Name'].'</td>';
					$tbody .='    <td>'.$report['secondary_fisherman'].'</td>';
					$tbody .='    <td>'.$jaal_qty.'</td>';
					$tbody .='    <td>'.$nav_qty.'</td>';
					$tbody .='    <td>'.$outward_amt.'</td>';
					$tbody .='    <td>'.$cash_paid.'</td>';
					$tbody .='    <td>'.$adjustment.'</td>';
					$tbody .='    <td>'.$advance_wages.'</td>';
					$tbody .='    <td>'.$AdvanceWagesDeduction.'</td>';
					
					$tbody .='    <td>'.$pending_amount.'</td>';
					$tbody .='</tr>';
					
					$t_nav_qty 					+= $report['nav_qty'];
					$t_jaal_qty 				+= $report['jaal_qty'];
					$t_outward_amt 				+= $outward_amt;
					$t_cash_paid 				+= $cash_paid;
					$t_adjustment 				+= $adjustment;
					$t_advance_wages 			+= $advance_wages;
					$t_AdvanceWagesDeduction 	+= $AdvanceWagesDeduction;
					$t_pending 					+= $pending_amount;
				}
				
				$tfoot ='<tr><th colspan="2">Total :</th>';
				$tfoot .='<th>'.number_format($t_jaal_qty, 2).'</th>';
				$tfoot .='<th>'.number_format($t_nav_qty, 2).'</th>';
				$tfoot .='<th>'.number_format($t_outward_amt, 2).'</th>';
				$tfoot .='<th>'.number_format($t_cash_paid, 2).'</th>';
				$tfoot .='<th>'.number_format($t_adjustment, 2).'</th>';
				$tfoot .='<th>'.number_format($t_advance_wages, 2).'</th>';
				$tfoot .='<th>'.number_format($t_AdvanceWagesDeduction, 2).'</th>';
				$tfoot .='<th>'.number_format($t_pending, 2).'</th></tr>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
				
			}
			
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($data));
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	
	function ajax_fisherman(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('outward_fisherman');
		if($form_validation){
			$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			$maingroup = $this->input->post('maingroup');
			$report_data = $this->RM->get_outward_fisherman($from_date, $to_date, $maingroup, $group_type);
			if(!empty($report_data)){
				$tbody = '';
				$t_opening_balance = $t_nav_qty = $t_jaal_qty = $t_outward_amt = $t_cash_paid = $t_adjustment = $t_AdvanceWagesDeduction =  $t_advance_wages =  $t_pending =  0;
				$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
				$search_key = 'All';
				if($maingroup){
					$search_key = $report_data[0]['Samiti_name'];
				}
				
				foreach($report_data as $report){
					$nav_qty = ($report['nav_qty'] != '') ? $report['nav_qty'] : 0.00;
					$jaal_qty = ($report['jaal_qty'] != '') ? $report['jaal_qty'] : 0.00;
										
					$opening_balance = ($report['opening_balance'] != '') ? $report['opening_balance'] : 0.00;  //debit
					$advance_wages = ($report['advance_wages'] != '') ? $report['advance_wages'] : 0.00;  //debit
					$returned_amt = ($report['returned_amt'] != '') ? $report['returned_amt'] : 0.00;  //credit
										
					$outward_amt = $cash_paid = 0.00;
					if(!empty($report['product_outward'])){
						$product_outward = explode('|', $report['product_outward']);
						$cash_paid = ($product_outward[0] != '') ? $product_outward[0] : 0.00;  //credit
						$outward_amt = ($product_outward[1] != '') ? $product_outward[1] : 0.00;  //debit
					}
					
					$product_liability_dep = $wages_liability_dep = 0.00;
					if(!empty($report['cash_deposited'])){
						$cash_deposited = explode('|', $report['cash_deposited']);
						$product_liability_dep = ($cash_deposited[0] != '') ? $cash_deposited[0] : 0.00;  //credit
						$wages_liability_dep = ($cash_deposited[1] != '') ? $cash_deposited[1] : 0.00;  //credit
					}
					
					$GroupLiabilityDeduction = $AdvanceWagesDeduction = 0.00;
					if(!empty($report['wages_deduction'])){
						$wages_deduction = explode('|', $report['wages_deduction']);						
						//$GroupLiabilityDeduction = ($wages_deduction[0] != '') ? $wages_deduction[0] : 0.00;  //credit
						$AdvanceWagesDeduction = ($wages_deduction[1] != '') ? $wages_deduction[1] : 0.00;  //debit
					}
					
					$GroupLiabilityDeduction = is_null($report['group_liability_deduction']) ? 0 : $report['group_liability_deduction'];
					
					$total_cash_paid = $cash_paid + $product_liability_dep + $wages_liability_dep;
					
					$debited_amount = $advance_wages + $outward_amt + $opening_balance;
					$credited_amount = $GroupLiabilityDeduction + $AdvanceWagesDeduction + $returned_amt + $cash_paid + $product_liability_dep + $wages_liability_dep;
					
					$pending_amount = $debited_amount - $credited_amount;
					
					
					$tbody .='<tr>';
					$tbody .='    <td>'.$report['Name'].'</td>';
					$tbody .='    <td>'.$report['secondary_fisherman'].'</td>';
					$tbody .='    <td>'.$opening_balance.'</td>';
					$tbody .='    <td>'.$jaal_qty.'</td>';
					$tbody .='    <td>'.$nav_qty.'</td>';
					$tbody .='    <td>'.$outward_amt.'</td>';
					$tbody .='    <td>'.$total_cash_paid.'</td>';
					$tbody .='    <td>'.$GroupLiabilityDeduction.'</td>';
					$tbody .='    <td>'.$advance_wages.'</td>';
					$tbody .='    <td>'.$AdvanceWagesDeduction.'</td>';
					
					$tbody .='    <td>'.$pending_amount.'</td>';
					$tbody .='</tr>';
					
					$t_nav_qty 					+= $report['nav_qty'];
					$t_jaal_qty 				+= $report['jaal_qty'];
					$t_opening_balance 			+= $opening_balance;
					$t_outward_amt 				+= $outward_amt;
					$t_cash_paid 				+= $total_cash_paid;
					$t_adjustment 				+= $GroupLiabilityDeduction;
					$t_advance_wages 			+= $advance_wages;
					$t_AdvanceWagesDeduction 	+= $AdvanceWagesDeduction;
					$t_pending 					+= $pending_amount;
				}
				
				$tfoot ='<tr>';
				$tfoot .='<th colspan="2">Summary :</th>';
				$tfoot .='<th>'.$t_opening_balance.'</th>';
				$tfoot .='<th>'.$t_jaal_qty.'</th>';
				$tfoot .='<th>'.$t_nav_qty.'</th>';
				$tfoot .='<th>'.$t_outward_amt.'</th>';
				$tfoot .='<th>'.$t_cash_paid.'</th>';
				$tfoot .='<th>'.$t_adjustment.'</th>';
				$tfoot .='<th>'.$t_advance_wages.'</th>';
				$tfoot .='<th>'.$t_AdvanceWagesDeduction.'</th>';
				$tfoot .='<th>'.$t_pending.'</th>';
				$tfoot .='</tr>';
				
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
				
			}
			
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($data));
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	
	//Outward Samiti Report
	function samiti_fisherman(){
		$data['page_title'] = 'Outward Samiti Fisherman Report';
		$data['ajax_url'] = 'reports/outward/ajax_samiti_fisherman';
		$data['content_view'] = 'reports/outward/report_outward_samiti_fisherman_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function ajax_samiti_fisherman(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('outward_samiti');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->input->post('group_type');
			
			$report_data = $this->RM->get_outward_samiti_fisherman($from_date, $to_date, $group_type);
			
			if(!empty($report_data)){
				$tbody = '';
				$search_date = $this->input->post('from_date').' To '.$this->input->post('to_date');
				$tOpBal = $tTotal = $tCashReceived = $tGroup = $tAdvance_wages = $tAdvance = $tPending = 0.00;
				
				foreach($report_data as $data){
					//Calculating Row Data
					$Name 			= $data['Name'];					
					$returned_amt = $data['returned_amt']; //credit
					$advance_wages = ($data['advance_wages'] != '') ? $data['advance_wages'] : '0'; //debit
					$opening_balance = $data['opening_balance']; //debit
					
					$cash_received 	= $total_outward 	= 0.00;
					if(!empty($data['product_outward'])){
						$outward_data = explode('/',$data['product_outward']);
						$cash_received 	= ($outward_data[0] != '') ? $outward_data[0] : '0'; //credit
						$total_outward 	= ($outward_data[1] != '') ? $outward_data[1] : '0'; //debit
					}
					
					$product_liability = $wages_liability = 0.00;
					if(!empty($data['cash_deposited'])){
						$cash_deposited = explode('/',$data['cash_deposited']);
						$product_liability 	= ($cash_deposited[0] != '') ? $cash_deposited[0] : '0'; //credit
						$wages_liability 	= ($cash_deposited[1] != '') ? $cash_deposited[1] : '0'; //credit
					}
					
					$GroupLiabilityDeduction = $AdvanceWagesDeduction = 0.00;
					if(!empty($data['wages_deduction'])){
						$wages_deduction = explode('|', $data['wages_deduction']);						
						//$GroupLiabilityDeduction = ($wages_deduction[0] != '') ? $wages_deduction[0] : 0.00;  //credit
						$AdvanceWagesDeduction = ($wages_deduction[1] != '') ? $wages_deduction[1] : 0.00;  //debit
					}
					
					$GroupLiabilityDeduction = is_null($data['group_liability_deduction']) ? 0 : $data['group_liability_deduction'];
					
					$debited_amount = $total_outward + $advance_wages + $opening_balance;
					$credited_amount = $cash_received + $returned_amt + $AdvanceWagesDeduction + $GroupLiabilityDeduction + $product_liability + $wages_liability;
					
					$total_cash_rec = $cash_received + $data['returned_amt'] + $product_liability;
					//$cash_received 	= $cash_received + $data['returned_amt'] + $product_liability; //credit
					$pending_amount = $debited_amount - $credited_amount;
					
					//Calculating column data
					$tOpBal			+= $data['opening_balance'];
					$tTotal 		+= $total_outward;
					$tCashReceived 	+= $total_cash_rec;
					$tGroup			+= $GroupLiabilityDeduction;
					$tAdvance_wages	+= $advance_wages;
					$tAdvance 		+= $AdvanceWagesDeduction;
					$tPending 		+= $pending_amount;
					
					//$product_outward = 
				
					$tbody .= '<tr>';
					$tbody .= '	<td>'.$Name.'</td>';
					$tbody .= '    <td>'.$opening_balance.'</td>';
					$tbody .= '    <td>'.$total_outward.'</td>';
					$tbody .= '    <td>'.$total_cash_rec.'</td>';					
					$tbody .= '    <td>'.$GroupLiabilityDeduction.'</td>';
					$tbody .= '    <td>'.$advance_wages.'</td>';
					$tbody .= '    <td>'.$AdvanceWagesDeduction.'</td>';
					$tbody .= '    <td>'.$pending_amount.'</td>';
					$tbody .= '</tr>';
				}
			
				$tfoot = '<tr>';
				$tfoot .= '    <th>Total Summary </th>';
				$tfoot .= '    <th>'.$tOpBal.'</th>';
				$tfoot .= '    <th>'.$tTotal.'</th>';
				$tfoot .= '    <th>'.$tCashReceived.'</th>';
				$tfoot .= '    <th>'.$tGroup.'</th>';
				$tfoot .= '    <th>'.$tAdvance_wages.'</th>';
				$tfoot .= '    <th>'.$tAdvance.'</th>';
				$tfoot .= '    <th>'.$tPending.'</th>';
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
	
	
	//3.Ledger Outward Fisherman Report (get_ledger_outward_fisherman_report)
	function ledger_outward_fisherman(){
		$data['content_view'] = 'reports/outward/report_ledger_outward_fisherman_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['page_title'] = 'Ledger Outward Fisherman Report';
		$data['ajax_url'] = 'reports/outward/ajax_ledger_outward_fisherman';
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function ajax_ledger_outward_fisherman(){
		$data = array('status' => 'danger', 
					  'message' => '<div class="alert alert-danger">
					  				<button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 
					  'data'=>'');
		$form_validation = $this->__setFormRules('ledger_outward_fisherman');
		if($form_validation){
			$from_date 		= $this->input->post('from_date');
			$to_date 		= $this->input->post('to_date');
			$group_type 	= $this->input->post('group_type');
			$maingroup 		= $this->input->post('maingroup');
			$fisherman_id 	= $this->input->post('fisherman_id');
			$maingroup_name = $this->input->post('maingroup_name');
			
			$report_data = $this->RM->get_ledger_outward_fisherman_report($from_date, $to_date, $group_type, $maingroup, $fisherman_id);
			
			$search_date = '';
			if(!empty($from_date) && !empty($to_date)){
				$search_date = $from_date.' To '.$to_date;
			}else if(!empty($from_date) && empty($to_date)){
				$search_date = $from_date.' To '.get_date('d/m/Y');
			}else if(empty($from_date) && empty($to_date)){
				$search_date = 'Till '.get_date('d/m/Y');
			}
			$search_key = $maingroup_name;
			$tbody = '';
			$tfoot = '';
			if(!empty($report_data)){
				$grand_total = 0;			
				foreach($report_data as $report){
					$tbody .='<tr>';
					$tbody .='    <td>'.$report['Code'].'</td>';
					$tbody .='    <td>'.$report['fisherman'].'</td>';
					$tbody .='    <td>'.$report['maingroup'].'</td>';
					$tbody .='    <td>'.$report['receipt_number'].'</td>';
					$tbody .='    <td>'.$report['outward_date'].'</td>';
					$tbody .='    <td>'.$report['product_type'].'</td>';
					$tbody .='    <td>'.$report['product'].'</td>';
					$tbody .='    <td>'.$report['quantity'].'</td>';
					$tbody .='    <td>'.$report['rate'].'</td>';
					$tbody .='    <td>'.$report['returnable'].'</td>';
					$tbody .='    <td>'.$report['total_price'].'</td>';
					$tbody .='</tr>';
					
					$grand_total += $report['total_price'];
				}
				
				$tfoot .='<tr><th colspan="10">Total :</th>';
				$tfoot .='<th>'.number_format($grand_total,2,'.','').'</th></tr>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			}else{
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'danger', 
							  'message' => '<div class="alert alert-danger">
											<button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 
							  'data'=>$html);
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//3.Ledger Outward Fisherman Report (get_ledger_outward_samiti_report)
	function ledger_outward_samiti(){
		$data['content_view'] = 'reports/outward/report_ledger_outward_samiti_v';
		$data['group_type'] = $this->RM->get_all_group_type();
		$data['page_title'] = 'Ledger Outward Samiti Report';
		$data['ajax_url'] = 'reports/outward/ajax_ledger_outward_samiti';
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', $data['page_title']);
		$this->template->layout($data);
	}
	
	function ajax_ledger_outward_samiti(){
		$data = array('status' => 'danger', 
					  'message' => '<div class="alert alert-danger">
					  				<button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 
					  'data'=>'');
		$form_validation = $this->__setFormRules('ledger_outward_samiti');
		if($form_validation){
			$from_date 		= $this->input->post('from_date');
			$to_date 		= $this->input->post('to_date');
			$group_type 	= $this->input->post('group_type');
			$maingroup 		= $this->input->post('maingroup');
			
			$maingroup_name 	= $this->input->post('maingroup_name');
			
			$report_data = $this->RM->get_ledger_outward_samiti_report($from_date, $to_date, $group_type, $maingroup);
			$tbody = '';
			$tfoot = '';
			$search_key = '';
			$search_date = '';
			if(!empty($report_data)){
				$tbody = '';
			
				if(!empty($from_date) && !empty($to_date)){
					$search_date = $from_date.' To '.$to_date;
				}else if(!empty($from_date) && empty($to_date)){
					$search_date = $from_date.' To '.get_date('d/m/Y');
				}else if(empty($from_date) && empty($to_date)){
					$search_date = 'Till '.get_date('d/m/Y');
				}
				$search_key = $maingroup_name;
				$grand_total = 0;			
				foreach($report_data as $report){
					$tbody .='<tr>';
					$tbody .='    <td>'.$report['maingroup'].'</td>';
					$tbody .='    <td>'.$report['receipt_number'].'</td>';
					$tbody .='    <td>'.$report['outward_date'].'</td>';
					$tbody .='    <td>'.$report['product_type'].'</td>';
					$tbody .='    <td>'.$report['product'].'</td>';
					$tbody .='    <td>'.$report['quantity'].'</td>';
					$tbody .='    <td>'.$report['rate'].'</td>';
					$tbody .='    <td>'.$report['returnable'].'</td>';
					$tbody .='    <td>'.$report['total_price'].'</td>';
					$tbody .='</tr>';
					
					$grand_total += $report['total_price'];
				}
				
				$tfoot .='<tr><th class="text-right" colspan="8">Total :</th>';
				$tfoot .='<th>'.number_format($grand_total,2,'.','').'</th></tr>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			}else{
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>$search_key, 'search_date'=>$search_date);
				$data = array('status' => 'danger', 
							  'message' => '<div class="alert alert-danger">
											<button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 
							  'data'=>$html);
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Common Functions
	//Ajax get maingroup
	function ajax_get_maingroup(){
		$data = array('status'=>'danger', 'msg'=>'Maingroup data not found', 'data'=>'<option value="">No group found</option>');
		$mg_type = $this->input->post('mg_type');
		if(!empty($mg_type)){
			$result = $this->RM->get_all_maingroups($mg_type);
			$maingroups = '<option value="0">All</option>';
			if(!empty($result)){
				foreach($result as $res){
					$maingroups .= '<option value="'.$res['ID'].'">'.$res['Name'].'</option>>';
				}
				$data = array('status'=>'success', 'msg'=>'Maingroup data.', 'data'=>$maingroups);
			}
		}		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//Ajax get maingroup fishermans
	function ajax_get_fishers(){
		$data = array('status'=>'danger', 'msg'=>'Fisherman data not found', 'data'=>'<option value="">No fisherman found</option>');
		$maingroup = $this->input->post('maingroup');
		$f_ids = $this->input->post('f_ids');
		if(!empty($maingroup)){
			$result = $this->RM->get_mg_fisherman($maingroup, $f_ids);
			$op_html = '<option value="0">All</option>';
			if(!empty($result['result1'])){
				foreach($result['result1'] as $res){
					$op_html .= '<option value="'.$res['id'].'">'.$res['text'].'</option>>';
				}
				$data = array('status'=>'success', 'msg'=>'Fishers data.', 'data1'=>$op_html, 'data2'=>$result['result2']);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'outward_samiti':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]');
			break;
			
			case 'outward_fisherman':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]');
				//$this->form_validation->set_rules('maingroup', 'Maingroup', 'trim|required|min_length[1]');
			break;
			
			case 'ledger_outward_fisherman':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim');
			break;
			
			case 'ledger_outward_samiti':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim');
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
		}else{
			$this->form_validation->set_message('validate_date', 'To Date must be  greater than or equal to From Date.');
		}
		return $data;
	}
}
