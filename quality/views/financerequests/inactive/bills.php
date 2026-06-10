<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/bills.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/script.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).on('focus','#datepicker',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    $(document).on('focus','#duedate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    $(document).on('focus','#begindate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    $(document).on('focus','#enddate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
<script type="text/javascript">
    $(function(){
        $('#cancelbill').click(function(){
            window.location = '<?php echo Yii::app()->createUrl('FinanceRequests/index');?>'
        });
    });

</script>
    <form method="POST" action="" id="billsform">
        <h1>Bills</h1>
        <table class="table table-bordered" align="center">
            <tbody>
                <tr>
                    <th ><span class="headings">Bill Type</span></th>
                    <td colspan="2"><span> <?php if($type==0) echo "Utility Bill"?>
                        <?php if($type==1) echo "Work Bill"?>
                        <?php if($type==2) echo "Purchase Bill"?>
                        <?php if($type==3) echo "Client Bill"?>
                        <?php if($type==4) echo "Travel Bill"?>
                        <?php if($type==5) echo "Cash Bill"?>
                        </span>
                    </td>
                    <input type="hidden" name="billtype" value="<?php echo $type?>"/>
                </tr>
            </tbody>
        </table>
        <button type="submit" name="billtypebutton" id="billtypebutton" style="display:none;"></button>
        <?php if($type==2):
            $this->renderPartial('_purchasebill', array('adminprojects'=> $adminprojects,'userprojects' => $userprojects,'resources'=> $resources));
            elseif($type==1):
            $this->renderPartial('_workbill', array('adminprojects'=>$adminprojects,'userprojects' => $userprojects));
            elseif($type==3):
            $this->renderPartial('_workbill', array('adminprojects'=>$adminprojects,'userprojects' => $userprojects));
            elseif($type==4):
            $this->renderPartial('_travelbill', array('username' => $username,'designation' => $designation));
            else:
            $this->renderPartial('_utilitybill', array('adminprojects'=>$adminprojects,'userprojects' => $userprojects));
        endif;?>
    </form>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#billtype').change(function(e){
                $('#billtypebutton').trigger('click');
            });
        });
    </script>