<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/advanceapproval.js" type="text/javascript"></script>
<?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if($user['superuser']==2): ?>
<!--<script type="text/javascript">
    $(document).on('click','.paymenttype',function(){
        var id=$(this).attr('data-id');
        var userid=$(this).attr('data-user');
        //var payment=($('input[name=paymenttype'+id+']:checked').val());
        var payment=($('input[type=radio][id=paymenttype'+id+']:checked').val());
        if(payment=='2'){
            $('#statusdiv'+id).html(
            '<input type="radio" class="advancestatus" name="advancestatus'+userid+''+id+'" id="advancestatus'+id+'" data-id="'+id+'" value="5" checked> Save as draft' +
            '<input type="radio" class="advancestatus" name="advancestatus'+userid+''+id+'" id="advancestatus'+id+'" data-id="'+id+'" value="2"> Deny'
            );
        }
        else {
            $('#statusdiv'+id).html(
            '<input type="radio" class="advancestatus" name="advancestatus'+userid+''+id+'" id="advancestatus'+id+'" data-id="'+id+'" value="1" checked> Approve' +
            '<input type="radio" class="advancestatus" name="advancestatus'+userid+''+id+'" id="advancestatus'+id+'" data-id="'+id+'" value="2"> Deny'
            );
        }
    });
</script>-->
<?php endif;?>
<h2 class="acc_trigger" id="advanceapproval"><a href="javascript:void(0)">3. Advance for approval</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="advanceappprovalsection">
                <div class="row show-grid">
                    <input type="hidden" id="listadvanceapproval">
                    <input type="hidden" id="closeadvfappr">
                    <form id="advapprovalform">
                        <table class="table table-bordered" id="advanceappprovaltable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Accounthead</th>
                                <th>Purpose</th>
                                <th>Amount</th>
                                <th>Voucher Type</th>
                                <th>Status</th>
                                <th colspan="1"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="advanceappprovalitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>