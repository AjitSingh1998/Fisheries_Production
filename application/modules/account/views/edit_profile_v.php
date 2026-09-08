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
                                   <h3 class="title">Profile</h3>
                                   <p>Add information about yourself to share on your profile. </p>
                                </div>
                                <div class="row border-bottom clear"></div>
                                <div class="row margin-top-20 margin-bottom-20">
                               	 <div class="col-sm-10 col-sm-offset-1">                                 	

									<?php
										echo validation_errors();
                                        $attr = array( 'class'=>'edit_profile', 'id'=>'edit_profile'); 
                                        echo form_open('account/edit_profile/', $attr);
                                    ?>
                                        <div class="form-group">
                                            <label>Full name:</label>
                                             <div class="row">
                                             <div class="col-sm-6">
                                             <input class="form-control input-lg" placeholder="Enter First name" name="first_name" value="<?=set_value('first_name',$userInfo['first_name'])?>" type="text">
                                             </div>
                                             <div class="col-sm-6">
                                            	 <input class="form-control input-lg" placeholder="Enter Last name" name="last_name" value="<?=set_value('last_name',$userInfo['last_name'])?>" type="text">
                                            </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                             <label>Contact number:</label>
                                             <div class="row">
                                             <div class="col-sm-6">
                                             <input class="form-control input-lg" placeholder="Primary contact number" name="phone_number" value="<?=set_value('phone_number',$userInfo['phone_number'])?>" type="number">
                                             </div>
                                             <div class="col-sm-6">
                                            	<input class="form-control input-lg" placeholder="Secondary contact number" name="phone_number2" value="<?=set_value('phone_number2',$userInfo['phone_number2'])?>" type="number">
                                            </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Address Information:</label>
                                             <div class="row">
                                                 <div class="col-sm-6">
                                                <input class="form-control input-lg" placeholder="State Name" name="state" value="<?=set_value('state',$userInfo['state'])?>" type="text">
                                               
                                                 </div>
                                                 <div class="col-sm-6">
                                                 <input class="form-control input-lg" placeholder="City Name" name="city" value="<?=set_value('city',$userInfo['city'])?>" type="text">                                                
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                             <div class="row">
                                                 <div class="col-sm-6">
                                              		<input class="form-control input-lg" placeholder="Enter Address" name="address" value="<?=set_value('address',$userInfo['address'])?>" type="text">
                                                 </div>
                                                 <div class="col-sm-6">
                                              		<input class="form-control input-lg" placeholder="Enter Zipcode/Pincode" name="postal_code" value="<?=set_value('postal_code',$userInfo['postal_code'])?>" type="number">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                             <div class="row">
                                                 <div class="col-sm-6">
                                              		<input class="form-control input-lg" placeholder="Primary email" name="email" value="<?=set_value('email',$userInfo['email'])?>" type="email">
                                                 </div>
                                                 <div class="col-sm-6">
                                                  	<label>Gender:</label>
                                                 	<label class="radio-inline">
                                                    	<?php $Male = ($userInfo['gender'] == 'Male') ? 'checked="checked"' : ''?>
										             	<input class="Xform-control" name="gender" value="Male" type="radio" <?=$Male?> > Male
													</label>
                                                 	<label class="radio-inline">
                                                    	<?php $Female = ($userInfo['gender'] == 'Female') ? 'checked="checked"' : ''?>
										             	<input class="Xform-control" name="gender" value="Female" type="radio" <?=$Female?> > Female
													</label>
                                                </div>
                                            </div>
                                        </div>
                                                                                
                                         <div class="border-bottom clear margin-bottom-10"></div>
                                         <button type="submit" class="btn btn-wide btn-success" name="submit" >
                                            Save
                                         </button>
                                    <?=form_close()?>
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
