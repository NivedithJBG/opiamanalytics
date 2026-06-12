
<?php 
use amnah\yii2\user\models\User;
use app\models\EstimateWorkType;
use app\models\EstimateActivityType;


?>

				<!--<div class="panel panel-default project-activities-tab  acco-four tab tab-wrapper">-->
				<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_activity.js?v=4" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {

    var actPanelUrl   = '<?= \yii\helpers\Url::to(["activity1/addiowactivities"]) ?>';
    var actAddUrl     = '<?= \yii\helpers\Url::to(["activity1/addiowactivity"]) ?>';
    var actListUrl    = '<?= \yii\helpers\Url::to(["activity1/listactivities"]) ?>';
    var placeholderHtml = '<div style="text-align:center;padding:50px 20px;color:#c0c0c0;"><span style="font-size:32px;display:block;margin-bottom:8px;">&#9776;</span>All activities are listed &mdash; use the left panel to filter by Project Type and Activity Type</div>';

    function refreshTopList() {
        $.ajax({
            type: 'POST',
            url: actListUrl,
            dataType: 'json',
            data: { Workgroup_Id: $('#IOWWorkgroupId2').val(), proid: $('#selectedProjectId').val(), worktypeid: $('#selectedWorktypeId').val() },
            success: function(data) {
                if (data.error === 'No') {
                    $('#projects-Activity-Lists').html(data.result);
                }
            }
        });
    }

    // Replace the _activity.js addiowactivitydrop handler with one using absolute URLs
    $(document).off('click', '.addiowactivitydrop').on('click', '.addiowactivitydrop', function() {
        var actid        = $(this).attr('data-v');
        // Each library activity carries its own type; fall back to the filter selection
        var activitytypeid = $('#iowactivitytypeid' + actid).val() || $('#wbsactivitytypelist').val();
        if (!activitytypeid || activitytypeid === '0') { alert('This activity has no Activity Type set in the library.'); return; }
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: actAddUrl,
            dataType: 'json',
            data: {
                actid:          actid,
                wbsid:          $('#IOWWorkgroupId2').val(),
                worktypeid:     $('#iowworktypeid'        + actid).val(),
                activitytypeid: activitytypeid,
                activityunit:   $('#masteriowactivityunit' + actid).val(),
                projectid:      $('#selectedProjectId').val(),
                activityname:   $('#iowactivitynamespan'  + actid).text(),
                amount:         $('#iowactivityamount'    + actid).val()
            },
            success: function(data) {
                if (data.error === 'No') {
                    $btn.hide();
                    $btn.next('span').html('<span style="color:green;font-size:12px;">&#10003; Added</span>').show();
                    refreshTopList();
                } else {
                    alert(data.errortext);
                    $btn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                alert('Request failed (' + xhr.status + ')');
                $btn.prop('disabled', false);
            }
        });
    });

    function loadActPanel() {
        var typeId   = $('#wbsactivitytypelist').val();
        var workType = $('#wbsworktypelist').val();
        var $r = $('#new-act-results');
        $r.html('<div style="padding:20px;text-align:center;color:#aaa;">Loading&#8230;</div>');
        $.ajax({
            type: 'POST',
            url: actPanelUrl,
            dataType: 'json',
            data: { Workgroup_Id: $('#selectedWorkgrouId').val(), activitytypeid: typeId, worktypeid: workType, type: 'nosearch' },
            success: function(resp) {
                $r.html(resp.error === 'No' ? resp.result : '<p style="padding:15px;color:red;">' + resp.errortext + '</p>');
            },
            error: function(xhr) {
                $r.html('<p style="padding:15px;color:red;">Request failed (HTTP ' + xhr.status + ')</p>');
            }
        });
    }

    // Filter buttons toggle: click again to deselect. No selection = all activities.
    $(document).on('click', '.new-act-type-btn', function() {
        var wasActive = $(this).hasClass('act-filter-active');
        $('.new-act-type-btn').removeClass('act-filter-active');
        $('#wbsactivitytypelist').val('');
        if (!wasActive) {
            $(this).addClass('act-filter-active');
            $('#wbsactivitytypelist').val($(this).data('typeid'));
        }
        loadActPanel();
    });

    $(document).on('click', '.new-proj-type-btn', function() {
        var wasActive = $(this).hasClass('act-filter-active');
        $('.new-proj-type-btn').removeClass('act-filter-active');
        $('#wbsworktypelist').val('');
        if (!wasActive) {
            $(this).addClass('act-filter-active');
            $('#wbsworktypelist').val($(this).data('workid'));
        }
        loadActPanel();
    });

    $(document).on('click', '.add-project-activities-btn', function() {
        $('#new-act-panel').slideDown(200);
        loadActPanel();
    });

    $(document).on('click', '.close-activity-list-btn', function() {
        $('#new-act-panel').slideUp(200);
        $('#new-act-results').html(placeholderHtml);
        $('.new-act-type-btn, .new-proj-type-btn').removeClass('act-filter-active');
        $('#wbsactivitytypelist').val('');
        $('#wbsworktypelist').val('');
    });

});
</script>
				  <!--<input type="radio" id="rd5" class="prjctactivities" name="rd">
					<div class="panel-heading" >
					  <h4 class="panel-title">
						<a  href="#">
						<span class="icon-directions_run"></span>Project Activities</a>
					  </h4>
					</div>
					<div  class="tab-content cOrder-body panel-collapse ">
					  <div class="panel-body">
						<div class="search-and-content-wrpr" >
							<div class="content-wrpr">-->
								<div id="project-activities-Body" style="display: none;">
									<!-- project-activities list -->
									<div class="project-activities-list-wrpr data-content-list">
										<div class="row">
											<div class="col-md-12 type project-boq proj" >
												<label><span>Project Activity - <b id="workgroupnamedisplay" style="color: black;"></b></span></label>
												<a href="#" class="btn btn-primary text-button back-butn-new close-prjctactvty" title="back">Back</a>
												<!--<a href="javascript:void(0);" style="display: none;"  class="btn btn-primary add-project-activities-btn showsearch"><span class="icon-add"></span>Add Activity</a>-->
												<a href="javascript:void(0);"   class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target=".added-activity-list-wrpr" style="display: none;"></a>
												<a href="javascript:void(0);" class="btn btn-primary" id="listiow" style="display: none;"></a>
												<a href="javascript:void(0);" class="btn btn-primary addForm" id="addBOQMap" style="display: none;"></a>
												<input type="hidden" id="IOWWorkgroupId2">
												<!-- <input type="hidden" id="IOWorkgroupId" name="IOWorkgroupId"> -->
												<input type="hidden" id="ProId" name="ProId">
												<input type="hidden" id="IOWorkgroupsId" name="IOWorkgroupId">
											</div>
											<input type="hidden" id="selectedWorktypeId">
											<input type="hidden" id="selectedWorkgrouId">	
											<div class="col-md-12 added-activity-list-wrpr collapse in" >
												<div class="preloader" id="Promain-preloader-projectactivity" style="display: none;" align="center">
													<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
												</div>
