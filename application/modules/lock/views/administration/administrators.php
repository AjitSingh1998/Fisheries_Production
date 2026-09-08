<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12"> 
       <div class="row">
            <div class="col-md-4" >
                  <h3>Administrator details </h3>
                  <p> The SMS number and email address will be used for staff reminders.  </p>
            </div>
            <div class="col-md-8" >
                <!-- -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="">First name <span class="symbol required"></span> </label>
                            <input class="form-control address" id="" name="" value="" type="text">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group ">
                        <label class=""> Last Name <span class="symbol required"></span> </label>                   
                        <input class="form-control address" id="" name="" value="" type="text">
                        </div>
                    </div>
                </div>
                <!-- -->
               
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group ">
                            <label class="">Telephone</label>
                            <input class="form-control address" id="" name="" value="" type="number">
                            <span></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group ">
                        <label class="">SMS number</label>                   
                            <div class="input-group">
                                <span class="input-group-addon">+91</span>
                                <input value="" class="form-control" type="number"><span></span>
                            </div>
                        <span></span>
                        </div>
                    </div>
                </div>                    
                <!-- -->
                <!-- -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group ">
                            <label class="">Email</label>
                           <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                </span>
                                <input value="" class="form-control" type="email"><span></span>
                            </div>
                            <span></span>
                        </div>
                    </div>
                </div>
                <!-- -->
                <!-- -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group ">
                            <label class="">Address</label>                   
                            <input value="" class="form-control" type="text"><span></span>
                            <span></span>
                        </div>
                        <div class="form-group ">
                            <input value="" class="form-control" type="text"><span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group ">
                            <label class=""> City </label>                   
                            <input value="" class="form-control" type="text"><span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
                <!-- -->
            </div>
        </div> 
        <div role="presentation" class="divider"></div>
        <!-- 2 -->                    
     
        <!-- 3 -->
        <div class="row">
            <div class="col-md-4" >
                  <h3> Photo </h3>
                  <p>  Upload a logo. </p>
            </div>
            <div class="col-md-8" >
                <!-- -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="logo">
                                <div style="width: 250px; height: 150px; line-height: 150px;">
                                    <img src="http://via.placeholder.com/250x150?text=Your+Logo" alt="">
                                </div>
                                <p> Accepted formats: PNG, GIF or JPG. Maximum file size is 2.0MB.</p>
                            </div>
                            <span id="upload" style="position: relative; overflow: hidden; direction: ltr;" class=""> <!-- Required for IE -->
                                  <span id="upload"> <!-- Required for IE -->
                                	   <a class="btn btn-bg-dr" type="button" name="upload" id="upload" value="Upload"><i class="fa fa-arrow-circle-o-up"></i>&nbsp;Edit logo</a> 
                   				  </span>
              					  <input multiple="multiple" name="file" style="position: absolute; right: 0px; top: 0px; font-family: Arial; font-size: 20px; margin: 0px; padding: 0px; cursor: pointer; opacity: 0;" type="file">
    					</span>
                        </div>
                    </div>
                </div>
                <!-- -->
            </div>
        </div>
        <!-- 3 -->   
        
        <div role="presentation" class="divider"></div>
        <div class="clearfix"></div>
        <!-- 4 -->
        <div class="col-md-12">
            <div class="text-right">
                <button type="button" class="btn btn-default padding-dr">Cancel</button>
                <button type="button" class="btn btn-success padding-dr">Save</button>
            </div>    
        </div>
        <!-- 4 -->
        </div>                
    
    </div>