<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<?=form_open($form_action, 'id="outward_form"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<div class="row">
	<div class="col-md-4">
        <label>Outward To:</label>
        <div class="input-group">
        	<?php
				$deposited_by = set_value('outward_to', @$outward_to);
				$by_fisher = '';
				$by_group = '';
				if(isset($deposited_by) && !empty($deposited_by)){
					if($deposited_by == 'Fisherman'){
						$by_fisher = 'checked="checked"';
					}elseif($deposited_by == 'Group'){
						$by_group = 'checked="checked"';
					}
				}else{
					$by_fisher = 'checked="checked"';
				}
			?>
            <div class="radio clip-radio radio-primary radio-inline">
                <input type="radio" id="to_fisher" name="outward_to" value="Fisherman" <?=$by_fisher?>>
                <label for="to_fisher">Fisherman</label>
            </div>
            <div class="radio clip-radio radio-primary radio-inline">
                <input type="radio" id="to_group" name="outward_to" value="Group" <?=$by_group?>>
                <label for="to_group">Group</label>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="form-group col-md-3">
    <label class="control-label">Group Type: <span class="symbol required"></span></label>
    <select class="form-control group_type_id" id="group_type_id" name="group_type_id">
      <option value="">Select Group</option>
      <?php
	  	$group_type_id = set_value('group_type_id', @$group_type);
		if($group_type_id){
			$m_groups = $this->SM->get_mg_data($group_type_id);
		}
        if(!empty($mg_type)){
            $selected = '';
			foreach($mg_type as $group){
                $op_val = $group['ID'];
                $op_text = $group['Name'];
        	
			if($group_type_id == $op_val){
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}
		?>
        <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
        <?php }} ?>
    </select>
  </div>
  
  <div class="form-group col-md-5">
    <label class="control-label">Main Group : <span class="symbol required"></span></label>
    <?php
    $maingroup_id = set_value('maingroup_id', @$main_group_id);
    $disabled = 'disabled="disabled"';
	if(isset($m_groups) && !empty($m_groups)){
		$mg_fishers = $this->SM->get_all_mg_fisherman($maingroup_id);
		//printrr($mg_fishers);
		$disabled = '';
	}
	?>
    <select class="form-control maingroup_id" id="maingroup_id" name="maingroup_id" <?=$disabled?>>
      <option value="">Select Maingroup</option>
      <?php
		if(!empty($m_groups)){
			$selected = '';
			foreach($m_groups as $smt){
				$op_val = $smt['ID'];
				$op_text = $smt['Name'];
				if($maingroup_id == $op_val){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}
		?>
      <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
      <?php }} ?>
    </select>
  </div>
  
  <?php 
  $css_fisher_div = 'style="display: block;"';
  if($deposited_by == 'Group'){
	  $css_fisher_div = 'style="display: none;"';
  }
  ?>
  <div class="form-group col-md-4 fisher_div" <?=$css_fisher_div?>>
    <label class="control-label">Fisherman : </label>
    <?php
    $fisherman_id = set_value('fisherman_id', @$fisherman_id);
	$disabled = 'disabled="disabled"';
	if(isset($mg_fishers) && !empty($mg_fishers)){$disabled = '';}
	?>
    <select class="form-control fisherman_id" id="fisherman_id" name="fisherman_id" <?=$disabled?>>
      <option value="">Select Fisherman</option>
      <?php
		if(isset($mg_fishers) && !empty($mg_fishers['result1'])){
			$selected = '';
			foreach($mg_fishers['result1'] as $fisher){
				$op_val = $fisher['id'];
				$op_text = $fisher['text'];
				if($fisherman_id == $op_val){
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
						if($name == $op_val || $name == $product['ID']){
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
      <input type="text" class="form-control sub_total" name="sub_total" readonly="readonly" value="<?=set_value('sub_total', @$sub_total)?>">
    </div>
    <div class="form-group">
      <label class="control-label">Cash Recieved :</label>
      <input type="text" class="form-control cash_received" name="cash_received" value="<?=set_value('cash_received', @$cash_received)?>">
    </div>
    <div class="form-group">
      <label class="control-label">Grand Total :</label>
      <input type="text" class="form-control grand_total" readonly="readonly" name="grand_total" value="<?=set_value('grand_total', @$grand_total)?>">
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
