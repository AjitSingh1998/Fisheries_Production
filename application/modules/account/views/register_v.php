<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-fullw padding-bottom-30 padding-top-70">

    <div class="container">

        <div class="row">  

             <div class="col-md-8 col-md-offset-2 " id="basic-register"> 

             		<?=validation_errors();?>

                	<?php 

					$message = $this->messageci->display();

					$this->session->set_flashdata('_messages', '');

					if (is_array($message)) { for($i=0; $i<count($message); $i++) echo $message[$i]; }

					?>

             </div>

       </div>    

        <div class="row" id="registerContainer">  

		<?php

            $attr = array('class'=>'basicinfo-form' ,  'id'=>'basicinfo-form'); 

            echo form_open('account/register', $attr );            

        ?> 

        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 text-center" id="box-register">                	

            <!-- start: REGISTER BOX -->				

                <div class="box-basicinfo">

                             

                <div class="errorHandler alert alert-danger no-display">

                    <i class="fa fa-remove-sign"></i> You have some form errors. Please check below.

                </div>   				

                <fieldset>
					<h3>Sign up for your account</h3>
             		   <p> Please enter details below. </p>    
                       <br />      
                    
                   <div class="inner-registration">
                        <div class="form-group">
                          <label> <strong>Select user group</strong> </label>
    
                          <select name="user_group" class="form-control">
    
                            <option value=""> Select    </option>
    
                            <?php									                              
    
                            $groups = array(  '6' => 'Coaching', '7' => 'Teacher' , '8' => 'Publisher' ); 
    
                            if(!empty($groups)){
    
                                foreach( $groups as $key => $group ){
    
                                    $selected = (!empty($user_group) && $user_group == $key ) ? 'selected="selected"' : ''; 
    
                                    echo '<option '.$selected.' value="'.$key.'"> '.$group.' </option>';											
    
                                }									
    
                            }									
    
                            ?>
    
                          </select> 
    
                        </div> 
    
                        <div class="form-group">
    
                            <span class="input-icon">
    
                            <input type="text" class="form-control" name="full_name" placeholder="Full name" value="<?=set_value('full_name')?>">
    
                                <i class="fa fa-user"></i> </span>
    
                        </div>                                                           
    
                        <div class="form-group">
    
                            <span class="input-icon">
    
                            <input type="text" class="form-control" name="email" placeholder="Email" value="<?=set_value('email')?>">
    
                            <i class="fa fa-envelope"></i> 
    
                            </span>
    
                        </div>
    
                        <div class="form-group form-actions">
    
                            <span class="input-icon">
    
                            <input type="number" class="form-control" name="phone" placeholder="Phone number" value="<?=set_value('phone')?>"> 
    
                            <i class="fa fa-phone"></i>
                             </span>                                       
    
                           
    
                        </div>  
    
                        <div class="form-group form-actions">
    
                            <span class="input-icon">
    
                            <input type="password" class="form-control" name="password" placeholder="Password" value="<?=set_value('password')?>"> 
    
                            <i class="fa fa-key"></i> </span>                                         
    
                        </div>                                
    
                        <div class="form-actions text-center">  
    
                            <button type="submit" class="btn btn-success">
    
                               Register <i class="fa fa-arrow-circle-right"></i>
    
                            </button>
    
                        </div>	
                   </div>					

                </fieldset>

                </div>

            <!-- end: REGISTER BOX -->

        </div>  

        <?=form_close()?>



        </div>

    </div>

</div>

