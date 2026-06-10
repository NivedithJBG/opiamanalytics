<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/opprojdocument.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="opprojdocuments"><a href="javascript:void (0)">2. Documents</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="opprojdoclistsection">
                <div class="row show-grid">
                    <div class="col-md-3" style="text-align: left;">
                        <h4 id="opprojdocprojectname"></h4>
                        <input type="hidden" name="project_id" id="opprojprojectid">
                    </div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger"  id="opadddoc"><span class="glyphicon glyphicon-plus-sign"></span>Create Document</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger"  id="opuploaddoc"><span class="glyphicon glyphicon-plus-sign"></span>Upload Document</button></div>
                    <!--<div class="col-md-3"><button type="button" class="btn btn-danger" id="listdoc"><span class="glyphicon glyphicon-list-alt"></span>List Documents</button></div>-->
                    <!--<div class="col-md-3"><button type="button" class="btn btn-danger" id="listuploaddoc"><span class="glyphicon glyphicon-list-alt"></span>List Upload Documents</button></div>-->
                </div>
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-3">
                        <select class="form-control" name="function" id="opprojfunction">
                            <option value="none">Select Function</option>
                            <?php
                            $departments=Departments::model()->findAll();
                            foreach($departments AS $department):?>
                                <option value="<?php echo $department->dep_id;?>"><?php echo $department->name;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button id="opprojdocumentsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="opprojdoctable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Project</th>
                                <th>Function</th>
                                <th>Folder</th>
                                <!--<th>Document Type</th>-->
                                <th>Subject</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="opprojdocitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="emailModel" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Email Document</h4>
            </div>
            <form action="" id="emaildocument" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">Email address:</label>
                        <input type="email" class="form-control" id="emailid" required>
                        <span class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" class="form-control" id="subject" required>
                        <span class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="body">Body:</label>
                        <textarea rows="8" cols="25" class="form-control" id="body" required></textarea>
                        <span class="error"></span>
                        <!--<input type="text" class="form-control" id="body" required>-->
                    </div>
                    <div class="mailloader" style="display: none">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/mail.gif" align="middle">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="type">
                    <input type="hidden" id="docid">
                    <div class="alert alert-success" id="succesinfo" style="display: none">

                    </div>
                    <div class="alert alert-warning" id="errorinfo" style="display: none">

                    </div>
                    <button type="button" class="btn btn-default" id="sendemail">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>