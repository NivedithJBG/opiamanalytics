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


    $('.numberOnly').keypress(function(event) {
        if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
                $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    }).on('paste', function(event) {
        event.preventDefault();
    });


    $( ".textOnly" ).keydown(function(e) {
        if ( e.ctrlKey || e.altKey) {
          e.preventDefault();
        } else {
          var key = e.keyCode;
          if (!((key == 8) || (key == 9) || (key == 20) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
              e.preventDefault();
          }
        }
    });


});



//------- TableAu--------------------------------------

var jwt;
  function getTableauDashboard(type, refresh = 0, otherParams= []){
    subDomain = $('#environment').val();
    if(subDomain == 'qa') 
      environment = 'QA';
    else                  
      environment = capitalizeFirstLetter(subDomain);

    height = '700px';
    if(type == 'Resource')
      height = '600px';

    $('.dashboardPopup').removeClass('dashboardPopupSmall');
    if(type == 'Resource')
      $('.dashboardPopup').addClass('dashboardPopupSmall');

    $('.dashboardPopupSuccessProgress').hide();
    $('.dashboardPopupSuccessmessage').html('');
    $('.dashboardPopupErrormessage').html('');

    //$('.refreshTableau').hide();
    $('#selectedDashboardType').val(type);
    $('#workBookName').val(type+'_Opiam_'+environment);
    $('.dashboardPopupTitle').html(type+' - Dashboard');

    if(refresh || !$('#dash'+type).html() || type == 'Resource'){




      selectedProject = otherParams['selectedProject'];
      if(!selectedProject)
        selectedProject = $('#selectedProject').val();

      selectedResource = otherParams['selectedResource'];
      if(!selectedResource)
        selectedResource = $('#selectedResource').val();

      var params = `<viz-parameter name=":toolbar" value="no"></viz-parameter>`;

      if(type != 'Organisation')
        params += `<viz-filter field="project" value="${selectedProject}"> </viz-filter>`;
      if(type == 'Resource')
        params += `<viz-filter field="Resource_id" value="${selectedResource}"> </viz-filter>`;

      var userid  = 'nivedithgeorge@geotech.net.in';
      var iss     = '3dbfde31-b49e-47e9-ad50-55d66ca4dae6';
      var kid     = '225a8990-9e4a-444e-ae24-5438fff61645';
      var secret  = 'KUmF9YIrveoN3qWesYK66qjtqj10qEywAkFvPl/VCTk=';
      
      var wurl = 'https://prod-apnortheast-a.online.tableau.com/t/opiam/views/'+type+'_Opiam_'+environment+'/'+type;
      //var wurl = 'https://prod-apnortheast-a.online.tableau.com/t/opiam/views/Labour_Opiam_QA/Labour';
      var scopes = ['tableau:views:embed', 'tableau:metrics:embed'];
      var token= createToken(userid,kid,secret,iss,scopes);
      let tp=`
      <tableau-viz id="tableauViz${type}" src="${wurl}" 
        token="${token}"
        height="${height}"
        width="100%"
        toolbar="no" 
        hide-tabs="true">
        ${params}
      </tableau-viz>`;


     document.getElementById('dash'+type).innerHTML =tp;

      setTimeout(function () {
        $('.refreshTableau').show();
      }, 5000);

    }
    
    $('.dashboardView').hide();
    $('#dash'+type).show();
  }




  $(document).on( "click", ".navbar-nav .icon-dashboard", function(){
      if($(".icon-dashboard").hasClass("active")) {x
        $('#project-title-head').html('Project Dashboard');
      }
      else{
        $('#project-title-head').html('Projects');
      }
  });

  $(document).on( "click", ".resourceTypeTab", function(){
    if(tableauDashboardVisible != false)
        getTableauDashboard($(this).attr("data-type"));
  });

  $(document).on( "click", ".resource-icon", function(e){
    $('#selectedResource').val($(this).attr("data-recourceid"));
    otherParamsArr = [];
    otherParamsArr['selectedProject'] = $(this).attr("data-project");
    otherParamsArr['selectedResource'] = $(this).attr("data-recourceid");

    if(tableauDashboardVisible != false)
        getTableauDashboard($(this).attr("data-type"), 0, otherParamsArr);
  });


  $(document).on( "click", ".dashboardPopupCloseBtn", function(){
      //location.reload();
  });


  $(document).on( "click", ".enlargeDashboard", function(){
      openFullscreen();
  });


  var refreshProgressInterval;
  //---REFRESH Tableau DASHBOARD-----
  $(document).on( "click", ".refreshTableau", function(){
      var type = $('#selectedDashboardType').val();
      var refreshTableauBtnContainer = '';
      $.ajax({
          type: 'POST',
          url: '../projectsmain/tableaurefresh',
          beforeSend: function () {
              refreshTableauBtnContainer = $('.refreshTableauBtnContainer').html();
              $('.refreshTableauBtnContainer').html('Refreshing...');
          },
          data: {workBookName:$('#workBookName').val()}, 
          dataType: "json",
          success: function (data) {
              
              $('.refreshTableauBtnContainer').html(refreshTableauBtnContainer);
              $('.refreshTableauBtnContainer').hide();
              
              if (data.error == 'Yes') {
                  $('.dashboardPopupErrormessage').html(data.message);
              }
              if (data.error == 'No') {
                  $('.dashboardPopupSuccessmessage').html(data.message);
                  $('.dashboardPopupSuccessProgress').show();
                  jobid = data.jobid;
                  setTimeout(function () {
                    $('.dashboardPopupSuccessmessage').html('Refreshing...');
                    moveProgessBar(10);
                    refreshProgressInterval = setInterval(function(){checkRefreshProgressInterval(jobid)}, 15000);
                  }, 5000);
              }

              //$('#dash'+type).html('<div style="padding:100px; text-align:center;">Refreshing...</div>');
              
              
          }
      });

  });
  //-------------------------------

  $( document ).ready(function() {
    getTableauDashboard('Performance');
    //$( "#dashboardPopup" ).trigger( "click" );
  });



