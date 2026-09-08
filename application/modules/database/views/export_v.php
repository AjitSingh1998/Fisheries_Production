<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
    <div class="panel panel-white" id="panel-container">
    	<div class="panel-heading border-light light-bg">
        	<h3 class="text-center">
				<a href="<?=site_url('database/backup')?>" class="btn btn-sm btn-primary"> <i class="fa fa-arrow-left"></i> Back</a>
                &nbsp; &nbsp; &nbsp; 
				<?=$page_title?>
                <input type="hidden" id="database_backup_id" name="backup_id" value="<?=@$backup_id;?>">
            </h3>
        </div>
        <div class="panel-body">
        	<div class="row">
            	<div class="col-sm-12" id="response_container">
                	<table class="table" id="tablelist">
                    	<thead>
                        	<tr>
                            	<th style="width:10%">Table Name</th>
                                <th style="width:10%">Status</th>
                                <th style="width:10%">Action</th>
                                <th style="width:40%">Response</th>
                            </tr>
                        </thead>
                        <tbody>
                	<?php
                    	if(isset($db_tables) && !empty($db_tables)){
							foreach($db_tables as $table){
								$status = '<span class="text-danger"><i class="fa fa-times"></i></span>';
								$action	= '<button type="button" class="btn btn-sm btn-primary save_backup" data-table="'.$table.'" data-bid="'.@$backup_id.'" data-url="'.$save_backup_url.'">Backup</button>';
								echo '<tr id="'.$table.'">
										<td style="width:10%" class="table">'.$table.'</th>
										<td style="width:10%" class="status">'.$status.'</td>
										<td style="width:10%" class="action">'.$action.'</td>
										<td style="width:40%" class="response"></td>
									 </tr>';
							}						
						}					
					?>
                		</tbody>
                	</table>    
                </div>
			</div>
        </div>
        <div class="panel-foot border-light light-bg padding-10">
        </div>
    </div>
</div>