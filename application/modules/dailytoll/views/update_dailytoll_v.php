<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
extract($dbdata);
?>
<?=form_open($form_action, 'class="form-horizontal"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<div class="row">
    <div class="col-sm-3">
        <label for="Date">Date : </label>
        <input type="text" name="date" class="form-control datepicker" value="<?=get_date('d/m/Y', @$Date);?>" id="date" data-psy="date">
    </div>
                      
    <div class="col-sm-3">
        <label for="point">Point : </label>
        <select class="form-control point" name="point">
          <option value="">Select Point</option>
          <?php
            $value = @$Point;
            if(!empty($fishingpoints)){
                $selected = '';
                foreach($fishingpoints as $point){
                    $op_val = $point['ID'];
                    $op_text = $point['Name'];
                
                if($value == $op_val){
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

<?=form_close()?>
<style> .datepicker.datepicker-dropdown{ z-index: 99999 !important; } </style>
<!--
<script>
$(document).ready(function(e) {
	 $('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true,
		todayHighlight: true
	});
});
</script>
-->