<?php /* @var $this Controller */ ?>
<?php return $this->render('/layouts/mainnew'); ?>
<div class="procu-accordion container-fluid">
    <div class="row">
		<div class="col-md-12">
		    <div class="panel-group acco-billofres-active" >
                <?php echo $content; ?>
            </div>
        </div>
    </div>
</div>
<?php //$this->endContent(); ?>