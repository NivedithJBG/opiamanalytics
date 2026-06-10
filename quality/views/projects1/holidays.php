<?php
$this->breadcrumbs=array(
    'Hoidays'=>array('holidays'),
);

?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/holidayfunction.js" type="text/javascript"></script>

    <h1>Holidays</h1>
    <div class="row show-grid">
        <div class="col-md-3">
            <button type="button" class="btn btn-primary" id="addholiday"><span class="glyphicon glyphicon-plus-sign"></span>Add holiday</button>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-success" id="listholidays" ><span class="glyphicon glyphicon-plus-sign"></span>List holidays</button>
        </div>
    </div>
    <!--Table to add resource-->
    <form id="addholidayform">
        <table class="table table-bordered" style="background-color:rgb(226, 226, 226);" id="addholidaytable">
            <tr id="createfields">
                <th>#</th>
                <th>
                    <select class="form-control" id="Project_Id" name="Project_Id">
                        <option value="0">For All Projects</option>
                        <?php
                        foreach($projects AS $project):
                            echo "<option value='".$project['Project_Id']."'>".$project['Name']."</option>";
                        endforeach;
                        ?>
                    </select>
                    <span class="error"></span>
                </th>
                <th> <input class="form-control" type="text" id="offdate" name="offdate" ><span class="error"></span><span id="clearspan2" class="clearspan">clear</span></th>
                <th><button type="button" class="btn btn-primary" id="saveholiday" >Save</button></th>
            </tr>
            <tr id="successmessage" style="display: none;">
                <td colspan="8">Holiday Added</td>
            </tr>
        </table>
    </form>

<div class="row show-grid" id="searchdiv" <?php echo (isset($_GET['vendor'])?'style="visibility: hidden;"':'');?>>
    <div class="col-md-5">
        <select class="form-control" id="selectproject">
            <option value="0">For All Projects</option>
            <?php
            foreach($projects AS $project):
                echo "<option value='".$project['Project_Id']."'>".$project['Name']."</option>";
            endforeach;
            ?>
        </select>
    </div>

    <div class="col-md-4">
        <input class="form-control" type="text" id="holidaydate" name="holidaydate" ><span id="clearspan1" class="clearspan">clear</span>
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary" id="holidaysearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
    </div>
</div>
    <table class="table table-bordered" id="holidaytable">

        <thead>
        <tr>
            <th>#</th>
            <th>Project</th>
            <th>Date</th>
            <th >Action</th>
        </tr>
        <tr class="preloader"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
        </thead>
        <tbody id="holidayitems">
        </tbody>
    </table>
    <script type="text/javascript">
/*        $("#searchname").autocompleteArray([<?php echo $dataProvider;?>]);
        $("#resourcename").autocompleteArray([<?php echo $dataProvider;?>]);*/
    </script>
