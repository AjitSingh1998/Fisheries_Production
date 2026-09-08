<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
    <div class="panel panel-white">
    	<div class="panel-heading border-light light-bg">
        	<h3 class="text-center">
				<?=$page_title?>
            </h3>
        </div>
        <div class="panel-body">
        	<div class="row">
            	<div class="col-sm-8 col-sm-offset-2" id="response_container">
                <?php echo validation_errors();?>
                </div>
			</div>
            <div class="row">
              <div class="col-md-1"></div>
                <div class="col-md-10">
                  <h2 class="alert alert-info text-center" id="process_bar">Please wait database backup is in process</h2>
                  <div class="alert alert-danger text-center" id="process_bar">Please do not click back button or refresh this page.</div>
                  <input type="hidden" id="new_db_name" name="new_db_name" value="<?=$new_db_name?>">
                  <table class="table table-hover" id="sample-table-1">
                    <thead>
                      <tr>
                        <th class="text-center" style="width:10%;">Table</th>
                        <th class="text-center" style="width:10%;">Actual Records</th>
                        <th class="text-center" style="width:10%;">Records Backup</th>
                        <th class="text-center" style="width:10%;">Status</th>
                        <th class="text-center" style="width:50%;">Response</th>
                      </tr>
                    </thead>
                    <tbody id="all_tables">
                      <?php
					  	if(!empty($db_tables)){
							foreach($db_tables as $row){
					  ?>
                      <tr id="<?=$row['table_name']?>">
                        <td class="text-center"><?=$row['table_name']?></td>
                        <td class="text-center actual_record"><?=$row['table_rows']?></td>
                        <td class="text-center records_backup"></td>
                        <td class="text-center status">Incomplete <i class="glyphicon glyphicon-remove-circle"></i></td>
                         <td class="response"></td>
                      </tr>
                      <?php }} ?>
                    </tbody>
                  </table>
                </div>
              <div class="col-md-1"></div>
            </div>
        </div>
        <div class="panel-foot border-light light-bg padding-10">
        	
        </div>
    </div>
</div>