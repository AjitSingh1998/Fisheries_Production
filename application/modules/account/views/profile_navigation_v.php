<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<style>
.listing-of-ul li.active a {
	background-color: rgb(88, 116, 139);
	color: #fff;
}

</style>
<?php
$class = $this->router->fetch_class();
$method = $this->router->fetch_method();
?>
<div class="panel">
    <div class="medium-gray padding-15 text-center">
        <div class="profile-img-wapper text-center">
            <?php
				if(!empty($userInfo) && !empty($userInfo['profileImage'])){
					$imageName = '150150/' . $userInfo['profileImage'];
					if(is_file(USER_PROFILE_IMAGE_DIR.$imageName) && file_exists(USER_PROFILE_IMAGE_DIR.$imageName)){
						$image = USER_PROFILE_IMAGE_URL . $imageName;
					}else{
						$image = 'http://www.placehold.it/200x150/EFEFEF/AAAAAA?text=no+image';	
					}						
				} else{
					$image = 'http://www.placehold.it/200x150/EFEFEF/AAAAAA?text=no+image';
				}
			?>
            <img src="<?=$image?>" alt=""  class="img img-responsive img-circle" style="margin:0 auto;" /> 
        </div>
        <h3 class="panel-title margin-top-15"> <strong><?=$userInfo['full_name']?></strong></h3>
    </div>
    <div class="panel-body nopadding">
        <ul class="list-unstyled listing-of-ul">
            <li class="<?=($method=='edit_profile')?'active':''?>"> <a href="<?=site_url('account/edit_profile')?>">Edit Profile</a> </li>
            <li class="<?=($method=='photo')?'active':''?>"> <a href="<?=site_url('account/photo')?>">Photo</a> </li>
            <li class="<?=($method=='change_password')?'active':''?>"> <a href="<?=site_url('account/change_password')?>">Change password</a> </li>
            <li class="<?=($method=='logout')?'active':''?>"> <a href="<?=site_url('account/logout')?>">Logout</a> </li>
        </ul>
    </div>
</div>
