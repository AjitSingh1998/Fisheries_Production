<?php 				 
$userType = loginCompanyInfo('group_name'); 

			
$profile_image = '150150/'.loginUserInfo('profileImage'); 
$user_id = loginUserInfo('admin_id'); 
$group_id = loginUserInfo('group_id'); 
$pmenu = $this->uri->segment(1); //$this->router->fetch_class(); 
$cmenu = $this->uri->segment(2); //$this->router->fetch_method();							 
$scmenu = $this->uri->segment(3); //$this->router->fetch_method();	 
$menulist = $this->common->getLeftNavigationMenuList($group_id); 
?>
<?php if(isset($user_id) && !empty($user_id)){ ?> 
<header class="navbar navbar-default navbar-static-top top-header-menu">
  <!-- start: NAVBAR HEADER --> 
  <div class="navbar-header"> 
    <button href="javascript:void(0)" class="menu-mobile-toggler btn no-radius pull-left hidden-md hidden-lg" id="horizontal-menu-toggler" data-toggle="collapse" data-target=".horizontal-menu"> <i class="fa fa-bars"></i> </button> 
    <a class="navbar-brand" href="<?=site_url()?>">  
    	<img src="<?php echo $this->template_path; ?>images/logo2.png" alt="Simran Fisheries" style="width:150px; height:50px;"/>
    </a>  
    <a class="navbar-brand navbar-brand-collapsed" href="<?=site_url()?>">SF</a>  
  </div> 
  <!-- end: NAVBAR HEADER -->  
  <!-- start: NAVBAR COLLAPSE --> 
  <div class="navbar-collapse collapse"> 
  	<?php  
	echo buildSimpleHorizontalNavigationBar(0, $menulist, 0, $pmenu, $cmenu, $scmenu);		 
	?>
    <ul class="nav navbar-right">
	  <?php 
	  $dbselector = getDatabaseList();
	  if($dbselector != ''){
	  ?>
      <li>
      	<div class="padding-10" id="database_container"><?php echo $dbselector;?></div>
      </li>
	  <?php } ?>
      <!-- start: USER DROPDOWN --> 
      <li class="dropdown current-user"> 
        <a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#" style="color:#58748B !important;"> 
			<?php if(is_file(USER_PROFILE_IMAGE_DIR.$profile_image) && file_exists(USER_PROFILE_IMAGE_DIR.$profile_image)){?> 
            <img src="<?=(USER_PROFILE_IMAGE_URL.$profile_image)?>" alt="" style="width:45px;">  
            <span class="username"><?=loginUserInfo('full_name')?></span> 
            <span class="caret"></span> 
            <?php }else{?> 
            <div class="lettericon" data-text="<?=ucfirst(loginUserInfo('full_name'))?>" data-color="auto" data-char-count="2" data-size="lg" data-box="circle"></div> 
            <span class="username"><?=loginUserInfo('full_name')?></span> 
            <span class="caret"></span> 
            <?php }?> 
        </a> 
        <ul class="dropdown-menu dropdown-light dropdown-current-user dropdown-large animated fadeInUpShort no-radius"> 
          <li> 
           <a href="<?=site_url('account/edit_profile')?>" class="no-radius no-margin light-bg"> 
            <div class="clearfix" style="padding: 5px 0;"> 
              <div class="thread-image pull-left margin-right-10">                 
                <?php if(is_file(USER_PROFILE_IMAGE_DIR.$profile_image) && file_exists(USER_PROFILE_IMAGE_DIR.$profile_image)){?> 
                <img src="<?=(USER_PROFILE_IMAGE_URL . $profile_image)?>" alt="" style="width:45px;">  
                <?php }else{?> 
                <div class="lettericon" data-text="<?=ucfirst(loginUserInfo('full_name'))?>" data-color="auto" data-char-count="2" data-size="lg" data-box="circle"></div> 
                <?php }?>                 
              </div> 
              <span class="thread-content wapper-user"> 
                <span class="username"><?=loginUserInfo('full_name')?></span> 
                <span class="user-email"><?=loginUserInfo('email')?></span> 
              </span> 
            </div> 
            </a>  
            </li> 
       		<li class="divider"></li> 
          <li>  
              <a class="no-radius no-margin" href="<?=site_url('account/edit_profile')?>"> 
              	<i class="fa fa-google-wallet margin-right-10"></i> Edit Profile  
              </a>  
          </li> 
          <li> <a class="no-radius no-margin" href="<?=site_url('account/change_password')?>"> <i class="fa fa-refresh margin-right-10"></i> Change Password</a> </li> 
          <li> <a class="no-radius no-margin" href="<?=site_url('account/logout')?>"><i class="fa fa-sign-out margin-right-10"></i> Log Out </a> </li> 
        </ul> 
      </li>
      <!-- end: USER DROPDOWN --> 
   </ul> 
  </div>
  <!-- end: NAVBAR COLLAPSE -->  
</header>

<!-- start: HORIZONTAL MENU -->
<div class="navbar navbar-default horizontal-menu collapse hidden-sm hidden-md hidden-lg">
    <div class="horizontal-menu-wrapper">
        <div class="horizontal-nav-container">
        	<?php
			echo buildHorizontalNavigationBar(0, $menulist, '0', $pmenu, $cmenu, $scmenu, $type='letter');
			?>        
            <ul class="nav navbar-nav navbar-right">
              <!-- start: USER DROPDOWN --> 
              <li class="dropdown"> 
                <a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#" style="color:#58748B !important;"> 
                    <?php if(is_file(USER_PROFILE_IMAGE_DIR.$profile_image) && file_exists(USER_PROFILE_IMAGE_DIR.$profile_image)){?> 
                    <img src="<?=(USER_PROFILE_IMAGE_URL . $profile_image)?>" alt="" style="width:45px;">  
                    <span class="username"><?=loginUserInfo('full_name')?></span> 
                    <span class="caret"></span> 
                    <?php }else{?> 
                    <div class="lettericon" data-text="<?=ucfirst(loginUserInfo('full_name'))?>" data-color="auto" data-char-count="2" data-size="lg" data-box="circle"></div> 
                    <span class="username"><?=loginUserInfo('full_name')?></span> 
                    <span class="caret"></span> 
                    <?php }?> 
                </a> 
                <ul class="dropdown-menu pull-right animated fadeInRight">
                  <li>  
                      <a class="no-radius no-margin" href="<?=site_url('account/edit_profile')?>"> 
                        <i class="fa fa-google-wallet margin-right-10"></i> Edit Profile  
                      </a>  
                  </li> 
                  <li> <a class="no-radius no-margin" href="<?=site_url('account/change_password')?>"> <i class="fa fa-refresh margin-right-10"></i> Change Password</a> </li> 
                  <li> <a class="no-radius no-margin" href="<?=site_url('account/logout')?>"><i class="fa fa-sign-out margin-right-10"></i> Log Out </a> </li> 
                </ul> 
              </li>
              <!-- end: USER DROPDOWN --> 
           </ul>
        </div>
        <!-- start: MENU TOGGLER FOR MOBILE DEVICES -->
        <div class="close-handle visible-xs-block visible-sm-block menu-toggler" data-toggle="collapse" data-target=".horizontal-menu">
            <div class="arrow-left"></div>
            <div class="arrow-right"></div>
        </div>
        <!-- end: MENU TOGGLER FOR MOBILE DEVICES -->
    </div>
</div>
<!-- end: HORIZONTAL MENU -->

<?php } ?> 