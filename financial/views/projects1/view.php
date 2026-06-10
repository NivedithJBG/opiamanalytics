<?php
/* @var $this ProjectsController */
/* @var $model projects */

$this->breadcrumbs=array(
	'Projects'=>array('index'),
	'View Project : '.$model->Name=>array('view','id'=>$model->Project_Id),
);
 ?>
<input type="hidden" name="projectid" name="projectid" id="projectid" value="<?php echo $model->Project_Id;?>">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/projectviewfunctions.js" type="text/javascript"></script>
<h1><?php echo $model->Name;?></h1>
<div class="row show-grid">
    <div class="col-md-6"><button type="button" class="btn btn-primary" id="workgroups"><span class="glyphicon glyphicon-plus-sign"></span>Work Groups</button></div>
    <div class="col-md-6"><button type="button" class="btn btn-primary" id="itemofworks"><span class="glyphicon glyphicon-plus-sign"></span>Item Of Works</button></div>
</div>
<?php
/**
 *  Section for workgroups
 */
 ?>

<div id="workgroupsdiv">
        <div class="row show-grid">
            <div class="col-md-9">
                <input class="form-control" id="worksearchname" type="text" placeholder="Search">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary" id="workgroupsearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-md-6"><button type="button" class="btn btn-primary" id="createworkgroup"><span class="glyphicon glyphicon-plus-sign"></span>Create Work Group</button></div>
        </div>
        <!--Table-->
        <form id="addworkgroupform">
            <table class="table table-bordered" style="background-color:rgb(226, 226, 226);" id="addworkgrouptable">
                <tr>
                    <th>#</th>
                    <th> <input class="form-control" type="text" id="workgroupname" name="workgroupname" placeholder="Work Group Name"><span class="error"></span></th>
                    <th><button type="button" class="btn btn-primary" id="saveworkgroup" >Save</button></th>
                </tr>
            </table>
        </form>
        <table class="table table-bordered" id="workgrouptable">

            <thead>
            <tr>
                <th>#</th>
                <th colspan="4">Work Group</th>
            </tr>
            <tr class="preloader"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
            </thead>
            <tbody id="workgroupitems">
            </tbody>
        </table>
</div>
<?php
/**
 *  Section for itemofworks
 */
?>
<div id="itemofworksdiv">
        <div class="row show-grid">
            <div class="col-md-4">
                <select class="form-control" id="selectworkgroup">
                    <option value="none">Select Work Group</option>
                    <?php $list = Workgroups::model()->findAll('Status=:type AND Project_Id=:project', array(':type' => '0',':project'=>$model->Project_Id));
                    foreach($list AS $type):
                        echo "<option value='".$type->Workgroup_Id."'>".$type->Name."</option>";
                    endforeach;
                    ?>
                </select>

            </div>
            <div class="col-md-5">
                <input class="form-control" id="iownamesearch" type="text" placeholder="Search">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary" id="searchiowname"><span class="glyphicon glyphicon-search" ></span>Search</button>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-md-6"><button type="button" class="btn btn-primary" id="createiow" value="<?php echo $model->Project_Id;?>"><span class="glyphicon glyphicon-plus-sign"></span>Create Item Of Work</button></div>
        </div>

        <table class="table table-bordered" id="resourcetable">

            <thead>
            <tr>
                <th>#</th>
                <th >Item Of Work</th>
                <th colspan="4">Work Group</th>
            </tr>
            <tr class="preloader"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
            </thead>
            <tbody id="iowitems">
            </tbody>
        </table>
</div>
        <script type="text/javascript">
            $("#searchname").autocompleteArray([<?php echo $dataProvider;?>]);
/*            $(document).on("click",".viewprojectbutton",function(){
                var projid=$(this).val();
                window.location.replace("<?php echo Yii::app()->createUrl('projects/view')?>/id/"+projid);
            })*/
            $(document).on("click","#createiow",function(){
                var projid=$(this).val();
                window.location.replace("<?php echo Yii::app()->createUrl('Itemofworks/create')?>?id="+projid);
            })
        </script>
