<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="loader">
    <div class="panel-heading border-light light-bg">
      <h3 class="text-center"><?=$page_title?></h3>
      
        <?=form_open('wages/advance_wage', 'id="adv_wages_form"')?>
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label">From Date: </label>
                    <input type="text" class="form-control datepicker" name="from_date" value="<?=set_value('from_date', @$from_date)?>">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label">To Date : </label>
                    <input type="text" class="form-control datepicker" name="to_date" value="<?=set_value('to_date', @$to_date)?>">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label">Receipt Number : </label>
                    <input type="text" class="form-control" name="receipt_number" value="<?=set_value('receipt_number', @$receipt_number)?>">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Party Name : </label>                    
                    <?php $party_name = set_value('party_id', @$party_id);?>
                    <select class="form-control select2" name="party_id">
                    	<?php if(isset($party_id) && !empty($party_id) && isset($party_name) && !empty($party_name)){?>
                    		<option value="<?=set_value('party_id', @$party_id)?>" selected="selected"> <?=set_value('party_name', @$party_name)?> </option>
                        <?php }?>
                    </select>
                    <input type="hidden" name="party_name" value="<?=set_value('party_name', @$party_name);?>" />
                </div>
            </div>
            <div class="col-sm-2">
            	<label>&nbsp;</label>
                <p class="text-center"><button type="submit" class="btn btn-primary" name="fileter_fisherman" value="fileter fisherman">Filter</button></p>
            </div>
        </div>
        <?=form_close()?>
      
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <?php echo $output;?>
            </div>
        </div>

	</div>
  </div>
</div>