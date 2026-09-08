<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
    <div class="panel panel-white" id="report_panel">
        <div class="panel-heading border-light">
            <h3>Monthly Production Report</h3>
            <div class="row">
            	<div class="col-sm-12 ajax-response"></div>
                <div class="col-md-2">
                    <label>Select Month</label>
                    <div class="input-group">
                    	<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        <input type="text" class="form-control monthpicker" placeholder="mm-yyyy" name="month" autocomplete="off">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label>&nbsp; </label>
                    <div class="input-group">
                        <button type="button" class="btn btn-primary find_report" data-url="reports/production/ajax_monthly"> <i class="fa fa-search"></i> Find</button>&nbsp;
                        <button type="button" id="export_data" data-target="data-table-grid" class="btn btn-warning"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                        <a id="dlink" style="display:none;"></a>
                        <button type="button" class="btn btn-success print_data"> <i class="fa fa-print"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered" id="data-table-grid">
              <thead>
                <tr>
                  <th colspan="<?=$colspan?>" style="text-align:center; font-size: 20px">Production Report </th>
                </tr>
                <?=$thead?>
              </thead>
              <tbody id="report_data"></tbody>
              <tbody id="report_footer"></tbody>
            </table>
            
        </div>
        <div class="panel-footer border-light"> </div>
    </div>
</div>
