<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php extract($dbdata); ?>
<?=form_open($form_action, 'class="form-horizontal" id="product_form"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<div class="form-group">
    <label class="col-md-3 control-label">Product Type <span class="symbol required"></span></label>
    <div class="col-sm-8">
        <select class="form-control pr_type" name="type">
          <option value="">Select Type</option>
          <?php
            if(isset($pr_type) && !empty($pr_type)){
                $value = set_value('type', @$type);
				if($value){
					$pr_cat = $this->PM->get_all_pr_cat($value);
				}
                $selected = '';
                foreach($pr_type as $option){
                    $op_val = $option['ID'];
                    $op_text = $option['Name'];
                    if($value == $op_val){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
            ?>
          <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
          <?php }} ?>
        </select>
	</div>
</div>


<div class="form-group">
    <label class="col-md-3 control-label">Product Category <span class="symbol required"></span></label>
    <div class="col-sm-8">
    	<?php
         $disabled = 'disabled="disabled"';
		 if(isset($pr_cat) && !empty($pr_cat)){
			 $disabled = '';
		 }
		?>
        <select class="form-control category" name="category" <?=$disabled?>>
          <option value="">Select Category</option>
          <?php
            if(isset($pr_cat) && !empty($pr_cat)){
                $type = set_value('category', @$Category_Id);
                $selected = '';
                foreach($pr_cat as $cat){
                    $op_val = $cat['ID'];
                    $op_text = $cat['Name'];
                    if($type == $op_val){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
            ?>
          <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
          <?php }} ?>
        </select>
	</div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"> Product Code <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Product Code" class="form-control" name="code" value="<?=set_value('code', $code)?>">
  </div>
</div>



<div class="form-group">
  <label class="col-sm-3 control-label"> Product Name <span class="symbol required"></span> </label>
  <div class="col-sm-8">
    <input type="text" placeholder="Product Name" class="form-control" name="name" value="<?=set_value('name', $Name)?>">
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label">Remark </label>
  <div class="col-sm-8">
    <textarea placeholder="Enter Remark" class="form-control" name="remark"><?=set_value('remark', $Remark)?>
</textarea>
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label">Status </label>
  <div class="col-sm-8">
    <select name="status" class="form-control">
        <option value="">Select Status</option>
        <option <?=(set_value('status', @$status) == 'Active' ? 'selected="selected"' : '')?> value="Active">Active</option>
        <option <?=(set_value('status', @$status) == 'Inactive' ? 'selected="selected"' : '')?> value="Inactive">Inactive</option>
    </select>
  </div>
</div>
<?=form_close()?>

<?php
	if(isset($scriptsrc) && !empty($scriptsrc)){	
		if(is_array($scriptsrc)){
			foreach($scriptsrc as $script){
				echo '<script src="'.$script.'"></script>'."\n";
			}
		}
	}
?>

