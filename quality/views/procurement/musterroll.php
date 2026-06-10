<style>
    .header, .homeurl{
        display:none;
    }
    .direct-work-order-cntnr label {
        display: block;
    }
    label {
        display: inline-block;
        width: 8em;
    }
    .purchase-order2-wrpr {
        text-align: left;
    }
</style>

<form method="POST" action="" id="placeorderform" >
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    <div class="purchase-order2-wrpr">
        <div class="col-md-12">
		    <h2 class="pOrder-title">Direct Work Order</h2>
	    </div>
	    <h4> <?php echo $activity;?></h4>
	    <?php echo $datarows;?>
	    <div class="col-md-12 text-center approve-cancel" style="margin-top:30px;">
	        <input type="hidden" name="ordertype" value="3">
            <button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder"><span class="icon-close"></span> Cancel</button>
            <button type="submit" value="Submit" id="purchaseorderbtn" class="btn btn-primary approve" name="submit"><span class="icon-check"></span> Submit</button>
        </div>
    </div>
</form>


<script type="text/javascript">
    $(document).on('click','#purchaseorderbtn',function(){

        var error=0;
        $('.error').hide();
        if($('#working_hours').val()=='')
        {
            $('#working_hours').next("span").html('Select Working Hours').show('slow');
            error=1;
        }

        if(error==0){
            setTimeout(function(){
                    parent.closeFrame2();

                    return true;
                    
            },500);
        }
        else{
            return  false;
        }
        
    });

    $(function(){
        $('#cancelorder').click(function(){
            setTimeout(function(){
                     
                parent.closeFrame2();
                    
            },500);
        });
    });

</script>