<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/banks.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Bank"><a href="javascript:void(0)">10. Banks</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <div class="col-md-3"><button type="button" class="btn btn-success"  id="addbank"><span class="glyphicon glyphicon-plus-sign"></span>Add Bank</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listbank"><span class="glyphicon glyphicon-list-alt"></span>List Bank</button></div>
                </div>
                <div id="banklistsection">
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="banktable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="bankitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div id="bankaddsection" class="row show-grid">
                    <form id="bankform">
                        <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="bankadd">
                            <tbody><tr>
                            <th>#</th>
                                <th><input class="form-control" type="text" id="bankname" name="bankname" placeholder="Bank Name"><span class="error" style="display: none;"></span></th>
                                <th><button type="button" class="btn btn-danger" id="savebank"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                            </tr>
                            </tbody></table>
                    </form>
                </div>
            </div>
        </div>
    </div>
