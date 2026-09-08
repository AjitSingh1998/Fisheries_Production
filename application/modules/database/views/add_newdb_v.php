<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
	<div class="row flex">
    	<div class="col-sm-3">
        	<?php $this->load->view('database/yearly_backup_navigation_v');?>
        </div>
        <div class="col-sm-9">       
            <div class="panel panel-white">
                <div class="panel-heading border-light light-bg">
                    <h3 class="text-center"><?=$page_title?></h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2" id="response_container">
                        <?php echo validation_errors();?>
                        </div>
                    </div>
                    <div class="row text-center">
                        <?=form_open('database/yearly_backup/add_newdb', 'class="form-horizontal"')?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="new_db_name">New DB Name</label>
                            <div class="col-sm-5">
                                <input type="text" name="new_db_name" placeholder="punasa2_xyz" id="new_db_name" class="form-control">
                            </div>
                        </div>
						<div class="form-group">
                            <label class="col-sm-4 control-label" for="display_name">Yearly Backup Name</label>
                            <div class="col-sm-5">
                                <input type="text" name="display_name" placeholder="BT 2017-2018 Backup" id="display_name" class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-success btn-xl">Start Backup</button>
                            </div>
                        </div>
                        <?=form_close()?>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        	<?php echo $output;?>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>