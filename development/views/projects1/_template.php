<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/template.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="template"><a href="javascript:void(0)">11. Document Template</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/DoccumentManager/CreateTemplate"><button type="button" class="btn btn-danger"  id="addtemplate"><span class="glyphicon glyphicon-plus-sign"></span>Add Template</button></a></div>
                <div class="col-md-3"><button type="button" class="btn btn-success" id="listtemplate"><span class="glyphicon glyphicon-list-alt"></span>List Template</button></div>
            </div>
            <div id="templatelistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchtemplate" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="templatesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="templatetable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Function</th>
                                <th>Name</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"></td></tr>
                            </thead>
                            <tbody id="templateitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>