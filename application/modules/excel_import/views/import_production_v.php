<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<?php
if(isset($this->setting['stylesheet']) && !empty($this->setting['stylesheet'])){
	if(is_array($this->setting['stylesheet'])){
		foreach($this->setting['stylesheet'] as $stylesheet){
			echo '<link rel="stylesheet" href="'.$stylesheet.'" />'."\n";
		}
	}
}
?>
<style>
.panel-scroll{overflow-y: auto;}
</style>
<div class="panel panel-white" id="panel_import_excel">
	<?php echo form_open('', 'method="post" id="import_form" enctype="multipart/form-data"');?>
    <div class="panel-heading border-light light-bg">
        <div class="row">
            <div class="col-sm-5 col-sm-offset-2">
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
                                <input type="file" name="file" required class="file-input" id="inputGroupFile01">
                            </div>
                            <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">
                                <i class="fa fa-times"></i> Remove
                            </a>
                        </div>
                    </div>
                </div>                   
            </div>
            <div class="col-sm-3">
                <input type="hidden" name="production_id" id="production_id" value="<?=$production_id?>">
                <input type="hidden" name="action_mode" id="action_mode" value="check">
                <input type="hidden" name="file_path" id="file_path" value="">
                
                <button type="submit" name="import" value="Import" class="btn btn-success">Import File</button>
                <?php $download = base_url('sample_excel_file/production.xlsx');?>
                <a href="<?=$download?>" target="_blank" class="btn btn-info" data-toggle="tooltip" data-title="Download Sample File">
                    <i class="fa fa-file-excel-o"></i> Download
                </a>
            </div>
        </div>        
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <div class="col-sm-12">
                <table class="table">
                    <thead id="header_data"></thead>
                </table>
            </div>
            <div class="col-sm-12 panel-scroll height-200" style="width:99%;">
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
    <div class="panel-footer border-light light-bg text-center">
    	<button type="button" class="btn btn-primary hidden" id="save_excel_data"><i class="fa fa-save"></i> Save Data</button>
    </div>
    <?php echo form_close();?>
</div>

<?php
if(isset($this->setting['scriptsrc']) && !empty($this->setting['scriptsrc'])){	
	if(is_array($this->setting['scriptsrc'])){
		foreach($this->setting['scriptsrc'] as $scriptsrc){
			echo '<script src="'.$scriptsrc.'"></script>'."\n";
		}
	}
}
?>
<script>
$(document).ready(function(e) {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>