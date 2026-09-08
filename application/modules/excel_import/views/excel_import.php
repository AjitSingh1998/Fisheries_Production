<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
    <div class="panel panel-white">
    	<div class="panel-heading border-light light-bg">
        	<h3 class="text-center">Import data through excel (.xls, .xlsx) file.</h3>
            <?php echo form_open('', 'method="post" id="import_form" enctype="multipart/form-data"');?>
            	<div class="row">
                	<div class="col-sm-3">
                    	<div class="form-group">
                        	<select name="method_name" class="form-control" id="data_type">
                            	<option value=""> Select Data Type </option>
                                <option value="dailyToll">Daily Toll</option>
                                <option value="fisherman">Fisher Man</option>
                                <option value="maingroup">Main Group</option>
                                <option value="products">Products</option>
                            </select>
                        </div>                    
                    </div>
                    <div class="col-sm-2">
                    	<button type="button" class="btn btn-info" id="download" data-toggle="tooltip" data-title="Download Sample File">
                        	<i class="fa fa-file-excel-o"></i> Download
                        </button>
                    </div>
                    <div class="col-sm-5">
                    	<div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="input-group">
                                <div class="form-control uneditable-input">
                                    <i class="fa fa-file fileupload-exists"></i>
                                    <span class="fileupload-preview"></span>
                                </div>
                                <div class="input-group-btn">
                                    <div class="btn btn-primary btn-file">
                                        <span class="fileupload-new"><i class="fa fa-folder-open-o"></i> Select file</span>
                                        <span class="fileupload-exists"><i class="fa fa-folder-open-o"></i> Change</span>
                                        <input type="file" class="file-input">
                                        <input type="file" name="file" required class="file-input" id="inputGroupFile01">
                                    </div>
                                    <a href="wsdindex.html#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">
                                        <i class="fa fa-times"></i> Remove
                                    </a>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <div class="col-sm-2">
                    	<button type="submit" name="import" value="Import" class="btn btn-success">Import File</button>
                    </div>
                </div>
            <?php echo form_close();?>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
            	<div class="row">
                    <div class="col-sm-12">
                        <table class="table">
                            <thead id="header_data"></thead>
                        </table>
                    </div>
                    <div class="col-sm-12 panel-scroll height-300" style="width:100%;">
                        <table class="table table-striped">
                            <tbody id="body_data"></tbody>
                        </table>
                    </div>
                    <div class="col-sm-12">
                        <table class="table">
                            <tfoot id="footer_data"></tfoot>
                        </table>
                    </div>
                </div>
            </div>       
        </div>
    </div>
</div>