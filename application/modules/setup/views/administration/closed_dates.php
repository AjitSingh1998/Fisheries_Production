<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<div class="panel panel-white">
  <div class="panel-body">
    <div class="container-fluid hoursStyles common-css">
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
           <button  type="button" class="btn btn-primary btn-padded" data-toggle="modal" data-target="#myModal">Add closed date</button>
        </div>
        </div>
        <div role="presentation" class="divider"></div>
        <!-- 1 --> 
        <div class="rg-row">
            <div class="col-md-12 ">
                <p> List the dates your business is closed for public holidays, maintenance or any other reason. Customers will not be able to place online bookings during these dates. </p>
            </div>
        </div>  
	</div>
  </div>
</div>

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background-color: skyblue;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add closed date </h4>
        </div>
        <div class="modal-body">
         <form id="closed_date">
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
    </div>
    <div class="modal-footer">
        <a href="javascript:void(0);" class="btn btn-default padding-dr">Cancel</a>
        <button class="btn btn-padded btn-primary" name="commit" type="submit">Save</button>
    </div>
</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  <!-- Modal -->