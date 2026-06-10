<?php
/**
 * Created by PhpStorm.
 * User: SolmindsDelli5
 * Date: 01-08-2017
 * Time: 03:52 PM
 */

//use dektrium\user\models\User;
//use Yii;
use amnah\yii2\user\models\User;
use app\models\DepartmentTab;
use app\models\UserTabs;
//use \amnah\yii2\user\models\User;
use app\models\ProjuserSelection;
use app\models\Projects;

?>
<div class="container-fluid procu-accordion">
    <div class="row">
    	<div class="col-md-12">
		    <div class="panel-group acco-billofres-active" id="accordionresources">
                
                <div class="panel panel-default acco-one tab">
                    <!-- <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/projects.js" type="text/javascript"></script> -->

                    <div class="panel-heading" id="selectedprocu-projctid">
                        <h4 class="panel-title" id="procuprojectlist">
                            <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseprojs">
                            <span class="icon-note1"></span>Templates</a>
                        </h4>
                    </div>

                    <div id="collapseprojs" class="tab-content cOrder-body panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="row">
                                

                                <?php 
                                if($template_projs){ 
                                    foreach ($template_projs as $key => $proj) {
                                        $days       = $proj->duration;
                                        $prjctdate  = '0 days';
                                        if($proj->duration!=0)
                                            $prjctdate = $days.' days';
                                        
                                ?>
                                <div class="col-md-4" id="project<?php echo $proj->Project_Id ?>">
                                    <div data-id="<?php echo $proj->Project_Id ?>" class="rprojectselection fav-project-wrpr card">
                                        <div class="card-body">
                                            <div class="row" style="width:100%; display:flex; align-items: center;">
                                                <div class="col-md-11">
                                                    <a href="#"><span class="icon-check"></span><?php echo $proj->Name ?></a>
                                                </div>
                                                <div class="col-md-1">
                                                    <a class=" icon-copy duplicateprojectbutton"  data-toggle="modal" data-target="#projFormCopyPopup" href="#projFormCopyPopup"  data-projectid="<?php echo $proj->Project_Id; ?>"   title="Duplicate Project"></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="type project-client-name">
                                                        <label>Client</label>
                                                        <span><?php echo $proj->client_name ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="type">
                                                        <label>Project Duration</label>
                                                        <span><?php echo $prjctdate ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="type text-right">
                                                        <label>Project Value</label>
                                                        <span><?php echo $proj->project_value ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <?php } } ?>




                            </div>
                        </div>

                    </div>
                </div>
                    
            </div>

            </div>
        </div>
    </div>

    <style>
        .tab-content{
            max-height:unset;
        }

    </style>





<div class="modal fade projFormCopyPopup" id="projFormCopyPopup" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" style="float: left;">Duplicate Project</h4>
                <button type="button" class="close projectFormPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                    <!-- edit form starts here -->
                    <div class="row">
                        <form id="duplicateprojectform">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" style="margin-left: 10px;">
                                        <label>Project Name</label>
                                        <input id="projectnames" name="projectname" type="text" class="form-control" >
                                        <span class="error" style="display: none;"></span>
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" style="margin-right: 10px;">
                                        <label>Duration</label>
                                        <input id="durationn" type="number" class="form-control editenggactivityduration" >
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input id="startdatee" type="Date" class="form-control editactivitystartdate" >
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input id="enddatee" type="date" class="form-control editactivityenddate" >
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                            
                                <div class="col-md-4">
                                    <div class="form-group" style="margin-left: 10px;">
                                        <label>Project Value</label>
                                        <input type="text" id="projectvalues" class="form-control" >
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Client Name</label>
                                        <input id="clientnames" type="text" class="form-control" >
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                    
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" style="margin-right: 10px;">
                                        <label>Work Hours</label>
                                        <input id="wrkhrss" type="text" class="form-control" >
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                    
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12 text-center" >
                                    <label></label>
                                    <button type="button" class="btn btn-primary save-btn savecopyproject" id="savecopyproject"><span class="icon-check"></span> Duplicate Project</button>
                                    <button type="button" class="btn btn-danger cancel projectFormPopup data-dismiss="modal" style="border-radius: 23px;" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
                                    <input type="hidden" id="savess">
                                    <input type="hidden" name="projectid" id="copy_projectid">
                                </div>

                            </div>
                        
                        </form> 
                        
                    </div>
                    <!-- edit form ends here -->

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
        
            </div>

        </div>
    </div>
</div>





<script>
  $( document ).ready(function() {


            setTimeout(function(){
                    var value = 1;
                    $('.panel-default').removeClass('acco-one acco-two acco-three acco-four acco-five acco-six acco-seven acco-eight acco-nine acco-ten acco-eleven acco-twelve acco-thirteen ');


                    $('.panel-default').each(function() {

                     var va = value++;
                      var wordss = {
                      1: 'one',
                      2: 'two',
                      3: 'three',
                      4: 'four',
                      5: 'five',
                      6: '',
                      7: 'seven',
                      8: 'one',
                      9: 'two',
                      10:'three',
                      11:'four'
                      
                      };
                      $(this).addClass('acco-'+wordss[va]);

                    });
                    
            },1000);



  });
</script>
