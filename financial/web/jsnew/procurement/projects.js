$(document).on('click','#procuprojectlist',function(){

    $('.acco-one').addClass('active');
    $('.acco-two').removeClass('active');
    $('.acco-three').removeClass('active');
    $('.acco-four').removeClass('active');

    $('.acco-five').removeClass('active');
    $('.acco-six').removeClass('active');
    $('.acco-seven').removeClass('active');
    



 setTimeout(function(){
                             
    //$('#projectlistsection').show('slow');
    $.ajax({
        type: 'POST',
        url: '../procurement/projectsearch',
        beforeSend : function(){
            $('#projectsearch').attr("disabled", true);
            $('.preloader').show();
        },
        dataType: "json",

        success: function(data){
            if(data.error=='No')
            {
                $('#procuprojlisting').html(data.result);
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
        url: '../projectsmain/userprojectprocu',
        dataType:"json",
        data: {prjctid:prjctid},
        success: function(data){
            if(data.error=='No')
            {

                $('#selectedprocu-projctid').html(data.result);

            }
        }
    });
});