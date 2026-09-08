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
                <div class="col-md-2">
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
                        <button type="button" class="btn btn-primary find_report" data-url="<?=$ajax_url?>"> <i class="fa fa-search"></i> Find</button>&nbsp;
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
                        <th width="15%">Samiti </th>
                        <th width="10%">Opening Balance</th>
                        <th width="10%">Total Outward</th>
                        <th width="15%">Cash Received</th>
                        <th width="20%">Outward Deduction</th>
                        <th width="20%">Advance Wages</th>
                        <th width="20%">Wages Deduction</th>
                        <th width="20%">Total Pending</th>
                    </tr>
                </thead>
                <tbody id="report_data"></tbody>
                <tbody id="report_footer"></tbody>
            </table>
        </div>
        <div class="panel-footer border-light"> </div>
    </div>
</div>
