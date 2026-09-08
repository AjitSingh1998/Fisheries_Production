<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<style>
input.form-control {
	padding-left: 0px !important;
	padding-right: 0px !important;
}
span.label{font-size:90% !important;font-weight:normal;}
</style>
<?php
$date = (isset($lastdailytoll) && !empty($lastdailytoll)) ? get_date('d/m/Y', $lastdailytoll['Date']) : '';
$defaultPoint = (isset($lastdailytoll) && !empty($lastdailytoll)) ? $lastdailytoll['Point'] : '';

$point_option = '';
$disabled = $page_code = $operator_name = '';
$datepicker = 'datepicker';
$dtollid = '';
if(!empty($fishingpoints)){
 foreach($fishingpoints as $point){
	$selected = ($defaultPoint == $point['ID']) ? 'selected="selected"' : '';
	$point_option .= '<option '.$selected.' value="'.$point['ID'].'">'.$point['Name'].'</option>';	
 }
}
if(!empty($dt_data)){
	$date = date("d/m/Y", strtotime($dt_data['Date']));
	$point_option = '<option value="'.$dt_data['Point'].'">'.$dt_data['Name'].'</option>';
	$page_code = $dt_data['page_code'];
	$dtollid = $dt_data['ID'];
	$operator_name = 'By '.$dt_data['operator_name'];
	$disabled = 'readonly="readonly"';
	$datepicker = '';
}

