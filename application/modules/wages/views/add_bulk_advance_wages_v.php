<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
    <div class="panel panel-white">
        <div class="panel-heading border-light light-bg">        	
            <div class="row">
                <div class="col-sm-2">
                	<a href="<?=site_url('wages/advance_wage')?>" class="btn btn-primary close-dailytollwindow">Back to Advance Wages</a>
                </div> 
                <div class="col-sm-4">
                	<h3 class="text-center">Add Bulk Advance Wages</h3>
                </div>
                 <div class="col-sm-2">
                	<input type="text" name="common_date" class="form-control datepicker" placeholder="dd/mm/yyyy" value="" >
                </div>  
                <div class="col-sm-4">
                	<textarea name="common_remark" class="form-control" placeholder="Common Remark"></textarea>
                </div>             
            </div>            
        </div>
        <div class="panel-body">
            <table class="table table-striped " width="100%">                
                <tbody id="advance_wages">
                	
                </tbody>
            </table>
            
            <table class="asset hidden">
                <tr id="row_asset" class="hidden editable text-center">
                    <td class="text-center">
                    	<button type="button" class="btn btn-danger btn-xs delete_row" data-advid="" title="Delete Row"><i class="fa fa-times"></i></button>
                        <input type="hidden" name="f_group" class="f_group" value="" />
                        <input type="hidden" name="f_maingroup" class="f_maingroup" value="" />
                        <input type="hidden" name="fisherman_id" class="fisherman_id" value="0" />
                        <input type="hidden" name="f_name" class="f_name" value="" />
						<input type="hidden" name="action" class="action" value="add" />
                    </td>
                    <td><span class="fisherman_name"></span></td>                    
                    <td><input type="text" name="amount" class="form-control" placeholder="amount" value="" ></td>
                    <td><input type="text" name="date" class="form-control datepicker" placeholder="dd/mm/yyyy" value="" ></td>
                    <td><textarea row="2" name="remark" class="form-control"></textarea></td>
                    <td class="success"><button type="button" class="btn btn-success btn-xs save_row" title="Save Row">Save</button></td>
                </tr>
            </table>
            
            <!-- end: WIZARD FORM --> 
        </div>
        <div class="panel-footer border-light light-bg">
            <form class="form-horizontal">
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="form-group form-group-lg">
                            <label class="control-label col-sm-4 text-right">Select Fisherman</label>
                            <div class="col-sm-6">
                                <select class="form-control select-fisherman"></select>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-primary btn-lg add_fisherman"><i class="fa fa-plus"></i> Add</button>
                            </div>
                        </div>
                    </div>
                </div>
			</form>
		</div>
    </div>
</div>