var i = 0;
function moveProgessBar(progress) {
  if (i == 0) {
    i = 1;
    var elem = document.getElementById("myBar");
    myBarWidth = parseInt((elem.style.width).replace("%",''))
    //var width = 1;
    //var width = progress;
    var width = progress-10;
    if(myBarWidth == 90)
      var width = progress;

    var id = setInterval(frame, 10);
    function frame() {
      if (width >= progress) {
        clearInterval(id);
        i = 0;
      } else {
        width++;
        elem.style.width = width + "%";
      }
    }
  }
}

var jobProgress = 20;
function checkRefreshProgressInterval(jobid) {
    $.ajax({
          type: 'POST',
          url: '../projectsmain/tableaurefreshprogress',
          data: {jobid:jobid}, 
          dataType: "json",
          success: function (data) {
              if (parseInt(data.progress) > 0) {
                  jobProgress = parseInt(data.progress); 
              }

              if(jobProgress < 90){
                jobProgress = jobProgress+10;
              }

              moveProgessBar(jobProgress);
              
              if(jobProgress >= 100){
                selectedDashboardType = $('#selectedDashboardType').val()
                clearInterval(refreshProgressInterval);

                $('#dash'+selectedDashboardType).html('<div style="padding:50px; text-align:center;">Refreshiing...</div>');
                setTimeout(function () {
                  $('.dashboardPopupSuccessProgress').hide();
                  $('.dashboardPopupSuccessmessage').html('');
                  $('.dashboardPopupErrormessage').html('');

                  getTableauDashboard(selectedDashboardType, 1);
                  $('.refreshTableauBtnContainer').show();
                }, 3000);


                
              }
          }
      });

}

