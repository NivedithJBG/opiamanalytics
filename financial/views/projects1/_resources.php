<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/resourcefunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Resources"><a href="#Resources">3. Resources</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addresources"><span class="glyphicon glyphicon-plus-sign"></span>Add Resources</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listresources"><span class="glyphicon glyphicon-list-alt"></span>List Resources</button></div>
            </div>
            <div id="resourceslistsection">
                <div id="resourcesearchdiv" class="row show-grid">
                    <div class="col-md-3">
                        <select id="searcselecttype" class="form-control">
                            <option value="none">Select Resource Type</option>
                            <?php
                            $typelist=Resourcetype::model()->findAll();
                            foreach($typelist AS $list):
                                echo "<option value='".$list->ResourceType_Id."'>".$list->Name."</option>";
                            endforeach;
                            ?>
                            </select>

                    </div>
                    <div class="col-md-2">
                        <select id="searcselectvendor" class="form-control">
                            <option value="none">Select Vendor</option>
                            <?php
                            $vendorlist=Vendors::model()->findAll();
                            foreach($vendorlist AS $list):
                                echo "<option value='".$list->Vendor_Id."'>".$list->Name."</option>";
                            endforeach;
                            ?> </select>

                    </div>
<!--                    <div class="col-md-3">
                    <select id="searcselectproject" class="form-control">
                        <option value="none">Select Project</option>
                        <option value="5">Project</option><option value="6">Project 1</option><option value="7">Project New</option>        </select>

                        </div>-->
                    <div class="col-md-2">
                        <input type="text" placeholder="Location" id="searchlocation" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="text" placeholder="Resource Search" id="searchresourcename" class="form-control">
                    </div>                    
                    <div class="col-md-2">
                        <button id="resourcesearch" style="height: 35px;" title="search" class="btn btn-primary" type="button"><span class="glyphicon glyphicon-search"></span></button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="resourcetable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <!--<th>Process</th>-->
                                <th>Resource Type</th>
                                <th>Resource</th>
                                <th>Location</th>
                                <th>Vendor</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>Last Updated</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="11" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="resourceitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="" class="row show-grid">
                <form id="resourceeditsection">
                    <table class="table table-bordered" id="resourcetableedit" style="display: table; overflow: hidden;">

                    </table>
                </form>
            </div>
            <div class="row show-grid">
                <form id="addvendorsection">
                    <table class="table table-bordered" id="addvendortable" style="display: table; overflow: hidden;">

                    </table>
                </form>
            </div>
            <div id="resourceeaddsection" class="row show-grid">
                <form id="addresourceform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                        <tbody><tr>

                            <!--<th>
                            <select class="form-control" id="addProcess" name="addProcess" >
                                    <option value="none">Select Process</option>
                                    <option value="2">Project Setup</option>
                                    <option value="3">Production</option>
                                    <option value="4">Logistics</option>
                                    <option value="5">Construction</option>
                                    <option value="8">Overheads</option>
                                </select><span class="error" style="display: none;"></span>
                                </th>-->

                            <th><select class="form-control" id="addresourcetype" name="addresourcetype" >
                                <option value="-1">Select Resource Type</option>
                                <?php
                                $typelist=Resourcetype::model()->findAll();
                                foreach($typelist AS $list):
                                    echo "<option value='".$list->ResourceType_Id."'>".$list->Name."</option>";
                                endforeach;
                                ?>
                            </select><span class="error" style="display: none;"></span></th>



                            <th  colspan="3"><input class="form-control" type="text" id="resourcename" name="resourcename" placeholder="Resource Name"><span class="error" style="display: none;"></span></th>
                            <th><select class="form-control" id="addresourcetypeaccount" name="addresourcetypeaccount" >
                                    <option value="-1">Select Resource Account</option>
                                    <?php
                                    $typelist=AccountsItem::model()->findAll();
                                    foreach($typelist AS $list):
                                        echo "<option value='".$list->id."'>".$list->name."</option>";
                                    endforeach;
                                    ?>
                                </select><span class="error" style="display: none;"></span></th>
                            </tr>
                        <tr>
                            <th><input class="form-control" type="text" id="resourcelocation" name="resourcelocation" placeholder="Resource Location"><span class="error" style="display: none;"></span></th>
                            <th><select class="form-control" id="addresourcevendor" name="addresourcevendor">
                                    <option value="-1">Select Vendor</option>
                                    <?php
                                    $vendorlist=Vendors::model()->findAll();
                                    foreach($vendorlist AS $list):
                                        echo "<option value='".$list->Vendor_Id."'>".$list->Name."</option>";
                                    endforeach;
                                    ?>
                                </select><span class="error" style="display: none;"></span></th>

                            <th><input class="form-control small55" type="text" id="resourceunit" name="resourceunit" placeholder="Unit"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control small55" type="text" id="resourcerate" name="resourcerate" placeholder="Rate"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveresource"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>