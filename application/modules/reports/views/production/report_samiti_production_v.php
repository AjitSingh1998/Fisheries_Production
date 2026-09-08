<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="report_panel">
    <div class="panel-heading border-light">
      <h3>Samiti Production Report</h3>
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
          <label>Search</label>
          <div class="input-group">
            <button type="button" class="btn btn-primary find_report" data-url="reports/production/ajax_samiti">
            <i class="fa fa-search"></i> Find</button>&nbsp;
            <?php /*?><button type="button" class="btn btn-warning export_data" data-target="#report_data"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;<?php */?>
            <button type="button" id="export_data" data-target="report_data" class="btn btn-warning"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
            <button type="button" class="btn btn-success print_data" data-target="#report_data"> <i class="fa fa-print"></i> Print</button>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <!--<div class="row">
      	<div class="col-sm-6"><h3>Samiti Production Report</h3></div>
      	<div class="col-sm-6"><h3>Date <span class="search_date"></span></h3></div>
      </div>-->
      <div class="row" id="report_data"></div>
      <a id="dlink" style="display:none;"></a>
    </div>
    <div class="panel-footer border-light"> </div>
  </div>
</div>
