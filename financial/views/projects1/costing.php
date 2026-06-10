
<input type="hidden" id="selectedSectorid">
<input type="hidden" id="selectedwbsid">
<input type="hidden" id="selectedsectoriowid">
<h4>Project Masters</h4>
<?php echo $this->renderPartial('_projects'); ?>
<?php //echo $this->renderPartial('_resourcegroup'); ?>
<?php echo $this->renderPartial('_resourcetypes'); ?>
<?php echo $this->renderPartial('_resources'); ?>
<?php echo $this->renderPartial('_vendortypes'); ?>
<?php echo $this->renderPartial('_vendors'); ?>
<?php echo $this->renderPartial('_trade');?>
<?php echo $this->renderPartial('_equipments');?>
<?php echo $this->renderPartial('_departments');?>
<?php echo $this->renderPartial('_folders');?>
<?php echo $this->renderPartial('_documenttype');?>
<?php echo $this->renderPartial('_template');?>
<h4>Process Estimate</h4>
<?php //echo $this->renderPartial('_investments'); ?>
<?php echo $this->renderPartial('_projectsetup'); ?>
<?php echo $this->renderPartial('_products'); ?>
<?php echo $this->renderPartial('_logistics'); ?>
<?php echo $this->renderPartial('_construction'); ?>
<?php //echo $this->renderPartial('_majorconsumables'); ?>
<?php //echo $this->renderPartial('_purchasedinputs'); ?>
<?php echo $this->renderPartial('_overheads'); ?>
<h4>Finance Masters</h4>
<?php echo $this->renderPartial('_accountgroups'); ?>
<?php echo $this->renderPartial('_accountsubgroups'); ?>
<?php echo $this->renderPartial('_bsitems'); ?>
<?php echo $this->renderPartial('_accounttypes'); ?>
<?php echo $this->renderPartial('_accounts'); ?>
<?php /*echo $this->renderPartial('_banks'); */?>
<?php echo $this->renderPartial('_schedule'); ?>
<?php /*echo $this->renderPartial('_subschedule'); */?>


