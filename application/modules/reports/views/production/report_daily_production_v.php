<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
    <div class="panel panel-white" id="report_panel">
        <div class="panel-heading border-light">
            <h3>Daily Production Report</h3>
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
                        <button type="button" class="btn btn-primary find_report" data-url="reports/production/ajax_daily"> <i class="fa fa-search"></i> Find</button>&nbsp;
						<button type="button" id="export_data" data-target="tbl-export" class="btn btn-warning"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                        <a id="dlink" style="display:none;"></a>
                        <button type="button" class="btn btn-success print_data"> <i class="fa fa-print"></i> Print</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered" id="tbl-export">
                <thead>
                    <tr>
                        <th colspan="4" style="text-align:center; font-size: 20px"> SIMRAN FISHERIES PRIVATE LIMITED, PUNASA </th>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align:left; font-size: 20px">Daily Production Report </th>
                        <th colspan="2" style="text-align:right; font-size: 20px">Date : <span id="search_date"></span></th>
                    </tr>
                    <tr>
                        <th width="25%"> </th>
                        <th width="25%">P2</th>
                        <th width="25%">Fishing Charge</th>
                        <th width="25%">Total Fishing Charge</th>
                    </tr>
                </thead>
                
                <tbody id="report_data"></tbody>
                
                <tbody id="report_footer">
                	<tr>
                        <td>Summary</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer border-light"> </div>
    </div>
</div>
