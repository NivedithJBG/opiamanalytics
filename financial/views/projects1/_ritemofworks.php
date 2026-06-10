<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/riowfunction.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="ritemofwork"><a href="#">3. Items Of Work</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">

            <div class="row show-grid">

                <div class="col-md-2" id="projectnamedisplay">

                </div>
                <input type="hidden" id="iowProjectId">
                <div class="col-md-2" id="workgroupnamedisplay">

                </div>
                <input type="hidden" id="IOWWorkgroupId">


            </div>
            <div id="iowlistsection" >

                <div class="row show-grid">
                    <form>
                        <table class="table table-bordered " id="iowtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Item of works</th>
                                <th>Number of cycles completed</th>
                                <th></th>

                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="iowitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div class="row show-grid" id="scheduleactivities">


            </div>

        </div>


    </div>

</div>