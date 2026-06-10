$(document).ready(function(){

//Set default open/close settings
    $('.acc_container').hide(function(){

        //$(this).toggleClass('active').next().slideDown();
        $('#project').addClass('active').next('.acc_container').slideDown();
        $('#listproject').trigger('click')
    }); //Hide/close all containers
//$('.acc_trigger:first').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container

    $( ".numberFormat" ).keyup(function() {
      
        Num = $(this).val();
        //function to add commas to textboxes
        Num += '';
        Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
        Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
        x = Num.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1))
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        $(this).val(x1 + x2);



    });

});