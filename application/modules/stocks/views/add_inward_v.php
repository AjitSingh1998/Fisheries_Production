<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?=form_open($form_action)?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<div class="row">
  <div class="form-group col-md-4">
    <label class="control-label">Receipt Number : <?php /*?><span class="symbol required"></span><?php */?></label>
    <input type="text" class="form-control" name="receipt_number" value="<?=set_value('receipt_number', @$info['receipt_number'])?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label">Inward Date : <span class="symbol required"></span></label>
    <input type="text" class="form-control datepicker" name="inward_date" value="<?=get_date('d M Y', set_value('inward_date', @$info['inward_date']))?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label">Vendor Name : <span class="symbol required"></span></label>
    <input type="text" class="form-control" name="vendor_name" value="<?=set_value('vendor_name', @$info['vendor_name'])?>">
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th width="3%">
          <?php if(isset($action_mode) && $action_mode == 'add'){ ?>
          	<button type="button" class="btn btn-success add_prow" title="Add Product"><i class="fa fa-plus"></i></button>
          <?php } ?>
          </th>
          <th width="15%">Type</th>
          <th width="16%">Category</th>
          <th width="24%">Name</th>
          <th width="11%">Qty</th>
          <th width="11%">Rate</th>
          <th width="11%">Total</th>
        </tr>
      </thead>
      <tbody id="products" class="table-hover">
        <?php
		$outward_data = set_value('product', $inward_item);
        if(isset($outward_data) && !empty($outward_data)){
		   if(array_key_exists('type', $outward_data) && !empty($outward_data['type'])){
			 foreach($outward_data['type'] as $key=>$outward){
				 $type 		= @$outward_data['type'][$key];
				 $category 	= @$outward_data['category'][$key];
				 $name 		= @$outward_data['name'][$key];
				 $qty 		= @$outward_data['qty'][$key];
				 $rate 		= @$outward_data['rate'][$key];
				 $total 	= @$outward_data['total'][$key];
				 
				 
				$pr_categories = $this->SM->get_pr_cat($type);
				$products = $this->SM->get_pr_items($category);
		?>
        <tr>
          <td>
          <?php if(isset($action) && $action == 'add'){ ?>
          	<button type="button" class="btn btn-danger remove_prow" title="Delete Product"> <span class="glyphicon glyphicon-remove-sign"></span> </button>
          <?php } ?>
          </td>
          <td>
            <select name="product[type][]" class="form-control pr_type">
                <option value="">Type</option>
                <?php
                if(!empty($pr_type)){
                    $selected = '';
					$value = $type;
					foreach($pr_type as $prt){
                        $op_val = $prt['ID'];
                        $op_text = $prt['Name'];
						if($value == $op_val){
							$selected = 'selected="selected"';
						}else{
							$selected = '';
						}
                ?>
              	<option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
              <?php }} ?>
            </select>
          </td>
          <td>
            <?php
			  $disabled = 'disabled="disabled"';
			  $value = $type;
			  if($value){$disabled = '';};
			?>
            <select name="product[category][]" class="form-control pr_category" <?=$disabled?>>
                <option value="">Category</option>
                <?php
                if(!empty($pr_categories)){
                    $selected = '';
					foreach($pr_categories as $categories){
                        $op_val = $categories['ID'];
                        $op_text = $categories['Name'];
						if($category == $op_val){
							$selected = 'selected="selected"';
						}else{
							$selected = '';
						}
                ?>
                <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
                <?php }} ?>
            </select>
          </td>
          <td>
            <?php
			  $disabled = 'disabled="disabled"';
			  $value = $category;
			  if($value){$disabled = '';};
			?>
            <select name="product[name][]" class="form-control pr_name" <?=$disabled?>>
              <option value="">Product</option>
              <?php
                if(!empty($products)){
                    $selected = '';
					foreach($products as $product){
                        $op_val = $product['ID'].'__'.$product['Rate'];
                        $op_text = $product['Name'];
						if($name == $op_val){
							$selected = 'selected="selected"';
						}else{
							$selected = '';
						}
                ?>
                <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
                <?php }} ?>
            </select>
          </td>
          <td><input type="text" name="product[qty][]" placeholder="Qty" class="form-control pr_qty" value="<?=$qty?>"></td>
          <td><input type="text" name="product[rate][]" value="<?=$rate?>" class="form-control pr_rate"></td>
          <td><input type="text" name="product[total][]" class="form-control pr_total" value="<?=$total?>" readonly="readonly"></td>
        </tr>
        <?php }}} ?>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-12">
      <div class="form-group">
        <label class="control-label" for="remark">Remark :</label>
        <textarea rows="3" class="form-control" name="remark" placeholder="Enter Remark"><?=set_value('remark', @$info['remark'])?></textarea>
      </div>
  </div>
</div>

<?=form_close()?>

<table class="table table-striped hidden asset_tbl" width="100%">
  <tr>
      <td><button type="button" class="btn btn-danger remove_prow" title="Delete Product"> <span class="glyphicon glyphicon-remove-sign"></span> </button></td>
      <td>
        <select name="product[type][]" class="form-control pr_type">
            <option value="">Type</option>
            <?php
            if(!empty($pr_type)){
                foreach($pr_type as $prt){
                    $op_val = $prt['ID'];
                    $op_text = $prt['Name'];
            ?>
          <option value="<?=$op_val?>"><?=$op_text?></option>
          <?php }} ?>
        </select>
      </td>
      <td>
        <select name="product[category][]" class="form-control pr_category" disabled="disabled">
            <option value="">Category</option>
        </select>
      </td>
      <td>
        <select name="product[name][]" class="form-control pr_name" disabled="disabled">
          <option value="">Product</option>
        </select>
      </td>
      <td><input type="text" name="product[qty][]" placeholder="Qty" class="form-control pr_qty"></td>
      <td><input type="text" name="product[rate][]" value="0" class="form-control pr_rate"></td>
      
      <td><input type="text" name="product[total][]" class="form-control pr_total" value="0" readonly="readonly"></td>
    </tr>
</table>

<?php
	if(isset($scriptsrc) && !empty($scriptsrc)){	
		if(is_array($scriptsrc)){
			foreach($scriptsrc as $script){
				echo '<script src="'.$script.'"></script>'."\n";
			}
		}
	}
?>

