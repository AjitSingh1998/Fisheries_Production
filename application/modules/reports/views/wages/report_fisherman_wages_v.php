<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="report_panel">
    <div class="panel-heading border-light">
      <h3>Samiti wise weekly wages report with account</h3>
      <div class="row">
        <div class="col-sm-12 ajax-response"></div>
        <div class="col-md-2">
          <label>From Date</label>
          <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="from_date" autocomplete="off">
          </div>
        </div>
        <div class="col-md-2">
          <label>To Date</label>
          <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="to_date" autocomplete="off">
          </div>
        </div>
        <div class="col-md-2">
          <label class="control-label">Group Type :</label>
          <select class="form-control group_type select2" name="group_type">
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
        <div class="col-md-2">
          <label class="control-label">MainGroup :</label>
          <select class="form-control maingroup select2" name="maingroup">
            <option value="">All</option>
            <?php
			if(!empty($maingroup)){
				foreach($maingroup as $group){
					$op_val = $group['ID'];
					$op_text = $group['Name'];
			?>
            <option value="<?=$op_val?>"><?=$op_text?></option>
            <?php }} ?>
          </select>
        </div>
        
        <div class="col-md-4">
          	<label>&nbsp; </label>
            <div class="input-group">
                <button type="button" class="btn btn-primary find_report" data-url="reports/wages/ajax_fisherman_wages"> <i class="fa fa-search"></i> Find</button>&nbsp;
                <button type="button" id="export_data" data-target="table_export" class="btn btn-warning"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                <a id="dlink" style="display:none;"></a>
                <button type="button" class="btn btn-success print_data"> <i class="fa fa-print"></i> Print</button>
            </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
    	<div class="table-responsive">
          <table id="table_export" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th id="maingroup_name" style="text-align:left; font-size: 20px"></th>
              </tr>
              <tr>
                <th rowspan="2" width="10%">Fisherman Name </th>
                <th rowspan="2" width="5%">Code </th>
                <th rowspan="2" width="5%" class="no-print">IFSC Code </th>
                <th rowspan="2" width="5%" class="no-print">Account No.</th>
                <th style="text-align:center;" id="search_date" colspan="0"></th>
                <th rowspan="2" width="5%">Weight</th>
                <th rowspan="2" width="5%">Rate</th>
                <th rowspan="2" width="5%">Total Amount</th>
                <th rowspan="2" width="5%">Defered wages (rs. 4 per kg.)</th>
                <th rowspan="2" width="5%">Deduction (boat / jaal)</th>
                <th rowspan="2" width="5%">Deduction (RASHAN)</th>
                <th rowspan="2" width="5%">Net Payble</th>
                <th rowspan="2" width="5%">Signature</th>
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
