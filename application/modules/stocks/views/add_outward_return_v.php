<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php extract($dbdata); ?>
<?=form_open($form_action, 'id="outward_form"')?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<input type="hidden" name="outward_to" value="<?=$outward_to?>" />
<div class="row">
  <div class="col-md-3">
    <div class="form-group">
      <label class="control-label">Group Type : </label>
      <input type="text" class="form-control" disabled="disabled" value="<?=$mgt_name?>" />
      <input type="hidden" name="mg_type" value="<?=$group_type?>" />
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label class="control-label">Group / Party : </label>
      <input type="text" class="form-control" disabled="disabled" value="<?=$mg_name?>" />
      <input type="hidden" name="maingroup" value="<?=$main_group_id?>" />
    </div>
  </div>
  <div class="col-md-5">
    <div class="form-group">
      <label class="control-label">Fisher Man :</label>
      <input type="text" class="form-control" disabled="disabled" value="<?=$f_cn?>" />
      <input type="hidden" name="fisherman" value="<?=$fisherman_id?>" />
    </div>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    <h5><strong>Returned Items</strong></h5>
	<?php if(isset($return_items) && !empty($return_items)){ ?>
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th width="15%">Type</th>
          <th width="16%">Category</th>
          <th width="20%">Name</th>
          <th width="11%">Qty</th>
          <th width="11%">Rate</th>
          <th width="11%">Total</th>
          <th width="11%">Return Date</th>
        </tr>
      </thead>
      <tbody id="returns" class="table-hover">
        <?php foreach($return_items as $return){ ?>
        <tr>
          <td><?=$return['type_name']?> </td>
          <td><?=$return['cat_name']?> </td>
          <td><?=$return['prod_name']?> </td>
          <td><?=$return['quantity']?> </td>
          <td><?=$return['rate']?> </td>
          <td><?=$return['total_price']?> </td>
          <td><?=get_date('', $return['return_date'])?> </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php }else{ echo '<div class="alert alert-danger text-center padding-5">No returned items found.</div>'; } ?>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    <h5><strong>Outward Items</strong></h5>
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th width="15%">Type</th>
          <th width="16%">Category</th>
          <th width="24%">Name</th>
          <th width="11%">Qty</th>
          <th width="11%">Rate</th>
          <th width="9%">Returnable</th>
          <th width="11%">Total</th>
        </tr>
      </thead>
      <tbody id="products" class="table-hover">
        <?php
		$outward_data = set_value('product', $outward_items);
		$item_id = set_value('item_id');
		$post = $this->input->post();
		//printrr($outward_data);
        if(isset($outward_data) && !empty($outward_data)){
		   if(array_key_exists('type', $outward_data) && !empty($outward_data['type'])){
			 foreach($outward_data['type'] as $key=>$outward){
				 $ID 		= @$outward_data['ID'][$key];
				 $type 		= @$outward_data['type'][$key];
				 $category 	= @$outward_data['category'][$key];
				 $name 		= @$outward_data['name'][$key];
				 $qty 		= @$outward_data['qty'][$key];
				 $rate 		= @$outward_data['rate'][$key];
				 $returnable = @$outward_data['return'][$key]; //returnable
				 $total 	= @$outward_data['total'][$key];
				 
				 $cat_name = @$outward_data['cat_name'][$key];
				 $type_name = @$outward_data['type_name'][$key];
				 $prod_name = @$outward_data['prod_name'][$key];
				
				 $readonly = '';
				 if($returnable == 'No'){
					 $readonly = 'readonly="readonly"';
				 }
		?>
        <tr>
          <td>
          	<input type="hidden" name="product[item_id][]" value="<?=$ID?>"  />
            <select name="product[type][]" class="form-control pr_type">
                <option value="<?=$type?>"><?=$type_name?></option>
            </select>
          </td>
          <td>
          <select name="product[category][]" class="form-control pr_type">
                <option value="<?=$category?>"><?=$cat_name?></option>
            </select>
          </td>
          <td>
            <select name="product[name][]" class="form-control pr_name">
              <option value="<?=$name?>"><?=$prod_name?></option>
            </select>
          </td>
          <td><input type="text" <?=$readonly?> name="product[qty][]" placeholder="Qty" class="form-control pr_qty" value="0"></td>
          <td><input type="text" <?=$readonly?> name="product[rate][]" value="0" class="form-control pr_rate"></td>
          <td>
            <select disabled="disabled" name="product[return][]" class="form-control pr_returns">
              <option <?=($returnable=='Yes') ? 'selected="selected"' : '';?>  value="Yes">Yes</option>
              <option <?=($returnable=='No') ? 'selected="selected"' : '';?> value="No">No</option>
            </select>
          </td>
          <td><input type="text" name="product[total][]" class="form-control pr_total" value="" readonly="readonly"></td>
        </tr>
        <?php }}} ?>
      </tbody>
    </table>
  </div>
</div>
<div class="row">
  <div class="form-group col-sm-4">
    <div class="form-group">
      <label class="control-label">Return Date : <span class="symbol required"></span></label>
      <input type="text" class="form-control datepicker" name="return_date" value="">
    </div>
  </div>
  <div class="form-group col-sm-8">
    <div class="form-group">
      <label class="control-label">Return Remark :</label>
      <textarea rows="2" class="form-control" name="return_remark" placeholder="Enter Remark"><?=set_value('return_remark', @$return_remark)?></textarea>
    </div>
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
