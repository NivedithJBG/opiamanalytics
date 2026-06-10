
<?php 
use amnah\yii2\user\models\User;
use app\models\ProjuserSelection;
use app\models\Projects;

?>



 <!-- src="embedding.3.0.js" -->
  <script type="module" src="https://prod-apnortheast-a.online.tableau.com/javascripts/api/tableau.embedding.3.latest.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js" integrity="sha512-E8QSvWZ0eCLGk4km3hxSsNmGWbLtSCSUcewDQPQWZF6pEU8GlT8a5fF32wOl1i8ftdMhssTrF/OhyGWwonTcXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script>
  var jwt;
  function go(type){
    if(!$('#dash'+type).html()){
      var userid = 'nivedithgeorge@geotech.net.in';
      var iss = '3dbfde31-b49e-47e9-ad50-55d66ca4dae6';
      var kid = '225a8990-9e4a-444e-ae24-5438fff61645';
      //var secret = 'Pu1wDpzqT0eoifFq5HgzjQ==:DSfq7iACQBDObkqf4TIDKubhpyFyPt26';
      var secret = 'KUmF9YIrveoN3qWesYK66qjtqj10qEywAkFvPl/VCTk=';
      
      //var wurl = 'https://prod-apnortheast-a.online.tableau.com/t/opiam/views/Demo/Estimate';
      
      var wurl = 'https://prod-apnortheast-a.online.tableau.com/t/opiam/views/'+type+'/'+type;

      var scopes = ['tableau:views:embed', 'tableau:metrics:embed'];
      var token= createToken(userid,kid,secret,iss,scopes);
      let tp=`
      <tableau-viz id="tableauViz" src="${wurl}" 
        token="${token}"
        height="400px"
        width="100%"
        toolbar="no" 
        hide-tabs="true">
          <viz-filter field="project" value="<?php echo $project->Name ?>"> </viz-filter>
      </tableau-viz>`;
      /*let tp=`
      <tableau-viz id="tableauViz" src="${wurl}" 
        token="${token}"
        height="800px"
        width="100%"
        toolbar="no" 
        hide-tabs="true">
      </tableau-viz>`;*/
        //document.getElementById('dash'+type).innerHTML =tp;
        $("#dashboardTitle").html(type+' Dashboard');
        $('.dashboardView').html(tp);
    }
    
    //$('.dashboardView').hide();
    //$('#dash'+type).show();
  }



    $(document).on('click','#showFullScreen',function(){
        var iframe = $('#firstembed').contents();
        iframe.find("<clickable_thing>").click(function(){
           alert("test");
        });
        document.getElementsByName("tableauViz").contentWindow.document.getElementById("fullscreen").click();
    });

</script>




<div class="container-fluid procu-accordion" style="margin-top: 20px;">
    <div class="row">
        <div class="col-md-12">  
            <div class="panel-group schdl acco-one-active" id="accordionprojindex">
                <div class="panel panel-default projects-tab acco-one tab tab-wrapper active">
                    <div class="panel-heading" id="selected-projctid">
                        <?php 
                            $uid = Yii::$app->user->Id; 
                            if($projuser = ProjuserSelection::find()->where(['userid' => $uid])->one()){
                                $model = Projects::findOne($projuser->projectid);
                        ?>
                              <h4 class="panel-title" id="projtitle">
                                <a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex">
                                <span class="icon-pie-chart"></span>Dashboard - <?php echo $model->Name ?></a>
                              </h4>
                        <?php } else { ?>
                              <h4 class="panel-title" id="projtitle">
                                <a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex">
                                <span class="icon-pie-chart"></span>Dashboard</a>
                              </h4>
                        <?php } ?>
                    </div>

                    <div id="collapseprojindex" class="tab-content cOrder-body panel-collapse collapse in">
                        <div class="panel-body">
                          


                          <div style="clear:both;padding-left:20px;padding-right:20px;padding-top:20px">
                            <div class="row" style="width:100%;">
                                                  
                              <ul class="nav nav-tabs text-center topsbars">
                                <li class="frstcl" data-toggle="pill" class="resourceTypeTab" id="resourceSchedule" data-type="Schedule"><a href="javascript:;'" ><span class="icon-schedule"></span> Schedule</a></li> 
                                
                                <li class="frstcl" data-toggle="pill" class="resourceTypeTab" id="resourcePlant" data-type="Plant"><a href="javascript:;"><span class="icon-settings2"></span> Plant & Equipment</a></li> 
                                
                                <li data-toggle="pill" class="resourceTypeTab" id="resourcesCost" data-type="Cost"><a href="javascript:;'"><span class="icon-dollar1"></span> Cost</a></li> 
                                                                
                                <li data-toggle="pill" class="resourceTypeTab"  id="resourcesLabour" data-type="Labour"><a href="javascript:;'"><span class="icon-tools"></span> Labour</a></li>

                                <li data-toggle="pill" class="resourceTypeTab"  id="resourcesSubcontractor" data-type="Subcontractor"><a href="javascript:;'"><span class="icon-users"></span> Sub Contractor</a></li>

                                <li data-toggle="pill" class="resourceTypeTab"  id="resourcesMaterials" data-type="Materials"><a href="javascript:;'"><span class="icon-truck"></span> Materials</a></li>

                                <!-- <li><a data-toggle="pill" href="#resourcesEstimate" class="resourceTypeTab" id="resourcesEstimate" data-type="Estimate"><span class="icon-calculator4"></span> Estimate</a></li> -->

                                <li data-toggle="pill" class="resourceTypeTab" id="resourcesSupport" data-type="Support"><a href="javascript:;'"><span class="icon-question1"></span> Support</a></li>
                              </ul>

                            </div>
                          </div>


                          <div class="row" style="padding-bottom:30px">
                            <div class="col-md-1"></div>  
                            <div class="col-md-10" style="border: 20px solid #ecedef;border-radius: 10px;padding: 0;">  
                                <div id="dashboardTitle" style="background-color: #ecedef;padding: 0px 0 15px 0;text-align: center;font-size: 15px;font-weight: bold;">
                                    Schedule Dashboard
                                </div>
                                <div><button type="button" class="btn btn-primary" id="showFullScreen"><span class="icon-check"></span> Show Fullscreen</button></div>


                                <div style="padding: 20px;" class="dashboardView"></div>
                            </div>
                            <div class="col-md-1"></div>  
                          </div>



                        </div> 
                    </div>
                </div>
              </div>
        </div>
    </div>
</div>










<style>
    .tab-content{
        max-height:unset;
    }
    .nav-tabs > li a {
      font-weight: bold;
    }
    .modal-dialog{
      width: 96%;
    }
</style>


<script>
   $(document).on( "click", ".navbar-nav .icon-dashboard", function(){
      if($(".icon-dashboard").hasClass("active")) {x
        $('#project-title-head').html('Project Dashboard');
      }
      else{
        $('#project-title-head').html('Projects');
      }
  });

  $(document).on( "click", ".resourceTypeTab", function(){
      go($(this).attr("data-type"));
  });

  $( document ).ready(function() {
      go('Schedule');
      $( "#resourceSchedule" ).trigger( "click" );

  });
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
</script>

<style>
.nav-tabs > li + li {
    margin-left: 50px;
}
</style>