function openFullscreen() {
    selectedDashboardType = $('#selectedDashboardType').val();
    var element =  document.getElementById("tableauViz"+selectedDashboardType).shadowRoot.querySelector('iframe');
    //var element =  selectedDashboard.getElementById("tableauViz").shadowRoot.querySelector('iframe');
    if (element.requestFullscreen) {
      element.requestFullscreen();
    } else if (element.mozRequestFullScreen) {
      element.mozRequestFullScreen();
    } else if (element.webkitRequestFullscreen) {
      element.webkitRequestFullscreen();
    } else if (element.msRequestFullscreen) {
      element.msRequestFullscreen();
    }
  }

  function createToken(userid,kid,secret,iss,scp){
    var header = {
      "alg": "HS256",
      "typ": "JWT",
      "iss": iss,
      "kid": kid,
    };
    var stringifiedHeader = CryptoJS.enc.Utf8.parse(JSON.stringify(header));
    var encodedHeader = base64url(stringifiedHeader);
    var claimSet = {
      "sub": userid,
      "aud":"tableau",
      "nbf":Math.round(new Date().getTime()/1000)-100,
      "jti":new Date().getTime().toString(),
      "iss": iss,
      "scp": scp,
      "exp": Math.round(new Date().getTime()/1000)+100
    };
    var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(claimSet));
    var encodedData = base64url(stringifiedData);
    var token = encodedHeader + "." + encodedData;
    var signature = CryptoJS.HmacSHA256(token, secret);
    signature = base64url(signature);
    var signedToken = token + "." + signature;
    return signedToken;
  }
  
  function base64url(source) {
    encodedSource = CryptoJS.enc.Base64.stringify(source);
    encodedSource = encodedSource.replace(/=+$/, '');
    encodedSource = encodedSource.replace(/\+/g, '-');
    encodedSource = encodedSource.replace(/\//g, '_');
    return encodedSource;
  }

//-----------------------------------------------------


function formatDate(date, mode = 'y-m-d', shortDate = false) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if(!shortDate){
      if (month.length < 2) 
          month = '0' + month;
      if (day.length < 2) 
          day = '0' + day;
    }
      
    if(mode == 'y-m-d')
      return [year, month, day].join('-');
    else if(mode == 'd-m-y')
      return [day, month, year].join('-');
}



function capitalizeFirstLetter(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}


function getTimeDurationSum(durations){
  //var da = ["01:08", "03:46", "03:24", "05:53", "01:45", "03:32", "05:19", "08:56", "01:49", "05:40", "05:21", "02:40", "04:26", "02:02", "04:42", "03:58", "02:06", "02:46", "05:21", "03:37", "02:55", "03:26", "04:16", "01:32", "01:42", "03:22", "01:55", "01:41", "05:10", "00:45", "03:23", "05:08", "02:22", "02:34", "02:49", "01:18", "02:13", "01:37", "03:36", "05:26", "05:00", "02:41", "03:08", "01:00", "02:19", "02:33", "03:43", "01:35", "02:59", "01:38", "04:05", "04:15", "03:43", "03:43", "00:25"];
  var da = durations;
  var mins = 0;
  var secs = 0;

  for(var i=0; i<da.length; i++) {
    mins = mins + Number(da[i].substr(0, 2));
    secs = secs + Number(da[i].substr(3, 2));
  }

  mins = mins + Math.floor(secs / 60);
  secs = secs % 60;

  return mins + "." + secs
}
//----------------------------------------------------------------

