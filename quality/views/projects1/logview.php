<h1>Log Book</h1>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>User</th>
        <th>Project</th>
        <th>Process</th>
        <th colspan="5">Activity</th>
    </tr>
    <tr style="background-color: #fff">
        <td><?php echo $username;?></td>
        <td><?php echo $projectname;?></td>
        <td><?php echo $process;?></td>
        <td colspan="5"><?php echo $name;?></td>
    </tr>
    <tr style="background-color: #fff"><td colspan="8"> </td></tr>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Equipment</th>
        <th>Start Time/Kms</th>
        <th>End Time/Kms</th>
        <th>Net Time/Kms</th>
        <th>Trips</th>
        <th>Diesel Consumed</th>
    </tr>
    </thead>
    <tbody>
    <?php echo $datarows;?>

    <tr>
        <td colspan="7"></td>
        <td><button type="button" class="btn btn-primary" onclick="goBack()">Cancel</button></td>
    </tr>
    </tbody>
</table>
<script>
    function goBack()
    {
        //window.history.back()
        newwindow = window.open('<?php echo Yii::app()->request->baseUrl; ?>/projects/report','_self',false);
    }
</script>