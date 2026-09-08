<?php
    //Start counting the buttons that we have:
    $buttons_counter = 0;
    if (!$unset_edit) {
        $buttons_counter++;
    }
    if (!$unset_read) {
        $buttons_counter++;
    }
    if (!$unset_delete) {
        $buttons_counter++;
    }
    if (!empty($list[0]) && !empty($list[0]->action_urls)) {
        $buttons_counter = $buttons_counter +  count($list[0]->action_urls);
    }
    $show_more_button  = $buttons_counter > 2 ? true : false;
	//$custom_action_buttons = NULL;
?>
<?php foreach($list as $num_row => $row){ ?>
    <tr>
    	<?php if (!empty($row->action_urls) || !$unset_edit || !$unset_read  || !$unset_delete  || count($bulk_actions) > 0) {?>
        <td <?php if (($unset_delete  && count($bulk_actions) <= 0) || count($bulk_actions) <= 0) { ?> style="border-right: none;"<?php } ?>>
            <?php if (!$unset_delete || count($bulk_actions) > 0) { ?>
                <input type="checkbox" class="select-row" data-id="<?php echo $row->primary_key_value; ?>" />
            <?php } ?>
        </td>
        <td <?php if ($unset_delete) { ?> style="border-left: none;"<?php } ?>> 
        		<?php if(empty($custom_action_buttons) && $custom_action_buttons == NULL){?>       		
                <div class="only-desktops"  style="white-space: nowrap">         	
                    <?php if(!$unset_edit){?>
                    	<?php
							$edit_button_text = isset($edit_button_text) ? $edit_button_text : $this->l('list_edit');
							$edit_button_class = isset($edit_button_class) ? $edit_button_class : 'btn btn-default';
							$edit_button_icon = isset($edit_button_icon) ? $edit_button_icon : 'fa fa-pencil';
							if(check_dependency($row, $action_dependency, $edit_button_text)){
						?>
                            <a class="<?php echo $edit_button_class;?>" href="<?php echo $row->edit_url?>">
                                <i class="<?php echo $edit_button_icon;?>"></i> 
                                <?php echo $edit_button_text; ?>
                             </a>
                    	<?php 
							} // close checking dependency menu
						} // close check status
						?>
                    <?php if (!empty($row->action_urls) || !$unset_read || !$unset_delete) { ?>
                        <?php if ($show_more_button) { ?>
                            <div class="btn-group dropdown">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    More
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php
                                    if(!empty($row->action_urls)){
                                        foreach($row->action_urls as $action_unique_id => $action_url){
										  $action = $actions[$action_unique_id];
										  if(check_dependency($row, $action_dependency, $action->label)){                                           
											if(isset($action->url_target) && $action->url_target == 'dialogbox'){												
                                        ?>
                                        	<li>
                                                <a href="javascript:void(0);" data-url="<?php echo $action_url;?>" class="<?=$action->image_url;?>">
                                                    <i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                </a>
                                            </li>
                                    	<?php }else if(isset($action->url_target) && $action->url_target == 'popupmodal'){?>
                                            
                                            <li>
                                                <a href="<?php echo $action_url;?>" class="<?=$action->image_url;?>">
                                                    <i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                </a>
                                            </li>
                                            
                                    	<?php } else { ?> 
                                            
                                            <li>
                                                <a href="<?php echo $action_url; ?>">
                                                    <i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                </a>
                                            </li>
                                        <?php } // close if else
										  } // close check dependency menu
										} // close foreach loop
                                    } // close action_url
                                    ?>
                                    <?php if (!$unset_read) { if(check_dependency($row, $action_dependency, $this->l('list_view'))){?>
                                        <li>
                                            <a href="<?php echo $row->read_url?>" class="<?php echo $read_button_class; ?>"><i class="fa fa-eye"></i> <?php echo $this->l('list_view')?></a>
                                        </li>
                                    <?php }} ?>
                                    <?php if (!$unset_delete) { if(check_dependency($row, $action_dependency, $this->l('list_delete'))){?>
                                        <li>
                                            <a data-target="<?php echo $row->delete_url?>" href="javascript:void(0)" title="<?php echo $this->l('list_delete')?>" class="delete-row">
                                                <i class="fa fa-times text-danger"></i>
                                                <span class="text-danger"><?php echo $this->l('list_delete')?></span>
                                            </a>
                                        </li>
                                    <?php }} ?>
                                </ul>
                            </div>
                            <?php } else {
                                if(!empty($row->action_urls)){
                                    foreach($row->action_urls as $action_unique_id => $action_url){
                                        $action = $actions[$action_unique_id];
										if(check_dependency($row, $action_dependency, $action->label)){
										if(isset($action->url_target) && $action->url_target == 'dialogbox'){
                                        ?>
                                            <a href="javascript:void(0);" data-url="<?php echo $action_url;?>" class="btn btn-default <?=$action->image_url;?>">
                                                <i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                            </a>
                                    	<?php }else if(isset($action->url_target) && $action->url_target == 'popupmodal'){?>
                                            <a href="<?php echo $action_url;?>" class="btn btn-default <?=$action->image_url;?>">
                                                <i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                            </a>
                                    	<?php } else { ?>                                    
                                            <a <?=($action->url_target != null)? 'target="'.$action->url_target.'"' : '';?> href="<?php echo $action_url; ?>" class="btn btn-default <?php echo $action->image_url;?>">
                                                <i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                            </a>                                    
                                    <?php	
										} // if else
									  } // close check dependency 
									} // foreach
                                } // if
                                if (!$unset_read) { if(check_dependency($row, $action_dependency, $this->l('list_view'))){?>
                                    <a class="btn btn-default <?php echo $read_button_class; /* $read_button_class ADDED BY MANISH */ ?>" href="<?php echo $row->read_url?>"><i class="fa fa-eye"></i> <?php echo $this->l('list_view')?></a>
                                <?php } }
                                if (!$unset_delete) { if(check_dependency($row, $action_dependency, $this->l('list_delete'))){?>
                                    <a data-target="<?php echo $row->delete_url?>" href="javascript:void(0)" title="<?php echo $this->l('list_delete')?>" class="delete-row btn btn-default">
                                        <i class="fa fa-times text-danger"></i>
                                        <span class="text-danger"><?php echo $this->l('list_delete')?></span>
                                    </a>
                                <?php } }?>
                            <?php } ?>
                    <?php } ?>
                </div>
                <div class="only-mobiles">
                    <div class="btn-group dropdown">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <?php echo $this->l('list_actions'); ?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php
                            if(!empty($row->action_urls)){
                                foreach($row->action_urls as $action_unique_id => $action_url){
                                    $action = $actions[$action_unique_id];
									if(check_dependency($row, $action_dependency, $action->label)){
									if(isset($action->url_target) && $action->url_target == 'dialogbox'){
								?>
									 <li>
										<a href="javascript:void(0);" data-url="<?php echo $action_url; ?>" <?=isset($action->image_url)? 'class="'.$action->image_url.'"' : '';?>>
											<i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
										</a>
									</li>                                        
								<?php }else if(isset($action->url_target) && $action->url_target == 'popupmodal'){?>
                                            
                                    <li>
                                        <a href="<?php echo $action_url;?>" <?=isset($action->image_url)? 'class="'.$action->image_url.'"' : '';?>>
                                            <i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                        </a>
                                    </li>
                                    
                                <?php } else { ?>
									<li>
									<a <?=($action->url_target != null)? 'target="'.$action->url_target.'"' : '';?> href="<?php echo $action_url; ?>">
										<i class="<?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
									</a>
									</li>                                
								<?php }}?>
                                   
                                <?php } //close foreach loop
                            } // //close if statement 
                            ?>
                            
							<?php if (!$unset_read) { if(check_dependency($row, $action_dependency, $this->l('list_edit'))){?>
                                <li>
                                    <a href="<?php echo $row->edit_url?>"><i class="fa fa-pencil"></i> <?php echo $this->l('list_edit'); ?></a>
                                </li>
                            <?php }} ?>
                            
                            <?php if (!$unset_read) { if(check_dependency($row, $action_dependency, $this->l('list_view'))){?>
                                <li>
                                    <a href="<?php echo $row->read_url?>"><i class="fa fa-eye"></i> <?php echo $this->l('list_view')?></a>
                                </li>
                            <?php } } ?>
                            <?php if (!$unset_delete) { if(check_dependency($row, $action_dependency, $this->l('list_delete'))){?>
                                <li>
                                    <a data-target="<?php echo $row->delete_url?>" href="javascript:void(0)" title="<?php echo $this->l('list_delete')?>" class="delete-row">
                                        <i class="fa fa-times text-danger"></i> <span class="text-danger"><?php echo $this->l('list_delete')?></span>
                                    </a>
                                </li>
                            <?php } }?>
                        </ul>
                    </div>
                </div>
                <?php }else{?>
                <?php echo $custom_action_buttons[$num_row];?>
                <?php }?>
        </td>
        <?php }?>
        
        <?php foreach($columns as $column){?>
            <td>
                <?php echo $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;' ; ?>
            </td>
        <?php }?>
    </tr>
<?php } ?>