$(document).on( "click", "#ganttchartPopupLink", function(){
      var projectid = $(this).data('projectid'); 

      $('#ganttchartPopupForm').attr('href',$('#ganttchartPopupForm').attr('data-link')+projectid);

      setTimeout(function () {
        $.ajax({
            type: 'POST',
            url: '../projectsmain/newganttchart?id='+projectid+'&layout=false',
            //async:false,
            success: function(data){
                $('#ganttchartPopupBody').html(data);
            }
        });


      }, 1000);

      

  });




  $(document).on( "click", "#holidayPopupLink", function(){
      var projectid = $(this).data('projectid'); 
      $('#holidayPopupForm').attr('href',$('#holidayPopupForm').attr('data-link')+projectid);
      getHolidayCalendar(projectid);
  });

  $(document).on( "click", ".holidayWeekSelector", function(){
    if(confirm("Are you sure, do you want to change the week wise off Holiday?")){
      var projectid = $(this).data('projectid'); 
      var selectedWeek = $(this).data('week'); 
      $('#holidayPopupForm').attr('href',$('#holidayPopupForm').attr('data-link')+projectid);
      getHolidayCalendar(projectid,'',selectedWeek);
    }
  });



  function getHolidayCalendar(projectid, selectedDate = '', selectedWeek = ''){
    
    $.ajax({
          type: 'POST',
          url: '../projectsmain/holidays',
          data: {projectid: projectid, selectedDate: selectedDate, selectedWeek: selectedWeek}, 
          dataType: "json",
          success: function(data){
              
              if (data.error == 'No') {
                  $('#holidayPopupBody').html(data.result);
                   //highlight_holidays = ["1-8-2023", "2-8-2023", "8-8-2023", "21-8-2023"];

                   highlight_holidays = data.holiday_arr;
                   holiday_weeks      = data.holiday_week_arr;

                   min_date = (data.last_reported_date) ? new Date(data.last_reported_date) : '';

                  // Initialize Holiday datepicker
                  $('.holidayDatepicker').datepicker({
                      beforeShowDay: function(date){
                         var month = date.getMonth()+1;
                         var year = date.getFullYear();
                         var day = date.getDate();
                         var newdate = day+"-"+month+'-'+year;// Change format of date

                         var weekNo =  date.getDay();
                         //Sunday = 0, Monday = 1, Tuesday = 2, Wednesday = 3, Thursday = 4, Friday = 5, Saturday = 6

                         if(jQuery.inArray(weekNo.toString(), holiday_weeks) != -1 || jQuery.inArray(newdate, highlight_holidays) != -1)
                              return [true, "holidayHighlight", 'Remove from Holiday' ];// Pass class name and tooltip text
                         else
                              return [true, "", 'Add as Holiday' ];// Pass class name and tooltip text

                      },
                      defaultDate:new Date(),
                      changeMonth: true,changeYear: true,
                      dateFormat: 'dd-mm-yy',
                      minDate: min_date,
                      onSelect: function(date) {
                        getHolidayCalendar(projectid,date);
                      },
                  }).datepicker('setDate', selectedDate);
              }
          }
      });
  }


  $(document).on('click','.weekly_off_selector',function(){
    $('.weekly_off_container').slideToggle(function(){
      if($('.weekly_off_container').is(":visible")){
        $('.weekly_off_arrow').removeClass('icon-keyboard_arrow_down').addClass('icon-keyboard_arrow_up');
      }
      else
        $('.weekly_off_arrow').removeClass('icon-keyboard_arrow_up').addClass('icon-keyboard_arrow_down');  
    });
  });


$(document).on('click','#updateProfile',function(){

    var error     = 0;
    var userid    = $('#edit_userid').val();
    var usrfname  = $('#edit_usrfname').val();
    var usrlname  = $('#edit_usrlname').val();
    var usremail  = $('#edit_usremail').val();
    var usrpswd   = $('#edit_usrpswd').val();
    var confirmpswd   = $('#edit_confirmpswd').val();

    if(usrfname == ''){
      $('#editprof-error-messages').html('Enter First Name').show();
      error=1;
    }
    if(usrpswd != ''){
      if(confirmpswd == '' || usrpswd != confirmpswd){
        $('#editprof-error-messages').html('Confirm password is incorrect').show();
        error=1;
      }
    }


    if(error==0){
          
      $.ajax({
          type: 'POST',
          url: '../projects/edituserss',
          beforeSend : function(){
              //$('#saveeditproject'+idval).attr("disabled", true);
          },
          dataType: "json",
          data:{userid:userid, usrrname: '', usrfname:usrfname, usrlname:usrlname, usremail:usremail, usremail:usremail, usrpswd:usrpswd},
          success: function(data){
              if(data.error=='No')
              { 
                  $('#editprof-error-messages').html('').hide();
                  $('#editprof-success-messages').html('Profile updated successfully!').show();                 
              }

          }
          });
    } 

});




