<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/manualcashflow.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="manualcashflow"><a href="javascript:void(0)">13. Cash Flow</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="col-md-12" id="monthslist"></div>
            <form action="" id="mancashflowform" method="post">
                <!--<div class="row show-grid" id="defaultfilter">
                    <div class="col-md-3">
                        <?php
/*                        $monthcurrent = date("F Y", strtotime( date( 'Y-m-01' )));
                        $months='<option value="none">Select Month</option>';
                        $months.="<option value='".date("Y-m-01", strtotime( date( 'Y-m-01' )))."'>$monthcurrent</option>";
                        for ($m=1; $m<=12; $m++) {
                            $month = date("F Y", strtotime( date( 'Y-m-01' )." +$m months"));
                            $months.="<option value='".date("Y-m-01", strtotime( date( 'Y-m-01' )." +$m months"))."'>$month</option>";
                        }
                        */?>
                        <select class='form-control' name="month" id="month"><?php /*echo $months;*/?></select>
                        <span class="error" style="float: left"></span>
                    </div>
                    <div class="col-md-3">
                        <input type="button" id="createcashflow" class="btn btn-primary" value="Create Cash Flow">
                    </div>
                    <div class="col-md-3">
                        <input type="button" id="projectcashflow" class="btn btn-primary" value="Project Cash Flow">
                    </div>
                </div>-->
                <div class="row show-grid" id="projcashfilter">
                    <div class="col-md-3">
                        <select class="form-control" id="projcashproj" name="projcashproj">
                            <option value="none">Select Project</option>
                            <?php $project=Projects::model()->findAll(array('condition'=>'Status=0'));
                            foreach($project AS $list):
                                echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                            endforeach;?>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>
                    <div class="col-md-3">
                        <?php
                        $monthcurrent = date("F Y", strtotime( date( 'Y-m-01' )));
                        $months='<option value="none">Select Month</option>';
                        $months.="<option value='".date("Y-m-01", strtotime( date( 'Y-m-01' )))."'>$monthcurrent</option>";
                        for ($m=1; $m<=12; $m++) {
                            $month = date("F Y", strtotime( date( 'Y-m-01' )." +$m months"));
                            $months.="<option value='".date("Y-m-01", strtotime( date( 'Y-m-01' )." +$m months"))."'>$month</option>";
                        }
                        ?>
                        <select class='form-control' name="month" id="projmonth"><?php echo $months;?></select>
                        <span class="error" style="float: left"></span>
                    </div>
                    <div class="col-md-3">
                        <input type="button" id="createprojcashflow" class="btn btn-primary" value="Create Cash Flow">
                    </div>
                    <!--<div class="col-md-3">
                        <input type="button" id="cashflow" class="btn btn-primary" value="Cash Flow">
                    </div>-->
                </div>
                <!--<div id="mancashflowlist">
                    <table class="table table-bordered" id="mancashflowtable">
                        <thead>
                        <tr>
                            <th colspan="6"><span id="title" style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center"></span>
                            </th>
                        </tr>
                        <tr>
                            <th class="small75">Sl No</th>
                            <th>Account Type</th>
                            <th>Account Head</th>                            
                            <th>Amount</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="preloader">
                            <td colspan="6" align="center">
                                <img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle">
                            </td>
                        </tr>
                        </thead>
                        <tbody id="mancashflowitems">

                        </tbody>
                    </table>
                    <div class="row show-grid">
                        <div class="col-md-3">
                            <select id="accounttype" name="accounttype" class="form-control">
                                <option value="0">Select Account type</option>
                                <?php
/*                                $acnttypes=AccountTypes::model()->findAll();
                                foreach($acnttypes AS $acnttype):
                                    if($model->account_type == $acnttype->type_id):
                                        $selected='selected';
                                    else:
                                        $selected='';
                                    endif;
                                    echo "<option value='".$acnttype->type_id."' $selected id='acnttype'>".$acnttype->name."</option>";
                                endforeach;
                                */?>

                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary accounttypes" id="searchaccounts">
                                <span class="glyphicon glyphicon-search"></span>Search
                            </button>
                        </div>
                    </div>
                    <table class="table table-bordered" id="accountstable" style="display: none;width: 83%">
                        <thead>
                        <tr>
                            <th>Account Head</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                        <tr id="aacntspreloader">
                            <td colspan="3" align="center">
                                <img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle">
                            </td>
                        </tr>
                        </thead>
                        <tbody id="accountsitems">

                        </tbody>
                    </table>
                </div>-->
                <div id="projcashflowlist" style="display: none">
                    <table class="table table-bordered" id="projcashflowtable">
                        <thead>
                        <tr>
                            <th colspan="6"><span id="projtitle" style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center"></span>
                            </th>
                        </tr>
                        <tr>
                            <th class="small75">Sl No</th>
                            <th>Account Subgroup</th>
                            <th>Cash Inflow</th>
                            <th>Cash Outflow</th>
                        </tr>
                        <tr class="preloader">
                            <td colspan="4" align="center">
                                <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle">
                            </td>
                        </tr>
                        </thead>
                        <tbody id="projcashflowitems">

                        </tbody>
                    </table>
                    <!--<div class="row show-grid">
                        <div class="col-md-3">
                            <select id="projaccounttype" name="projaccounttype" class="form-control">
                                <option value="0">Select Account type</option>
                                <?php
/*                                $acnttypes=AccountTypes::model()->findAll();
                                foreach($acnttypes AS $acnttype):
                                    if($model->account_type == $acnttype->type_id):
                                        $selected='selected';
                                    else:
                                        $selected='';
                                    endif;
                                    echo "<option value='".$acnttype->type_id."' $selected>".$acnttype->name."</option>";
                                endforeach;
                                */?>

                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" id="searchprojaccounts">
                                <span class="glyphicon glyphicon-search"></span>Search
                            </button>
                        </div>
                    </div>-->
                    <!--<table class="table table-bordered" id="projaccountstable" style="display: none;width: 83%">
                        <thead>
                        <tr>
                            <th>Account Head</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                        <tr id="projaacntspreloader">
                            <td colspan="3" align="center">
                                <img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle">
                            </td>
                        </tr>
                        </thead>
                        <tbody id="projaccountsitems">

                        </tbody>
                    </table>-->
                </div>
            </form>
        </div>
    </div>
</div>