<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<style>
/* hack for print */
@media print {
   a[href]:after {
      visibility: hidden;
   }
}
</style>
<?php
$wages_id = @$wages_data['ID'];
$disabled = '';
$disabled_for_xp = 'disabled="disabled"'; // for export and print button
if(!empty($wages_id)){$disabled = 'disabled="disabled"'; $disabled_for_xp = '';}
$from_date = (@$wages_data['from_date'] != '') ? get_date('d/m/Y', @$wages_data['from_date']) : '';
$to_date = (@$wages_data['to_date'] != '') ? get_date('d/m/Y', @$wages_data['to_date']) : '';
$MainGroup = @$wages_data['MainGroup'];
?>

<div class="container-fluid container-fullw">
  <div class="panel panel-white" id="loader">
    <div class="panel-heading border-light light-bg">
      <h3 class="text-center"><?=$page_title?></h3>
      <div class="ajax-response"></div>
      <div class="row">
        <div class="col-md-2">
            <label>&nbsp; </label>
            <div class="input-group">
            	<a href="<?=site_url('wages/wages_sheet')?>" class="btn btn-primary close-dailytollwindow"><i class="fa fa-arrow-left"></i> Back to Wages Sheet</a> 
            </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group fdate_grp">
            <label>From Date : </label>
            <input type="hidden" name="wages_id" class="wages_id" value="<?=$wages_id?>" />
            <input type="text" class="form-control datepicker" name="from_date" id="from_date" value="<?=$from_date?>" <?=$disabled?>>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group tdate_grp">
            <label>To Date : </label>
            <input type="text" class="form-control datepicker"  name="to_date" id="to_date" value="<?=$to_date?>" <?=$disabled?>>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <label> Maingroup </label>
            <select id="maingroup" name="maingroup" class="form-control maingroup" <?=$disabled?>>
              <option value="">Select Maingroup</option>
              <?php
				$maingroup_value = $MainGroup;
				if(!empty($all_maingroup)){
					$selected = '';
					foreach($all_maingroup as $option){
						$op_val = $option['ID'];
						$op_text = $option['Name'];
					
					if($maingroup_value == $op_val){
						$selected = 'selected="selected"';
					}else{
						$selected = '';
					}
			  ?>
              <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
              <?php }} ?>
            </select>
          </div>
        </div>
        
        <div class="col-md-4">
            <label>&nbsp; </label>
            <div class="input-group">
                <button type="button" data-pid="table#wages_sheet" data-fname="Wages Sheet" class="btn btn-warning export_data" <?=$disabled_for_xp?>> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                <button type="button" data-pid="table#wages_sheet" class="btn btn-success print_data" <?=$disabled_for_xp?>> <i class="fa fa-print"></i> Print</button>
            </div>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <table class="table table-bordered table-striped" id="wages_sheet" width="130%">
        <thead>
          <tr>
            <th><span data-toggle="tooltip" title="Fisherman ID">F.ID</span></th>
            <th><span data-toggle="tooltip" title="Fisherman Name">Fisherman</span></th>
            <th><span data-toggle="tooltip" title="Account Number">Acc No</span></th>
            <th><span data-toggle="tooltip" title="Total Major (Weight)">TMj Wt</span></th>
            <th><span data-toggle="tooltip" title="Total Minor (Weight)">TMn Wt</span></th>
            <th><span data-toggle="tooltip" title="Total Sawal (Weight)">TSw Wt</span></th>
            <th><span data-toggle="tooltip" title="Total Wages (Weight x Fee)">Total Wages</span></th>
            <th><span data-toggle="tooltip" title="Government Charges">GC</span></th>
            <th><span data-toggle="tooltip" title="Group Liability">GL</span></th>
            <th><span data-toggle="tooltip" title="Advance Wages Liability">AdL</span></th>
            <th><span data-toggle="tooltip" title="Group Liability Deduction">GLD</span></th>
            <th><span data-toggle="tooltip" title="Advance Liability Deduction">ALD</span></th>
            <th><span data-toggle="tooltip" title="Net Wages Amount">NWA</span></th>
          </tr>
        </thead>
        <tbody id="wages_list">
          <?php echo $wage_items;?>
        </tbody>
      </table>
      <!-- end: WIZARD FORM --> 
    </div>
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