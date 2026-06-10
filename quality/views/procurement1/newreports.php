<div class="container-fluid procu-accordion">
     <div class="row">
        <div class="col-md-12">
            <div class="panel-group acco-billofres-active" id="accordionprocureport">
                <?php echo $this->render('stockreports'); ?>
                <?php echo $this->render('workersreport'); ?> 
                <?php echo $this->render('plantandequipment.php'); ?>
            </div>
        </div>
    </div>

    <style>
        .tab-content{
            max-height:unset;
        }

    </style>
</div>