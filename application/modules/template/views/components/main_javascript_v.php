<!-- start: MAIN JAVASCRIPTS -->
<script src="<?php echo base_url()?>assets/plugins/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>        
<script src="<?php echo base_url()?>assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/components-modernizr/modernizr.js"></script>
<script src="<?php echo base_url()?>assets/plugins/js-cookie/src/js.cookie.js"></script>
<script src="<?php echo base_url()?>assets/plugins/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/jquery-fullscreen/jquery.fullscreen-min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/switchery/dist/switchery.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/jquery.knobe/dist/jquery.knob.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/slick.js/slick/slick.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/jquery-numerator/jquery-numerator.js"></script>
<script src="<?php echo base_url()?>assets/plugins/ladda/dist/spin.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/ladda/dist/ladda.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/ladda/dist/ladda.jquery.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/sweetalert/dist/sweetalert.min.js"></script>
<script src="<?php echo base_url()?>assets/plugins/jQuery-toaster/jquery.toaster.js"></script>
<!-- end: MAIN JAVASCRIPTS -->
<script src="<?php echo $this->template_path; ?>js/letter-icons.js"></script>
<script src="<?php echo $this->template_path; ?>js/main.js"></script>
<script src="<?php echo base_url(); ?>assets/js/sweetalert.js"></script>
<script src="<?php echo base_url(); ?>assets/js/custom.js?v=<?=time()?>"></script>
<script>
	jQuery(document).ready(function() {
		Main.init();
	});			
</script>

<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
<?php
	// Load grocery crud javascript file
	if(isset($js_files) && !empty($js_files)){
		foreach($js_files as $file){
			echo '<script src="'. $file.'"></script>'."\n";
		} 
	}
	if(isset($this->setting['scriptsrc']) && !empty($this->setting['scriptsrc'])){	
		if(is_array($this->setting['scriptsrc'])){
			foreach($this->setting['scriptsrc'] as $scriptsrc){
				echo '<script src="'.$scriptsrc.'"></script>'."\n";
			}
		}
	}
?>
<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->

<!-- start: JavaScript Event Handlers for this page -->
<?php
$message = $this->messageci->display();
$this->session->set_flashdata('_messages', '');
$msg = '';
if(is_array($message) && !empty($message)){
    for($i=0; $i < count($message); $i++) $msg .= $message[$i];
}
?>
<script>
$(document).ready(function() {
	$.toaster({settings:{timeout:10000,toast:{template:'<?=$msg?>'}}, message:''});
			
	//$('.modal-open').on('click', 'a', function(event) {
//			$('#map').load($(this).attr('href'));
//			event.preventDefault();
//		});
	
	//$('.modal-open').on('click', 'a', function(event){
	$('.modal-open').click(function(event){					
		var $data = $(this).data();
		var modalClass = $(this).data('modal-class');					
		$('.modal').addClass(modalClass);										
		if($('.modal').hasClass(modalClass)){
			$('.modal.'+modalClass).load(site_url + $data.url, function(responseTxt, statusTxt, xhr){										
				if(statusTxt == "success"){								
					$('.modal.'+modalClass).modal({ keyboard: false, show: true, backdrop: 'static' });
					$('.modal.ui-draggable .modal-dialog').draggable({ cursor: 'move', handle: '.modal-header' });
					$('.ui-draggable .modal-header').css('cursor', 'move');
				}else{
					//if(statusTxt == "error"){
					alert("Error: " + xhr.status + ": " + xhr.statusText);
				}
			});		
		}
		event.preventDefault();	
	});	
				
});			
</script>
<?php
	if(isset($this->setting['scripts']) && !empty($this->setting['scripts'])){
		echo $this->setting['scripts']."\n";
	}
?>        
<!-- end: JavaScript Event Handlers for this page -->