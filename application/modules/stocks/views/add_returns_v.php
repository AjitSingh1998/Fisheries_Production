<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?=form_open($form_action)?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<div class="row">
  <div class="form-group col-md-4">
    <label class="control-label" for="adhaar">Receipt Number :</label>
    <input type="text" class="form-control" name="adhaar" placeholder="Enter Adhaar No" value="<?=set_value('adhaar', @$pf_data['Adhaar'])?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label" for="maingroup">Return Date : <span class="symbol required"></span></label>
    <input type="tel" class="form-control" name="remark" value="<?=set_value('remark', @$pf_data['Remark'])?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label" for="accountno">Vendor Name :</label>
    <input type="tel" class="form-control" name="accountno" value="<?=set_value('accountno', @$pf_data['AccountNo'])?>">
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th><button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button></th>
          <th>Product Type :</th>
          <th>Product Category :</th>
          <th>Product Name :</th>
          <th>Quantity :</th>
          <th>Rate :</th>
          <th>Total :</th>
        </tr>
      </thead>
      <tbody id="secondary" class="table-hover">
      	<?php
		
		$sf 	= set_value('sf', @$sf_data);
        if(isset($sf) && !empty($sf)){
		  //echo '<pre>'; print_r($sf_id); print_r($sf); echo '</pre>';
		  
		  if(!empty($sf['samiti'])){
			$i = 0;
			foreach($sf['samiti'] as $key=>$value){
				//echo $key.'--'.$value.'<br>';
				$samiti = $sf['samiti'][$key];
				$code 	= $sf['code'][$key];
				$name 	= $sf['name'][$key];
				$sf_id 	= $sf['id'][$key];
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
            <td>
                <button type="button" class="btn btn-danger btn-xs remove_row" title="Delete Row">
                    <span class="glyphicon glyphicon-remove-sign"></span>
                </button>
            </td>
        </tr>
        <?php $i++;}}} ?>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div class="form-group col-sm-8">
      <div class="form-group">
        <label class="control-label" for="remark">Remark :</label>
        <textarea rows="2" class="form-control" name="remark" placeholder="Enter Remark"><?=set_value('remark', @$pf_data['Remark'])?></textarea>
      </div>
  </div>
  <div class="form-group col-sm-4">
      <div class="form-group">
        <label class="control-label" for="maingroup">Total Price : <span class="symbol required"></span></label>
        <input type="number" class="form-control" name="remark" value="<?=set_value('remark', @$pf_data['Remark'])?>">
      </div>
  </div>
</div>

<div class="row" id="submit_response">
  <div class="col-sm-12">
  	<p>&nbsp;</p>
	<?php
	$message = $this->messageci->display();
	$this->session->set_flashdata('_messages', '');
	$msg = '';
	if (is_array($message)) { for($i=0; $i<count($message); $i++) $msg .= $message[$i]; }
	echo $msg;
	?>
    <?=validation_errors()?>
  </div>
</div>
<?=form_close()?>


