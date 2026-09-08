<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid container-fullw">
    <div class="panel panel-white" id="report_panel">
    	<div class="panel-heading light-border">
        <h3 class="text-center"><?=$page_title?></h3>
        	<?php echo form_open(site_url('wages/samiti_wages_sheet'));?>
            	<div class="row">
                    <div class="col-sm-12 ajax-response"></div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                      <label>From Date:</label>
                      <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="from_date" >
                      </div>
                    </div>
                    <div class="col-md-2">
                      <label>To Date:</label>
                      <div class="input-group"> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" name="to_date" >
                      </div>
                    </div>
                    <div class="col-md-2">
                      <label class="control-label">Group Type :</label>
                      <select class="form-control group_type select2" name="group_type">
                        <option value="">All</option>
                        <?php
                        if(!empty($allgrouptype)){
                            foreach($allgrouptype as $group){
                                $op_val = $group['ID'];
                                $op_text = $group['Name'];
								$selected = '';
								if(@$group_type == $op_val) $selected = ' selected="selected"';
                        ?>
                        <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
                        <?php }} ?>
                      </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label>Sheet For:</label>
                        <div class="input-group">
                           <input type="text" class="form-control margin-right-10" name="serial_no" value="<?=@$serial_no?>" placeholder="Serial No" style="width: 85px !important;" />

                            <div class="radio clip-radio radio-primary radio-inline">
                                <input type="radio" id="axis" name="sheet_for" value="axis" checked="checked">
                                <label for="axis">Axis Bank</label>
                            </div>
                            <div class="radio clip-radio radio-primary radio-inline">
                                <input type="radio" id="icici" name="sheet_for" value="icici" >
                                <label for="icici">ICICI Bank</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-1">
                        <label>&nbsp; </label>
                        <div class="input-group">
                            <button type="button" name="filterdata" value="Search" class="btn btn-primary get_wages_sheet" data-url="<?=$data_url?>"> 
                            	<i class="fa fa-search"></i> Search </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="button" id="export_data" data-target="data-table-grid" class="btn btn-warning" data-toggle="tooltip" data-title="Download Excel">
                        	<i class="fa fa-file-excel-o"></i> </button>&nbsp;
                        <a id="dlink" style="display:none;"></a>
                        <button type="button" class="btn btn-success print_data" data-toggle="tooltip" data-title="Print"> <i class="fa fa-print"></i></button>
                	</div>
                </div>
                
            <?php echo form_close();?>
        </div>
        <div class="panel-body">
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="data-table-grid">
                    <thead id="sheet_head"></thead>
                    <tbody id="sheet_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
