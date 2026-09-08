<title><?php echo $this->setting['document_title']; ?></title>
<!-- start: META -->
<!--[if IE]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="title" content="<?php echo $this->setting['meta_title']; ?>" />
<meta name="keywords" content="<?php echo $this->setting['meta_keywords']; ?>" />
<meta name="description" content="<?php echo $this->setting['meta_description']; ?>" />
<meta name="author" content="<?php echo $this->setting['site_name']; ?>" />
<!-- start: GOOGLE FONTS -->
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
<!-- end: GOOGLE FONTS -->
<!-- start: MAIN CSS -->
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/themify-icons/themify-icons.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/flag-icon-css/css/flag-icon.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/animate.css/animate.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/perfect-scrollbar/css/perfect-scrollbar.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/switchery/dist/switchery.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/ladda/dist/ladda-themeless.min.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/slick.js/slick/slick.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/slick.js/slick/slick-theme.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/sweetalert/dist/sweetalert.css">
<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/toastr/toastr.min.css">

<!-- end: MAIN CSS -->
<!-- start: CSS REQUIRED FOR THIS PAGE ONLY -->
<?php
	if(isset($this->setting['stylesheet']) && !empty($this->setting['stylesheet'])){

		if(is_array($this->setting['stylesheet'])){
			foreach($this->setting['stylesheet'] as $stylesheet){
				echo '<link rel="stylesheet" href="'.$stylesheet.'" />'."\n";
			}
		}
	}
	// load grocery crud css files
	if(isset($css_files) && !empty($css_files)){
		foreach($css_files as $file){
			echo '<link type="text/css" rel="stylesheet" href="'.$file.'" />'."\n";
		}
	}
	
	if(isset($this->setting['style']) && !empty($this->setting['style'])){
		echo '<style>'.$style.'</style>'."\n";
	}
?>        
<!-- end: CSS REQUIRED FOR THIS PAGE ONLY -->

<!-- start: Packet CSS -->
<link rel="stylesheet" href="<?php echo $this->template_path; ?>css/styles.css">
<link rel="stylesheet" href="<?php echo $this->template_path; ?>css/plugins.css">
<link rel="stylesheet" href="<?php echo $this->template_path; ?>css/themes/<?php echo $this->template_style; ?>" id="skin_color">

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css?v=<?=time()?>">

<!-- end: Packet CSS -->
<!-- Favicon -->
<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />

<script type="text/javascript">
var base_url = "<?php echo base_url()?>";
var site_url = "<?php echo site_url('/')?>";
var template_path = "<?php echo $this->template_path; ?>";
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_token_value = '<?php echo $this->security->get_csrf_hash(); ?>';
var csrf_cookie_name = '<?php echo $this->config->item('csrf_cookie_name'); ?>';
</script>