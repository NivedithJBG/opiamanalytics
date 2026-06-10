<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projectsfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="project"><a href="#Projects">1. Projects</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addproject"><span class="glyphicon glyphicon-plus-sign"></span>Add Projects</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listproject"><span class="glyphicon glyphicon-list-alt"></span>List Projects</button></div>
            </div>
            <div id="projectlistsection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="projecttable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#Projects</th>
                                <th>Projects</th>
                                <th colspan="2">Status</th>


                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="projectitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="projectaddsection" class="row show-grid">
                <form id="addprojectform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="projectname" name="projectname" placeholder="Project Name"><span class="error" style="display: none;"></span></th>
                            <!--<th style="font-size:12px;">Sector</th>
                            <th style="width: 16%">
                                <select class="form-control" id="sector" name="sector">
                                    <option value="0">Select Sector</option>
                                    <?php /*$sector=Sector::model()->findAll();
                                    foreach($sector AS $sectors):
                                        echo "<option value='".$sectors->Sector_id."' >".$sectors->Name."</option>";
                                    endforeach;*/?>
                                </select>
                            </th>-->
                            <th style="font-size:12px;">Cash Account</th>
                            <th style="width: 17%">
                                <select class="form-control cashaccount" id="cashaccount" name="cashaccount">
                                    <option value="0">Select Account</option>
                                    <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=1','order'=>'name ASC'));
                                    foreach($acnts AS $accounts):
                                        echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                    endforeach;?>
                                </select>
                                <span class="error" style="display: none;"></span>
                            </th>
                            <th style="font-size: 12px;">Bank Account</th>
                            <th style="width: 17%">
                                <select class="form-control bankaccount" id="bankaccount" name="bankaccount">
                                    <option value="0">Select Account</option>
                                    <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                                    foreach($acnts AS $accounts):
                                        echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                    endforeach;?>
                                </select>
                                <span class="error" style="display: none;"></span>
                            </th>
                            <!--<th>
                                <input type="radio" class="account" name="account" value="1" >Cash
                                <input type="radio" class="account" name="account" value="2" >Bank
                                <span class="error " style="display: none;position: absolute;margin-top: 22px;margin-left: -99px;"><br></span>
                            </th>-->
                            <th><button type="button" class="btn btn-danger" id="saveproject"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>