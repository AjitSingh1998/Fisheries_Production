<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div class="Profile">

  <div class="margin-top-40 margin-bottom-40">
        <div class="container">
         <div class="container-fluid">
         	<div class="panel panel-white">
         		<div class="panel-body">
                    <div class="display-flex"> 
                        <div class="col-sm-3 light-gray-bg left nopadding">
                            <?php $this->load->view('account/profile_navigation_v')?>
                        </div>
                        
                        <!------------------------- RIGHT FILTER RESULT------------------------- -->
                        
                        <div class="col-sm-9 border-left right-side">
                            <div class="panel margin-bottom-0">
                                <div class="panel-heading text-center">
                                   <h3 class="title">Change Your Password</h3>
                                   <p>Add information about yourself to share on your profile. </p>
                                </div>
                                <div class="row border-bottom clear"></div>
                                <div class="row margin-top-20 margin-bottom-20">
                               	 <div class="col-sm-10 col-sm-offset-2">
									<?php echo form_open('account/change_password','class="form-horizontal" id="change-password"')?>
                                        <div class="row">                                        
                                            <div class="col-md-12">
                                                <?php echo validation_errors();?>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">														
                                                    <label class="col-sm-3 control-label">Old Password <span class="symbol required"></span></label>
                                                    <div class="col-md-6">
                                                        <input type="password" class="form-control" name="oldpass" value="">
                                                    </div>
                                                </div>
                                                <div class="form-group">														
                                                    <label class="col-sm-3 control-label">New Password <span class="symbol required"></span></label>
                                                    <div class="col-md-6">
                                                        <input type="password" class="form-control" name="newpass" id="newpass" value="">
                                                    </div>
                                                </div>	
                                                <div class="form-group">														
                                                    <label class="col-sm-3 control-label">Confirm Password <span class="symbol required"></span></label>
                                                    <div class="col-md-6">
                                                        <input type="password" class="form-control" name="newpassconf" value="">
                                                    </div>
                                                </div>
                                            </div>												
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-3 control-label"></label>
                                            <div class="col-md-6">
                                                <button class="btn btn-primary" type="submit">
                                                    Update password <i class="fa fa-arrow-circle-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php echo form_close()?>
                                 </div>
                                </div>
                            </div>
                    </div>
                    </div>
        	    </div>
            </div> 
		</div>
  </div>
</div>

