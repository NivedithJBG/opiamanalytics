
<div class="panel panel-default acco-two tab  ">
    <input type="hidden" id="resourcessearch">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/billresources.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd1" name="rd">-->
    <div class="panel-heading">
      <h4 class="panel-title " id="billresources">
        <a  data-toggle="collapse" data-parent="#accordionindex" href="#collapseindents">
        <span class="icon-edit1"></span>Indents</a>
      </h4>
    </div>
    <div id="collapseindents" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">
            <div class="acc_containerssss">
                <div class="block">
                    <div class="">
                        <div id="resourcelistsection">
                            <div class="row show-grid">
                                <!--Table-->
                                <form>
                                    <div class="resource-list" id="billresourcetable" style="">
                                         <table class="table table-bordered indent-table" id="billresourcetable" style="display: table; overflow: hidden;">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            
                                            <th class="widthdate newdates">Date</th>
                                            <th class="newrestype">Resource Type</th>
                                            <th class="widthres newres">Resource name</th>
                                            <!-- <th>User</th>
                                            <th>Activity</th> -->
                                            
                                           <!--  <th class="widthres">Resource name</th> -->
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                            <th colspan="4"></th>
                                        </tr>
                                        <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                        </thead>
                                        <tbody id="resourceitems">

                                        </tbody>
                                       </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>     
        </div>
    </div>
    <!--<h2 style="display:none;" class="acc_trigger" id="billresources"><a href="javascript:void(0)">1. Bill of  Resources</a></h2>-->
</div>    