//---- Reload page after 15 minutes if inactivity -----------------------------------

/*let inactivityTimer;
function startInactivityTimer() {
  const inactivityDuration = 15 * 60 * 1000; // 15 minutes (in milliseconds)
  inactivityTimer = setTimeout(reloadPageOnInactivity, inactivityDuration);
}

function reloadPageOnInactivity() {
  // Reload the page when no user activity happens for 15 minutes
  location.reload();
}

function handleUserActivity() {
  clearTimeout(inactivityTimer);
  startInactivityTimer();
}

// Add event listeners for user activity events (e.g., click, keypress, etc.)
document.addEventListener('click', handleUserActivity);
document.addEventListener('keypress', handleUserActivity);
// Add more event listeners for other user activities as needed

// Start the inactivity timer initially
startInactivityTimer();
*/
//----------------------------------------------------------------------------------


  $(document).on( "click", ".resourceUsageTab", function(){
        type = $(this).attr("data-type");
        $('.resource_usage_container').hide();
        $('#resUsage'+type+'Container').show();

  });

  $(document).on( "click", ".orderManagementTab", function(){
        type = $(this).attr("data-type");
        $('.order_management_container').hide();
        $('#order'+type+'Container').show();
  });

  $(document).on( "click", ".invoiceManagementTab", function(){
        type = $(this).attr("data-type");
        $('.invoice_management_container').hide();
        $('#invoice'+type+'Container').show();
  });


  $(document).on( "click", ".equipment-movement", function(){
        type = $(this).attr("data-type");
        console.log('#'+type+'EquipmentContainer');
        $('.equipment-movement-tab').removeClass('equipment-movement-active').addClass('equipment-movement');
        $('#'+type+'-equipment-tab').addClass('equipment-movement-active');
        $('.invoiceEquipmentContainer').hide();
        $('#'+type+'EquipmentContainer').show();
  });

  $(document).on( "click", ".receive_equip_btn", function(){
        eqpmovementid = $(this).attr("data-id");
        received_date = $('#received_date'+eqpmovementid).val();

        if(confirm("Are you sure? Do you want to recieve this equipment?")){
          $('#receive_equip_btn_container'+eqpmovementid).html('Processing...');
          $.ajax({
              type: 'POST',
              url: '../procurement/equipmentreceive',
              data: {eqpmovementid: eqpmovementid, received_date:received_date}, 
              dataType: "json",
              success: function(data){
                  if(data.error == 'No'){
                    $('#receive_equip_btn_container'+eqpmovementid).html('<span style="color: green;">Receieved</span>');
                  }
              }
          });
        }
  });

  $(document).on( "click", ".despatch_equip_btn", function(){
        eqpmovementid = $(this).attr("data-id");
        despatched_date = $('#despatched_date'+eqpmovementid).val();

        if(confirm("Are you sure? Do you want to despatch this equipment?")){
          $('#despatch_equip_btn_container'+eqpmovementid).html('Processing...');
          $.ajax({
              type: 'POST',
              url: '../procurement/equipmentdespatch',
              data: {eqpmovementid: eqpmovementid, despatched_date:despatched_date}, 
              dataType: "json",
              success: function(data){
                  if(data.error == 'No'){
                    $('#despatch_equip_btn_container'+eqpmovementid).html('<span style="color: green;">Despatched</span>');
                  }
                    
              }
          });
        }
  });

  $(document).on( "click", ".duplicateProject", function(){
        projectid = $(this).attr("data-projectid");
        if(confirm("Are you sure? Do you want to copy this project?")){
          $.ajax({
              type: 'POST',
              url: '../projects/duplicate',
              data: {projectid: projectid}, 
              dataType: "json",
              success: function(data){
                  if(data.error == 'No')
                    alert("Copied successfully");
              }
          });
        }

  });
  
  $(document).on( "keyup", ".resourceRate", function(e){
    venresid    = $(this).attr("data-venresid");
    venresactid = $(this).attr("data-venresactid");
    changeResourceAmounts(venresid, venresactid);
  });

  $(document).on( "keyup", ".resourceQty", function(e){
    venresid    = $(this).attr("data-venresid");
    venresactid = $(this).attr("data-venresactid");
    changeResourceAmounts(venresid, venresactid);
  });

  $(document).on( "change", ".actvtyResSelectAll", function(e){
    venresid      = $(this).attr("data-venresid");
    if($(this).is(":checked"))
      $(".actvtyResSelect"+venresid).prop('checked', true);
    else
      $(".actvtyResSelect"+venresid).prop('checked', false);

    resource_type_id  = $(this).attr("data-resource_type_id");
    noValidation      = false;
    if(resource_type_id == 33) noValidation = true;

    changeResourceTotals(venresid, noValidation);
  });

  $(document).on( "change", ".actvtyResSelect", function(e){
    venresid            = $(this).attr("data-venresid");
    resource_type_id    = $(this).attr("data-resource_type_id");
    noValidation        = false;
    if(resource_type_id == 33) noValidation = true;
    changeResourceTotals(venresid, noValidation);
  });

  function changeResourceAmounts(venresid, venresactid){
    resourceRate  = $('#resourceRate'+venresactid).val();
    resourceQty   = $('#resourceQty'+venresactid).val();
    resAmt        = parseFloat(resourceRate * resourceQty).toFixed(2);
    $('#resourceAmt'+venresactid).html(resAmt.toLocaleString({style:"currency", maximumFractionDigits :2}));
    changeResourceTotals(venresid);
  }

  function changeResourceTotals(venresid, noValidation = false){
    var totQty  = 0;
    var totAmt  = 0;
    var error   = 0;
    var selCnt  = 0;

    $('.actvtyResSelect'+venresid).each(function(){
        if($(this).is(":checked")){
          selCnt++;
          venresactid   = $(this).attr("data-venresactid");

          if(!noValidation){
            resourceRate  = $('#resourceRate'+venresactid).val();
            resourceQty   = $('#resourceQty'+venresactid).val();

            resAmt        = (resourceRate * resourceQty);
            totQty        += parseFloat(resourceQty);
            totAmt        += parseFloat(resAmt);
          }
          else 
           resAmt = 1;

          if(resAmt && !error )
            error = 0;
          else
            error = 1;
        }
    });
    totAmt        = totAmt.toFixed(2);

    $('#qtyTotal'+venresid).html(parseFloat(totQty));
    $('#amtTotal'+venresid).html(totAmt.toLocaleString({style:"currency", maximumFractionDigits :2}));

    if(error == 0 && selCnt > 0)
      $('#place_order_btn_'+venresid).removeAttr('disabled');
    else
      $('#place_order_btn_'+venresid).attr('disabled', 'disabled');

  }


  $(document).on( "click", ".place_order_btn", function(e){
        $('#placeOrderContainer').html('<div style="padding:50px; text-align:center;">Loading...</div>');
        $('#receiveOrderContainer').html('<div style="padding:50px; text-align:center;">Loading...</div>');

        vendorid    = $(this).attr("data-vendorid");
        resourceid  = $(this).attr("data-resourceid");
        proj_ven_res_id  = $(this).attr("data-proj_ven_res_id");
        ven_res_id  = vendorid+'_'+resourceid;

        error = 0;
        $('.actvtyResSelect'+proj_ven_res_id).each(function(){
            if($(this).is(":checked")){
              resourceRate  = $('#resourceRate'+proj_ven_res_id+'_'+$(this).val()).val();
              resourceQty   = $('#resourceQty'+proj_ven_res_id+'_'+$(this).val()).val();
              if(resourceRate && resourceQty && !error)
                error = 0;
              else
                error = 1;
            }
        });

        if(error == 0){
          setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: '../procurement/placeresourceorder',
                data: $('#place_order_form_'+proj_ven_res_id).serialize()+'&vendor='+vendorid,
                //data: $('#place_order_form_'+proj_ven_res_id).serialize()+'&vendor='+$('#vendor_'+ven_res_id).val(),
                success: function(data){
                  $('#placeOrderContainer').html(data);
                }
            });
          }, 1000);
        }
        else{
          alert("Please enter all details");
        }
  });


  $(document).on( "click", ".generate_muster_btn", function(e){
        $('#generateMusterContainer').html('<div style="padding:50px; text-align:center;">Loading...</div>');

        vendorid    = $(this).attr("data-vendorid");
        resourceid  = $(this).attr("data-resourceid");
        actid       = $(this).attr("data-actid");
        act_res_ven_key       = $(this).attr("data-act_res_ven_key");
        
        ven_res_id  = vendorid+'_'+resourceid;
        ven_res_act_id  = vendorid+'_'+resourceid+'_'+actid;

        setTimeout(function () {
          $.ajax({
              type: 'POST',
              url: '../procurement/generatemusterroll',
              data: $('#generate_muster_form_'+act_res_ven_key).serialize(),
              success: function(data){
                $('#generateMusterContainer').html(data);
              }
          });
        }, 1000);
  });


  $(document).on( "click", ".receive_order_btn", function(e){
      $('#receiveOrderContainer').html('<div style="padding:50px; text-align:center;">Loading...</div>');
      $('#placeOrderContainer').html('<div style="padding:50px; text-align:center;">Loading...</div>');

      $.ajax({
            type: 'POST',
            url: '../procurement/receiveresourceorder',
            data: {orderid: $(this).attr("data-orderid")}, 
            success: function(data){
              $('#receiveOrderContainer').html(data);

            }
        });
  });

  $(document).on( "click", ".raise_bill_btn", function(e){
      $('#raiseBillContainer').html('<div style="padding:50px; text-align:center;">Loading...</div>');
      
      current_qty = $(this).attr("data-qty");
      $.ajax({
            type: 'POST',
            url: '../procurement/raisewobill',
            data: {orderid: $(this).attr("data-orderid"), activityid: $(this).attr("data-actid")}, 
            success: function(data){
              $('#raiseBillContainer').html(data);

              $('.resourceqntty').val(current_qty);
              $('.resourceqntty').trigger('keyup');
            }
        });
  });


  var labourCnt = 1;
  $(document).on( "click", ".skilledLabourAddMore", function(e){
        resid       = $(this).attr("data-resid");
        labourtype  = $(this).attr("data-labourtype");

        rowId = 'SubConLabourRow_'+resid;
        if(labourtype == 'unskilled')
          rowId = 'SubConLabourRow_'+labourtype+'_'+resid;

        newId = 'SubConLabourRow_'+resid+'_'+labourCnt;
        subConLabourRow = $('#'+rowId).clone().attr('id', newId);
        subConLabourRow.find('.SubConIconContainer').html('<a class="btn btn-danger icon-times icon_close_small" onclick="$(\'#'+newId+'\').remove();" title="Remove"></a>');
        subConLabourRow.find('.res_trade_name').attr('data-reslabourid', resid+'_'+labourCnt);
        subConLabourRow.find('.res_trade_rate').attr('id', 'res_trade_rate_'+resid+'_'+labourCnt).val('');
        subConLabourRow.find('.no_of_skilled_labour').val('');
        subConLabourRow.find('.no_of_unskilled_labour').val('');
        subConLabourRow.find('.res_trade_name').val('');

        if(labourtype == 'unskilled')
          $("#SubConLabourRow_unskilled_"+resid).after(subConLabourRow);
        if(labourtype == 'skilled')
          $("#SubContractorTable_"+resid).append(subConLabourRow);

        labourCnt++;
  });
  

 var causeofdelayCnt = 1;
  $(document).on( "click", ".causeOfDelayAddMore", function(e){

        actid = $(this).attr("data-actid");
        rowId = 'causeOfDelayRow_'+actid;
        newId = 'causeOfDelayRow_'+actid+'_'+causeofdelayCnt;

        causeOfDelayRow = $('#'+rowId).clone().attr('id', newId);
        causeOfDelayRow.find('.causeDelayIconContainer').html('<a class="btn btn-danger icon-times icon_close_small" onclick="$(\'#'+newId+'\').remove();" title="Remove"></a>');
        causeOfDelayRow.find('.cause_of_delay').val('').attr('placeholder', 'Select Cause of Delay');
        causeOfDelayRow.find('#cause_of_delay_hours').val('');
        $("#causeOfDelayTable_"+actid).append(causeOfDelayRow);

        causeofdelayCnt++;
  });
  

  $(document).on( "click", ".tooltipClickCauseDelay", function(e){
      if($('#tooltip_cause_of_delay').is(':visible')){
        $('#tooltip_cause_of_delay').hide();
      }else{
        $('#tooltip_cause_of_delay').show();
      }
  });

  $(document).on( "click", ".tooltip_cause_of_delay_close", function(e){
       $('#tooltip_cause_of_delay').hide();
  });



  $(document).on( "change", "#invoiceHistory", function(e){
      if($(this).is(":checked")){
        $('.invoiceContent').hide();
        $('.invoiceContentHistory').show();
      }
      else{
        $('.invoiceContentHistory').hide();
        $('.invoiceContent').show();
      }

  });


  $(document).on( "change", ".move_equipment", function(e){
      
      eq_key    = $(this).attr("data-eq_key");
      cur_proj  = $(this).find(':selected').attr('data-cur_proj');

      $('#movefrom_'+eq_key).val(cur_proj);
      $('#movefromselect_'+eq_key).val(cur_proj);

  });

  $(document).on( "change", ".po_history_checkbox", function(e){
      
      if($(this).is(":checked")){
        $('.order_po_container').hide();
        $('.order_po_history_container').show();
      }
      else{
        $('.order_po_history_container').hide();
        $('.order_po_container').show();
      }

  });


$(document).on('click','.duplicateprojectbutton',function(){
    var idval=$(this).data('projectid');
    var error=0;
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/updatesave',
            beforeSend : function(){
               
            },
            dataType: "json",
            data: {projectid:idval},
            success: function(data){
                 // alert(data.durat);
                    $("#addwindow").hide();
                    $("#editwindow").show();
                    $('#durationn').val(data.durat);
                    $('#projectnames').val(data.prjname);
                    $('#projectvalues').val(data.prvalue);
                    $('#clientnames').val(data.clientnamesss);
                    $('#wrkhrss').val(data.wrkhrss);
                    $('#startdatee').val(data.strtdate);
                    $('#enddatee').val(data.endddate);
                    $('#editcashaccount').html(data.cashrow);
                    $('#editbankaccount').html(data.bankrow);
                    $('#copy_projectid').val(idval);

                }
            });
        }
});

  $(document).on( "click", ".savecopyproject", function(){
        projectid = $(this).attr("data-projectid");
          $.ajax({
              type: 'POST',
              url: '../projects/duplicate',
              data: $('#duplicateprojectform').serialize(),
              dataType: "json",
              success: function(data){
                  if(data.error == 'No')
                    location.reload();
                    //alert("Copied successfully");
              }
          });
  });
