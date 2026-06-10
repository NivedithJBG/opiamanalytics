$(function(){
   $('#addholiday').click(function(){
       $('#addholiday').addClass('btn-success');
       $('#addholiday').removeClass('btn-primary');
       $('#listholidays').addClass('btn-primary');
       $('#listholidays').removeClass('btn-success');
       $('#addholidaytable').slideDown();
       $('#searchdiv').slideUp();
       $('#holidaytable').hide()
   });
    $('#listholidays').click(function(){
       $('#addholiday').addClass('btn-primary');
       $('#addholiday').removeClass('btn-success');
       $('#listholidays').addClass('btn-success');
       $('#listholidays').removeClass('btn-primary');
       $('#searchdiv').slideDown();
       $('#addholidaytable').slideUp();
       $('#holidaysearch').trigger('click');
   });
    $('#clearspan1').click(function(){
        $( "#holidaydate" ).val('');
    });
    $('#clearspan2').click(function(){
        $( "#offdate" ).val('');
    });
    $('#saveholiday').click(function(){
        var error=0;
        $('.error').hide();
        if($('#offdate').val()=='')
        {
            $('#offdate').next("span").html('Please select Holiday Date').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projects/addholiday',
                beforeSend : function(){
                    $('#saveholiday').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#Project_Id').val(),holiday:$('#offdate').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#listholidays').trigger('click');
                        $('#selectproject').val($('#Project_Id').val())
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#saveholiday').attr("disabled", false);
                }
            });
        }
    });
    $('#holidaysearch').click(function(){

        var Project_id=$('#selectproject').val();
        var holidaydate=$('#holidaydate').val();
        $.ajax({
            type: 'POST',
            url: '../projects/holidaysearch',
            beforeSend : function(){
                $('#holidaytable').show()
                $('#holidaysearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {Project_id:Project_id,holidaydate:holidaydate},
            success: function(data){
                if(data.error=='No')
                {

                    $('#holidayitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#holidaysearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('click','.deletholidaybutton',function(){
    var holidayid=$(this).val();
    var r = confirm("Are you sure you want to delete this Holiday?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects/deleteholiday',
            beforeSend : function(){
                $('#deletholidaybutton'+holidayid).attr("disabled", true);
            },
            dataType: "json",
            data: {holidayid:holidayid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#holidayrow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletholidaybutton'+holidayid).attr("disabled", true);
            }
        });
    }
});
$(function() {
    $( "#offdate" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat:'yy-mm-dd'
    });
    $( "#holidaydate" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat:'yy-mm-dd'
    });
});