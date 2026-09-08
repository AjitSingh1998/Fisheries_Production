<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<?php
$group_type = set_value('group_type', $group_type);
$maingroup = set_value('maingroup', $maingroup_id);
$f_cn = set_value('f_cn', $f_cn); //Fisherman code/name
$fisher_man = set_value('fisherman', $Fisherman);
$select2 = 'select-fisherman';
if(isset($Fisherman) && !empty($Fisherman)){
	$select2 = '';
}
?>
<div class="row">
  <div class="col-md-5">
    <div class="form-group">
        <label class="control-label">Fisherman</label>
        <select name="fisherman" class="form-control <?=$select2?>" >
        <?php
        if(!empty($fisher_man)){
			echo '<option value="'.$fisher_man.'" selected="selected">'.$f_cn.'</option>';
		}        
        ?>
        </select>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-3">
    <div class="form-group">
      <label class="control-label">Date : <span class="symbol required"></span></label>
      <input type="text" class="form-control datepicker" name="date" value="<?=get_date('d M Y', set_value('date', @$Date))?>">
    </div>
  </div>
  <!--<div class="col-sm-4">
    <div class="form-group">
      <label class="control-label">Receipt Number : </label>
      <input type="text" class="form-control" name="receipt_number" value="<?=set_value('receipt_number', @$receipt_number)?>">
    </div>
  </div>-->
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
<script>
$(document).ready(function(e) {
    $('input, select, textarea').prop('disabled', true);
	$('.save_form').remove();
});
</script>
