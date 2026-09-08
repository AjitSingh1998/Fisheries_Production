<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
	<div class="row">
    	 <div class="col-sm-6 col-sm-offset-3">
            <div class="panel panel-white" id="panel-container">
                <div class="panel-heading border-light light-bg">
                    <h3 class="text-center">
                        <a href="<?=site_url('database/backup')?>" class="btn btn-default"> <i class="fa fa-arrow-left"></i> Back</a>
                        <?=$page_title?>
                    </h3>
                </div>
                <?php echo form_open(site_url('database/export'), 'method="POST"');?>
                <div class="panel-body">
                
                    <div class="form-group">
                        <?php echo validation_errors(); ?>
                        <label class="control-label"> Select Database to have backup</label>
                        <select name="dbname" class="form-control">
                            <option value="">DB Name</option>
                            <?php
                            if(isset($allDatabase) && !empty($allDatabase)){
                                foreach($allDatabase as $db){
									$selected = ($db['backup_dbname'] == set_value('dbname')) ? 'selected="selected"' : '';
									
                                    echo '<option '.$selected.' value="'.$db['backup_dbname'].'">'.$db['display_name'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"> Backup Name </label>
                        <input type="text" name="backup_name" class="form-control" value="<?=set_value('backup_name')?>">
                    </div>
                </div>
                <div class="panel-foot border-light light-bg padding-10">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" name="export_db" class="btn btn-primary">Start Backup</button>
                        </div>
                    </div>
                </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>