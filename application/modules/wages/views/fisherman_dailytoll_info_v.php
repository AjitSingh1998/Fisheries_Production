<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->

<table class="table table-striped" id="fisher-wages" width="100%">
  <thead>
    <tr><td colspan="23"> <h4 class="text-center">Daily Toll Detail Info for <span class="text-bold fname"><?=$fName?></span> &nbsp;From: <?=$fdt?> To: <?=$tdt?></h4></td></tr>
    <tr>
      <th rowspan="2" style="width:6%">Toll Date</th>
      <th class="text-center" colspan="2" style="width:6%">Catla:</th>
      <th class="text-center" colspan="2" style="width:6%">Rohu:</th>
      <th class="text-center" colspan="2" style="width:6%">Mragal:</th>
      <th class="text-center" colspan="2" style="width:6%">KaalBasu:</th>
      <th class="text-center" colspan="2" style="width:6%">Anya Karp:</th>
      <th class="text-center" colspan="2" style="width:6%">Sawal:</th>
      <th class="text-center" colspan="2" style="width:6%">LMj:</th>
      <th class="text-center" style="width:4%">LMn:</th>
      <th class="text-center" colspan="2" style="width:10%">Total Major</th>
      <th style="width:4%" class="success text-center">Total:</th>
      <th style="width:4%" class="success text-center"></th>
      <th colspan="3" style="width:6%" class="info text-center">Toll Rate</th>
    </tr>
    <tr>
      <th class="text-center" style="width:2%">Qty.</th>
      <th class="text-center" style="width:4%">Wt.</th>
      <th class="text-center" style="width:2%">Qty.</th>
      <th class="text-center" style="width:4%">Wt.</th>
      <th class="text-center" style="width:2%">Qty.</th>
      <th class="text-center" style="width:4%">Wt.</th>
      <th class="text-center" style="width:2%">Qty.</th>
      <th class="text-center" style="width:4%">Wt.</th>
      <th class="text-center" style="width:2%">Qty.</th>
      <th class="text-center" style="width:4%">Wt.</th>
      <th class="text-center" style="width:2%">Qty.</th>
      <th class="text-center" style="width:4%">Wt.</th>
      <th class="text-center" style="width:2%;">Qty.</th>
      <th class="text-center" style="width:4%;">Wt.</th>
      <th class="text-center" style="width:4%;">Wt.</th>
      <th class="text-center" style="width:2%;">Mj.Q</th>
      <th class="text-center" style="width:4%;">Mj.W</th>
      <th style="width:2%" class="success">Qty.</th>
      <th style="width:4%" class="success text-center">Wt.</th>
      <th style="width:2%" class="info text-center">Mj.Fee</th>
      <th style="width:2%" class="info text-center">Mn.Fee</th>
      <th style="width:2%" class="info text-center">Sw.Fee</th>
    </tr>
  </thead>
  <tbody id="sale_products" class="table-hover">
    <?php 
	$tCqty = $tCwt = $tRqty = $tRwt = $tMqty = $tMwt = $tKqty = $tKwt = $tAqty = $tAwt = $tSqty = $tSwt = $tLqty = $tLwt = $tLocalminor = $tTqty = $tTwt =  0;
	if(!empty($dti_data)){
		foreach($dti_data as $dti){
	?>
    <tr class="saved text-center">
      <td style="width:6%"><?=get_date('',$dti['Date'])?></td>
      <td><?=$dti['Cqty']?></td>
      <td><?=$dti['Cwt']?></td>
      <td><?=$dti['Rqty']?></td>
      <td><?=$dti['Rwt']?></td>
      <td><?=$dti['Mqty']?></td>
      <td><?=$dti['Mwt']?></td>
      <td><?=$dti['Kqty']?></td>
      <td><?=$dti['Kwt']?></td>
      <td><?=$dti['Aqty']?></td>
      <td><?=$dti['Awt']?></td>
      <td><?=$dti['Sqty']?></td>
      <td><?=$dti['Swt']?></td>
      
      <!-- Local Major qty and weight -->
      <td><?=$dti['Lqty']?></td>
      <td><?=$dti['Lwt']?></td>
      
      <!-- Local Minor weight -->
      <td><?=$dti['Localminor']?></td>
      
      <!-- Total qty and weight of Catla, Rohu, Mragal, KaalBasu, Anya Karp:, Local Major -->
      <td class="warning"><?=$dti['Tmqty']?></td>
      <td class="warning"><?=$dti['Tmwt']?></td>
      
      <!-- Total qty and weight of all fishes -->
      <td class="success"><?=$dti['Tqty']?></td>
      <td class="success"><?=$dti['Twt']?></td>
      <td class="info"><?=$dti['MajorFee']?></td>
      <td class="info"><?=$dti['MinorFee']?></td>
      <td class="info"><?=$dti['SawalFee']?></td>
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
    <tr class="g_total success">
      <th></th>
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
      <th class="info text-center"></th>
      <th class="info text-center"></th>
      <th class="info text-center"></th>
    </tr>
  </tfoot>
</table>



