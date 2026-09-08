<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div class="Profile">
<div class="margin-top-40 margin-bottom-40">
    <div class="container">
        <div class="container-fluid">
            <div class="panel panel-white">
                <div class="panel-body">
                    <div class="display-flex">
                        <div class="col-sm-3 light-gray-bg left nopadding">
                            <?php $this->load->view('account/profile_navigation_v')?>
                        </div>
                        
                        <!------------------------- RIGHT FILTER RESULT------------------------- -->
                        
                        <div class="col-sm-9 border-left right-side">
                            <div class="panel margin-bottom-0">
                                <div class="panel-heading text-center">
                                    <h3 class="title">Update Profile Photo</h3>
                                    <p>Add information about yourself to share on your profile. </p>
                                </div>
                                <div class="row border-bottom clear"></div>
                                <div class="row margin-top-20 margin-bottom-20">
                                    <div class="col-sm-10 col-sm-offset-2">
                                        <?php
                                        $attr = array( 'class'=>'photo', 'id'=>'photo', 'name'=>'photo', 'enctype'=>'multipart/form-data'); 
                                        echo form_open('account/photo', $attr);
										
										if(!empty($userInfo) && !empty($userInfo['profileImage'])){
											$imageName = '150150/'.$userInfo['profileImage'];
											$image = USER_PROFILE_IMAGE_URL . $imageName;
										} else{
											$image = 'http://www.placehold.it/200x150/EFEFEF/AAAAAA?text=no+image';
										}
                                    ?>
                                        <div class="form-group">
                                            <label for="package_image" class="control-label">Choose Image:</label>
                                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;"> <img class="img img-responsive" src="<?=$image?>" alt=""/> </div>
                                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width:200px;max-height:150px;line-height:20px;"></div>
                                                <div> <span class="btn btn-light-grey btn-file"> <span class="fileupload-new"><i class="fa fa-picture-o"></i> Select </span> <span class="fileupload-exists"><i class="fa fa-picture-o"></i> Change </span>
                                                    <input type="file" name="profileImage">
                                                    </span> <a href="#" class="btn fileupload-exists btn-light-grey" data-dismiss="fileupload"> <i class="fa fa-times"></i> Remove </a> </div>
                                            </div>
                                        </div>
                                        <div class="border-bottom clear margin-bottom-10"></div>
                                        <button type="submit" class="btn btn-wide btn-success"> Save </button>
                                        <?=form_close()?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
