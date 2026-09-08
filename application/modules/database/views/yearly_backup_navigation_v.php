<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<!-- start: BREADCRUMB -->
<style>
.nav>li.active>a{background-color: #eee;}
</style>
<?php
	$class = $this->router->fetch_class();
	$method = $this->router->fetch_method();
	$page = $this->uri->segment(3);
?>
<div class="panel panel-white">
	<div class="panel-heading border-light light-bg">
        <h3 class="text-center"> Backup Menu</h3>
    </div>
    <div class="panel-body">
    	
        <ul class="nav navigation">
            <li <?=($page=='setting') ? 'class="active"' : '';?>><a href="<?=site_url('database/setting')?>">Database Setting</a></li>
            <li <?=($page=='add_newdb') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/add_newdb')?>">Create Database</a></li>
            <li <?=($page=='export') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/export')?>">Backup Database</a></li>
            <li <?=($page=='import') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/import')?>">Import Backup</a></li>
            <li <?=($page=='fisherman_balance') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/fisherman_balance')?>">Fisherman Balance</a></li>
            <li <?=($page=='samiti_balance') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/samiti_balance')?>">Samiti Balance</a></li>
            <li <?=($page=='assign_database') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/assign_database')?>">Assign Database</a></li>
            <li <?=($page=='delete_data') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/delete_data')?>">Delete Data</a></li>
            <li <?=($page=='complete') ? 'class="active"' : '';?>><a href="<?=site_url('database/yearly_backup/complete')?>">Complete</a></li>
        
        </ul>
	</div>
</div>