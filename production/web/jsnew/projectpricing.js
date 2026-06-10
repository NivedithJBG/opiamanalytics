
$(document).on('click','#projects',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#projectlistsection').show('slow');
    $.ajax({
        type: 'POST',
        url: '../ProjectPricing/Projects',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#projectitems').html(data.result);
                $('#projecttable').show();
            }

            $('#projectsearch').attr("disabled", false);
            $('.preloader').hide();
        }
    });
    return false;
});
$(document).on( "click", ".viewDocuments", function(){
    var projectid=$(this).val();
    $('#selectedProjectId').val(projectid);
    var url='../doccumentManager/index?project='+projectid;
    window.location.href = url;
});

