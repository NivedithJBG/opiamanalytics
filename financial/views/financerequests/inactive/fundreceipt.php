<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/receipt.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).on('focus','#datepicker',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
</script>
<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Fund Receipt</h1>
<form method="POST" action="" id="receiptform">
    <table class="table table-bordered" align="center" style="width: 50%">
        <tbody>
        <tr>
            <th><span class="headings">Project</span></th>
            <td ><select class="form-control" name="Receipt_Project" id="receiptproject"  title="Select Project">
                <option value="0">Select Project</option>
                <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                <?php foreach($adminprojects AS $data):?>
                    <option value="<?php echo $data['Project_Id'];?>"><?php echo $data['Name']; ?></option>
                    <?php endforeach;?>
                <?php else: ?>
                <?php foreach($userprojects AS $data):?>
                    <option value="<?php echo $data['projectid'];?>"><?php echo $data['Name']; ?></option>
                    <?php endforeach;?>
                <?php endif;?>
            </select>
                <span class='error'></span>
            </td>

        </tr>
        <tr>
            <th><span class="headings">Place</span></th>
            <td>
                <select class="form-control" name="place" id="place">
                    <option value="0">Select Place</option>
                    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                        <?php foreach($adminprojects AS $data):?>
                            <option value="<?php echo $data['Project_Id'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php foreach($userprojects AS $data):?>
                            <option value="<?php echo $data['projectid'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                    <?php /*$projects=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                    foreach($projects AS $project):*/?><!--
                        <option value="<?php /*echo $project['Project_Id']*/?>"><?php /*echo $project['Name']*/?></option>
                        --><?php /*endforeach;*/?>
                </select>
                <span class='error'></span></td>
        </tr>
        <tr>
            <th style="width: 40%"><span class="headings">Date</span></th>
            <td style="width: 60%"><input type="text" class="form-control" name="Receipt_Date" id="datepicker" value="<?php echo date("d-m-Y");?>"></td>
        </tr>
        <tr>
            <th><span class="headings">Purpose</span></th>
            <td><textarea rows="4" class="form-control" cols="50" id="Purpose" name="Receipt_Purpose" ></textarea><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Amount</span></th>
            <td><input type="text" class="form-control" placeholder="Amount" name="Receipt_Amount" id="Amount"><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Account Head</span></th>
            <td>
                <select class="form-control" id="Account" name="Receipt_Account">
                    <option value="0">Select Account</option>
                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        echo "<option value='".$accounts->id."'>".$accounts->name."</option>";
                    endforeach;?>
                </select>
                <span class='error'></span></td>
        </tr>
        <tr >
            <th ><button type="submit" class="btn btn-primary" id="savereceipt" name="Receipt_savereceipt">Save</button></th>
            <th ><button type="button" class="btn btn-primary" id="cancelreceipt" name="Receipt_cancelreceipt" onclick="goBack()">Cancel</button></th>
        </tr>
        </tbody>
        </thead>
    </table>
</form>