<div style="text-align:center; margin-bottom:18px;">
    <span style="display:inline-block; background:#f5f5f5; border:1px solid #ddd; border-bottom:none; border-radius:6px 6px 0 0; padding:7px 80px; font-size:13px; font-weight:600; letter-spacing:0.5px; color:#333;">Activities</span>
</div>
<div id="projects-Activity-Lists"></div>
		
											</div><hr>
											<div class="col-md-12 type project-boq" style="margin-top:20px;">
													<div class="col-md-2">
														<a href="javascript:void(0);"  class="btn btn-primary add-project-activities-btn showsearch"><span class="icon-add"></span>Add Activity</a>
														<a href="#"  class="btn btn-primary close-activity-list-btn" style="display:none;">Close Activity</a>
													</div>
													<a href="javascript:void(0);" style="display: none;" class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target=".added-activity-list-wrpr"></a>
													
													<div class="col-md-10" id="search-show" style="display:none;margin-left: 104px;">
														<input type="text" class="wbs-act-search" id="searchenggactname" placeholder="Search Activities">
														<button class="btn btn-primary wbs-act-search-btn search-button" id="enggactsearchNow" 0 type="button"><span class="icon-search5"></span></button>
														<input type="hidden" id="enggactsearch">
													</div>
											</div>
											<!-- hidden inputs for compatibility -->
											<input type="hidden" id="wbsworktypelist" value="">
											<input type="hidden" id="wbsactivitytypelist" value="">

											<!-- Activity allocation panel -->
											<div class="col-md-12" id="new-act-panel" style="display:none; margin-top:16px; padding-left:0; padding-right:0;">
												<div class="new-act-panel-wrap">

													<!-- Left sidebar: filters -->
													<div class="new-act-sidebar">

														<?php
														$workTypes = EstimateWorkType::find()->where(['estworktype_status' => 0])->orderBy(['sortorder' => SORT_ASC])->all();
														if(!empty($workTypes)):
														?>
														<div class="act-panel-section-lbl">Project Type</div>
														<?php foreach($workTypes as $wt): ?>
														<button type="button" class="act-filter-btn new-proj-type-btn" data-workid="<?= $wt->estworktype_id ?>">
															<?= $wt->estworktype_name ?>
														</button>
														<?php endforeach; ?>
														<div class="act-sidebar-divider"></div>
														<?php endif; ?>

														<div class="act-panel-section-lbl">Activity Type</div>
														<?php
														$actTypes = EstimateActivityType::find()->where(['activitytype_status' => 0])->orderBy(['sortorder' => SORT_ASC])->all();
														foreach($actTypes as $at):
														?>
														<button type="button" class="act-filter-btn new-act-type-btn" data-typeid="<?= $at->activitytype_id ?>">
															<?= $at->activitytype_name ?>
														</button>
														<?php endforeach; ?>

													</div><!-- /sidebar -->

													<!-- Right: results -->
													<div class="new-act-results-area">
														<div id="new-act-results">
															<div style="text-align:center;padding:50px 20px;color:#c0c0c0;">
																<span style="font-size:32px;display:block;margin-bottom:8px;">&#9776;</span>
																All activities are listed &mdash; use the left panel to filter by Project Type and Activity Type
															</div>
														</div>
													</div><!-- /results -->

												</div>
											</div>
										</div>
									</div>
									<!-- project-activities list end -->
									<!-- add Map BOQ -->
									<div class="add-form add-wbs-form" id="boqactsearch-area">	
										<div class="row">
											<div class="col-md-12 text-center">
												<h3><span>Search BOQ Items</span></h3>
											</div>
											<div class="content-search-wrpr-new col-md-12 col-sm-12">
												<input class="form-control" id="searchboqitem" placeholder="Search BOQ slno or item"> 
												
												<button id="boqactsearch" class="btn btn-primary" type="button" value=""><span class="icon-search5"></span>Search</button>
												<span>&nbsp;</span><span>&nbsp;</span>
												<button id="boqactsearchCancel" class="btn btn-danger cancel" type="button"><span class="icon-check"></span>Cancel</button>
											</div>
											<div class="col-md-12">
												<span>&nbsp;</span>
											</div>
											<div class="col-md-12 custom-padding-activity-new">
												<div id="boqsearchlisting"></div>
											</div>
										</div>	
									</div>
									<!-- add Map BOQ end here-->
								</div>
								<!--<div id="project-activities-Body-NOData">
								<div class="row"><div class="col-md-12"><div class="text-center">Please Select IOW Activities</div></div></div>
								</div>
							</div>
						</div> 
					</div>
					</div>
			  </div>-->