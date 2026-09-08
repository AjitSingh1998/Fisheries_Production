<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="container-fluid container-fullw">
    <div class="panel panel-white" id="report_panel">
        <div class="panel-heading border-light">
            <h3>Report P1 with Fisherman Code</h3>
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
                    <label class="control-label">Maingroup :</label>
                    <select class="form-control select2" name="maingroup">
                      <option value="0">All</option>
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
                        <button type="button" class="btn btn-primary find_report" data-url="reports/ajax_p1_withcode"> <i class="fa fa-search"></i> Find</button>&nbsp;
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
                        <th colspan="22" style="text-align:center; font-size: 20px">म.प्र. मत्स्य महासंघ (सह.) मर्यादित वाण सागर जलाशय <br> दैनिक मत्स्य उत्पादन रिपोर्ट (सदस्यवार) </th>
                    </tr>
                    <tr>
                        <th colspan="11" style="text-align:left; font-size: 20px">Samiti Name : <span id="search_key"></span></th>
                        <th colspan="11" style="text-align:right; font-size: 20px">Date : <span id="search_date"></span></th>
                    </tr>
                    <tr>
                        <th rowspan="2" width="10%">Fisher Man </th>
                        <th rowspan="2" width="10%">Code </th>
						<th rowspan="2" width="10%">P.Code </th>
                        <th colspan="2" width="10%">Catla  :</th>
                        <th colspan="2" width="10%">Rohu  :</th>
                        <th colspan="2" width="10%">Mragal  :</th>
                        <th colspan="2" width="10%">KaalBasu  :</th>
                        <th colspan="2" width="10%">Anya Karp :</th>
                        <th colspan="2" width="10%">Sawal :</th>
                        <th colspan="2" width="10%">Local Major :</th>
                        <th rowspan="2" width="5%">Local Minor Wt. :</th>
                        <th colspan="2" width="15%">Total Major :</th>
                        <th colspan="2" width="15%">Total :</th>
                    </tr>
                    <tr>
                        <th width="5%">Qty. </th>
                        <th width="5%">Wt. </th>
                        <th width="5%">Qty. </th>
                        <th width="5%">Wt. </th>
                        <th width="5%">Qty. </th>
                        <th width="5%">Wt. </th>
                        <th width="5%">Qty. </th>
                        <th width="5%">Wt. </th>
                        <th width="5%">Qty. </th>
                        <th width="5%">Wt. </th>
                        <th width="5%">Qty. </th>
                        <th width="5%">Wt. </th>
                        <th width="5%">Qty. </th>
                        <th width="5%">Wt. </th>
                        <th width="7%">Qty. </th>
                        <th width="7%">Wt. </th>
                        <th width="7%">Qty. </th>
                        <th width="7%">Wt. </th>
                    </tr>
                </thead>
                <tbody id="report_data">
                    
                </tbody>
                <tbody>
                    <tr id="report_footer"></tr>
                    <tr>
                        <th colspan="7" style="text-align:center; font-size: 15px">हस्ताक्षर <br>
                            अध्यक्ष / समिति प्रतिनिधि </th>
                        <th colspan="7" style="text-align:center; font-size: 15px">हस्ताक्षर <br>
                            निविदाकार प्रतिनिधि </th>
                        <th colspan="8" style="text-align:center; font-size: 15px">हस्ताक्षर <br>
                            केंद्र प्रभारी </th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer border-light"> </div>
    </div>
</div>
