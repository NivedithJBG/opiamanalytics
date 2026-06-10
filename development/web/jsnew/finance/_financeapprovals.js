$(document).on('click','.fundverradio',function(){

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../financerequests/fundverdata',
        data:{},
        success: function(data){
            if(data.error=='No'){
                $('#fin-ver-header').html(data.result);
            }else{
                alert(data.errortext);
            }
        }
    }); 

});

$(document).ready(function(){

   
     $(document).on('click','.selectbankapp',function(){
        //e.preventDefault();alert('ff');
        var idCheck = $(this).attr("data-id");//alert(idCheck);
        var iidCheck = $('#selectbankname_'+idCheck).val();
        $('#projectBank').val(idCheck);
        if(idCheck){
            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/getaddrowsapprov',
                data:{aheadapprovid: $(this).attr("data-id"),aheadname: iidCheck},
                success: function(data){
                    if(data.error=='No'){
                        $('#fin-Approval-header').show();
                        $('#fin-Approval-body').show();
                        $('#fin-Approval-content').html(data.resulttwo);
                    }else{
                        alert(data.errortext);
                    }
                }
            }); 
        }   
    });

    $(document).on('change','.fin-app-project',function(e){
        e.preventDefault();
        var searchproject = $('#fin-app-project').val();
        var searchBank = $('#projectBank').val();
        if(searchproject != 'none'){

            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/getprojectsforapprove',
                data:{searchproject: searchproject,searchBank: searchBank},
                success: function(data){
                    if(data.error=='No'){
                        $('#fin-Approval-body').show();
                        $('#fin-Approval-content').html(data.resultsearch);
                    }else{
                        //alert("No Request avilable in selected project");
                        $('#fin-Approval-body').show();
                        $('#fin-Approval-content').html('<br><br><br><br><h5> No result Found.. </h5>');
                    }
                }
            });

        }    
    });

    $(document).on('click','.ApproveMyfund',function(e){
        e.preventDefault();
        var finreqID = $(this).attr("data-id");
        var bankID = $('#ApprovefundBank-'+finreqID).attr("data-id");
        var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();
        if(finreqID != ''){

            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/approvemyfund',
                data:{finreqID: finreqID,status: 1},
                success: function(data){
                    if(data.error=='No'){
                        //$('#fin-Approval-body').show();
                        //$('#fin-Approval-content').html(data.resultsearch);ApproveMyfundActive-
                        //alert("Not -----approved");
                    }else if(data.error=='Yes'){
                        $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                        $('#ApproveMyfund-'+finreqID).removeClass("innactive");
                        $('#RejectMyfund-'+finreqID).addClass("innactive");
                        //alert("Approved");
                    }
                }
            });

        }    
    });

    $(document).on('click','.RejectMyfund',function(e){
        e.preventDefault();
        var finreqID = $(this).attr("data-id");
        var bankID = $('#RejectMyfund-'+finreqID).attr("data-id");
        var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();
        if(finreqID != ''){

            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/approvemyfund',
                data:{finreqID: finreqID,status: 2},
                success: function(data){
                    if(data.error=='No'){
                        //$('#fin-Approval-body').show();
                        //$('#fin-Approval-content').html(data.resultsearch);
                        //alert("Not -----denied");
                    }else if(data.error=='Yes'){
                        $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                        $('#RejectMyfund-'+finreqID).removeClass("innactive");
                        $('#ApproveMyfund-'+finreqID).addClass("innactive");
                        //alert("Rejected");
                    }
                }
            });

        }    
    });


});

