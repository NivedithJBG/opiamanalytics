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
?>
<div class="container-fluid procu-accordion">
    <div class="row">
    	<div class="col-md-12">
		    <div class="panel-group acco-billofres-active" id="accordionresources">
                <?php 
                    $uid = Yii::$app->user->Id; 
                    $row = User::findOne($uid);
                    $roleid = $row->superuser;
                    
                    $user=User::find()->where(['id'=>Yii::$app->user->id])->one();
                    $functiontabs=DepartmentTab::find()->orderby(['sortorder'=>SORT_ASC])->all();

                    if($user->role_id !=0){

                        foreach ($functiontabs as $key => $functiontab) {

                            $functabs=UserTabs::find()->where(['function_id'=>2])->andWhere(['tab_id'=>$functiontab->tab_id])->andWhere(['role_id'=>$user->role_id])->one();

                                if($functabs){
                                    if($functabs->tab_id==35){

                                    }elseif($functabs->tab_id==36){

                                    }else{
                                  //echo $functiontab->tabname;exit;
                                    echo $this->render(''.$functiontab->tabname.'');
                                 }
                                }
                        }
                    }else{
                     echo $this->render('projects');
                     echo $this->render('order_resources', 
                            array(  
                                    'selProjId'             => $selProjId,
                                    'resourcesArr'          =>$resourcesArr,
                                    'subContArr'            =>$subContArr,
                                    'labourArr'             =>$labourArr,
                                    'plantEquipArr'         =>$plantEquipArr,
                                    'resourceTypes'         =>$resourceTypes,
                                    'resourceGroups'        =>$resourceGroups,
                                    'activeProjects'        => $activeProjects,
                                    'mareial_due_cnt'       => $mareial_due_cnt,
                                    'subcon_due_cnt'        => $subcon_due_cnt,
                                    'purchaseOrdersHistory' => $purchaseOrdersHistory
                                )
                            );        
                     //echo $this->render('resource_repeat_orders', array('activities' => $activities));        
                    }
                    ?>
                    
                </div>

            </div>
        </div>
    </div>

    <style>
        .tab-content{
            max-height:unset;
        }

    </style>

</div>




<!---  PLACE ORDER FORM POPUP ---->
<div class="modal fade placeOrderPopup" id="placeOrderPopup" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"  style="float: left;">Place Order</h4>
                <button type="button" class="close placeOrderPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
                            <img src="/sreejith/opiam_analytics/web/images/loader.gif" align="middle">
                        </div>

                        <div id="placeOrderContainer" class="row ">
                            <div style="padding:50px; text-align:center;">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
        
            </div>

        </div>
    </div>
</div>
<!-------------->




<!---  RECEIVE ORDER FORM POPUP ---->
<div class="modal fade receiveOrderPopup" id="receiveOrderPopup" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"  style="float: left;">Receive Order</h4>
                <button type="button" class="close receiveOrderPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
                            <img src="/sreejith/opiam_analytics/web/images/loader.gif" align="middle">
                        </div>

                        <div id="receiveOrderContainer" class="row ">
                            <div style="padding:50px; text-align:center;">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
        
            </div>

        </div>
    </div>
</div>
<!-------------->




<!---  RAISE BILL POPUP ---->
<div class="modal fade raiseBillPopup" id="raiseBillPopup" >
    <div class="modal-dialog modal-lg" style="width:80%;">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"  style="float: left;">Raise Bill</h4>
                <button type="button" class="close raiseBillPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
                            <img src="/sreejith/opiam_analytics/web/images/loader.gif" align="middle">
                        </div>

                        <div id="raiseBillContainer" class="row ">
                            <div style="padding:50px; text-align:center;">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
        
            </div>

        </div>
    </div>
</div>
<!-------------->


<!---  GENERATE MUSTER POPUP ---->
<div class="modal fade generateMusterPopup" id="generateMusterPopup" >
    <div class="modal-dialog modal-lg" style="width:80%;">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"  style="float: left;">Muster Roll</h4>
                <button type="button" class="close generateMusterPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
                            <img src="/sreejith/opiam_analytics/web/images/loader.gif" align="middle">
                        </div>

                        <div id="generateMusterContainer" class="row ">
                            <div style="padding:50px; text-align:center;">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
        
            </div>

        </div>
    </div>
</div>
<!-------------->



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
