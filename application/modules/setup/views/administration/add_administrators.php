<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="panel panel-white">
  <div class="panel-body">
    <div class="container-fluid hoursStyles common-css">
        <form class="form-vertical hoursStyles CUSTOM-tab-style common-css"  method="post" id="add_administrators" style="padding-top: 27px;">
            <!-- 1 -->  
            <div class="row"> 
                <div class="col-sm-10">
                    <h1> <a href="/Setup/staff_notifications">
                            <?=lang('page_title')?>
                         </a> &nbsp; <i class="fa fa-angle-right"></i> &nbsp;
                            <?=$page_title?>
                    </h1>
                  </div>
                <div class="col-sm-2 btn-align-">
                   <button type="button" class="btn btn-primary btn-padded">Save</button>
                </div>
            </div>
            <div role="presentation" class="divider"></div>
            <!-- 1 --> 
             
            <!-- 2 -->
            <div class="row">
                <div class="col-md-4" >
                      <h3>Administrator details </h3>
                      <p> The SMS number and email address will be used for staff reminders.  </p>
                </div>
                <div class="col-md-8" >
                    <!-- -->
                    <div class="row">
                        <div class="col-md-6 nopaddingLeft">
                            <div class="form-group">
                                <label class="">First name <span class="symbol required"></span> </label>
                                <input class="form-control address" id="" name="" value="" type="text">
                            </div>
                        </div>
                        <div class="col-md-6 nopaddingRight ">
                            <div class="form-group ">
                            <label class=""> Last Name <span class="symbol required"></span> </label>                   
                            <input class="form-control address" id="" name="" value="" type="text">
                            </div>
                        </div>
                    </div>
                    <!-- -->
                
                    <!-- -->
                    <div class="row">
                        <div class="col-sm-6 nopaddingLeft">
                            <div class="form-group ">
                                <label class="">Telephone</label>
                                <input class="form-control address" id="" name="" value="" type="number">
                                <span></span>
                            </div>
                        </div>
                        <div class="col-sm-6 nopaddingRight">
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
                        <div class="col-sm-12 nopaddingLeft">
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
                        <div class="col-sm-6 nopaddingLeft">
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
                        <div class="col-md-12 nopadding">
                            <div class="form-group">
                                <div class="logo">
                                    <div style="width: 250px; height: 150px; line-height: 150px;">
                                        <img src="http://via.placeholder.com/250x150?text=Your+Logo" alt="">
                                    </div>
                                    <p> Accepted formats: PNG, GIF or JPG. Maximum file size is 2.0MB.</p>
                                </div>
                                <a data-output-class="logo" href="" class="btn btn-primary btn-padded modal-open">
                                    <i class="fa fa-pencil-square-o"></i>&nbsp;Edit logo
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- -->
                </div>
            </div>
            <!-- 3 -->   
            
            <!-- 4 -->  
            <div class="row">
            <div class="col-md-12">
                <div class="form-actions text-right">
                    <button type="button" class="btn btn-default padding-dr">Cancel</button>
                    <button type="button" class="btn btn-primary padding-dr">Save</button>
                </div>
            </div>
            </div>
            <div role="presentation" class="divider"></div>
            <!-- 4 -->  
        </form>
    </div>
  </div>
</div>