?>
<div class="container-fluid container-fullw">
    <div class="panel panel-white">
        <div class="panel-heading border-light light-bg">
        	<h3 class="text-center">DailyToll Detail Info <br> <small><?=$operator_name?></small></h3>
            <div class="row">
                <div class="col-sm-3">
                	<a href="<?=site_url('dailytoll')?>" class="btn btn-primary close-dailytollwindow">Back to DailyToll</a>
                </div>
                <div class="col-sm-9">
                    <form class="form-inline" action="">
                      <div class="form-group date_grp">
                        <label for="Date">Date : </label>
                        <input type="tel" class="form-control <?=$datepicker?>" value="<?=$date;?>" id="date" <?=$disabled?>>
                      </div>
                      <div class="form-group">
                        <label for="point">&nbsp;&nbsp;&nbsp; Point : </label>
                        <select class="form-control" id="point" style="min-width:200px;"><?=$point_option?></select>
                      </div>
                      <div class="form-group">
                        <label for="pno">&nbsp;&nbsp;&nbsp; P. No. : </label>
                        <input type="tel" class="form-control page_code disabled" readonly="readonly" name="page_code" value="<?php echo $page_code;?>">
                      </div>
                      <div class="form-group">
                        <label>&nbsp; </label>
                        <div class="input-group">
                            <button type="button" class="btn btn-warning export_data"> <i class="fa fa-file-excel-o"></i> Excel</button>&nbsp;
                            <button type="button" class="btn btn-success print_data"> <i class="fa fa-print"></i> Print</button>
                       	</div>
                       </div>
                    </form>
                </div>
                
            </div>            
        </div>
        <div class="panel-body">
            <table class="table table-striped " width="100%">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:3%;"></th>
                        <th rowspan="2" style="width:20%;" class="text-center"><span data-toggle="tooltip" title="">Code ( Group / Name ):</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Catla">Catla:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Rohu">Rohu:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Mragal">Mragal:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="KaalBasu">KaalBasu:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Anya Karp">Anya Karp:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Sawal">Sawal:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Local Major">LMj:</span></th>
                        <th style="width:4%;" class="text-center"><span data-toggle="tooltip" title="Local Minor">LMn:</span></th>
                        <th colspan="2" style="width:10%;" class="success text-center"><span data-toggle="tooltip" title="Total Major">Total Major</span></th>
                        <th colspan="2" style="width:10%;" class="success text-center"><span data-toggle="tooltip" title="Total">Total:</span></th>
                        <th style="width:4%;" class="success noExl no-print"></th>
                    </tr>
                    <tr class="text-center">
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Major Quantity">Mj.Q</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Major Weight">Mj.W</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Quantity">Qty.</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Weight">Wt.</span></th>
                        <th style="width:4%;" class="success noExl no-print"></th>
                    </tr>
                </thead>
                <tbody id="sale_products" class="table-hover">
                    <?php 
					$tCqty = $tCwt = $tRqty = $tRwt = $tMqty = $tMwt = $tKqty = $tKwt = $tAqty = $tAwt = $tSqty = $tSwt = $tLqty = $tLwt = $tLocalminor = $tTqty = $tTwt =  '0.00';
					if(!empty($dti_data)){
						foreach($dti_data as $dti){
					?>
                    <tr class="saved text-center">
                        <td class="text-center">
                        	<button type="button" class="btn btn-danger btn-xs noExl no-print delete_row" data-dtiid="<?=url_encryptor("encrypt", $dti['ID']);?>" title="Delete Row"> 
                            	<i class="fa fa-times"></i>
                            </button>
                            <input type="hidden" name="f_group" class="f_group noExl" value="<?=$dti['group_type']?>" />
                            <input type="hidden" name="f_maingroup" class="f_maingroup noExl" value="<?=$dti['Samiti']?>" />
                            <input type="hidden" name="fisherman_id" class="fisherman_id noExl" value="<?=$dti['CompanyId']?>" />
                            <input type="hidden" name="f_name" class="f_name noExl" value="<?=$dti['Name']?>" />
                            <input type="hidden" name="govt_charges" class="govt_charges noExl" value="<?=$dti['govt_charges']?>" />
                            <input type="hidden" name="f_MajorFee" class="f_MajorFee noExl" value="<?=$dti['MajorFee']?>" />
                            <input type="hidden" name="f_MinorFee" class="f_MinorFee noExl" value="<?=$dti['MinorFee']?>" />
                            <input type="hidden" name="f_SawalFee" class="f_SawalFee noExl" value="<?=$dti['SawalFee']?>" />
                        </td>
                        <td class="text-left">
                            <span class="fisherman_name">
								<span class="code"><?=$dti['Code']?></span>
                                <span class="label label-warning"><?=$dti['Samiti_name']?></span> 
								<span class="name"><?=$dti['Name']?> </span>
                            </span>
                        </td>
                        <td class="td_editable cc qty" data-name="cqty" data-class="form-control qty"><?=$dti['Cqty']?></td>
                        <td class="td_editable cc wt warning" data-name="cwt" data-class="form-control wt"><?=$dti['Cwt']?></td>
                        <td class="td_editable cc qty" data-name="rqty" data-class="form-control qty"><?=$dti['Rqty']?></td>
                        <td class="td_editable cc wt warning" data-name="rwt" data-class="form-control wt"><?=$dti['Rwt']?></td>
                        <td class="td_editable cc qty" data-name="mqty" data-class="form-control qty"><?=$dti['Mqty']?></td>
                        <td class="td_editable cc wt warning" data-name="mwt" data-class="form-control wt"><?=$dti['Mwt']?></td>
                        <td class="td_editable cc qty" data-name="kqty" data-class="form-control qty"><?=$dti['Kqty']?></td>
                        <td class="td_editable cc wt warning" data-name="kwt" data-class="form-control wt"><?=$dti['Kwt']?></td>
                        <td class="td_editable cc qty" data-name="aqty" data-class="form-control qty"><?=$dti['Aqty']?></td>
                        <td class="td_editable cc wt warning" data-name="awt" data-class="form-control wt"><?=$dti['Awt']?></td>
                        <td class="td_editable cc qty" data-name="sqty" data-class="form-control sqty qty"><?=$dti['Sqty']?></td>
                        <td class="td_editable cc wt warning" data-name="swt" data-class="form-control swt wt"><?=$dti['Swt']?></td>
                        
                        <!-- Local Major qty and weight -->
                        <td class="td_editable cc qty" data-name="lqty" data-class="form-control lqty qty"><?=$dti['Lqty']?></td>
                        <td class="td_editable cc wt warning" data-name="lwt" data-class="form-control lwt wt"><?=$dti['Lwt']?></td>
                        
                        <!-- Local Minor weight -->
                        <td class="td_editable cc wt warning" data-name="localminor" data-class="form-control localminor_wt wt"><?=$dti['Localminor']?></td>
                        
                        <!-- Total qty and weight of Catla, Rohu, Mragal, KaalBasu, Anya Karp:, Local Major -->
                        <td class="success td_editable" data-name="tmqty" data-class="form-control tmqty"><?=$dti['Tmqty']?></td>
                        <td class="success td_editable" data-name="tmwt" data-class="form-control tmwt"><?=$dti['Tmwt']?></td>
                        
                        <!-- Total qty and weight of all fishes -->
                        <td class="success td_editable" data-name="tqty" data-class="form-control tqty"><?=$dti['Tqty']?></td>
                        <td class="success td_editable" data-name="twt" data-class="form-control twt"><?=$dti['Twt']?></td>
                        
                        <td class="success noExl no-print"><button type="button" data-mode="edit" data-dtollid="<?=$dtollid?>" data-dtiid="<?=url_encryptor("encrypt", $dti['ID']);?>" class="btn btn-warning btn-xs edit_row" title="Edit Row">Edit</button></td>
                    </tr>
                    <?php 
						$tCqty += $dti['Cqty'];
						$tCwt +=  $dti['Cwt'];
						$tRqty +=  $dti['Rqty'];
						$tRwt +=  $dti['Rwt'];
						$tMqty +=  $dti['Mqty'];
						$tMwt +=  $dti['Mwt'];
						$tKqty +=  $dti['Kqty'];
						$tKwt +=  $dti['Kwt'];
						$tAqty +=  $dti['Aqty'];
						$tAwt +=  $dti['Awt'];
						$tSqty +=  $dti['Sqty'];
						$tSwt +=  $dti['Swt'];
						$tLqty +=  $dti['Lqty'];
						$tLwt +=  $dti['Lwt'];
						$tLocalminor +=  $dti['Localminor'];
						$tTqty +=  $dti['Tqty'];
						$tTwt +=  $dti['Twt'];
					}} ?>
                </tbody>
                <tbody>
                    <tr class="text-center">
                        <th rowspan="2" style="width:3%;"></th>
                        <th rowspan="2" style="width:20%;" class="text-center"><span data-toggle="tooltip" title="">Code ( Group / Name ):</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:4%;" class="warning"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Major Quantity">Mj.Q</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Major Weight">Mj.W</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Quantity">Qty.</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Weight">Wt.</span></th>
                        <th style="width:4%;" class="success noExl no-print"></th>
                    </tr>
                    <tr>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Catla">Catla:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Rohu">Rohu:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Mragal">Mragal:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="KaalBasu">KaalBasu:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Anya Karp">Anya Karp:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Sawal">Sawal:</span></th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Local Major">LMj:</span></th>
                        <th style="width:4%;" class="text-center"><span data-toggle="tooltip" title="Local Minor">LMn:</span></th>
                        <th colspan="2" style="width:10%;" class="success text-center"><span data-toggle="tooltip" title="Total Major">Total Major</span></th>
                        <th colspan="2" style="width:10%;" class="success text-center"><span data-toggle="tooltip" title="Total">Total:</span></th>
                        <th style="width:4%;" class="success text-center noExl no-print"></th>
                    </tr>
                    <tr class="g_total success">
                        <th>Total: </th>
                        <th>
                        	<div class="input-group noExl no-print">
                            	<input type="text" name="search_fisher" class="form-control" placeholder="Search Fisherman">
                                <span class="input-group-btn">
                                    <button class="btn btn-default search_fisher" type="button"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </th>
                        <th class="cqty text-center"><?=$tCqty?></th>
                        <th class="cwt text-center warning"><?=$tCwt?></th>
                        <th class="rqty text-center"><?=$tRqty?></th>
                        <th class="rwt text-center warning"><?=$tRwt?></th>
                        <th class="mqty text-center"><?=$tMqty?></th>
                        <th class="mwt text-center warning"><?=$tMwt?></th>
                        <th class="kqty text-center"><?=$tKqty?></th>
                        <th class="kwt text-center warning"><?=$tKwt?></th>
                        <th class="aqty text-center"><?=$tAqty?></th>
                        <th class="awt text-center warning"><?=$tAwt?></th>
                        <th class="sqty text-center"><?=$tSqty?></th>
                        <th class="swt text-center warning"><?=$tSwt?></th>
                        <th class="lqty text-center"><?=$tLqty?></th>
                        <th class="lwt text-center warning"><?=$tLwt?></th>
                        <th class="localminor text-center warning"><?=$tLocalminor?></th>
                        <th class=""></th>
                        <th class=""></th>
                        <th class="gtqty text-center"><?=$tTqty?></th>
                        <th class="gtwt text-center"><?=$tTwt?></th>
                        <th noExl no-print></th>
                    </tr>
                </tbody>
            </table>
            
            <table class="asset hidden">
                <tr id="row_asset" class="hidden editable text-center">
                    <td class="text-center">
                    	<button type="button" class="btn btn-danger btn-xs noExl no-print delete_row" data-dtiid="" title="Delete Row">
                    		<i class="fa fa-times"></i>
                        </button>
                        <input type="hidden" name="f_group" class="f_group" value="" />
                        <input type="hidden" name="f_maingroup" class="f_maingroup" value="" />
                        <input type="hidden" name="fisherman_id" class="fisherman_id" value="0" />
                        <input type="hidden" name="f_name" class="f_name" value="" />
                        <input type="hidden" name="govt_charges" class="govt_charges" value="" />
                        <input type="hidden" name="f_MajorFee" class="f_MajorFee" value="" />
                        <input type="hidden" name="f_MinorFee" class="f_MinorFee" value="" />
                        <input type="hidden" name="f_SawalFee" class="f_SawalFee" value="" />
                    </td>
                    <td class="text-left"><span class="fisherman_name"></span></td>
                    <td class="td_editable cc qty" data-name="cqty" data-class="form-control qty"><input type="text" value="0" name="cqty" class="form-control qty"></td>
                    <td class="td_editable cc wt warning" data-name="cwt" data-class="form-control wt"><input type="text" value="0" name="cwt" class="form-control wt"></td>
                    <td class="td_editable cc qty" data-name="rqty" data-class="form-control qty"><input type="text" value="0" name="rqty" class="form-control qty"></td>
                    <td class="td_editable cc wt warning" data-name="rwt" data-class="form-control wt"><input type="text" value="0" name="rwt" class="form-control wt"></td>
                    <td class="td_editable cc qty" data-name="mqty" data-class="form-control qty"><input type="text" value="0" name="mqty" class="form-control qty"></td>
                    <td class="td_editable cc wt warning" data-name="mwt" data-class="form-control wt"><input type="text" value="0" name="mwt" class="form-control wt"></td>
                    <td class="td_editable cc qty" data-name="kqty" data-class="form-control qty"><input type="text" value="0" name="kqty" class="form-control qty"></td>
                    <td class="td_editable cc wt warning" data-name="kwt" data-class="form-control wt"><input type="text" value="0" name="kwt" class="form-control wt"></td>
                    <td class="td_editable cc qty" data-name="aqty" data-class="form-control qty"><input type="text" value="0" name="aqty" class="form-control qty"></td>
                    <td class="td_editable cc wt warning" data-name="awt" data-class="form-control wt"><input type="text" value="0" name="awt" class="form-control wt"></td>
                    <td class="td_editable cc qty" data-name="sqty" data-class="form-control sqty qty"><input type="text" value="0" name="sqty" class="form-control sqty qty"></td>
                    <td class="td_editable cc wt warning" data-name="swt" data-class="form-control swt wt"><input type="text" value="0" name="swt" class="form-control swt wt"></td>
                    <td class="td_editable cc qty" data-name="lqty" data-class="form-control lqty qty"><input type="text" value="0" name="lqty" class="form-control lqty qty"></td>
                    <td class="td_editable cc wt warning" data-name="lwt" data-class="form-control lwt wt"><input type="text" value="0" name="lwt" class="form-control lwt wt"></td>
                    <td class="td_editable cc wt warning" data-name="localminor" data-class="form-control localminor_wt wt"><input type="text" value="0" name="localminor" class="form-control localminor_wt wt"></td>
                    <td class="success td_editable" data-name="tmqty" data-class="form-control tmqty"><input type="number" value="0" name="tmqty" class="form-control tmqty" readonly="readonly"></td>
                    <td class="success td_editable" data-name="tmwt" data-class="form-control tmwt"><input type="number" value="0" name="tmwt" class="form-control tmwt" readonly="readonly"></td>
                    <td class="success td_editable" data-name="tqty" data-class="form-control tqty"><input type="number" value="0" name="tqty" class="form-control tqty" readonly="readonly"></td>
                    <td  class="success td_editable" data-name="twt" data-class="form-control twt"><input type="number" value="0" name="twt" class="form-control twt" readonly="readonly"></td>
                    <td class="success noExl no-print"><button type="button" data-mode="add" data-dtollid="<?=$dtollid?>" data-dtiid="" class="btn btn-success btn-xs save_row" title="Save Row">Save</button></td>
                </tr>
            </table>
            
            <!-- end: WIZARD FORM --> 
        </div>
        <div class="panel-footer border-light light-bg">
            <form class="form-horizontal">
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="form-group form-group-lg">
                            <label class="control-label col-sm-4 text-right">Select Fisherman</label>
                            <div class="col-sm-6">
                                <select class="form-control select-fisherman"></select>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-primary btn-lg add_fisherman"><i class="fa fa-plus"></i> Add</button>
                            </div>
                        </div>
                    </div>
                </div>
			</form>
		</div>
    </div>
</div>
