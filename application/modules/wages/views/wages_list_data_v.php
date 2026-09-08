	<?php
	if(!empty($wage_items)){
		$gt_wages = $gt_major_wt = $gt_minor_wt = $gt_sawal_wt = $gt_govt_deduction = $gt_total_personal_liability = $gt_total_group_liability = $gt_advance_wages = $gt_GroupLiabilityDeduction = $gt_AdvanceWagesDeduction = $gt_final_wages = 0;
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
			
			$grp_liability = (float)$data['group_liability']; // new liabiliy
			$grp_liability_returned = (float)$data['returned_amt'];
			
			$grp_liability_deduction = (float)@$data['group_liability_deducted'];
			$total_group_liability = $grp_liability - ($grp_liability_returned + $grp_liability_deduction);
			
			$advance_wages = (float)$data['advance_wages'];
			
			$total_personal_liability = (float)@$data['personal_liability'];
			
			$GroupLiabilityDeduction = ($data['GroupLiabilityDeduction'] != NULL) ? (float)$data['GroupLiabilityDeduction'] : 0;
			$AdvanceWagesDeduction = ($data['AdvanceWagesDeduction'] != NULL) ? (float)$data['AdvanceWagesDeduction'] : 0;
			
			$net_amount = (float)$data['net_amount'];
			
			$final_wages = $net_amount - ($GroupLiabilityDeduction + $AdvanceWagesDeduction);
			$class = ' fishing';
			$style = 'style="display: table-row;"';
			if($TotalWage == 0){
				$class = ' zero_fishing';
				$style = 'style="display: none;"';
			}
	?>
	<tr class="editable for_<?=$data['f_id'].$class?>" <?=$style?>>
		<input type="hidden" name="group_type_id" value="<?=$data['f_group_type']?>" />
        <input type="hidden" name="MainGroup" value="<?=$data['f_maingroup']?>" />
        <input type="hidden" name="FishermanId" value="<?=$data['f_id']?>" />
        
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
        <input type="hidden" name="advance_wages" value="<?=$advance_wages?>" />
        
        <input type="hidden" name="GovDeduction" value="<?=$govt_deduction?>" />
        
        <input type="hidden" name="net_amount" class="net_amount" value="<?=$net_amount?>" />
        <input type="hidden" name="mode" class="mode" value="<?=$action_mode?>" />
        <input type="hidden" name="item_id" class="item_id" value="<?=$wages_item_id?>" />
        <td><?=$data['f_code']?></td>
		<td><a href="<?=site_url('wages/dailytoll_info?fid='.$data['f_id'].'&fdt='.$fdt.'&tdt='.$tdt.'&mg='.$mg)?>" class="loadDailyTollInfo text-danger"><?=$data['f_name']?></a>
		<a href="<?=site_url('wages/ajax_user_liab_info?fid='.$data['f_id'].'&fdt='.$fdt.'&tdt='.$tdt.'&mg='.$mg)?>"
               class="no-print loadUserLiabInfo" data-toggle="tooltip" data-title="Check Group Members Liability"><i class="fa fa-group"></i></a>
		</td>
        <td class="acc_no"><?=$AccountNo?></td>
        <td class="mj_amt"><?=$data['major_wt']?></td>
        <td class="mn_amt"><?=$data['minor_wt']?></td>
        <td class="sw_amt"><?=$data['sawal_wt']?></td>
        <td class="t_amt"><?=number_format($TotalWage,2,'.','')?></td>
        <td class="govt_dedc"><?=number_format($govt_deduction,2,'.','')?></td>
        <td class="prs_liab"><?=number_format($total_personal_liability,2,'.','')?></td>        
		<td class="grp_liab"><?=number_format($total_group_liability,2,'.','')?></td>
        <td class="adv_liab"><?=number_format($advance_wages,2,'.','')?></td>
        <td><!-- Group Liability Deduction --><input type="text" name="gld_amount" value="<?=number_format($GroupLiabilityDeduction,2,'.','')?>" class="form-control gld_amount" <?=$disabled?> /></td>
        <td><!-- Advance Liability Deduction --><input type="text" name="ald_amount" value="<?=number_format($AdvanceWagesDeduction,2,'.','')?>" class="form-control ald_amount" <?=$disabled?> /></td>
        <td><input type="text" name="final_wages" value="<?=number_format($final_wages,2,'.','')?>" class="form-control disabled final_wages" readonly="readonly" /></td>
        <td class="no-print noExl"><?=$action_btn?></td>
	</tr>
	<?php 
		//calculating grand totals
		$gt_major_wt = $gt_major_wt + $data['major_wt'];
		$gt_minor_wt = $gt_minor_wt + $data['minor_wt'];
		$gt_sawal_wt = $gt_sawal_wt + $data['sawal_wt'];
		$gt_wages = $gt_wages + $TotalWage;
		$gt_govt_deduction = $gt_govt_deduction + $govt_deduction;
		$gt_total_personal_liability = $gt_total_personal_liability + $total_personal_liability;
		$gt_total_group_liability = $gt_total_group_liability + $total_group_liability;
		$gt_advance_wages = $gt_advance_wages + $advance_wages;
		
		$gt_GroupLiabilityDeduction = $gt_GroupLiabilityDeduction + $GroupLiabilityDeduction;
		$gt_AdvanceWagesDeduction = $gt_AdvanceWagesDeduction + $AdvanceWagesDeduction;
		$gt_final_wages = $gt_final_wages + $final_wages;
	}}?>
    <tr class="">
        <th colspan="3">Summary</th>
        <th><?=number_format($gt_major_wt, 2, '.', '')?></th>
        <th><?=number_format($gt_minor_wt, 2, '.', '')?></th>
        <th><?=number_format($gt_sawal_wt, 2, '.', '')?></th>
        <th><?=number_format($gt_wages, 2, '.', '')?></th>
        <th><?=number_format($gt_govt_deduction, 2, '.', '')?></th>
        <th><?=number_format($gt_total_personal_liability, 2, '.', '')?></th>        
		<th><?=number_format($gt_total_group_liability, 2, '.', '')?></th>
        <th><?=number_format($gt_advance_wages, 2, '.', '')?></th>
        <th><?=number_format($gt_GroupLiabilityDeduction, 2, '.', '')?></th>
        <th><?=number_format($gt_AdvanceWagesDeduction, 2, '.', '')?></th>
        <th><?=number_format($gt_final_wages, 2, '.', '')?></th>
        <th></th>
	</tr>
    
    