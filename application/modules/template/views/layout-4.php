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
	<!-- end: HEAD -->
	<body>
		<div id="app" class="lyt-4">
			<div class="app-content">	
				<!-- start: TOP NAVBAR -->
				<?php $this->load->view('components/main_header_v'); ?>
				<!-- end: TOP NAVBAR -->				
				<div class="main-content no-margin">
					<div class="wrap-content container" id="container">
                    	<!-- start: BREADCRUMB -->
						<?php /*?><?php if((isset($page_title) && !empty($page_title)) || (isset($breadcrumb) && !empty($breadcrumb))){?>
                            <div class="breadcrumb-wrapper">
                                <?php if(isset($page_title) && !empty($page_title)){?>
                                    <h4 class="mainTitle no-margin"><?php echo $page_title;?></h4>
                                <?php } // close page heading?>
                                <?php if(isset($breadcrumb) && !empty($breadcrumb)){?>
                                    <ul class="pull-right breadcrumb">
                                        <li><a href="index.html"><i class="fa fa-home margin-right-5 text-large text-dark"></i>Home</a></li>
                                        <li>Pages</li>
                                        <li>Starter Page</li>
                                    </ul>
                                <?php }?>
                            </div>
                        <?php } // close page heading and breadcrumb?>
                        <!-- end: BREADCRUMB --> <?php */?>
                                           						
                        <!-- start: PAGE-CONTENT -->
                        <!--<div class="container-fluid container-fullw"></div>-->
                        <?php
							if(isset($content_view) && !empty($content_view)){
								$this->load->view($content_view);
							}
						?>                        
                        <!-- end: PAGE-CONTENT -->
					</div>
                </div>
            </div>
                              
			<?php
				$this->load->view('components/footer_v');
				$this->load->view('components/template_settings_v');				
			?>            
		</div>
		<?php $this->load->view('components/main_javascript_v'); ?>
        <div id="common-modal-asset" class="modal fade modal-aside horizontal right" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog modal-lg">
            <div class="modal-content">
                <div class="panel" id="panel_container">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    <h4 class="modal-title"></h4>
                  </div>
                  <div class="modal-body" id="asset-modal-body">
                    <div class="text-center margin-top-50">
                        <img src="<?=base_url('assets/images/loading.gif')?>" class="img img-responsive" />
                    </div>
                  </div>
                  <div class="ajax-response padding-left-15 padding-right-15"></div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-o" data-dismiss="modal"> Close </button>
                    <button type="button" class="btn btn-primary save_form"> Submit </button>
                  </div>
                </div>
            </div>
          </div>
    	</div>
    </body>
</html>
