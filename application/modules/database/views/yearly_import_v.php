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
                    <input type="hidden" id="database_backup_id" value="<?=@$backup_id?>">
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12" id="response_container">
                        	
                            <p class="text-center">
                            	<strong>
                                	Import database manually from 
                                    <span class="text-danger"><?=$default_db?></span> 
                                    To <span class="text-danger"><?=@$db_tables[0]['backup_dbname']?></span>
                                    and after completion import click on following button to proceed next step!
                                </strong>
                            </p>
                            <p class="text-center">
                            	<a href="<?=site_url('database/yearly_backup/fisherman_balance/'.$backup_id)?>" class="btn btn-primary"> Process Next Step</a>
                            </p>
                            
                            <?php /*?><table class="table" id="tablelist">
                                <thead>
                                    <tr>
                                        <th style="width:10%">Table Name</th>
                                        <th style="width:10%">Total Records</th>
                                        <th style="width:10%">Imported Records</th>
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
                                        $action	= '<button type="button" class="btn btn-sm btn-primary import_backup" data-table="'.$table['table_name'].'" data-bid="'.@$backup_id.'" data-url="database/yearly_backup/import_data">Import</button>';
                                        echo '<tr id="'.$table['table_name'].'">
                                                <td style="width:10%" class="table">'.$table['table_name'].'</td>
                                                <td style="width:10%" class="total_records">'.@$table['total_records'].'</td>
                                                <td style="width:10%" class="imported_records">0</td>
                                                <td style="width:10%" class="status">'.$status.'</td>
                                                <td style="width:10%" class="action">'.$action.'</td>
                                                <td style="width:40%" class="response">
													<span class="text-message"></span>
													<span class="loader"></span>
												</td>
                                             </tr>';
                                    }						
                                }					
                            ?>
                                </tbody>
                            </table><?php */?>    
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>