<?php defined('BASEPATH') OR exit('No direct script access allowed');
//printrr($pf_data);
extract($dbdata);
?>
<?=form_open($form_action)?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<input type="hidden" name="pf_id" value="<?=@$pf_data['ID']?>" />
<div class="row">
  <div class="form-group col-md-2">
    <label class="control-label">Code :</label>
    <input type="text" class="form-control" disabled="disabled" value="<?=@$pf_data['Code']?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label">Name : <span class="symbol required"></span></label>
    <input type="text" class="form-control" disabled="disabled" value="<?=@$pf_data['Name']?>">
  </div>
  <div class="form-group col-md-3">
    <label class="control-label">Group Type: <span class="symbol required"></span></label>
    <select class="form-control maingroup_type" disabled="disabled">
      <option value="">Select Type</option>
      <?php
		$value = @$pf_data['group_type_id'];
		if($value){
			$m_groups = $this->SM->get_all_mg_data($value);
		};
        if(!empty($mg_type)){
            $selected = '';
			foreach($mg_type as $group){
                $op_val = $group['ID'];
                $op_text = $group['Name'];
        	
			if($value == $op_val){
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}
		?>
      <option <?=$selected?> value="<?=$op_val?>">
      <?=$op_text?>
      </option>
      <?php }} ?>
    </select>
  </div>
  <div class="form-group col-md-3">
    <label class="control-label">Main Group : </label>
    <?php
    $disabled = 'disabled="disabled"';
	if(isset($m_groups) && !empty($m_groups)){$disabled = '';}
	?>
    <select class="form-control maingroup" disabled="disabled" >
      <option value="">Select Group</option>
      <?php
		if(!empty($m_groups)){
			$maingroup = @$pf_data['MainGroup'];
			$selected = '';
			foreach($m_groups as $smt){
				$op_val = $smt['ID'];
				$op_text = $smt['Name'];
				if($maingroup == $op_val){
					$selected = 'selected="selected"';
				}else{
					$selected = '';
				}
	   ?>
      <option <?=$selected?> value="<?=$op_val?>">
      <?=$op_text?>
      </option>
      <?php }} ?>
    </select>
  </div>
</div>

<div class="row">
	<div class="form-group col-md-12">
	<?php
$outward_data = isset($outward_items) ? $outward_items : array(); 
if(!empty($outward_data)){ 
?>
    <h5 class="text-danger"><strong>Outward Items :</strong></h5>
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th width="15%">Type</th>
          <th width="16%">Category</th>
          <th width="20%">Name</th>
          <th width="11%">Qty</th>
          <th width="11%">Rate</th>
          <th width="11%">Total</th>
        </tr>
      </thead>
      <tbody id="products" class="table-hover">
        <?php
		   if(array_key_exists('type', $outward_data) && !empty($outward_data['type'])){
			 foreach($outward_data['type'] as $key=>$outward){
				 $fid 		= @$outward_data['fid'][$key];
				 $outid 	= @$outward_data['outid'][$key];
				 $name 		= @$outward_data['name'][$key];
				 $qty 		= @$outward_data['qty'][$key];
				 $rate 		= @$outward_data['rate'][$key];
				 $total 	= @$outward_data['total'][$key];
				 
				 //$pr_categories = $this->SM->get_pr_cat($type);
				 //$products = $this->SM->get_pr_items($category);
				 
				 $pr_Type = @$outward_data['type_name'][$key];
				 $pr_Cat = @$outward_data['cat_name'][$key];
				 $pr_Name = @$outward_data['prod_name'][$key];
		?>
        <tr>
          <td>
            <select name="product[type][]" class="form-control pr_type" disabled="disabled">
              	<option><?=$pr_Type?></option>
            </select>
          </td>
          <td>
            <select name="product[category][]" class="form-control pr_category" disabled="disabled">
                <option><?=$pr_Cat?></option>
            </select>
          </td>
          <td>
            <select name="product[name][]" class="form-control pr_name" disabled="disabled">
              <option value="">Product</option>
                <option><?=$$pr_Name?></option>
            </select>
          </td>
          <td><input type="text" placeholder="Qty" class="form-control pr_qty" value="<?=$qty?>" disabled="disabled"></td>
          <td><input type="text" value="<?=$rate?>" class="form-control pr_rate" disabled="disabled"></td>
          <td><input type="text" class="form-control pr_total" value="<?=$total?>" disabled="disabled"></td>
        </tr>
        <?php }}?>
      </tbody>
    </table>
    <?php }else {echo '<div class="alert alert-danger text-center padding-5">No outward items found.</div>';} ?>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    <h5 class="text-danger"><strong>Returned Items</strong></h5>
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
    <h5 class="text-danger"><strong>Total Liabilily/Outstanding Detail :</strong></h5>
    <table class="table table-striped " width="100%">
      <tbody>
