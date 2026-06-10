<h1>Log Book</h1>
<form action="" method="post">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th colspan="5">Activity</th>
        </tr> 
        <tr style="background-color: #fff">
            <td><?php echo $date;?></td>
            <td><?php echo $username;?></td>
            <td colspan="5"><?php echo $name;?></td>
        </tr>
        <tr style="background-color: #fff"><td colspan="7"> </td></tr>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Equipment</th>
            <th>Start Time/Kms</th>
            <th>End Time/Kms</th>
            <th>Trips</th>
            <th>Diesel Consumed</th>
        </tr>
        </thead>
        <tbody>
        <?php echo $datarows;?>
        <tr>
            <td colspan="5"></td>
            <td><button type="submit" class="btn btn-primary" name="update" value="update">Update</button></td>
            <td><button type="button" class="btn btn-primary" onclick="goBack()">Cancel</button></td>
        </tr>
        </tbody>
    </table>
</form>
<script>
    function goBack()
    {
        //window.history.back()
        newwindow = window.open('<?php echo Yii::app()->request->baseUrl; ?>/projects/report','_self',false);
    }
</script>