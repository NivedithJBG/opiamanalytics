<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/journal.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="journals"><a href="javascript:void(0)">6.Journal</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/journals"><button type="button" class="btn btn-success"  id="addjournal"><span class="glyphicon glyphicon-plus-sign"></span>Create Journal</button></a> </div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listjournals"><span class="glyphicon glyphicon-list-alt"></span>List Journals</button></div>
                </div>
                <div id="journallistsection">

                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="journeltable" style="display: table; overflow: hidden;">
                                <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if(User::model()->isAdmin() || User::model()->findbyPk(Yii::app()->user->id)->superuser==2) : ?>
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Place</th>
                                    <th>Credit Account</th>
                                    <th>Debit Account</th>
                                    <th >Total Amount</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <?php else:?>
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Place</th>
                                    <th>Credit Account</th>
                                    <th >Total Amount</th>
                                    <th ></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <?php endif;?>
                                <tbody id="journelitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>