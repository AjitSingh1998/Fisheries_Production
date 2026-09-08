<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<div class="container-fluid container-fullw">
	<div class="row flex">
    	<div class="col-sm-3">
        	<?php $this->load->view('database/yearly_backup_navigation_v');?>
        </div>
        <div class="col-sm-9">
            <div class="panel panel-white">
                <div class="panel-heading border-light light-bg">
                    <h3 class="text-center"><?=$page_title?></h3>
                </div>
                <div class="panel-body">        	 
                     <div class="row">
                        <div class="col-sm-12"><?php echo $output; ?></div>
                    </div>                
                </div>
            </div>
        </div>
	</div>
</div>