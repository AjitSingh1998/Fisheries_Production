<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
    <div class="panel panel-white">
        <div class="panel-body">
    
            <!-- 1 -->
            <div class="row">
                <div class="col-md-4">
                      <h3>Details </h3>
                      <p> Basic information about you and your business. </p>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label id="business_name" class="control-label">Business name</label>
                        <input type="text" class="form-control address" id="business_name" name="business_name" value="">
                    </div>
                    <div class="form-group">
                        <label class=""> Business website 
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </label>
                        <input class="form-control address" id="" name="" value="" type="text">
                    </div>
                </div>
            </div>
            <!-- 1 -->
            
            <div role="presentation" class="divider"></div>
            
            <!-- 2 -->
            <div class="row">
                <div class="col-md-4" >
                      <h3> Regional settings </h3>
                      <p>  Specify region specific settings for your business. </p>
                </div>
                <div class="col-md-8">
                    <!-- -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="">Country</label>
                                <select name="" class="form-control">
                                    <option value="US"> United States </option>
                                    <option value="GY">Guyana</option>
                                    <option value="HT">Haiti</option>
                                    <option value="HM">Heard and Mc Donald Islands</option>
                                    <option value="HN">Honduras</option>
                                    <option value="HK">Hong Kong</option>
                                    <option value="HU">Hungary</option>
                                    <option value="IS">Iceland</option>
                                    <option value="IN">India</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="">Currency</label>                   
                                <select name="" class="form-control">
                                    <option value="DHS">Dirham</option>
                                    <option value="DPO">Dominican Peso</option>
                                    <option value="XCD">East Caribbean Dollar</option>
                                    <option value="EGP">Egyptian Pound</option>
                                    <option value="EUR">Euro</option>
                                    <option value="HKD">Hong Kong Dollar</option>
                                    <option value="HUF">Hungarian Forint</option>
                                    <option value="ISK">Icelandic króna</option>
                                    <option value="INR">Indian Rupee</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- -->
                    
                    <!-- -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="">Time zone</label>
                                <select name="" class="form-control">
                                    <option value="77">(GMT-04:00) Santiago</option>
                                    <option value="78">(GMT-04:00) Caracas</option>
                                    <option value="80">(GMT-04:00) Eastern Time (US &amp; Canada)</option>
                                    <option value="81">(GMT-04:00) Indiana (East)</option>
                                    <option value="68">(GMT-03:00) Brasilia</option>
                                    <option value="69">(GMT-03:00) City of Buenos Aires</option>
                                    <option value="70">(GMT-03:00) Cayenne, Fortaleza</option>
                                    <option value="72">(GMT-03:00) Montevideo</option>
                                    <option value="74">(GMT-03:00) Atlantic Time (Canada)</option>
                                    <option value="73">(GMT-02:50) Newfoundland</option>
                                    <option value="71">(GMT-02:00) Greenland</option>
                                    <option value="66">(GMT-01:00) Cabo Verde Is.</option>
                                    <option value="10">(GMT+00:00) Monrovia, Reykjavik</option>
                                    <option value="65">(GMT+00:00) Azores</option>
                                    <option value="8">(GMT+01:00) Casablanca</option>
                                    <option value="9">(GMT+01:00) Dublin, Edinburgh, Lisbon, London</option>                                          
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- -->
                    
                    <!-- -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="">Date format</label>
                                <select name="" class="form-control">
                                    <option value="1">26 Jun 2017</option>
                                    <option selected="selected" value="2">Jun 26, 2017</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="">Time format</label>                   
                                <select name="" class="form-control">
                                    <option selected="selected" value="1">4:40AM</option>
                                    <option value="2">04:40</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- -->
                </div>
            </div>
            <!-- 2 -->
            
            <div role="presentation" class="divider"></div>
            
            <!-- 3 -->
            <div class="row">
                <div class="col-md-4" >
                      <h3> Business description </h3>
                      <p>  Enter an optional description of your business for use on your <a href="" > mini-website </a>.  </p>
                </div>
                <div class="col-md-8" >
                    <div class="form-group">
                      <textarea name="" class="form-control" id="" rows="10" ></textarea>  
                    </div>
                </div>
            </div>
            <!-- 3 -->
            
            <div role="presentation" class="divider"></div>
            
            <!-- 4 -->
            <div class="row">
                <div class="col-md-4" >
                      <h3> Business logo </h3>
                      <p>  Upload a logo to appear on your emails, invoices and mini-website. </p>
                </div>
                <div class="col-md-8" >
                    <!-- -->
                    <div class="row">
                        <div class="col-md-12">                        	
                            <div class="form-group">
                                <div class="logo">
                                      <img src="http://via.placeholder.com/250x150?text=Your+Logo" class="img-thumbnail" alt="Company Logo">
                                </div>
                            </div>
                            <a data-output-class="logo" href="" class="btn btn-primary btn-padded modal-open">
                                <i class="fa fa-pencil-square-o"></i>&nbsp;Edit logo
                            </a>
                        </div>
                    </div>
                    <!-- -->
                </div>
            </div>
            <!-- 4 -->
            
            <div role="presentation" class="divider"></div>
            
            <!-- 5 -->
            <div class="row">
                <div class="col-md-4" >
                      <h3> Get social! </h3>
                      <p>  Enter your social networking accounts and we'll help you promote your business. </p>
                </div>
                <div class="col-md-8" >
                    <div class="form-group">
                                <label class="">Twitter account</label>                   
                                <div class="input-group">
                                <span class="input-group-addon"><strong>@</strong></span>
                                <input value="" class="form-control " type="text"><span></span>
                                </div>
                                <span>  e.g. Timely</span>                                     
                            </div>
					<div class="form-group">
                                <label class="">Facebook page</label>                   
                                <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-facebook" aria-hidden="true"></i></span>
                                <input value="" class="form-control" type="text"><span></span>
                                </div>
                                <span>  e.g. http://www.facebook.com/liketimely </span>                                       
                            </div>
                </div>
            </div>
            <!-- 5 -->
            
            <div role="presentation" class="divider"></div>
             
            <!-- 6 -->
            <div class="row">
            	<div class="col-md-4"></div>
                <div class="col-md-8">                           
					<button type="button" class="btn btn-success padding-dr">Save Details</button>
                </div>
            </div>
            <!-- 6 -->
        </div>
    </div>
</div>