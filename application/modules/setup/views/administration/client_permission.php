<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="panel panel-white">
  <div class="panel-body">    
    <!-- start: PAGE CONTENT -->
			
			<!-- start: PAGE CONTENT -->
			<div class="row">
                <div class="col-md-12">
                    <!-- start: TABLE WITH IMAGES PANEL -->
                    
                    <div class="tabbable tabs-left">
                        <ul id="myTab3" class="nav nav-tabs tab-bricky">
                        	<?php
							if(isset($group_list) && !empty($group_list)){
								foreach($group_list as $group){
								$active = ($group['group_id'] == $group_id) ? ' class="active"':'';								
							?>
                                <li<?=$active?>>
                                    <a href="<?=site_url('settings/administration/client_permission/'.$group['group_id'])?>"><?php echo $group['group_name']?></a>
                                </li>
                            <?php }}?>           
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active">
								<?php 
                                    if(in_array('edit', $actions)){
                                        echo form_open(site_url('settings/administration/client_permission/'.$group_id)); 
                                ?>
                                        <div class="menu_tree_view">
                                            <?php echo buildPermissionMenuList(0, $menu_list, 0, $group_menu_list);?>
                                        </div>
                                        <div class="action_buttons">
                                            <button type="button" class="btn btn-default"> Cancel</button>
                                            <button type="submit" class="btn btn-primary"> Submit</button>
                                        </div>
                                <?php 
                                        echo form_close(); 
                                    }else{
                                        echo buildPermissionMenuList(0, $menu_list, 0, $group_menu_list);
                                    }
                                ?>        
                            </div>
                        </div>
                    </div>
                    <!-- end: TABLE WITH IMAGES PANEL -->
                </div>
            </div>
			<!-- end: PAGE CONTENT-->
<!-- end: MAIN CONTAINER -->
</div>
</div>