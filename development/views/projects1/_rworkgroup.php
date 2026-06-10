<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/rworkgroupfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="rworkgroup"><a href="#">2. WBS</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">

            <div class="row show-grid">
                <div class="col-md-2" style="text-align: left;" id="dispprojectname">
                </div>

            </div>
            <div id="workgrouplistsection">
                <!--<div class="row show-grid" style="background-color: rgb(186, 211, 235);padding-top: 18px;">
                    <div class="col-md-4">
                          <input class="form-control" id="searchname" type="text" placeholder="Search">
                    </div>
                      <div class="col-md-2">
                        <button type="button" class="btn btn-danger" id="resourcesearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
                      </div>
                    </div>-->
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="workgrouptable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th >WBS</th>
                                <th ></th>

                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="3" align="center"><img src="/geotech/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="workgroupitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>



        </div>
    </div>
</div>