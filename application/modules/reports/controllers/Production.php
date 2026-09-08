<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Production extends MY_Controller {
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
		redirect(site_url('reports/production/daily'));
	}
	
	//1. Daily Production Report (get_daily_production_report)
	function daily(){
		$data['content_view'] = 'reports/production/report_daily_production_v';
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Daily Production Report');
		$this->template->layout($data);
	}
	//$group_type = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
	
	function ajax_daily(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('ajax_daily');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$group_type = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
			$tbody = '';
			$tfoot = '';
			$t_p2 = $t_fc =  0;
			$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
			if(!empty($group_type)){
				foreach($group_type as $group){
					$report_data = $this->RM->get_daily_production_report($from_date, $to_date, $group['ID']);
					if(!empty($report_data)){
						foreach($report_data as $report){
							$Tmwt = ($report['Tmwt'] != '') ? $report['Tmwt'] : '0.00' ;
							$Localminor = ($report['Localminor'] != '') ? $report['Localminor'] : '0.00' ;
							$Swt = ($report['Swt'] != '') ? $report['Swt'] : '0.00' ;
							
							//fc = fishing_charge
							$m1_fc = $Tmwt*$report['MajorFee'];
							$m2_fc = $Localminor*$report['MinorFee'];
							$sw_fc = $Swt*$report['SawalFee'];
							
							$tbody .='<tr>';
								$tbody .='<th>Production '.$report['group_name'].'</th>';
								$tbody .='<td>'.($report['Twt']).'</td>';
								$tbody .='<td></td>';
								$tbody .='<td></td>';
							$tbody .='</tr>';
							$tbody .='<tr>';
								$tbody .='<td>M1</td>';
								$tbody .='<td>'.$Tmwt.'</td>';
								$tbody .='<td>'.$report['MajorFee'].'</td>';
								$tbody .='<td>'.$m1_fc.'</td>';
							$tbody .='</tr>';
							$tbody .='<tr>';
								$tbody .='<td>M2</td>';
								$tbody .='<td>'.$Localminor.'</td>';
								$tbody .='<td>'.$report['MinorFee'].'</td>';
								$tbody .='<td>'.$m2_fc.'</td>';
							$tbody .='</tr>';
							$tbody .='<tr>';
								$tbody .='<td>Sawal</td>';
								$tbody .='<td>'.$Swt.'</td>';
								$tbody .='<td>'.$report['SawalFee'].'</td>';
								$tbody .='<td>'.$sw_fc.'</td>';
							 $tbody .='</tr>';
							
							$t_p2 += ($Tmwt+$Localminor+$Swt);
							$t_fc +=  ($m1_fc+$m2_fc+$sw_fc);
						}
					}else{
						$tbody .='<tr>';
							$tbody .='<th>Production '.$group['Name'].'</th>';
							$tbody .='<td>0.00</td>';
							$tbody .='<td></td>';
							$tbody .='<td>0</td>';
						$tbody .='</tr>';
					}
				}
				$tfoot .='<tr>';
				$tfoot .=' <th>Summary :</th>';
				$tfoot .=' <th>'.$t_p2.'</th>';
				$tfoot .=' <th></th>';
				$tfoot .=' <th>'.$t_fc.'</th>';
				$tfoot .='</tr>';
				$tfoot .= $this->datatable_scripts;
			}
			$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>'', 'search_date'=>$search_date);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//2. Monthly Production Report (get_monthly_production_report)
	function monthly(){
		$data['content_view'] = 'reports/production/report_monthly_production_v';
		$group_type = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
		$thead = '<tr><th rowspan="2" width="10%">Date </th>';
		$thead2 = '<tr>';
		
		$colspan = (count($group_type) * 3) + 2;
		foreach($group_type as $group){
			$thead .= '<th colspan="3" width="15%">Production '.$group['Name'].'</th>';
			$thead2 .= '<th width="5%">M1 </th>
						<th width="5%">M2 </th>
						<th width="5%">S </th>';
		}
		
		$thead2 .= '</tr>';
		$thead .= '<th rowspan="2" width="10%">Total Production</th></tr>';
		$data['thead'] = $thead.$thead2;
		$data['colspan'] = $colspan;
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Monthly Production Report');
		$this->template->layout($data);
	}
	
	function ajax_monthly(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('ajax_monthly');
		if($form_validation){
			
			$from_date = date('Y-m-01', strtotime($this->input->post('month')));
			$to_date = date('Y-m-t', strtotime($this->input->post('month')));
			
			//$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			//$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			
			$report_data = $this->RM->get_monthly_production_report($from_date, $to_date);
			
			if(!empty($report_data)){
				$tbody = '';
				$t_production = $t_m1 = $t_m2 = $t_sw = 0;
				$search_date = $this->input->post('from_date').' - '.$this->input->post('to_date');
				
				$group_type = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
				$group_id = array();
				foreach($group_type as $group){
					$group_id[] = $group['ID'];
				}
				
				$begin = new DateTime( $from_date );
				$end = new DateTime( $to_date );
				$end = $end->modify( '+1 day' ); 
				
				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);
				$i=0;
				$col_total = array();
				$tbody = '';
				//printr($daterange);
				foreach($daterange as $date){
					$toll_date = $date->format("Y-m-d");
					$tbody .= '<tr><td>'.get_date('d-M', $toll_date).'</td>';
					if(array_key_exists($toll_date, $report_data)){	
						$production = 0;					
						foreach($group_id as $gid){
							$dailytoll_data = @$report_data[$toll_date][$gid];
							
							$tbody .= '<td>'.($dailytoll_data['major_wt']=='' ? 0.00 : $dailytoll_data['major_wt']).'</td>';
							$tbody .= '<td>'.($dailytoll_data['minor_wt']=='' ? 0.00 : $dailytoll_data['minor_wt']).'</td>';
							$tbody .= '<td>'.($dailytoll_data['sawal_wt']=='' ? 0.00 : $dailytoll_data['sawal_wt']).'</td>';
							  
							$col_total[$i][] = $dailytoll_data['major_wt'];
							$col_total[$i][] = $dailytoll_data['minor_wt'];
							$col_total[$i][] = $dailytoll_data['sawal_wt'];
							
							$production   += $dailytoll_data['total_wt'];
							$t_production += $dailytoll_data['total_wt'];
							
						}
						$tbody .= '<td>'.$production.'</td>';
					}else{
						foreach($group_id as $gid){
							
							$tbody .= '<td>0</td>';
							$tbody .= '<td>0</td>';
							$tbody .= '<td>0</td>';
							
							$col_total[$i][] = 0;
							$col_total[$i][] = 0;
							$col_total[$i][] = 0;
							
						}
						$tbody .= '<td>0.00</td>';
					}
					$tbody .= '</tr>';
					$i++;
				}
				
				$tfoot = '<tr><th class="text-right">Total : </th>';
				$total_value = array();
				foreach($col_total as $col){
					foreach($col as $key=>$value){
						$total_value[$key] = $value + (isset($total_value[$key]) ? $total_value[$key] : 0.00);
					}
				}
				$grand_total = 0.00;
				foreach($total_value as $total){
					$tfoot .='<th>'.$total.'</th>';
					$grand_total += $total;
				}				
				$tfoot .='<th>'.$grand_total.'</th></tr>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>'', 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			}
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//3. Yearly Production Report (get_yearly_production_report)
	function yearly_production(){
		$data['content_view'] = 'reports/production/report_yearly_production_v';
		
		$group_type = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
		$thead = '<tr><th rowspan="2" width="10%">Month </th>';
		$thead2 = '<tr>';
		
		$colspan = (count($group_type) * 3) + 2;
		foreach($group_type as $group){
			$thead .= '<th colspan="3" width="15%">Production '.$group['Name'].'</th>';
			$thead2 .= '<th width="5%">M1 </th>
						<th width="5%">M2 </th>
						<th width="5%">S </th>';
		}
		
		$thead2 .= '</tr>';
		$thead .= '<th rowspan="2" width="10%">Total Production</th></tr>';
		$data['thead'] = $thead.$thead2;
		$data['colspan'] = $colspan;
		
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Yearly Production Report');
		$this->template->layout($data);
	}
	
	function ajax_yearly(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		$form_validation = $this->__setFormRules('ajax_yearly');
		if($form_validation){
			
			$year = $this->input->post('date');
			$report_data = $this->RM->get_yearly_production_report($year);
			//printr($report_data);
			if(!empty($report_data)){
				$tbody = '';
				$t_production = $t_m1 = $t_m2 = $t_sw = 0;
				$search_date = $this->input->post('date');
				
				$group_type = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
				$group_id = array();
				foreach($group_type as $group){
					$group_id[] = $group['ID'];
				}
				$i=0;
				for ($m=1; $m<=12; $m++) {
				    $month_name = date('F', mktime(0,0,0,$m, 1, date('Y')));
					$month_number = date('m', mktime(0,0,0,$m, 1, date('Y')));
					
					$tbody .= '<tr><td>'.$month_number.' - '.$month_name.'</td>';
					if(array_key_exists($month_number, $report_data)){						
						$production = 0;
						foreach($group_id as $gid){
							$dailytoll_data = @$report_data[$month_number][$gid];
							
							$tbody .= '<td>'.($dailytoll_data['major_wt']=='' ? 0.00 : $dailytoll_data['major_wt']).'</td>';
							$tbody .= '<td>'.($dailytoll_data['minor_wt']=='' ? 0.00 : $dailytoll_data['minor_wt']).'</td>';
							$tbody .= '<td>'.($dailytoll_data['sawal_wt']=='' ? 0.00 : $dailytoll_data['sawal_wt']).'</td>';
							  
							$col_total[$i][] = $dailytoll_data['major_wt'];
							$col_total[$i][] = $dailytoll_data['minor_wt'];
							$col_total[$i][] = $dailytoll_data['sawal_wt'];
							
							$production += $dailytoll_data['total_wt'];
							$t_production += $dailytoll_data['total_wt'];
							
						}
						$tbody .= '<td>'.$production.'</td>';
					}else{
						foreach($group_id as $gid){
							
							$tbody .= '<td>0</td>';
							$tbody .= '<td>0</td>';
							$tbody .= '<td>0</td>';
							
							$col_total[$i][] = 0;
							$col_total[$i][] = 0;
							$col_total[$i][] = 0;
							
						}
						$tbody .= '<td>0.00</td>';
					}
					$tbody .= '</tr>';
					$i++;
				}
				
				$tfoot = '<tr><th class="text-right">Total : </th>';
				$total_value = array();
				foreach($col_total as $col){
					foreach($col as $key=>$value){
						$total_value[$key] = $value + (isset($total_value[$key]) ? $total_value[$key] : 0.00);
					}
				}
				$grand_total = 0.00;
				foreach($total_value as $total){
					$tfoot .='<th>'.$total.'</th>';
					$grand_total += $total;
				}				
				$tfoot .='<th>'.$grand_total.'</th></tr>';
				$tfoot .= $this->datatable_scripts;
				
				$html = array('tbody'=>$tbody, 'tfoot'=>$tfoot, 'search_key'=>'', 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			}
			
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//4. Samiti Production Report (get_samiti_production_report)
	function samiti(){
		$data['content_view'] = 'reports/production/report_samiti_production_v';
		
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Samiti Production Report');
		$this->template->layout($data);
	}
	
	function ajax_samiti(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_samiti');
		if($form_validation){
			
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			
			$search_date = $this->input->post('from_date') ." - ". $this->input->post('to_date');
			
			$group_type = $this->db->select('ID, Name')->get(MAINGROUP_TYPE)->result_array();
			$html = '';
			if(!empty($group_type)){
				foreach($group_type as $group){
					
					$html .= '<div class="col-md-'.(count($group_type) == 1 ? '12' : '6').'">
							  <table class="table table-striped table-bordered">
								<thead>
								  <tr>
									<th colspan="5" style="text-align:center; font-size: 20px">'.$group['Name'].'</th>
								  </tr>
								  <tr>
									<th>Point </th><th>Total </th><th>Major </th><th>Minor </th><th>Sawal </th>
								  </tr>
								</thead>
								<tbody>';
					
					$report_data = $this->RM->get_samiti_production_report($from_date, $to_date, $group['ID']);
					//printr($report_data);
					$Twt = $Tmwt = $lmwt = $Swt = 0;
					if(!empty($report_data)){
						foreach($report_data as $dt){
							$tollData = $dt['toll_data'];
							$major = $minor = $sawal = $total = 0;
							if($tollData !== NULL){
								$tollData = explode('|', $tollData);
								$major = (!isset($tollData[0]) ? 0 : $tollData[0]);
								$minor = (!isset($tollData[1]) ? 0 : $tollData[1]);
								$sawal = (!isset($tollData[2]) ? 0 : $tollData[2]);
								$total = (!isset($tollData[3]) ? 0 : $tollData[3]);
								
								$Tmwt += $major;
								$lmwt += $minor;
								$Swt += $sawal;	
								$Twt += $total;					
							}
							
							$html .= '<tr>
									 <td>'.$dt['Name'].'</td><td>'.$total.'</td><td>'.$major.'</td><td>'.$minor.'</td><td>'.$sawal.'</td>
								     </tr>';
							
						}
					}
					$html .= '</tbody>
								<tbody>
								  <tr><th>Summary </th><th>'.$Twt.'</th><th>'.$Tmwt.'</th><th>'.$lmwt.'</th><th>'.$Swt.'</th></tr>
								</tbody>
							  </table>
							</div>';
				}
			}
			$html .= $this->datatable_scripts;
			
			$html = array('tbody'=>$html, 'tfoot'=>'', 'search_key'=>'', 'search_date'=>$search_date);
			$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
						
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');
		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	//5. Inactive Fisherman (get_inactive_fisherman_report)
	function inactive_fisherman(){
		$data['content_view'] = 'reports/production/report_inactive_fisherman_v';
		$data['all_maingroup'] = $this->RM->get_all_maingroups();
		$this->template->set('stylesheet', $this->stylesheet_array);
		$this->template->set('scriptsrc', $this->scriptsrc_array);
		
		$this->template->set('document_title', 'Inactive Fisherman');
		$this->template->layout($data);
	}
	
	function ajax_inactive_fisherman(){
		$data = array('status' => 'danger', 'message' => '<div class="alert alert-danger"><button data-dismiss="alert" class="close">×</button> No data found, Please try again.</div>', 'data'=>'');
		
		$form_validation = $this->__setFormRules('ajax_inactive_fisherman');
		if($form_validation){
			$from_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('from_date')));
			$to_date = get_date('Y-m-d', str_replace('/', '-', $this->input->post('to_date')));
			$maingroup = $this->input->post('maingroup');
		
			$report_data = $this->RM->get_inactive_fisherman_report($from_date, $to_date, $maingroup);
			if(!empty($report_data)){
				//printr($report_data);
				$tbody = '';
				$search_date = $this->input->post('from_date').' To '.$this->input->post('to_date');
				foreach($report_data as $report){
					$tbody .='<tr>';
                    $tbody .='    <td>'.$report['f_name'].'</td>';
					$tbody .='    <td>'.$report['f_code'].'</td>';
                    $tbody .='    <td>'.$report['f_maingroup'].'</td>';
                    $tbody .='</tr>';
				}
				
				$html = array('tbody'=>$tbody, 'tfoot'=>'', 'search_key'=>'', 'search_date'=>$search_date);
				$data = array('status' => 'success', 'message' => 'Report data', 'data'=> $html);
			}
		}else{
			$data = array('status' => 'danger', 'message' => validation_errors(), 'data'=>'');

		}
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}
	
	function __setFormRules($setRulesFor = ''){
		switch($setRulesFor){
			case 'ajax_daily':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
			break;
			case 'ajax_monthly':
				$this->form_validation->set_rules('month', 'Month', 'trim|required|min_length[1]');
			break;
			case 'ajax_yearly':
				$this->form_validation->set_rules('date', 'Year', 'trim|required|min_length[1]');
			break;
			case 'ajax_samiti':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be  greater than or equal to From Date.'));
			break;
			case 'ajax_inactive_fisherman':
				$this->form_validation->set_rules('from_date', 'From Date', 'trim|required|min_length[1]');
				$this->form_validation->set_rules('to_date', 'To Date', 'trim|required|min_length[1]|callback_validate_date', array('validate_date' => 'To Date must be greater than or equal to From Date.'));
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