<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/rproject.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="rproject"><a href="#">1. Project</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">

            <div id="projectlistsection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="projecttable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Project</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                               <!-- <th></th> -->
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="12" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="projectitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>