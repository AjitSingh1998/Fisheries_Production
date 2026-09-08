<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<?=form_open($form_action, 'class="form-horizontal"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<div class="form-group">
  <label class="col-sm-3 control-label"> Name <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Cateogry Name" class="form-control" name="name" value="<?=set_value('name', @$Name)?>">
  </div>
</div>
<div class="form-group">
<label class="col-sm-3 control-label">Type <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <select class="form-control" name="type">
      <option value="">Type</option>
		<?php
		$type = set_value('type', @$Type_id);
        if(!empty($pr_type)){
            $selected = '';
			foreach($pr_type as $prt){
                $op_val = $prt['ID'];
                $op_text = $prt['Name'];
				if($type == $op_val){
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
<div class="form-group">
  <label class="col-sm-3 control-label">Remark </label>
  <div class="col-sm-8">
    <textarea placeholder="Enter Remark" class="form-control" name="remark"><?=set_value('remark', @$Remark)?></textarea>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label">Status </label>
  <div class="col-sm-8">
    <select name="status" class="form-control">
        <option value="">Select Status</option>
        <option <?=(set_value('status', @$status) == 'Active' ? 'selected="selected"' : '')?> value="Active">Active</option>
        <option <?=(set_value('status', @$status) == 'Inactive' ? 'selected="selected"' : '')?> value="Inactive">Inactive</option>
    </select>
  </div>
</div>

<?=form_close()?>
