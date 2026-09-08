<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<style>
table.dataTable tbody th, table.dataTable tbody td{padding: 8px 0px !important;}
table.dataTable thead th, table.dataTable thead td{padding: 10px 15px !important;}
</style>
<div class="container-fluid container-fullw">
	<div class="row flex">
    	<div class="col-sm-2">
        	<?php $this->load->view('database/yearly_backup_navigation_v');?>
        </div>
        <div class="col-sm-10">
            <div class="panel panel-white" id="panel-container">
                <div class="panel-heading border-light light-bg">
                    <h3 class="text-center">
                        <?php echo form_open(site_url('database/yearly_backup/samiti_balance'), 'method="get" style="display: inline-block;"');?>
                            <select name="group_type" id="samitiGroupType" onchange="this.form.submit();" >
                                <option>Select Group</option>
                                <?php
                                    if(isset($group_type) && !empty($group_type)){
                                        foreach($group_type as $type){
                                            $selected = '';
											if($type['ID'] == @$group_id){
												$selected = 'selected="selected"';
											}
                                            echo '<option '.$selected.' value="'.$type['ID'].'">'.$type['Name'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        <?php echo form_close();?>
						
						<?=$page_title?>
                        
                        <button type="button" id="export_data" data-target="tablelist" data-filename="Samiti Closing Balance" class="btn btn-warning"> <i class="fa fa-file-excel-o"></i> Excel</button>
                        <a id="dlink" style="display:none;"></a>
                        <button type="button" class="btn btn-success print_data" data-target="#tablelist"> <i class="fa fa-print"></i> Print</button>
                         <button type="button" class="btn btn-danger update_closing_balance"> <i class="fa fa-pencil-square-o"></i> Update Balance</button>
                    
                    </h3>
                </div>
                <div class="panel-body" id="tablelist">
                    <div class="row">
                        <div class="col-sm-12" id="response_container">
                        	<div class="row">
                                <div class="col-sm-12">
                                	<table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width:55px;">S.N.</th>
                                                <th style="width:205px;">Samiti</th>
                                                <th style="width:95px;">Product <br>Balance</th>
                                                <th style="width:95px;">Wages <br>Balance</th>
                                                <th style="width:95px;">Outward <br>Liability</th>
                                                <th style="width:95px;">Adwance <br> Liability</th>
                                                <th style="width:95px;">Return <br>Outward</th>
                                                <th style="width:95px;">Deducted <br>Outward</th>
                                                <th style="width:95px;">Deducted <br>Advance</th>
                                                <th style="width:95px;">Cash for <br>Outward</th>
                                                <th style="width:95px;">Cash for <br>Advance</th>
                                                <th style="width:95px;">Closing <br>Balance</th>
                                                <th style="width:55px;">Action</th>
                                            </tr>
                                        </thead>
                                    </table> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                	<table class="table" id="tablelist">
                                        <tbody>
                                    <?php
                                        $total_opening_product = $total_opening_wages = $total_outward = $total_advance = $total_return = $total_outward_deduction = $total_wages_deduction = $total_cash_outward = $total_cash_wage = $total_closing = 0;
                                        
                                        if(isset($closing_balance) && !empty($closing_balance)){
                                            $i=0;
                                            foreach($closing_balance as $balance){
                                                $i++;
                                $debit = ($balance['opening_product_balance'] + $balance['opening_wages_balance'] + $balance['outward_liability'] + $balance['advance_wages']);
                                $credit = ($balance['outward_returned_amt'] + $balance['GroupLiabilityDeduction'] + $balance['AdvanceWagesDeduction'] + $balance['cash_product_liability'] + $balance['cash_wages_liability']);
                                $closing = $debit - $credit;
                                                
                                                
                                                                                
                                                echo '<tr>
                                                        <td style="width:55px;">'.$i.'</td>
                                                        <td style="width:205px;">'.$balance['Samiti'].'</td>
                                                        <td style="width:55px;">'.$balance['opening_product_balance'].'</td>
                                                        <td style="width:95px;">'.$balance['opening_wages_balance'].'</td>
                                                        <td style="width:95px;">'.$balance['outward_liability'].'</td>
                                                        <td style="width:95px;">'.$balance['advance_wages'].'</td>
                                                        <td style="width:95px;">'.$balance['outward_returned_amt'].'</td>
                                                        <td style="width:95px;">'.$balance['GroupLiabilityDeduction'].'</td>
                                                        <td style="width:95px;">'.$balance['AdvanceWagesDeduction'].'</td>
                                                        <td style="width:95px;">'.$balance['cash_product_liability'].'</td>
                                                        <td style="width:95px;">'.$balance['cash_wages_liability'].'</td>
                                                        <td style="width:95px;">'.number_format($closing,2,'.','').'</td>
														<td style="width:55px;">
                                                            <button type="button" class="btn btn-danger update_balance" data-url="database/yearly_backup/update_samiti_balance/'.$balance['ID'].'">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </button>
                                                        </td>
                                                     </tr>';
                                                     
                                                     
                                                $total_opening_product += $balance['opening_product_balance'];
                                                $total_opening_wages += $balance['opening_wages_balance'];
                                                $total_outward += $balance['outward_liability'];
                                                $total_advance += $balance['advance_wages'];
                                                $total_return += $balance['outward_returned_amt'];
                                                $total_outward_deduction += $balance['GroupLiabilityDeduction'];
                                                $total_wages_deduction += $balance['AdvanceWagesDeduction'];
                                                $total_cash_outward += $balance['cash_product_liability'];
                                                $total_cash_wage += $balance['cash_wages_liability'];
                                                $total_closing += $closing; 
                                                     
                                            }
                                            
                                                        
                                        }					
                                    ?>
                                        </tbody>
                                    </table> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                	<table class="table">
                                        <tfoot>
                                            <tr>
                                                <th style="width:55px;"></th>
                                                <th style="width:205px;">Summary </th>
                                                <th style="width:95px;"><?=$total_opening_product;?></th>
                                                <th style="width:95px;"><?=$total_opening_wages;?></th>
                                                <th style="width:95px;"><?=$total_outward;?></th>
                                                <th style="width:95px;"><?=$total_advance;?></th>
                                                <th style="width:95px;"><?=$total_return;?></th>
                                                <th style="width:95px;"><?=$total_outward_deduction;?></th>
                                                <th style="width:95px;"><?=$total_wages_deduction;?></th>
                                                <th style="width:95px;"><?=$total_cash_outward;?></th>
                                                <th style="width:95px;"><?=$total_cash_wage;?></th>
                                                <th style="width:55px;"><?=$total_closing;?></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th style="width:55px;">S.N.</th>
                                                <th style="width:205px;">Samiti</th>
                                                <th style="width:95px;">Product <br>Balance</th>
                                                <th style="width:95px;">Wages <br>Balance</th>
                                                <th style="width:95px;">Outward <br>Liability</th>
                                                <th style="width:95px;">Adwance <br> Liability</th>
                                                <th style="width:95px;">Return <br>Outward</th>
                                                <th style="width:95px;">Deducted <br>Outward</th>
                                                <th style="width:95px;">Deducted <br>Advance</th>
                                                <th style="width:95px;">Cash for <br>Outward</th>
                                                <th style="width:95px;">Cash for <br>Advance</th>
                                                <th style="width:95px;">Closing <br>Balance</th>
                                                <th style="width:55px;">Action</th>
                                            </tr>
                                        </tfoot>
                                    </table> 
                                </div>
                            </div>
                               
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>