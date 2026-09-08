<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="report_panel">
    <div class="panel-heading border-light">
      <h3><?=$page_title?></h3>
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
          <select class="form-control select2 group_type" name="group_type">
            <option value="">Select Group</option>
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
        
        <div class="col-md-4">
            <label class="control-label">Maingroup :</label>
            <select multiple="multiple" class="form-control maingroup select2" name="maingroup[]">
              <?php
              /*
                if(!empty($all_maingroup)){
                    foreach($all_maingroup as $smt){
                        $op_val = $smt['ID'];
                        $op_text = $smt['Name'];
                ?>
              <option value="<?=$op_val?>"><?=$op_text?></option>
              <?php }}
			  */
			  ?>
            </select>
        </div>
        <div class="col-md-2">
          	<label>&nbsp; </label>
            <div class="input-group">
                <button type="button" class="btn btn-primary find_report" data-url="<?=$data_url?>"> <i class="fa fa-search"></i> Find</button>&nbsp;
                <button type="button" id="export_data" data-target="data-table-grid" class="btn btn-warning" data-toggle="tooltip" data-title="Download Excel"> <i class="fa fa-file-excel-o"></i> </button>&nbsp;
                <a id="dlink" style="display:none;"></a>
                <button type="button" class="btn btn-success print_data" data-toggle="tooltip" data-title="Print"> <i class="fa fa-print"></i> </button>
            </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered" id="data-table-grid">
            <thead>
              <tr>
              	<th colspan="7" id="search_key"></th>
                <th class="text-right" colspan="7" id="search_date"></th>
              </tr>
              <tr>
                <th>Samiti Name </th>
                <th>Major Wt</th>
                <th>Minor Wt</th>
                <th>Sawal Wt</th>
                <th>Total Weight</th>
                <th>Total Amount</th>
                <th>Present Day</th>
                <th>Opening Balance</th>
                <th>Total Outward</th>
                <th>Cash Deposit</th>
                <th>Outward Deduction</th>
                <th>Advance Wages</th>
                <th>Wages Deduction</th>
                <th>Net Pending</th>
              </tr>
            </thead>
        	<tbody id="report_data"></tbody>
            <tbody id="report_footer"></tbody>
        </table>
    </div>
    <div class="panel-footer border-light"> </div>
  </div>
</div>
