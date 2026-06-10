
<h1>Import Ledger Balance</h1>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="panel-body form-horizontal payment-form">


        <div class="form-group">
            <label class="col-sm-3 control-label" for="Type">Select CSV <span class="required">*</span></label>
            <div class="col-sm-4">
                <input type="file" name="importcsv" class="form-control" id="importcsv">
            </div>
        </div>
        <?php if($message!=''):?>
            <div class="alert alert-info" role="alert">
                <strong><?php echo $message;?></strong>
            </div>
        <?php endif;?>
        <div class="form-group">
            <div class="col-sm-3 pull-left"> </div>
            <div class="col-sm-3 pull-left">
                <button class="btn btn-primary cancelbtn" type="button" value="Reset">Cancel</button>
            </div>
            <div class="col-sm-3 pull-left">
                <button type="submit" class="btn btn-primary" id="Upload" name="submit">Upload</button>
            </div>
        </div>
    </div>
</form>