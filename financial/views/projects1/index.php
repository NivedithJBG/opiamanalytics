<?php
/* @var $this ProjectsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Projects'=>array('index'),
);

?>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/projectfunctions.js" type="text/javascript"></script>
<h1>Projects</h1>
<div class="row show-grid">
    <div class="col-md-6"><button type="button" class="btn btn-success" id="addproject"><span class="glyphicon glyphicon-plus-sign"></span>Add Project</button></div>
</div>
<!--Table-->
<form id="addresourceform">
    <table class="table table-bordered" style="background-color:rgb(226, 226, 226);" id="resourcevalueadd">
        <tr>
            <th>#</th>
            <th> <input class="form-control" type="text" id="projectname" name="projectname" placeholder="Project Name"><span class="error"></span></th>
            <th><button type="button" class="btn btn-primary" id="saveproject" >Save</button></th>
        </tr>
    </table>
</form>
<div class="row show-grid" id="searchdiv">
    <div class="col-md-9">
        <input class="form-control" id="searchname" type="text" placeholder="Search">
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary" id="projectsearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
    </div>
</div>


<table class="table table-bordered" id="resourcetable">

    <thead>
    <tr>
        <th>#</th>
        <th >Project</th>
        <th class="small200">Work groups</th>
        <th colspan="3"></th>
    </tr>
    <tr class="preloader"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
    </thead>
    <tbody id="resourceitems">
    </tbody>
</table>
<script type="text/javascript">
    $("#searchname").autocompleteArray([<?php echo $dataProvider;?>]);
    $(document).on("click",".viewprojectbutton",function(){
        var projid=$(this).val();
        window.location.replace("<?php echo Yii::app()->createUrl('projects')?>/"+projid);
    })
</script>


