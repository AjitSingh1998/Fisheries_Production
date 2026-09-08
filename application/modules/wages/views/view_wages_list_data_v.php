	<?php
	if(!empty($wage_items)){
		foreach($wage_items as $data){
			$wages_item_id = '';
			$action_mode = 'add';
			$action_btn = '<button type="button" class="btn btn-success btn-xs save_wage_row" title="Save Row">Save</button>';
			$disabled = '';
			if($data['wage_item_id'] != NULL){
				$wages_item_id = $data['wage_item_id'];
				$action_mode = 'edit';
				$disabled = 'disabled="disabled"';
				$action_btn = '<button type="button" class="btn btn-warning btn-xs edit_wage_row" title="Edit Row">Edit</button>';
			}
			
			$AccountNo = $data['AccountNo'];
			$govt_deduction = $data['govt_deduction'];
			$TotalWage = number_format($data['total_wages'], 2, '.', '');
			
			$grp_liability =  $data['group_liability']; // new liabiliy
			$grp_liability_returned = $data['returned_amt'];
			$grp_liability_deduction = $data['group_liability_deducted'];
			$total_group_liability = $grp_liability - ($grp_liability_returned + $grp_liability_deduction);
			
			$advance_wages = $data['advance_wages'];
			//$advance_wages_deduction = $data['advance_wages_deduction'];
			//$advance_wages = ($advance_wages-$advance_wages_deduction); //Advance liability = advanced amount - deposited amount

			
			$GroupLiabilityDeduction = ($data['GroupLiabilityDeduction'] != NULL) ? $data['GroupLiabilityDeduction'] : '0';
			$AdvanceWagesDeduction = ($data['AdvanceWagesDeduction'] != NULL) ? $data['AdvanceWagesDeduction'] : '0';
			
			$net_amount = $data['net_amount'];
	?>
	<tr class="editable for_<?=$data['f_id']?>">
        <td><?=$data['f_id']?></td>
		<td><a href="<?=site_url('wages/dailytoll_info?fid='.$data['f_id'].'&fdt='.$fdt.'&tdt='.$tdt.'&mg='.$mg)?>" class="loadDailyTollInfo text-danger"><?=$data['f_name']?></a></td>
        <td class="acc_no"><?=$AccountNo?></td>
        <td class="mj_amt"><?=$data['major_wt']?></td>
        <td class="mn_amt"><?=$data['minor_wt']?></td>
        <td class="sw_amt"><?=$data['sawal_wt']?></td>
        <td class="t_amt"><?=$TotalWage?></td>
        <td class="govt_dedc"><?=$govt_deduction?></td>
        <td class="grp_liab"><?=$total_group_liability?></td>
        <td class="adv_liab"><?=$advance_wages?></td>
        <td><!-- Group Liability Deduction --><input type="text" name="gld_amount" value="<?=$GroupLiabilityDeduction?>" class="form-control disabled gld_amount" <?=$disabled?> /></td>
        <td><!-- Advance Liability Deduction --><input type="text" name="ald_amount" value="<?=$AdvanceWagesDeduction?>" class="form-control disabled ald_amount" <?=$disabled?> /></td>
        <td><input type="text" name="final_wages" value="<?=$net_amount-($GroupLiabilityDeduction+$AdvanceWagesDeduction)?>" class="form-control disabled final_wages" /></td>
	</tr>
	<?php }} ?>