<?php 
use amnah\yii2\user\models\User;
use app\models\AccountsItem;
use app\models\UserAccounts;
use app\models\Cashbills;
use app\models\Cashadvance;
use app\models\Projects;
use app\models\UserProjects;
use app\models\Vendors;

?>

<div class="panel panel-default journal-tab acco-three tab">
    <div style="display: none;">
        <script type="text/javascript" >

            var max_fields = 10;
            var x = 1;

            $(document).on( "click", ".add_field_buttonn", function(){  

                var type=$(this).attr('data-id');
                if (type=='debit'){ 
                    $("#creditaddmore").attr('disabled', true);
                    $("#creditNarration0").attr('readonly', true);
                    $("#creditamount0").attr('readonly', true);
                    if(x < max_fields){ //max input box allowed
                        //text box increment
                        $('#adddebitrow').after('<div class="col-md-12 add-row-wrpr" id="childD'+x+'"><div class="row">' +
                            '<div class="col-md-4">'+
                                '<div class="form-group">'+
                                    '<label>Debit Account</label>'+
                                    '<select class="form-control debitaccount" id="debitaccount'+x+'" name="debitaccount[]" data-id="'+x+'">'+
                                    '<option value="0">Select Account</option>'+
                                    '<?php $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                        foreach($acnts AS $accounts):
                            echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                        endforeach;?></select><span class="error"></span>' +
                                '</div>'+
                            '</div>'+
                            '<div class="col-md-4">'+
                                '<div class="form-group">'+
                                    '<label>Naration</label>'+
                                    '<textarea class="form-control Narration" id="debitNarration'+x+'" name="Journal_Narration[]" data-id="'+x+'" autocomplete="off"></textarea>'+
                                    '<span class="error"></span>'+
                                '</div>'+
                            '</div>'+
                            '<div class="col-md-4">'+
                                '<div class="row">'+
                                    '<div class="col-md-10">'+
                                        '<label>Amount</label>'+
                                        '<input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount'+x+'" data-id="'+x+'" place="Date" />'+
                                        '<span class="error"></span>'+
                                    '</div>'+
                                    '<div class="col-md-2 icon-groups">'+
                                        '<a class="btn btn-primary icon-remove remove_field1" data-id="D'+x+'" href="javascript:void(0)"></a>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div></div>');
                        x++;    
                    }
                }else if(type=='credit') { 
                    $("#debitaddmore").attr('disabled', true);
                    $("#debitNarration0").attr('readonly', true);
                    $("#debitamount0").attr('readonly', true);
                    if(x < max_fields){ //max input box allowed
                        $('#addcreditrow').after('<div class="col-md-12 add-row-wrpr" id="childC'+x+'">'+
                            '<div class="row">'+
                                '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                        '<label>Credit Account</label>'+
                                        '<select class="form-control creditaccount" id="creditaccount'+x+'" name="creditaccount[]" data-id="'+x+'">'+
                                            '<option value="0">Select Account</option>'+
                                            '<?php $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                        foreach($acnts AS $accounts):
                            echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                        endforeach;?></select><span class="error"></span>' +
                                    '</div>'+
                                '</div>'+
                                '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                        '<label>Naration</label>'+
                                        '<textarea class="form-control Narration" id="creditNarration'+x+'" name="Journal_Narration[]" data-id="'+x+'" autocomplete="off"></textarea>'+
                                        '<span class="error"></span>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="col-md-4">'+
                                    '<div class="row">'+
                                        '<div class="col-md-10">'+
                                            '<label>Amount</label>'+
                                            '<input type="text" class="form-control creditamount" placeholder="Amount" name="creditamount[]" id="creditamount'+x+'" data-id="'+x+'" place="Date" />'+
                                            '<span class="error"></span>'+
                                        '</div>'+
                                        '<div class="col-md-2 icon-groups">'+
                                            '<a class="btn btn-primary icon-remove remove_field2" data-id="C'+x+'" href="javascript:void(0)"></a>'+
                                       ' </div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>');
                        x++;
                    }
                }

            });   


            $(document).on( "click", ".remove_field1", function(){  
                //e.preventDefault();
                var JRid = $(this).attr("data-id");
                $('#child'+JRid).remove();


                var count =document.getElementsByClassName('debitamount').length;
                

                if(count == 1){
                    $('#creditaddmore').attr('disabled',false);
                    $('#creditNarration0').attr('readonly',false);
                    $("#creditamount0").attr('readonly', false);

                }else{
                    $('#creditaddmore').attr('disabled',true);
                    $('#creditNarration0').attr('readonly',true);
                    $("#creditamount0").attr('readonly', true);
                }
                var debitrate=0;
                $('.debitamount').each(function(){
                    //debitrate=debitrate+$(this).val()*1;

                    var debt_amnt = $(this).val();
                    if(debt_amnt){
                    debitrate = parseFloat(debitrate) + parseFloat(debt_amnt);
                }
            
                    $('.creditamount').val(debitrate);
                });
                

                //$('#amount').html(totalrate);
               // $('#creditamount').val(debitrate);

                x--;
            });


            $(document).on( "click", ".remove_field2", function(){  
                //e.preventDefault();
                var JRid = $(this).attr("data-id");
                $('#child'+JRid).remove();

                 var count =document.getElementsByClassName('creditamount').length;


                if(count == 1){
                   $('#debitaddmore').attr('disabled',false);
                    $('#debitNarration0').attr('readonly',false);
                    $("#debitamount0").attr('readonly', false);

                }else{
                     $('#debitaddmore').attr('disabled',true);
                    $('#debitNarration0').attr('readonly',true);
                    $("#debitamount0").attr('readonly', true);
                }
                var creditrate=0;
                $('.creditamount').each(function(){
                    //debitrate=debitrate+$(this).val()*1;

                    var debt_amnt = $(this).val();
                    if(debt_amnt){
                    creditrate = parseFloat(creditrate) + parseFloat(debt_amnt);
                }
            
                    $('.debitamount').val(creditrate);
                });
               

                //$('#amount').html(totalrate);
               // $('#creditamount').val(debitrate);

                x--;
            });

            /*$(document).ready(function() {
                var max_fields      = 10; //maximum input boxes allowed
                var wrapper         = $(".add-journal-form"); //Fields wrapper
                var add_button      = $(".add_field_buttonn"); //Add button ID
                var x = 1; //initlal text box count
                $(add_button).click(function(e){ //on add input button click
                    e.preventDefault();
                    var type=$(this).attr('data-id');
                    if (type=='debit'){ 
                        $("#creditaddmore").attr('disabled', 'disabled');
                        $("#creditNarration0").attr('disabled', 'disabled');
                        if(x < max_fields){ //max input box allowed
                            //text box increment
                            $('#adddebitrow').after('<div class="col-md-12 add-row-wrpr" id="childD'+x+'"><div class="row">' +
                                '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                        '<label>Debit Account</label>'+
                                        '<select class="form-control debitaccount" id="debitaccount'+x+'" name="debitaccount[]" data-id="'+x+'">'+
                                        '<option value="0">Select Account</option>'+
                                        '<?php $acnts//=AccountsItem::find()->orderBy(['name'=> SORT_ASC])->all();
                            //foreach($acnts AS $accounts):
                                //echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                            //endforeach;?></select><span class="error"></span>' +
                                    '</div>'+
                                '</div>'+
                                '<div class="col-md-4">'+
                                    '<div class="form-group">'+
                                        '<label>Naration</label>'+
                                        '<textarea class="form-control Narration" id="debitNarration'+x+'" name="Journal_Narration[]" data-id="'+x+'" autocomplete="off"></textarea>'+
                                        '<span class="error"></span>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="col-md-4">'+
                                    '<div class="row">'+
                                        '<div class="col-md-10">'+
                                            '<label>Amount</label>'+
                                            '<input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount'+x+'" data-id="'+x+'" place="Date" />'+
                                            '<span class="error"></span>'+
                                        '</div>'+
                                        '<div class="col-md-2 icon-groups">'+
                                            '<a class="btn btn-primary icon-remove remove_field1" data-id="D'+x+'" href="#"></a>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div></div>');
                            x++;    
                        }
                    }else if(type=='credit') { 
                        $("#debitaddmore").attr('disabled', 'disabled');
                        $("#debitNarration0").attr('disabled', 'disabled');
                        if(x < max_fields){ //max input box allowed
                            $('#addcreditrow').after('<div class="col-md-12 add-row-wrpr" id="childC'+x+'">'+
                                '<div class="row">'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group">'+
                                            '<label>Credit Account</label>'+
                                            '<select class="form-control creditaccount" id="creditaccount'+x+'" name="creditaccount[]">'+
                                                '<option value="0">Select Account</option>'+
                                                '<?php $acnts//=AccountsItem::find()->orderBy(['name'=> SORT_ASC])->all();
                            //foreach($acnts AS $accounts):
                                //echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                            //endforeach;?></select><span class="error"></span>' +
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group">'+
                                            '<label>Naration</label>'+
                                            '<textarea class="form-control Narration" id="creditNarration'+x+'" name="Journal_Narration[]" data-id="'+x+'" autocomplete="off"></textarea>'+
                                            '<span class="error"></span>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="row">'+
                                            '<div class="col-md-10">'+
                                                '<label>Amount</label>'+
                                                '<input type="text" class="form-control creditamount" placeholder="Amount" name="creditamount[]" id="creditamount'+x+'" data-id="'+x+'" place="Date" />'+
                                                '<span class="error"></span>'+
                                            '</div>'+
                                            '<div class="col-md-2 icon-groups">'+
                                                '<a class="btn btn-primary icon-remove remove_field1" data-id="C'+x+'" href="#"></a>'+
                                           ' </div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>');
                            x++;
                        }
                    }

                });

                $(wrapper).on("click",".remove_field1", function(e){
                    e.preventDefault();
                    var JRid = $(this).attr("data-id");
                    $('#child'+JRid).remove();
                    var debitrate=0;
                    $('.debitamount').each(function(){
                        debitrate=debitrate+$(this).val()*1;
                    });
                    $('#amount').html(totalrate);
                    $('#creditamount').val(debitrate);

                    x--;
                });
            });*/

        </script>

    </div>


    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_financejournals.js" type="text/javascript"></script>

    <!-- <input type="radio" id="rd5" class="finjouradio" name="rd"> -->
	<div class="panel-heading" >
	  <h4 class="panel-title" id="finjouradio">
		<a data-toggle="collapse" data-parent="#accordionfin" href="#collapsejournal">
        
		<span class="icon-book2"></span>Journal</a>
	  </h4>
	</div>
										
	<div id="collapsejournal" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body">
    		<div class="search-and-content-wrpr">
    		
    			<div class="search-and-actions-wrpr row">
    				<div class="content-search-wrpr col-md-3 col-sm-3">
    					<input type="text" placeholder="Search All" id="jsearchrestypename" class="form-control">
    					<button id="jresourcetypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
    				</div>
    				
    				<div class="content-action-wrpr col-md-9 col-sm-9">
    					<a href="#" class="btn btn-primary finjournladd addForm" title="Add "><span class="icon-add"></span> Add</a>
    					<a href="#" class="btn btn-primary list-fundreceipt"><span class="icon-th-list"></span> List</a>
    				</div>
    			</div>
    			<div class="content-wrpr">
    				
                    <!-- add form start -->
                    <form id="journalform">
                        
                        
                        
                    </form>
    				
    				<!-- add form -end -->
    				<!-- list start here -->
    				
    					<div class="journal-list-wrpr">
    					
    						<div class="row">
                            <div class="preloader" id="fin-preloader-jtab" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
    							<div class="col-md-12">
                                    
                                    <label style="text-align: center;font-size: 15px;">Journal</label>
                                
    							
    							</div>
    							<div class="col-md-12"><br>
                                    <div id="j-body">

                                    </div>   
    							</div>
    							
    						</div>
    																																														
    						
                        </div>
    				<!-- list end here -->
    				
    			</div>
    			
    		</div>
	   </div>
	</div>
</div>
