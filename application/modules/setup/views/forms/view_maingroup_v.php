<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<div class="form-group">
    <label class="col-sm-3 control-label">Main Group <span class="symbol required"></span></label>
    <div class="col-sm-8">
        <select class="form-control" name="type">
          <option value="">Select Type</option>
          <?php
            if(!empty($mg_type)){
                $type = set_value('type', @$Type);
                $selected = '';
                foreach($mg_type as $mtype){
                    $op_val = $mtype['ID'];
                    $op_text = $mtype['Name'];
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
  <label class="col-sm-3 control-label"> Main Group Name <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Main Group Name" class="form-control" name="group_name" value="<?=set_value('group_name', $Name)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> Bank Name </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Bank Name" class="form-control" name="bank_name" value="<?=set_value('bank_name', $bank_name)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> Branch Name </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Branch Name" class="form-control" name="branch_name" value="<?=set_value('branch_name', $branch_name)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> IFSC Code </label>
  <div class="col-sm-8">
    <input type="text" placeholder="IFSC Code" class="form-control" name="ifsc_code" value="<?=set_value('ifsc_code', $ifsc_code)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> Account No </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Account No" class="form-control" name="account_number" value="<?=set_value('account_number', $account_number)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> Major Fee </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Major Fee" class="form-control" name="major_fee" value="<?=set_value('major_fee', $major_fee)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> Minor Fee </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Minor Fee" class="form-control" name="minor_fee" value="<?=set_value('minor_fee', $minor_fee)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> Sawal Fee </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Sawal Fee" class="form-control" name="sawal_fee" value="<?=set_value('sawal_fee', $sawal_fee)?>">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label">Remark </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Remark" class="form-control" name="remark" value="<?=set_value('remark', @$Remark)?>" />
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label"> Status <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <select name="status" class="form-control">
    	<option value="">Select Status</option>
        <option <?=(set_value('status', $status) == 'Active' ? 'selected="selected"' : '')?> value="Active">Active</option>
        <option <?=(set_value('status', $status) == 'Inactive' ? 'selected="selected"' : '')?> value="Inactive">Inactive</option>
    </select>
  </div>
</div>
<script>
$(document).ready(function(e) {
	$('input, select').prop('disabled', true);
    $('.save_form').remove();
});
</script>
