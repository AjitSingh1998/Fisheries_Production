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
                    <h3 class="text-center">
                        <a href="<?=$go_back_url?>" data-toggle="tooltip" data-title="Go Back"><i class="fa fa-arrow-circle-left"></i></a>
                        <?=$page_title?>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2" id="response_container">
                        <?php echo validation_errors();?>
                        </div>
                    </div>
                    <div class="row text-center">
                        <?=form_open('database/assign_database', 'class="form-horizontal"')?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="admin_name">Admin Name</label>
                            <div class="col-sm-5">                    	
                                <select class="form-control" name="admin_name" id="admin_name">
                                    <option value="">Select Admin</option>
                                    <?php 
                                    if(isset($admin_list) && !empty($admin_list)){
                                        foreach($admin_list as $admin){
                                            $selected = '';
                                            if($admin['admin_id'] == set_value('admin_name')){
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option '.$selected.' value="'.$admin['admin_id'].'">'.$admin['first_name'].' '.$admin['last_name'].'</option>';
                                        }
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="display_name">Database Name</label>
                            <div class="col-sm-5">                    	
                                <select class="form-control" name="db_name">
                                    <option value="">Select DB</option>
                                    <?php 
                                    if(isset($db_list) && !empty($db_list)){
                                        foreach($db_list as $db){
                                            $selected = '';
                                            if($db['backup_id'] == set_value('db_name')){
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option '.$selected.' value="'.$db['backup_id'].'">'.$db['display_name'].'</option>';
                                        }
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success btn-xl">Set Database</button>
                        </div>
                        <?=form_close()?>
                    </div>
                </div>
                <div class="panel-foot border-light light-bg padding-10">
                    
                </div>
            </div>
        </div>
	</div>
</div>