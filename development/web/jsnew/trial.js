$(document).on('click','#trial',function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#trialsection').hide();

    $(function(){

        $('#trialsearch').click(function(){

            $('#trialsection').slideDown('slow');

            $.ajax({

                type: 'POST',

                url: '../FinanceRequests/TrialBalance',

                beforeSend : function(){

                    $('#projectsearch').attr("disabled", true);

                    $('.preloader').show();

                },

                dataType: "json",

                data: {fromdate:$('#trialfromdate').val(),todate:$('#trialtodate').val()},

                success: function(data){

                    if(data.error=='No')

                    {

                        $('#trialitems').html(data.result);

                        $('#trialtable').show();

                        $('#printtrial').html(data.print);
                        //$('#exporttrial').html(data.export);

                        $('#trialinfo').html(data.trial);

                    }

                    else

                    {

                        alert(data.errortext);

                    }

                    $('.preloader').hide();

                }

            });

        });

    });





});