<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="report_panel">
    <div class="panel-heading light-border">
      <h3 class="text-center">
        <?=$page_title?>
      </h3>
      <?php echo form_open(site_url('setup/change_toll_rate'),'class="change_rate_form" id="change_rate_form"');?>
      <div class="row">
        <div class="col-sm-12 ajax-response"></div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <label>Change For:</label>
          <div class="input-group">
            <div class="radio clip-radio radio-primary radio-inline">
              <input type="radio" id="for_fisher" name="change_for" value="Fisherman" checked="checked">
              <label for="for_fisher">Fisherman</label>
            </div>
            <div class="radio clip-radio radio-primary radio-inline">
              <input type="radio" id="for_group" name="change_for" value="Group">
              <label for="for_group">Group</label>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-2">
          <label class="control-label">Group Type : <span class="symbol required"></span></label>
          <select class="form-control group_type select2" name="group_type">
            <option value="">Select Group</option>
            <?php
			if(!empty($allgrouptype)){
				foreach($allgrouptype as $group){
					$op_val = $group['ID'];
					$op_text = $group['Name'];
					$selected = '';
					if(@$group_type == $op_val) $selected = ' selected="selected"';
			?>
            <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
            <?php }} ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="control-label">Main Group : <span class="symbol required"></span></label>
          <select class="form-control maingroup_id" id="maingroup_id" name="maingroup_id">
            <option value="">Select Maingroup</option>
          </select>
        </div>
        <div class="form-group col-md-3 fisher_div">
          <label class="control-label">Fisherman : <span class="symbol required"></span></label>
          <select class="form-control fisherman_id" id="fisherman_id" name="fisherman_id">
            <option value="">Select Fisherman</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-2">
            <label class="control-label">Major Rate :</label>
            <input type="text" class="form-control strict_numeric" onkeyup="checkDecimal(this);" name="major_rate" >
        </div>
        <div class="form-group col-md-2">
            <label class="control-label">Minor Rate :</label>
            <input type="text" class="form-control strict_numeric" onkeyup="checkDecimal(this);" name="minor_rate" >
        </div>
        <div class="form-group col-md-2">
            <label class="control-label">Sawal Rate :</label>
            <input type="text" class="form-control strict_numeric" onkeyup="checkDecimal(this);" name="sawal_rate" >
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-2">
          <label class="control-label">From Date:</label>
          <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="from_date" >
          </div>
        </div>
        <div class="form-group col-md-2">
          <label class="control-label">To Date:</label>
          <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="to_date" >
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-center">
          <button type="button" class="btn btn-primary rate_btn" data-url="<?=$data_url?>">Change Rate</button>
        </div>
      </div>
      <?php echo form_close();?> </div>
  </div>
</div>
