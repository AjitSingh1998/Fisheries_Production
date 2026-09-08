	<?php
	if(!empty($wage_items)){
		$sm_major_wt = $sm_minor_wt = $sm_sawal_wt = $sm_TotalWage = $sm_govt_deduction = 0;
		$sm_total_group_liability = $sm_total_advance_wages = $sm_GroupLiabilityDeduction = $sm_AdvanceWagesDeduction = $sm_final_wages = 0;
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
			$TotalWage = $data['total_wages'];
			
			$product_balance = (float)$data['product_balance'];
			$wages_balance = (float)$data['wages_balance'];
			
			$liability_deduction = explode('/', (string)$data['liability_deduction']);
			$group_deduction = (float)@$liability_deduction[0];
			$wages_deduction = (float)@$liability_deduction[1];
			
			$cash_deposited = explode('/', (string)$data['cash_deposited']);
			$product_cd = (float)@$cash_deposited[0];
			$wages_cd = (float)@$cash_deposited[1];
			
			$grp_liability = (float)$data['group_liability']; // new liabiliy
			$grp_liability_returned = (float)$data['returned_amt'];
			$total_group_liability = ($grp_liability + $product_balance) - ($grp_liability_returned + $group_deduction + $product_cd);
			
			$advance_wages = (float)$data['advance_wages'];
			$total_advance_wages = ($advance_wages + $wages_balance) - ($wages_deduction + $wages_cd); //Advance liability = advanced amount - deposited amount
			
			$GroupLiabilityDeduction = ($data['GroupLiabilityDeduction'] != NULL) ? (float)$data['GroupLiabilityDeduction'] : 0.00;
			$AdvanceWagesDeduction = ($data['AdvanceWagesDeduction'] != NULL) ? (float)$data['AdvanceWagesDeduction'] : 0.00;
			
			$net_amount = (float)$data['net_amount'];
			
			$final_wages = $net_amount - ($GroupLiabilityDeduction+$AdvanceWagesDeduction);
			$class = ' fishing';
			if($TotalWage == 0){
				$class = ' zero_fishing';
			}
	?>
	<tr class="editable for_<?=$data['f_id'].$class?>">
		<input type="hidden" name="group_type_id" value="<?=$data['f_group_type']?>" />
        <input type="hidden" name="MainGroup" value="<?=$data['f_maingroup']?>" />
        <input type="hidden" name="FishermanId" value="0" />
        
        <!-- Fisher Man toll rate -->
        <input type="hidden" name="MajorFee" value="<?=$data['MajorFee']?>" />
        <input type="hidden" name="MinorFee" value="<?=$data['MinorFee']?>" />
        <input type="hidden" name="SawalFee" value="<?=$data['SawalFee']?>" />
        
         <!-- Fisher Man Toll Weight -->
        <input type="hidden" name="major_wt" value="<?=$data['major_wt']?>" />
        <input type="hidden" name="minor_wt" value="<?=$data['minor_wt']?>" />
        <input type="hidden" name="sawal_wt" value="<?=$data['sawal_wt']?>" />
              
         <!-- Fisher Man toll wages -->
        <input type="hidden" name="major_wage" value="<?=$data['major_wage']?>" />
        <input type="hidden" name="minor_wage" value="<?=$data['minor_wage']?>" />
        <input type="hidden" name="sawal_wage" value="<?=$data['sawal_wage']?>" />
        
        <input type="hidden" name="TotalWage" value="<?=$TotalWage?>" />
        
        <input type="hidden" name="group_liability" value="<?=$total_group_liability?>" />
        <input type="hidden" name="advance_wages" value="<?=$total_advance_wages?>" />
        
        <input type="hidden" name="GovDeduction" value="<?=$govt_deduction?>" />
        
        <input type="hidden" name="net_amount" class="net_amount" value="<?=$net_amount?>" />
        <input type="hidden" name="mode" class="mode" value="<?=$action_mode?>" />
        <input type="hidden" name="item_id" class="item_id" value="<?=$wages_item_id?>" />
        <td><?=$data['f_maingroup']?></td>
		<td><?=$data['samiti_name']?></td>
        <td class="acc_no"><?=$AccountNo?></td>
        <td class="mj_amt"><?=$data['major_wt']?></td>
        <td class="mn_amt"><?=$data['minor_wt']?></td>
        <td class="sw_amt"><?=$data['sawal_wt']?></td>
        <td class="t_amt"><?=$TotalWage?></td>
        <td class="govt_dedc"><?=$govt_deduction?></td>
        <td class="grp_liab"><?=$total_group_liability?></td>
        <td class="adv_liab"><?=$total_advance_wages?></td>
        <td><!-- Group Liability Deduction --><input type="text" name="gld_amount" value="<?=$GroupLiabilityDeduction?>" class="form-control gld_amount" <?=$disabled?> /></td>
        <td><!-- Advance Liability Deduction --><input type="text" name="ald_amount" value="<?=$AdvanceWagesDeduction?>" class="form-control ald_amount" <?=$disabled?> /></td>
        <td><input type="text" name="final_wages" value="<?=$final_wages?>" class="form-control disabled final_wages" readonly="readonly" /></td>
        <td class="no-print"><?=$action_btn?></td>
	</tr>
    
	<?php 
		$sm_major_wt = $sm_major_wt + $data['major_wt'];
		$sm_minor_wt = $sm_minor_wt + $data['minor_wt'];
		$sm_sawal_wt = $sm_sawal_wt + $data['sawal_wt'];
		$sm_TotalWage = $sm_TotalWage + $TotalWage;
		$sm_govt_deduction = $sm_govt_deduction + $govt_deduction;
		$sm_total_group_liability = $sm_total_group_liability + $total_group_liability;
		$sm_total_advance_wages = $sm_total_advance_wages + $total_advance_wages;
		$sm_GroupLiabilityDeduction = $sm_GroupLiabilityDeduction + $GroupLiabilityDeduction;
		$sm_AdvanceWagesDeduction = $sm_AdvanceWagesDeduction + $AdvanceWagesDeduction;
		$sm_final_wages = $sm_final_wages + $final_wages;
	} ?>
	<tr>
        <th colspan="3">Summary</th>
        <th><?=$sm_major_wt?></th>
        <th><?=$sm_minor_wt?></th>
        <th><?=$sm_sawal_wt?></th>
        <th><?=$sm_TotalWage?></th>
        <th><?=$sm_govt_deduction?></th>
        <th><?=$sm_total_group_liability?></th>
        <th><?=$sm_total_advance_wages?></th>
        <th><?=$sm_GroupLiabilityDeduction?></th>
        <th><?=$sm_AdvanceWagesDeduction?></th>
        <th><?=$sm_final_wages?></th>
        <th></th>
    </tr>
	<?php } ?>
    
    