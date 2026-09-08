<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<table class="table table-striped" id="fisher-wages" width="100%">
  <thead>
    <tr><th colspan="23"> <h4 class="text-center"><?=$page_title?> Up To: <?=$tdt?></h4></th></tr>
    <tr>
      <th class="text-center">F.Code</th>
      <th class="text-center">Fisherman</th>
      <th class="text-center">Ope. Products Balance</th>
      <th class="text-center">Ope. Wages Balance</th>
      <th class="text-center">Outward Liability</th>
      <th class="text-center">Advance Wages</th>
      <th class="text-center">Returned Amt</th>
      <th class="text-center">Outward Deducted</th>
      <th class="text-center">Adv. Wages Deduction</th>
      <th class="text-center">Cash Deposit Product</th>
      <th class="text-center">Cash Deposit Wages</th>
      <th class="text-center">Remaining Liability</th>
    </tr>
  </thead>
  <tbody id="sale_products" class="table-hover">
    <?php 
	$t_opb = $t_owb = $t_ol = $t_ora = $t_aw = $t_awd = $t_cdfp = $t_cdfw = $t_od = $g_total = 0;
	if(!empty($liability_info)){
		
		foreach($liability_info as $row){
			$type = '';
			if(count($liability_info) == 1){
				$type = 'Primary';
			}else{
				if($row['Primary'] == $row['Secondary']){
					$type = 'Secondary';
				}else{
					$type = 'Primary';
				}
			}
			
			//opening_account_balance
			$opening_account_balance = explode('/', $row['opening_account_balance']);
			$ope_products_balance = is_null(@$opening_account_balance[0]) ? 0 : @$opening_account_balance[0]; //Debit
			$ope_wages_balance = is_null(@$opening_account_balance[1]) ? 0 : @$opening_account_balance[1]; //Debit
			
			//outward_liability
			$outward_liability = $row['outward_liability']; //Debit
			
			//outward_returned_amt
			$outward_returned_amt = $row['outward_returned_amt']; //Credit
			
			//advance_wages
			$advance_wages = $row['advance_wages']; //Debit
			
			//advance_wages_deduction
			$advance_wages_deduction = $row['advance_wages_deduction']; //Credit
			
			//cash_deposited
			$cash_deposited = explode('/', $row['cash_deposited']);
			$cash_deposited_for_product = is_null(@$cash_deposited[0]) ? 0 : @$cash_deposited[0]; //Credit
			$cash_deposited_for_wages = is_null(@$cash_deposited[1]) ? 0 : @$cash_deposited[1]; //Credit
			
			//outward_deducted
			$outward_deducted = $row['outward_deducted']; //Credit
			
			//Remaining Calculation	
			$total_debit = $ope_products_balance + $ope_wages_balance + $outward_liability + $advance_wages;
			$total_credit = $outward_returned_amt + $advance_wages_deduction + $cash_deposited_for_product + $cash_deposited_for_wages + $outward_deducted;
			$remaining = $total_debit - $total_credit;
	?>
    <tr class="text-center">
      <td><?=$row['Code']?></td>
      <td><?=$row['Name'].' ('.$type.')'?></td>
      <td><?=$ope_products_balance?></td>
      <td><?=$ope_wages_balance?></td>
      <td><?=$outward_liability?></td>
      <td><?=$advance_wages?></td>
      <td><?=$outward_returned_amt?></td>
      <td><?=$outward_deducted?></td>
      <td><?=$advance_wages_deduction?></td>
      <td><?=$cash_deposited_for_product?></td>
      <td><?=$cash_deposited_for_wages?></td>
      <td><?=number_format($remaining,2,'.','')?></td>
    </tr>
    <?php 
	$t_opb += $ope_products_balance;
	$t_owb += $ope_wages_balance;
	$t_ol += $outward_liability;
	$t_aw += $advance_wages;
	$t_ora += $outward_returned_amt;
	$t_od += $outward_deducted;
	$t_awd += $advance_wages_deduction;
	$t_cdfp += $cash_deposited_for_product;
	$t_cdfw += $cash_deposited_for_wages;
	$g_total += $remaining;
	}} ?>
    <tr>
      <th class="text-right" colspan="2">Summary</th>
      <th class="text-center"><?=number_format($t_opb,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_owb,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_ol,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_aw,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_ora,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_od,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_awd,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_cdfp,2,'.','')?></th>
      <th class="text-center"><?=number_format($t_cdfw,2,'.','')?></th>
      <th class="text-center"><?=number_format($g_total,2,'.','')?></th>
    </tr>
  </tbody>
</table>



