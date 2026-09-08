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
$date = date("d/m/Y");
$point_option = '';
$disabled = $page_code = $operator_name = '';
$datepicker = 'datepicker';
$dtollid = '';
if(!empty($fishingpoints)){
 foreach($fishingpoints as $point){
	$point_option .= '<option value="'.$point['ID'].'">'.$point['Name'].'</option>';	
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
                        <th rowspan="2" style="width:20%;" class="text-center">Code ( Group / Name ):</th>
                        <th colspan="2" style="width:7%;" class="text-center">Catla:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Rohu:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Mragal:</th>
                        <th colspan="2" style="width:7%;" class="text-center">KaalBasu:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Anya Karp:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Sawal:</th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Local Major">LMj:</span></th>
                        <th style="width:4%;" class="text-center"><span data-toggle="tooltip" title="Local Minor">LMn:</span></th>
                        <th colspan="2" style="width:10%;" class="text-center">Total Major</th>
                        <th colspan="2" style="width:10%;" class="success text-center">Total:</th>
                        <th style="width:4%;" class="success noExl no-print"></th>
                    </tr>
                    <tr class="text-center">
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:5%;"><span data-toggle="tooltip" title="Total Major Quantity">Mj.Q</span></th>
                        <th style="width:5%;"><span data-toggle="tooltip" title="Total Major Weight">Mj.W</span></th>
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
                        <td class="text-center"></td>
                        <td class="text-left">
                            <span class="fisherman_name">
								<span class="code"><?=$dti['Code']?></span>
                                <span class="label label-warning"><?=$dti['Samiti_name']?></span> 
								<span class="name"><?=$dti['Name']?> </span>
                            </span>
                        </td>
                        <td class="td_editable cc qty" data-name="cqty" data-class="form-control qty"><?=$dti['Cqty']?></td>
                        <td class="td_editable cc wt" data-name="cwt" data-class="form-control wt"><?=$dti['Cwt']?></td>
                        <td class="td_editable cc qty" data-name="rqty" data-class="form-control qty"><?=$dti['Rqty']?></td>
                        <td class="td_editable cc wt" data-name="rwt" data-class="form-control wt"><?=$dti['Rwt']?></td>
                        <td class="td_editable cc qty" data-name="mqty" data-class="form-control qty"><?=$dti['Mqty']?></td>
                        <td class="td_editable cc wt" data-name="mwt" data-class="form-control wt"><?=$dti['Mwt']?></td>
                        <td class="td_editable cc qty" data-name="kqty" data-class="form-control qty"><?=$dti['Kqty']?></td>
                        <td class="td_editable cc wt" data-name="kwt" data-class="form-control wt"><?=$dti['Kwt']?></td>
                        <td class="td_editable cc qty" data-name="aqty" data-class="form-control qty"><?=$dti['Aqty']?></td>
                        <td class="td_editable cc wt" data-name="awt" data-class="form-control wt"><?=$dti['Awt']?></td>
                        <td class="td_editable cc qty" data-name="sqty" data-class="form-control sqty qty"><?=$dti['Sqty']?></td>
                        <td class="td_editable cc wt" data-name="swt" data-class="form-control swt wt"><?=$dti['Swt']?></td>
                        
                        <!-- Local Major qty and weight -->
                        <td class="td_editable cc qty" data-name="lqty" data-class="form-control lqty qty"><?=$dti['Lqty']?></td>
                        <td class="td_editable cc wt" data-name="lwt" data-class="form-control lwt wt"><?=$dti['Lwt']?></td>
                        
                        <!-- Local Minor weight -->
                        <td class="td_editable cc wt" data-name="localminor" data-class="form-control localminor_wt wt"><?=$dti['Localminor']?></td>
                        
                        <!-- Total qty and weight of Catla, Rohu, Mragal, KaalBasu, Anya Karp:, Local Major -->
                        <td class="warning td_editable" data-name="tmqty" data-class="form-control tmqty"><?=$dti['Tmqty']?></td>
                        <td class="warning td_editable" data-name="tmwt" data-class="form-control tmwt"><?=$dti['Tmwt']?></td>
                        
                        <!-- Total qty and weight of all fishes -->
                        <td class="success td_editable" data-name="tqty" data-class="form-control tqty"><?=$dti['Tqty']?></td>
                        <td class="success td_editable" data-name="twt" data-class="form-control twt"><?=$dti['Twt']?></td>
                        
                        <td class="success noExl no-print"></td>
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
                <tfoot>
                    
                    <tr class="text-center">
                        <th rowspan="2" style="width:3%;"></th>
                        <th rowspan="2" style="width:20%;" class="text-center">Code ( Group / Name ):</th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:3%;"><span data-toggle="tooltip" title="Quantity">Qty.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:4%;"><span data-toggle="tooltip" title="Weight">Wt.</span></th>
                        <th style="width:5%;"><span data-toggle="tooltip" title="Total Major Quantity">Mj.Q</span></th>
                        <th style="width:5%;"><span data-toggle="tooltip" title="Total Major Weight">Mj.W</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Quantity">Qty.</span></th>
                        <th style="width:5%;" class="success"><span data-toggle="tooltip" title="Total Weight">Wt.</span></th>
                        <th style="width:4%;" class="success noExl no-print"></th>
                    </tr>
                    <tr>
                        <th colspan="2" style="width:7%;" class="text-center">Catla:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Rohu:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Mragal:</th>
                        <th colspan="2" style="width:7%;" class="text-center">KaalBasu:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Anya Karp:</th>
                        <th colspan="2" style="width:7%;" class="text-center">Sawal:</th>
                        <th colspan="2" style="width:7%;" class="text-center"><span data-toggle="tooltip" title="Local Major">LMj:</span></th>
                        <th class="text-center" style="width:4%;"><span data-toggle="tooltip" title="Local Minor">LMn:</span></th>
                        <th colspan="2" style="width:10%;">Total Major</th>
                        <th colspan="2" style="width:10%;" class="success text-center">Total:</th>
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
                        <th class="cwt text-center"><?=$tCwt?></th>
                        <th class="rqty text-center"><?=$tRqty?></th>
                        <th class="rwt text-center"><?=$tRwt?></th>
                        <th class="mqty text-center"><?=$tMqty?></th>
                        <th class="mwt text-center"><?=$tMwt?></th>
                        <th class="kqty text-center"><?=$tKqty?></th>
                        <th class="kwt text-center"><?=$tKwt?></th>
                        <th class="aqty text-center"><?=$tAqty?></th>
                        <th class="awt text-center"><?=$tAwt?></th>
                        <th class="sqty text-center"><?=$tSqty?></th>
                        <th class="swt text-center"><?=$tSwt?></th>
                        <th class="lqty text-center"><?=$tLqty?></th>
                        <th class="lwt text-center"><?=$tLwt?></th>
                        <th class="localminor text-center"><?=$tLocalminor?></th>
                        <th class=""></th>
                        <th class=""></th>
                        <th class="gtqty text-center"><?=$tTqty?></th>
                        <th class="gtwt text-center"><?=$tTwt?></th>
                        <th noExl no-print></th>
                    </tr>
                </tfoot>
            </table>
            <!-- end: WIZARD FORM --> 
        </div>
    </div>
</div>
