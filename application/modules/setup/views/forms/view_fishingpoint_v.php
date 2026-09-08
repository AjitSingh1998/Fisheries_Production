<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
extract($dbdata);
?>
<div class="form-group">
  <label class="col-sm-3 control-label"> Fishing Point Name <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Fishing Point Name" class="form-control" name="name" value="<?=set_value('name', $Name)?>" >
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label">Remark </label>
  <div class="col-sm-8">
    <textarea placeholder="Enter Remark" class="form-control" name="remark" ><?=set_value('remark', $Remark)?></textarea>
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label"> Status <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <select name="status" class="form-control" >
    	<option value="">Select Status</option>
        <option <?=(set_value('status', $status) == 'Active' ? 'selected="selected"' : '')?> value="Active">Active</option>
        <option <?=(set_value('status', $status) == 'Inactive' ? 'selected="selected"' : '')?> value="Inactive">Inactive</option>
    </select>
  </div>
</div>

<script>
$(document).ready(function(e) {
	$('input, select, textarea').prop('disabled', true);
    $('.save_form').remove();
});
</script>
