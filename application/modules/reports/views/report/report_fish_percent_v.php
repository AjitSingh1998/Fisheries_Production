<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
    <div class="panel panel-white" id="report_panel">
        <div class="panel-heading border-light">
            <h3>Fish Percent</h3>
            <div class="row">
            	<div class="col-sm-12 ajax-response"></div>
                <div class="col-md-2">
                    <label>From Date</label>
                    <div class="input-group">
                    	<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="from_date" autocomplete="off">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label>To Date</label>
                    <div class="input-group">
                    	<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="to_date" autocomplete="off">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label>&nbsp; </label>
                    <div class="input-group">
                        <button type="button" class="btn btn-primary find_report" data-url="reports/ajax_fish_percent"> <i class="fa fa-search"></i> Find</button>&nbsp;
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
                        <th colspan="10" style="text-align:center; font-size: 20px">Percentage of fish</th>
                    </tr>
                    <tr>
                        <th colspan="10" style="text-align:left; font-size: 20px">Date : <span id="search_date"></span></th>
                    </tr>
                    <tr>
                        <th width="10%">Date </th>
                        <th width="10%">Catla</th>
                        <th width="10%">Rohu</th>
                        <th width="10%">Mragal</th>
                        <th width="10%">KaalBasu</th>
                        <th width="10%">Anya Karp</th>
                        <th width="10%">Sawal</th>
                        <th width="10%">Local Major</th>
                        <th width="10%">Local Minor</th>
                        <th width="10%">Total</th>
                    </tr>
                </thead>
                <tbody id="report_data"></tbody>
                <tbody id="report_footer"></tbody>
            </table>
        </div>
        <div class="panel-footer border-light"> </div>
    </div>
</div>
