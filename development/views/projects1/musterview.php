<h1>Muster Roll</h1>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <td><?php echo $date;?></td>
            <th>User</th>
            <td><?php echo $username;?></td>
            <th>Activity</th>
            <td colspan="5"><?php echo $name;?></td>
        </tr>
        <tr style="background-color: #fff"><td colspan="10"> </td></tr>
        <tr>
            <th>#</th>
            <th>Name of the Employee</th>
            <th>Trade</th>
            <th>Days Worked</th>
            <th>Rate</th>
            <th>Overtime Hours</th>
            <th>OT Rate</th>
            <th>Wages Earned</th>
            <th>Deductions</th>
            <th>Net Wages Earned</th>
        </tr>
    </thead>
    <tbody>
    <?php echo $datarows;?>
    </tbody>
</table>