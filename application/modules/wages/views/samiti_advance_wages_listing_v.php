<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="loader">
    <div class="panel-heading border-light light-bg">
      <h3 class="text-center"><?=$page_title?></h3>
      
        <?=form_open('wages/samiti_advance_wages', 'id="adv_wages_form" method="GET"')?>
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label">From Date: </label>
                    <input autocomplete="off" type="text" class="form-control datepicker" name="from_date" value="<?=set_value('from_date', @$from_date)?>">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label">To Date : </label>
                    <input autocomplete="off" type="text" class="form-control datepicker" name="to_date" value="<?=set_value('to_date', @$to_date)?>">
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
                                     
                    <?php $party_name = set_value('party_name', @$party_name);?>
                    <select class="form-control select2" name="party_name">
                      <option value="">Select Party</option>
                      <?php
					  	$m_groups = $this->WM->get_mg_data('');
                        if(!empty($m_groups)){
                            $selected = '';
                            foreach($m_groups as $smt){
                                $op_val = $smt['ID'];
                                $op_text = $smt['Name'];
                                if($party_name == $op_val){
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
            <div class="col-sm-2">
            	<label>&nbsp;</label>
                <p class="text-center"><button type="submit" class="btn btn-primary" name="fileter_samiti" value="fileter samiti">Filter</button></p>
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