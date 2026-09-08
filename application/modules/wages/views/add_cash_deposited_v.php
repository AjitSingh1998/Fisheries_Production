<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<?=form_open($form_action, 'id="deposit_cash_payment"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<?php
$f_cn = set_value('f_cn', @$f_cn); //Fisherman code/name
?>
<input type="hidden" name="f_cn" id="f_cn" value="<?=$f_cn?>" />

<div class="row">
    <div class="col-md-4">
        <label>Deposited By:</label>
        <div class="input-group">
        	<?php
				$deposited_by = set_value('deposited_by', @$deposited_by);
				$by_fisher = '';
				$by_group = '';
				if(isset($deposited_by) && !empty($deposited_by)){
					if($deposited_by == 'Fisherman'){
						$by_fisher = 'checked="checked"';
					}elseif($deposited_by == 'Group'){
						$by_group = 'checked="checked"';
					}
				}else{
					$by_fisher = 'checked="checked"';
				}
			?>
            <div class="radio clip-radio radio-primary radio-inline">
                <input type="radio" id="by_fisher" name="deposited_by" value="Fisherman" <?=$by_fisher?>>
                <label for="by_fisher">Fisherman</label>
            </div>
            <div class="radio clip-radio radio-primary radio-inline">
                <input type="radio" id="by_group" name="deposited_by" value="Group" <?=$by_group?>>
                <label for="by_group">Group</label>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="form-group col-md-3">
    <label class="control-label">Group Type: <span class="symbol required"></span></label>
    <select class="form-control group_type_id" id="group_type_id" name="group_type_id">
      <option value="">Select Group</option>
      <?php
	  	$group_type_id = set_value('group_type_id', @$group_type_id);
		if($group_type_id){
			$m_groups = $this->WM->get_mg_data($group_type_id);
		}
        if(!empty($mg_type)){
            $selected = '';
			foreach($mg_type as $group){
                $op_val = $group['ID'];
                $op_text = $group['Name'];
        	
			if($group_type_id == $op_val){
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}
		?>
        <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
        <?php }} ?>
    </select>
  </div>
  
  <div class="form-group col-md-5">
    <label class="control-label">Main Group : <span class="symbol required"></span></label>
    <?php
    $maingroup_id = set_value('maingroup_id', @$maingroup_id);
    $disabled = 'disabled="disabled"';
	if(isset($m_groups) && !empty($m_groups)){
		$mg_fishers = $this->WM->get_mg_fisherman($maingroup_id);
		//printrr($mg_fishers);
		$disabled = '';
	}
	?>
    <select class="form-control maingroup_id" id="maingroup_id" name="maingroup_id" <?=$disabled?>>
      <option value="">Select Maingroup</option>
      <?php
		if(!empty($m_groups)){
			$selected = '';
			foreach($m_groups as $smt){
				$op_val = $smt['ID'];
				$op_text = $smt['Name'];
				if($maingroup_id == $op_val){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}
		?>
      <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
      <?php }} ?>
    </select>
  </div>
  
  <?php 
  $css_fisher_div = 'style="display: block;"';
  if($deposited_by == 'Group'){
	  $css_fisher_div = 'style="display: none;"';
  }
  ?>
  <div class="form-group col-md-4 fisher_div" <?=$css_fisher_div?>>
    <label class="control-label">Fisherman : </label>
    <?php
    $fisherman_id = set_value('fisherman_id', @$fisherman_id);
	$disabled = 'disabled="disabled"';
	if(isset($mg_fishers) && !empty($mg_fishers)){$disabled = '';}
	?>
    <select class="form-control fisherman_id" id="fisherman_id" name="fisherman_id" <?=$disabled?>>
      <option value="">Select Fisherman</option>
      <?php
		if(isset($mg_fishers) && !empty($mg_fishers['result1'])){
			$selected = '';
			foreach($mg_fishers['result1'] as $fisher){
				$op_val = $fisher['id'];
				$op_text = $fisher['text'];
				if($fisherman_id == $op_val){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}
		?>
      <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
      <?php }} ?>
    </select>
  </div>
  
</div>

<div class="row">
  <div class="col-sm-3">
    <div class="form-group">
      <label class="control-label">Date : <span class="symbol required"></span></label>
      <input type="text" class="form-control datepicker" name="deposit_date" value="<?=set_value('deposit_date', get_date('d/m/Y', @$deposit_date))?>">
    </div>
  </div>
  <div class="col-sm-3">
    <div class="form-group">
      <label class="control-label">Receipt Number : </label>
      <input type="text" class="form-control" name="receipt_number" value="<?=set_value('receipt_number', @$receipt_number)?>">
    </div>
  </div>
  <div class="col-sm-3">
    <div class="form-group">
      <label class="control-label">Product Liability : <span class="symbol required"></span></label>
      <input type="text" class="form-control amount" name="product_liability" value="<?=set_value('product_liability', @$product_liability)?>">
    </div>
  </div>
  <div class="col-sm-3">
    <div class="form-group">
      <label class="control-label">Wages Liability : <span class="symbol required"></span></label>
      <input type="text" class="form-control amount" name="wages_liability" value="<?=set_value('wages_liability', @$wages_liability)?>">
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
      <div class="form-group">
      	<label class="control-label">Return Remark :</label>
      	<textarea rows="5" class="form-control" name="remark" placeholder="Enter Remark"><?=set_value('remark', @$remark)?></textarea>
  	  </div>
  </div>
</div>
<br><br><br><br><br><br><br>
<?=form_close()?>
<?php
	if(isset($scriptsrc) && !empty($scriptsrc)){	
		if(is_array($scriptsrc)){
			foreach($scriptsrc as $script){
				echo '<script src="'.$script.'"></script>'."\n";
			}
		}
	}
?>
