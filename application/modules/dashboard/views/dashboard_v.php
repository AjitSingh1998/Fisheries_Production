<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<div class="container-fluid container-fullw bg-white">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mainTitle">Welcome to Bansagar Fisheries Management System</h2>
            <p class="text-large">System Overview & Management Shortcuts</p>
        </div>
    </div>
    <hr />
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-sm-3">
            <div class="panel panel-tile bg-primary text-center padding-15" style="border-radius: 6px; background-color: #007bff; color: #fff; margin-bottom: 20px;">
                <div class="panel-body">
                    <h1 class="step text-white" style="font-size: 36px; margin: 10px 0; font-weight: bold; color: #fff;"><?= number_format($total_fishermen) ?></h1>
                    <span class="text-white text-bold"><i class="fa fa-users"></i> Registered Fishermen</span>
                </div>
            </div>
        </div>
        
        <div class="col-sm-3">
            <div class="panel panel-tile text-center padding-15" style="border-radius: 6px; background-color: #28a745; color: #fff; margin-bottom: 20px;">
                <div class="panel-body">
                    <h1 class="step text-white" style="font-size: 36px; margin: 10px 0; font-weight: bold; color: #fff;"><?= number_format($total_products) ?></h1>
                    <span class="text-white text-bold"><i class="fa fa-cubes"></i> Total Products</span>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="panel panel-tile text-center padding-15" style="border-radius: 6px; background-color: #17a2b8; color: #fff; margin-bottom: 20px;">
                <div class="panel-body">
                    <h1 class="step text-white" style="font-size: 36px; margin: 10px 0; font-weight: bold; color: #fff;"><?= number_format($total_groups) ?></h1>
                    <span class="text-white text-bold"><i class="fa fa-sitemap"></i> Main Groups</span>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="panel panel-tile text-center padding-15" style="border-radius: 6px; background-color: #ffc107; color: #333; margin-bottom: 20px;">
                <div class="panel-body">
                    <h1 class="step text-dark" style="font-size: 36px; margin: 10px 0; font-weight: bold; color: #212529;"><?= number_format($total_dailytoll) ?></h1>
                    <span class="text-dark text-bold"><i class="fa fa-balance-scale"></i> Daily Toll Records</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Access Section -->
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-12">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-rocket"></i> Quick Module Access</h4>
                </div>
                <div class="panel-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                            <a href="<?= site_url('dailytoll') ?>" class="btn btn-block btn-default padding-15" style="border: 1px solid #ddd; border-radius: 6px;">
                                <i class="fa fa-balance-scale fa-2x text-primary"></i>
                                <div style="margin-top: 8px; font-weight: bold;">Daily Toll</div>
                            </a>
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                            <a href="<?= site_url('setup/fisherman') ?>" class="btn btn-block btn-default padding-15" style="border: 1px solid #ddd; border-radius: 6px;">
                                <i class="fa fa-users fa-2x text-success"></i>
                                <div style="margin-top: 8px; font-weight: bold;">Fishermen</div>
                            </a>
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                            <a href="<?= site_url('stocks') ?>" class="btn btn-block btn-default padding-15" style="border: 1px solid #ddd; border-radius: 6px;">
                                <i class="fa fa-cubes fa-2x text-info"></i>
                                <div style="margin-top: 8px; font-weight: bold;">Stocks</div>
                            </a>
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                            <a href="<?= site_url('wages') ?>" class="btn btn-block btn-default padding-15" style="border: 1px solid #ddd; border-radius: 6px;">
                                <i class="fa fa-money fa-2x text-warning"></i>
                                <div style="margin-top: 8px; font-weight: bold;">Wages</div>
                            </a>
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                            <a href="<?= site_url('reports') ?>" class="btn btn-block btn-default padding-15" style="border: 1px solid #ddd; border-radius: 6px;">
                                <i class="fa fa-line-chart fa-2x text-danger"></i>
                                <div style="margin-top: 8px; font-weight: bold;">Reports</div>
                            </a>
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                            <a href="<?= site_url('setup') ?>" class="btn btn-block btn-default padding-15" style="border: 1px solid #ddd; border-radius: 6px;">
                                <i class="fa fa-cogs fa-2x text-purple"></i>
                                <div style="margin-top: 8px; font-weight: bold;">Setup</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>