<?php
$prod_liability = isset($pf_data['prod_liability']) ? $pf_data['prod_liability'] : 0.00;
$total_cash_received = isset($pf_data['total_cash_received']) ? $pf_data['total_cash_received'] : 0.00;
$returned_amt = isset($pf_data['returned_amt']) ? $pf_data['returned_amt'] : 0.00;
$deposit_amt = isset($pf_data['deposit_amt']) ? $pf_data['deposit_amt'] : 0.00;
$remaining_amount = ($prod_liability - ($returned_amt + $deposit_amt + $total_cash_received));
?>
        <tr>
			<td width="90%" class="text-right">Total Outward Amount: </td>
            <td class="text-right" width="10%"><?=number_format($prod_liability, 2)?></td>
        </tr>
        <tr>
			<td class="text-right">Total Cash Received: </td>
            <td class="text-right">-<?=number_format($total_cash_received, 2)?></td>
        </tr>
        <tr>
			<td class="text-right">Total Outward Return: </td>
            <td class="text-right">-<?=number_format($returned_amt, 2)?></td>
        </tr>
        <tr>
			<td class="text-right">Total Deposited Amount: </td>
            <td class="text-right">-<?=number_format($deposit_amt, 2)?></td>
        </tr>
        <tr>
			<td class="text-right"><span class="text-danger text-bold">Remaining Amount: </span></td>
            <td class="text-right">
            	<input type="hidden" name="remaining_amount" value="<?=$remaining_amount?>"  />
                <span class="text-danger text-bold">
				<?=number_format($remaining_amount,2)?>
                </span>
            </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    <h5 class="text-danger"><strong>Secondary Fisherman :</strong></h5>
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th>Code :</th>
          <th>Samiti :</th>
          <th>Fisherman :</th>
          <th>Liability Amount :</th>
        </tr>
      </thead>
      <tbody id="secondary" class="table-hover">
        <?php
		$sf = set_value('sf', @$sf_data);
        if(isset($sf) && !empty($sf)){
		  if(!empty($sf['samiti'])){
			foreach($sf['samiti'] as $key=>$value){
				$samiti = $sf['samiti'][$key];
				$code 	= $sf['code'][$key];
				$name 	= $sf['name'][$key];
				$sf_id 	= $sf['id'][$key];
				$sf_amount 	= $sf['amount'][$key];
				
		?>
        <tr>
          <td><?=$code?></td>
          <td><?=$samiti?>
            <input type="hidden" name="sf[samiti][]" value="<?=$samiti?>">
            <input type="hidden" name="sf[code][]" value="<?=$code?>">
            <input type="hidden" name="sf[name][]" value="<?=$name?>">
            <input type="hidden" name="sf[id][]" value="<?=$sf_id?>">
          </td>
          <td><?=$name?></td>
          <td> <input type="text" class="form-control" name="sf[amount][]" value="<?=$sf_amount?>" /> </td>
        </tr>
        <?php }}} ?>
      </tbody>
    </table>
  </div>
</div>

<div class="row" id="submit_response">
  <div class="col-sm-12">
    <p>&nbsp;</p>
    <?=validation_errors()?>
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
