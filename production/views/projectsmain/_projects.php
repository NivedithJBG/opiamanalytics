
<?php echo $this->render('overrelay-projectdocs'); ?>

<!-- Structural wrapper only: other panels (_schedulelisting, _estimate,
     _schedulereport, etc.) still target data-parent="#accordionprojindex"
     for Bootstrap collapse grouping. The old project list/add/edit/select
     UI has been removed — the "Add Project" popup covers that now. -->
<div class="panel-group schdl acco-one-active" id="accordionprojindex"></div>
