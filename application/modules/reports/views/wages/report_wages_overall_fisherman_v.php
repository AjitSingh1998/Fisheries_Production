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
                    <th colspan="6" id="search_key"></th>
                    <th class="text-right" colspan="5" id="search_date"></th>
                  </tr>
                  <tr>
                    <th>Fisherman Name </th>
                    <th>Major Wt</th>
                    <th>Minor Wt</th>
                    <th>Sawal Wt</th>
                    <th>Total Weight</th>
                    <th>Total Amount</th>
                    <th>Present Day</th>
                    <th>Opening Balance</th>
                    <th>Advance Wages</th>
                    <th>Cash Deposit</th>
                    <th>Net Pending</th>
                  </tr>
                </thead>
                <tbody id="report_data"></tbody>
                <tbody id="report_footer"></tbody>
            </table>
    	</div>
    </div>
    <div class="panel-footer border-light"> </div>
  </div>
</div>

<div id="daily-toll-info" class="modal fade modal-aside vertical bottom bs-example-modal-bottom" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <h4 class="modal-title text-center"></h4>
      </div>
      <div class="modal-body" id="asset-modal-body">
        <div class="text-center margin-top-50"> <img src="<?=base_url('assets/images/loading.gif')?>" class="img img-responsive" /> </div>
      </div>
      
    </div>
  </div>
</div>

