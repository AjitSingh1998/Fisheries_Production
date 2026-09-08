<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
	<div class="row flex">
    	<div class="col-sm-3">
        	<?php $this->load->view('database/yearly_backup_navigation_v');?>
        </div>
        <div class="col-sm-9">
            <div class="panel panel-white" id="panel-container">
                <div class="panel-heading border-light light-bg">
                    <h3 class="text-center"><?=$page_title?></h3>
                    <p class="text-center">
                        Data will be detele from this table 
                        <span class="text-danger"><?=@$db_name?></span> 
                        Please cross check before proceed this process 
                    </p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12" id="response_container">
                            <table class="table" id="tablelist">
                                <thead>
                                    <tr>
                                        <th style="width:10%">Table Name</th>
                                        <th style="width:10%">Total Records</th>
                                        <th style="width:10%">Deleted Records</th>
                                        <th style="width:10%">Status</th>
                                        <th style="width:10%">Action</th>
                                        <th style="width:40%">Response</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php
                                if(isset($db_tables) && !empty($db_tables)){
                                    foreach($db_tables as $table){
										$total_records = $this->db->count_all_results($table);
                                        $status = '<span class="text-danger"><i class="fa fa-times"></i></span>';
                                        $action	= '<button type="button" class="btn btn-sm btn-primary delete_data" data-table="'.$table.'" data-bid="'.$backup_id.'" data-url="database/yearly_backup/ajax_delete_data">Delete</button>';
                                        echo '<tr id="'.$table.'">
                                                <td style="width:10%" class="table">'.$table.'</td>
                                                <td style="width:10%" class="total_records">'.$total_records.'</td>
                                                <td style="width:10%" class="deleted_records">0</td>
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
            </div>
        </div>
	</div>
</div>