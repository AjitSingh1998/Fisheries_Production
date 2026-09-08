<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
extract($dbdata);
if(isset($stylesheet) && !empty($stylesheet)){
	if(is_array($stylesheet)){
		foreach($stylesheet as $style){
			echo '<link rel="stylesheet" href="'.$style.'" />'."\n";
		}
	}
}
?>

<?=form_open($form_action)?>
<input type="hidden" name="action_mode" value="<?=$action_mode?>" />
<input type="hidden" class="f_ids" value="<?=(@$pf_data['ID'] != "")? @$pf_data['ID'] : '0'?>" />
<div class="row">
  <div class="form-group col-md-2">
    <label class="control-label">Code : <span class="symbol required"></span></label>
    <input type="text" class="form-control" name="code" placeholder="Enter Code" value="<?=set_value('code', @$pf_data['Code'])?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label">Name : <span class="symbol required"></span></label>
    <input type="text" class="form-control" name="name" placeholder="Enter Name" value="<?=set_value('name', @$pf_data['Name'])?>">
  </div>
  <div class="form-group col-md-3">
    <label class="control-label">Group Type: <span class="symbol required"></span></label>
    <select class="form-control maingroup_type" id="maingroup_type" name="maingroup_type">
      <option value="">Select Type</option>
      <?php
		$value = set_value('maingroup_type', @$pf_data['group_type_id']);
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
        <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
        <?php }} ?>
    </select>
  </div>
  <div class="form-group col-md-3">
    <label class="control-label">Main Group : <span class="symbol required"></span></label>
    <?php
    $disabled = 'disabled="disabled"';
	if(isset($m_groups) && !empty($m_groups)){$disabled = '';}
	?>
    <select class="form-control maingroup" id="maingroup" name="maingroup" <?=$disabled?>>
      <option value="">Select Group</option>
      <?php
		if(!empty($m_groups)){
			$maingroup = set_value('maingroup', @$pf_data['MainGroup']);
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
  <div class="form-group contact col-md-2">
    <label class="control-label">Contact :</label>
    <input type="tel" class="form-control" name="contact" placeholder="Enter contact" value="<?=set_value('contact', @$pf_data['Contact'])?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label">Village :</label>
    <input type="text" class="form-control" name="village" placeholder="Enter Village" value="<?=set_value('village', @$pf_data['Village'])?>">
  </div>
  <div class="form-group col-md-3">
    <label class="control-label">Document Name : <span class="symbol required"></span></label>
	<select class="form-control" name="doc_name">
      <option value="">Select Doc Type</option>
      <option <?=(set_value('doc_name', @$pf_data['doc_name'])=='Aadhar') ? 'selected="selected"' : '';?> value="Aadhar">Aadhar</option>
      <option <?=(set_value('doc_name', @$pf_data['doc_name'])=='Voterid') ? 'selected="selected"' : '';?> value="Voterid">Voter ID</option>
      <option <?=(set_value('doc_name', @$pf_data['doc_name'])=='Rashancard') ? 'selected="selected"' : '';?> value="Rashancard">Rashan Card</option>
      <option <?=(set_value('doc_name', @$pf_data['doc_name'])=='DL') ? 'selected="selected"' : '';?> value="DL">Driving Licence</option>
    </select>    
  </div>
  <div class="form-group col-md-3">
    <label class="control-label">Document Number : <span class="symbol required"></span></label>
    <input type="text" class="form-control" name="doc_number" placeholder="Enter Number" value="<?=set_value('doc_number', @$pf_data['doc_number'])?>">
  </div>
</div>
<div class="row">
  <div class="form-group col-md-2">
    <label class="control-label">IFSC Code :</label>
    <input type="text" class="form-control" name="ifsccode" placeholder="Enter IFSC Code" value="<?=set_value('ifsccode', @$pf_data['IfscCode'])?>">
  </div>
  <div class="form-group col-md-4">
    <label class="control-label">Bank Name :</label>
    <input type="text" class="form-control" name="bank" placeholder="Enter Bank Name" value="<?=set_value('bank', @$pf_data['Bank'])?>">
  </div>
  <div class="form-group col-md-3">
    <label class="control-label">Account No :</label>
    <input type="tel" class="form-control" name="accountno" placeholder="Enter Account No" value="<?=set_value('accountno', @$pf_data['AccountNo'])?>">
  </div>
  
  <div class="form-group col-md-3">
    <label class="control-label">Branch Name :</label>
    <input type="text" class="form-control" name="branch" placeholder="Enter Branch Name" value="<?=set_value('branch', @$pf_data['Branch'])?>">
  </div>
</div>
<div class="row">
  <div class="form-group majorfee col-md-2">
    <label class="control-label">Major Fee : <span class="symbol required"></span></label>
    <input type="text" class="form-control" name="majorfee" placeholder="Enter Major Fee" value="<?=set_value('majorfee', @$pf_data['MajorFee'])?>">
  </div>
  <div class="form-group minorfee col-md-2">
    <label class="control-label">Minor Fee : <span class="symbol required"></span></label>
    <input type="text" class="form-control" name="minorfee" placeholder="Enter Minor Fee" value="<?=set_value('minorfee', @$pf_data['MinorFee'])?>">
  </div>
  <div class="form-group sawalfee col-md-2">
    <label class="control-label">Sawal Fee : <span class="symbol required"></span></label>
    <input type="text" class="form-control" name="sawalfee" placeholder="Enter Sawal Fee" value="<?=set_value('sawalfee', @$pf_data['SawalFee'])?>">
  </div>
  <div class="form-group col-md-6">
    <label class="control-label">Remark :</label>
    <textarea rows="2" class="form-control" name="remark" placeholder="Enter Remark"><?=set_value('remark', @$pf_data['Remark'])?></textarea>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-3">
  	<label class="control-label"> Status <span class="symbol required"></span> </label>
    <select name="status" class="form-control">
        <option value="">Select Status</option>
        <option <?=(set_value('status', @$pf_data['status']) == 'Active' ? 'selected="selected"' : '')?> value="Active">Active</option>
        <option <?=(set_value('status', @$pf_data['status']) == 'Inactive' ? 'selected="selected"' : '')?> value="Inactive">Inactive</option>
    </select>
  </div>
  <div class="col-sm-5">
    <label> Add Secondary Fisherman </label>
    <select class="form-control select-fisherman"></select>
  </div>
  <div class="col-sm-2">
    <label>
        &nbsp;
    </label>
    <button type="button" class="form-control btn btn-primary add_sfm"><i class="fa fa-plus"></i> Add</button>
  </div>
</div>

<div class="row">
  <div class="form-group col-md-12">
    <h5><strong>Secondary Fisherman :</strong></h5>
    <table class="table table-striped " width="100%">
      <thead>
        <tr>
          <th>Code :</th>
          <th>Samiti :</th>
          <th>Fisherman :</th>
          <th>&nbsp; </th>
        </tr>
      </thead>
      <tbody id="secondary" class="table-hover">
      	<?php
		$joined_fisher_ids = array();
		if(isset($sf_data) && !empty($sf_data)){
			$joined_fisher_ids = array_flip($sf_data['id']);
		}
		$sf = set_value('sf', @$sf_data);
        if(isset($sf) && !empty($sf)){
		  if(!empty($sf['samiti'])){
			$i = 0;
			foreach($sf['samiti'] as $key=>$value){
				$db_id  = @$sf['db_id'][$key];
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
                <input type="hidden" class="f_ids" name="sf[id][]" value="<?=$sf_id?>">
            </td>
            <td><?=$name?></td>
            <td>
                <?php if(!array_key_exists($sf_id, $joined_fisher_ids)){ ?>
                <button type="button" class="btn btn-danger btn-xs remove_row" title="Delete Row">
                    <span class="glyphicon glyphicon-remove-sign"></span>
                </button>
                <?php } ?>
            </td>
        </tr>
        <?php $i++;}}} ?>
      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <?php /*?>
  <div class="col-sm-2">
    <label>
        Group Type
    </label>
    <select id="sf_grouptype" name="sf_mg_type" class="form-control sf_mg_type">
        <option value="">Select Group</option>
        <?php
		$value = set_value('sf_mg_type', $sf_mg_type);
		if($value){
			$m_groups = $this->SM->get_mg_data($value);
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
        <option <?=$selected?> value="<?=$op_val?>"><?=$op_text?></option>
        <?php }} ?>
    </select>
  </div>
  <div class="col-sm-3">
    <label>
        Group
    </label>
    <select id="sf_maingroup" name="sf_mg" class="form-control sf_mg" disabled="disabled">
        <option value="">Select Maingroup</option>
    </select>
  </div>
  <div class="col-sm-5">
    <label>
        Fisherman
    </label>
    <select id="sf_fisherman" name="sf_fisher" class="form-control sf_fisherman" disabled="disabled">
        <option value="">&nbsp;</option>
    </select>
  </div>
  <?php */?>
  
  
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
