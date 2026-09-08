<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<?=form_open($form_action, 'id="adv_wages_form"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<div class="row">
    <div class="form-group col-md-3">
        <label class="control-label">Group Type: <span class="symbol required"></span></label>
        <select class="form-control group_type" id="group_type" name="group_type">
          <option value="">Select Group</option>
          <?php
            $group_type = set_value('group_type', @$group_type);
            if($group_type){
                $m_groups = $this->WM->get_mg_data($group_type);
            }
            if(!empty($allgrouptype)){
                $selected = '';
                foreach($allgrouptype as $group){
                    $op_val = $group['ID'];
                    $op_text = $group['Name'];
                
                if($group_type == $op_val){
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
        $maingroup = set_value('maingroup', @$maingroup_id);
        $disabled = 'disabled="disabled"';
        if(isset($m_groups) && !empty($m_groups)){
            $mg_fishers = $this->WM->get_mg_fisherman($maingroup);
            //printrr($mg_fishers);
            $disabled = '';
        }
        ?>
        <select class="form-control maingroup" id="maingroup" name="maingroup" <?=$disabled?>>
          <option value="">Select Maingroup</option>
          <?php
            if(!empty($m_groups)){
                $selected = '';
                foreach($m_groups as $smt){
                    $op_val = $smt['ID'];
                    $op_text = $smt['Name'];
                    if($maingroup == $op_val){
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
<br />
<div class="row">
  <div class="col-sm-3">
    <div class="form-group">
      <label class="control-label">Date : <span class="symbol required"></span></label>
      <input type="text" class="form-control datepicker" name="date" value="<?=set_value('date', get_date('d/m/Y', @$Date))?>">
    </div>
  </div>
  <div class="col-sm-4">
    <div class="form-group">
      <label class="control-label">Receipt Number : </label>
      <input type="text" class="form-control" name="receipt_number" value="<?=set_value('receipt_number', @$receipt_number)?>">
    </div>
  </div>
  <div class="col-sm-5">
    <div class="form-group">
      <label class="control-label">Amount : <span class="symbol required"></span></label>
      <input type="text" class="form-control" name="amount" value="<?=set_value('amount', @$Amount)?>">
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
      <div class="form-group">
      	<label class="control-label">Return Remark :</label>
      	<textarea rows="2" class="form-control" name="remark" placeholder="Enter Remark"><?=set_value('remark', @$Remark)?></textarea>
  	  </div>
  </div>
</div>

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
