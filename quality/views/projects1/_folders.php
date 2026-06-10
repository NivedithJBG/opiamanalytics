<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/folders.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="folders"><a href="javascript:void(0)">9. Folders</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addfolder"><span class="glyphicon glyphicon-plus-sign"></span>Add Folder</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listfolders"><span class="glyphicon glyphicon-list-alt"></span>List Folders</button></div>
            </div>
            <div id="folderlistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchfolder" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="foldersearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="foldertable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Function</th>
                                <th>Name</th>
                                <th>Prefix</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="folderitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="folderadd" class="row show-grid">
                <form id="folderform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="folderadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><select class="form-control" name="function" id="function">
                                    <option value="none">Select Function</option>
                                    <?php
                                        $functions=Departments::model()->findAll(array('order'=>'name ASC'));
                                        foreach($functions AS $function):
                                            echo "<option value='".$function['dep_id']."'>".$function['name']."</option>";
                                        endforeach;
                                    ?>
                                </select><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="foldername" name="foldername" placeholder="Folder Name"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="prefixname" name="prefixname" placeholder="Prefix"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savefolder"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>