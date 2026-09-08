<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="report_panel">
    <div class="panel-heading border-light">
      <h3><?=$page_title?></h3>
      <div class="row">
        <div class="col-sm-12 ajax-response"></div>
        <div class="col-md-2">
          <label>From Month</label>
          <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="text" class="form-control monthpicker" placeholder="mm-yyyy" name="from_date" autocomplete="off">
          </div>
        </div>
        
        <div class="col-md-2">
          <label>To Month</label>
          <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="text" class="form-control monthpicker" placeholder="mm-yyyy" name="to_date" autocomplete="off">
          </div>
        </div>
        
        <div class="col-md-3">
          <label class="control-label">Group Type :</label>
          <select class="form-control select2" name="group_type">
            <option value="">All</option>
            <?php
			if(!empty($group_type)){
				foreach($group_type as $group){
					$op_val = $group['ID'];
					$op_text = $group['Name'];
			?>
            <option value="<?=$op_val?>"><?=$op_text?></option>
            <?php }} ?>
          </select>
        </div>
        
        <div class="col-md-3">
          	<label>&nbsp; </label>
            <div class="input-group">
                <button type="button" class="btn btn-primary find_report" data-url="<?=$data_url?>"> <i class="fa fa-search"></i> Find</button>&nbsp;
                <button type="button" id="export_data" data-target="data-table-grid" class="btn btn-warning"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                <a id="dlink" style="display:none;"></a>
                <button type="button" class="btn btn-success print_data"> <i class="fa fa-print"></i> Print</button>
            </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
          <div class="table-responsive">
          <table class="table table-striped table-bordered" id="data-table-grid">
            <thead>            
              <tr>
                <th rowspan="2" width="1%">S.No.</th>
                <th rowspan="2" width="10%">Samiti Name </th>
                <th style="text-align:center;" id="search_date" colspan="0"></th>
                <th rowspan="2" width="10%">Total Amount</th>
                <th rowspan="2" width="10%">Defered wages (rs. 4 per kg.)</th>
                <th rowspan="2" width="10%">Deduction (boat / jaal)</th>
                <th rowspan="2" width="10%">Deduction (RASHAN)</th>
                <th rowspan="2" width="10%">Net Payble</th>
              </tr>
              <tr id="search_key"></tr>
            </thead>
            <tbody id="report_data"></tbody>
            <tbody id="report_footer"></tbody>
          </table>
          </div>
    </div>
    <div class="panel-footer border-light"> </div>
  </div>
</div>
