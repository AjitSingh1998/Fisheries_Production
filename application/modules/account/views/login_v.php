<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<!-- Template Name: Packet - Responsive Admin Template build with Twitter Bootstrap 3.x | Author: ClipTheme -->
<!--[if IE 8]><html class="ie8" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="<?php echo $this->setting['language_iso_code']; ?>">
	<!--<![endif]-->
	<!-- start: HEAD -->
	<head>
		<?php
        	$this->load->view('components/head_element_v');
		?>
	</head>
	<body>
		<div id="app" class="lyt-4">
			<div class="app-content">				
				<div class="main-content no-margin">
					<div class="wrap-content container" id="container">
                    	
                        <div class="row">
	<div class="main-login col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
	<?php 
    $message = $this->messageci->display();
    $this->session->set_flashdata('_messages', '');
    if(is_array($message) && !empty($message)){
        for($i=0; $i < count($message); $i++) echo $message[$i];
    }
    
    ?>
    <!-- start: LOGIN BOX -->
    <div class="box-login">
                
         <?php echo form_open('account/login', 'class="login-form"')?>
            <div class="errorHandler alert alert-danger no-display">
                <i class="fa fa-remove-sign"></i> You have some form errors. Please check below.
            </div>   				
            <fieldset>
            	<div class="box-dr">
                	<div class="text-center">
                       <h3>Login to Bansagar Fisheries</h3>
     				   <p> Please enter your Email and password to log in. </p>
                    </div>
                    <br/>
                    <br/>
                  <div class="row">
                  	  <div class="col-md-8 col-md-offset-2">
                        <div class="form-group">
                            <span class="input-icon">
                                <input type="text" class="form-control" name="email" placeholder="Email" value="">
                                <i class="fa fa-user"></i> </span>
                            <!-- To mark the incorrectly filled input, you must add the class "error" to the input -->
                            <!-- example: <input type="text" class="login error" name="login" value="Username" /> -->
                        </div>
                        <div class="form-group form-actions">
                            <span class="input-icon">
                                <input type="password" class="form-control password" name="password" placeholder="Password" value=""> 
                                <i class="fa fa-key"></i>
                                <a class="forgot" href="#"><i class="fa fa-question"></i></a>
                            </span>
                        </div>
                        <div class="form-actions text-center">
                            <button type="submit" class="btn red_button btn-red ">
                                Login <i class="fa fa-arrow-circle-right"></i>
                            </button>
                        </div>
                      </div>                    	
                  </div>
                </div>		
            </fieldset>
        <?php echo form_close();?>	
    </div>
    <!-- end: LOGIN BOX -->
</div>
</div>
						
					</div>
                </div>
            </div>    
		</div>
		<?php $this->load->view('components/main_javascript_v'); ?>
	</body>
</html>
