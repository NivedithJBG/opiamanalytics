$(document).on('click','#rprojectlist',function(){

    $('.acco-one').addClass('active');
    $('.acco-two').removeClass('active');
    $('.acco-three').removeClass('active');
    $('.acco-four').removeClass('active');

    $('.acco-five').removeClass('active');
    $('.acco-six').removeClass('active');
    $('.acco-seven').removeClass('active');
    $('.acco-eight').removeClass('active');
    $('.acco-nine').removeClass('active');
    $('.acco-ten').removeClass('active');
    $('.acco-eleven').removeClass('active');
    $('.acco-twelve').removeClass('active');
    $('.acco-thirteen').removeClass('active');



 setTimeout(function(){
                             
    //$('#projectlistsection').show('slow');
    $.ajax({
        type: 'POST',
        url: '../projects/projectsearch',
        beforeSend : function(){
            $('#projectsearch').attr("disabled", true);
            $('.preloader').show();
        },
        dataType: "json",

        success: function(data){
            if(data.error=='No')
            {
                $('#operationprojlisting').html(data.result);
                $('#projecttable').show();
            }
            else
            {
                alert(data.errortext);
            }
            $('#projectsearch').attr("disabled", false);
            $('.preloader').hide();
        }
    });
        },100);
    //return false;
});

$(document).on("click",'.rprojectselection',function(){
    var prjctid=$(this).attr("data-id");
    $.ajax({
        type: 'POST',
        url: '../projectsmain/userproject',
        dataType:"json",
        data: {prjctid:prjctid},
        success: function(data){
            if(data.error=='No')
            {
                $('#selected-projctid').html(data.result);
                location.reload();
            }
        }
    });
});