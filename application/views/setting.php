<div class="container-fluid container-fullw">

	<?php if(isset($extra_content) && !empty($extra_content)) { ?>
    <div class="row">
        <div class="col-sm-12">
            <?php echo $extra_content; ?>
        </div>
    </div>
    <?php  } ?>
    
    <div class="row">
        <div class="col-sm-12">
            <?php echo $output; ?>
        </div>
    </div>

</div>