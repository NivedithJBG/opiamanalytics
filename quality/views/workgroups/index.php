<?php
/* @var $this ProjectsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    'Projects'=>array('projects1/index'),
    'Work Groups'=>array('index?id='.$project->Project_Id),
);

?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/workgroupfunctions.js" type="text/javascript"></script>
<h1>Work Groups (<?php echo $project->Name;?>)</h1>
<div class="row show-grid">
    <div class="col-md-6"><button type="button" class="btn btn-success" id="addworkgroup"><span class="glyphicon glyphicon-plus-sign"></span>Add Work Group</button></div>
</div>
<!--Table-->
<form id="addworkgroupform">
    <table class="table table-bordered" style="background-color:rgb(226, 226, 226);" id="workgroupadd">
        <tr>
            <th>#</th>
            <th><input type="hidden" name="Project_Id" value="<?php echo $project->Project_Id;?>">
                <input class="form-control" type="text" id="workgroupname" name="workgroupname" placeholder="Work Group Name"><span class="error"></span></th>
            <th><button type="button" class="btn btn-primary" id="saveworkgroup" >Save</button></th>
        </tr>
    </table>
</form>
<div class="row show-grid" id="searchdiv">
    <div class="col-md-9">
        <input class="form-control" id="projectid" type="hidden" value="<?php echo $project->Project_Id;?>">
        <input class="form-control" id="searchname" type="text" placeholder="Search">
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary" id="workgroupsearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
    </div>
</div>


<table class="table table-bordered" id="workgrouptable">

    <thead>
    <tr>
        <th>#</th>
        <th >Work group</th>
        <th >Item Of work</th>
        <th colspan="2"></th>
    </tr>
    <tr class="preloader"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
    </thead>
    <tbody id="workgroupitems">
    </tbody>
</table>
