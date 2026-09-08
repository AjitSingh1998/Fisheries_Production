<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<?=form_open($form_action, 'id="outward_form"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<?php
$mg_type = set_value('mg_type', $group_type);
$maingroup = set_value('maingroup', $main_group_id);
$f_cn = set_value('f_cn', $f_cn); //Fisherman code/name
$fisherman = set_value('fisherman', $fisherman_id);
$select2 = 'select-fisherman';
if(isset($fisherman_id) && !empty($fisherman_id)){
	$select2 = '';
}
?>
<input type="hidden" name="mg_type" id="mg_type" value="<?=$mg_type?>" />
<input type="hidden" name="maingroup" id="maingroup" value="<?=$maingroup?>" />
<input type="hidden" name="f_cn" id="f_cn" value="<?=$f_cn?>" />
<div class="row">
  <div class="col-md-5">
    <div class="form-group">
        <label class="control-label">Fisherman</label>
        <select name="fisherman" class="form-control <?=$select2?>" >
        <?php
        if(!empty($fisherman)){
			echo '<option value="'.$fisherman.'" selected="selected">'.$f_cn.'</option>';
		}        
        ?>
        </select>
    </div>
  </div>
</div>
<div class="row">
  <div class="form-group col-md-12">
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th width="3%"><button type="button" class="btn btn-success add_prow" title="Add Product"><i class="fa fa-plus"></i></button></th>
          <th width="15%">Type</th>
          <th width="16%">Category</th>
          <th width="20%">Name</th>
          <th width="11%">Qty</th>
          <th width="11%">Rate</th>
          <th width="11%">Returnable</th>
          <th width="11%">Total</th>
        </tr>
      </thead>
      <tbody id="products" class="table-hover">
        <?php
		$outward_data = set_value('product', $outward_items);
        if(isset($outward_data) && !empty($outward_data)){
		   if(array_key_exists('type', $outward_data) && !empty($outward_data['type'])){
			 foreach($outward_data['type'] as $key=>$outward){
				 $item_id 	= @$outward_data['ID'][$key];
				 $fid 		= @$outward_data['fid'][$key];
				 $outid 	= @$outward_data['outid'][$key];
				 $type 		= @$outward_data['type'][$key];
				 $category 	= @$outward_data['category'][$key];
				 $name 		= @$outward_data['name'][$key];
				 $qty 		= @$outward_data['qty'][$key];
				 $rate 		= @$outward_data['rate'][$key];
				 $return 	= @$outward_data['return'][$key];
				 $total 	= @$outward_data['total'][$key];
				 
				 
				 $pr_categories = $this->SM->get_pr_cat($type);
				 $products = $this->SM->get_pr_items($category);
		?>
        <tr>
          <td>
          <input type="hidden" value="<?=$item_id?>" name="product[ID][]"/>
          <button type="button" class="btn btn-danger remove_prow" data-item_id="<?=$item_id?>" data-fid="<?=$fid?>" data-outid="<?=$outid?>" title="Delete Product">
          	 <span class="glyphicon glyphicon-remove-sign"></span> </button>
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
          <td>
            <select name="product[return][]" class="form-control pr_returns">
              <option <?=($return=='Yes') ? 'selected="selected"' : '';?>  value="Yes">Yes</option>
              <option <?=($return=='No') ? 'selected="selected"' : '';?> value="No">No</option>
            </select>
          </td>
          <td><input type="text" name="product[total][]" class="form-control pr_total" value="<?=$total?>" readonly="readonly"></td>
        </tr>
        <?php }}} ?>
      </tbody>
    </table>
  </div>
</div>
<div class="row">
  <div class="form-group col-sm-8">
    <div class="form-group">
      <label class="control-label">Receipt Number : <span class="symbol required"></span></label>
      <input type="text" class="form-control" name="receipt_number" value="<?=set_value('receipt_number', @$receipt_number)?>">
    </div>
    <div class="form-group">
      <label class="control-label">Outward Date : <span class="symbol required"></span></label>
      <input type="text" class="form-control datepicker" name="outward_date" value="<?=get_date('d M Y', set_value('outward_date', @$outward_date))?>">
    </div>
    <div class="form-group">
      <label class="control-label">Remark :</label>
      <textarea rows="3" class="form-control" name="remark" placeholder="Enter Remark"><?=set_value('remark', @$remark)?></textarea>
    </div>
  </div>
  <div class="form-group col-sm-4">
    <div class="form-group">
      <label class="control-label">Sub Total : <span class="symbol required"></span></label>
      <input type="number" class="form-control sub_total" name="sub_total" readonly="readonly" value="<?=set_value('sub_total', @$sub_total)?>">
    </div>
    <div class="form-group">
      <label class="control-label">Cash Recieved :</label>
      <input type="number" class="form-control cash_received" name="cash_received" value="<?=set_value('cash_received', @$cash_received)?>">
    </div>
    <div class="form-group">
      <label class="control-label">Grand Total :</label>
      <input type="number" class="form-control grand_total" readonly="readonly" name="grand_total" value="<?=set_value('grand_total', @$grand_total)?>">
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
      <td>
        <select name="product[return][]" class="form-control pr_returns">
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>
      </td>
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
