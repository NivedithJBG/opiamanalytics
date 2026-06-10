<style>
    .header, .homeurl{
        display:none;
    }
    label {
    display: inline-block;
    width: 7em;
}
    
</style>

<body class="procurement" style="background:#fff !important">
    <form method="POST" action="" id="placeorderform" >

        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <div class="purchase-order2-wrpr">
            <div class="col-md-12" >
                <h2 class="pOrder-title" style="text-align: left;">Despatch Order</h2>
            </div>

            <?php echo  $datarowss; ?>

            <div class="row direct-work-order-cntnr">
        
                <div class="col-md-4" style="text-align: left;">
                    <label>Date</label>
                    <?php echo $date;?>
                </div>
            
                <div class="col-md-4 " style="text-align: left;">
                    <label>GSTN</label>
                    <input class="form-control" type="text" value="32AAFCG7358B1ZV" placeholder="Enter GSTN"    disabled />
                    <input type="hidden" name="ordertype" value="5">
                </div>
                <div class="col-md-4 ">
                    <div style="height:13px; ">&nbsp;</div>
                    <div class="center approve-cancel">
                        <button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                        <button type="submit" value="Submit" id="despatchorderbtn" class="btn btn-primary approve" name="submit"><span class="icon-check"></span> Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- </div> -->

    </form>
</body>

<script type="text/javascript">

    $(function(){
        $('#cancelorder').click(function(){
            //window.location = '<?php //echo Yii::app()->createUrl('procurement/index');?>'
            setTimeout(function(){
                     
                    // $('.ui-dialog-buttonset button').trigger('click');
                      //alert('sdf');
                      //$('.customModal ').addClass('abc');
                      parent.closeFrame2();
                    
            },500);
        });
    });

    $(document).on('focus','.date',function(){
        var id=$(this).attr('data-id');
        $('#date'+id+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});

    });

    $(document).on('click','#despatchorderbtn',function(){
        var error=0;
        $('.error').hide();

        $('.from').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#from"+id).next("span").html('Select Move from').show('slow');
                error=1;
            }
        });
        $('.to').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#to"+id).next("span").html('Select Move to').show('slow');
                error=1;
            }
        });
        $('.date').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#date"+id).next("span").html('Select Date').show('slow');
                error=1;
            }
        });
        $('.vehicle_no').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#vehicle_no"+id).next("span").html('Enter Period of Vehicle No').show('slow');
                error=1;
            }
        });

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
</script>