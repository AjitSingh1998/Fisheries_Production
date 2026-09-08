<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-sm-9">
    	<p>List the dates your business is closed for public holidays, maintenance or any other reason. Customers will not be able to place online bookings during these dates. </p>
    </div>
    <div class="col-sm-3">
    	<div class="text-right">
         <button type="button" class="btn btn-bg-dr padding-dr" data-toggle="modal" data-target="#myModal">Add closed date</button>
  	 	</div>
        
        <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        
        <div class="modal-body">
    <div class="alert alert-info">
    	Online bookings can not be placed during closed dates
    </div>
    <div class="form-group required ">
        <label class="" for="">Start date</label>
        <input class="input-small form-control" id="" name="" value="Jul 10 2017" style="width: 110px;" type="text"> 
    </div>
    <div class="form-group required ">
        <label class="" for="">End date</label>
        <input class="input-small form-control" id="" name="" value="Jul 10 2017" style="width: 110px;" type="text"> 
    </div>
    <div class="form-group required ">
        <label class="" for="">Description</label>
        <textarea class="form-control" cols="20" id="" name="" rows="2"></textarea>
    </div>
    <div class="form-actions row">
    <div class="col-xs-6 text-left">
    	<button type="submit" class="btn btn-bg-dr btn-padded">Save</button>
    </div>
    <div class="col-xs-6 text-right">
    	
          <button type="button" class="btn btn-default btn-padded " data-dismiss="modal">Close</button>
     
    </div>
    </div>
    </div>
        
      </div>
      
    </div>
  </div>
    </div>

</div>