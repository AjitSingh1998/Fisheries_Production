<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="report_panel">
    <div class="panel-heading border-light">
      <h3>Wages Fisherman Report</h3>
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
        <div class="col-md-3">
            <label class="control-label">Maingroup :</label>
            <select class="form-control select2" name="maingroup">
              <option value="">Select Maingroup</option>
              <?php
                if(!empty($all_maingroup)){
                    foreach($all_maingroup as $smt){
                        $op_val = $smt['ID'];
                        $op_text = $smt['Name'];
                ?>
              <option value="<?=$op_val?>"><?=$op_text?></option>
              <?php }} ?>
            </select>
        </div>
        <div class="col-md-4">
          	<label>&nbsp; </label>
            <div class="input-group">
                <button type="button" class="btn btn-primary find_report" data-url="reports/ajax_wages_fisherman"> <i class="fa fa-search"></i> Find</button>&nbsp;
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
                <th rowspan="2" width="10%">Fisherman Name </th>
                <th rowspan="2" width="10%">Bank Name / Branch </th>
                <th rowspan="2" width="10%">IFSC </th>
                <th rowspan="2" width="10%">A/C No. </th>
                <th style="text-align:center;" class="colm"></th>
                <th rowspan="2" width="10%">Total Amount</th>
                <th rowspan="2" width="10%">Defered wages (rs. 4 per kg.)</th>
                <th rowspan="2" width="10%">Deduction (boat / jaal)</th>
                <th rowspan="2" width="10%">Deduction (RASHAN)</th>
                <th rowspan="2" width="10%">Net Payble</th>
              </tr>
              <tr class="colm1"> </tr>
            </thead>
        	<tbody id="report_data"></tbody>
        </table>
        </div>
    </div>
    <div class="panel-footer border-light"> </div>
  </div>
</div>
