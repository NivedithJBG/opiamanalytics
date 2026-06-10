<?php
use app\models\TermsCondtns;
use app\models\TermsCondtnsContent;
?>

<div class="panel panel-default acco-four termscond-tab tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/termscondtn.js" type="text/javascript"></script>
    <!-- <input type="radio" id="rd4" name="rd"> -->
    <div class="panel-heading" >
        <h4 class="panel-title acc_trigger" id="termscond">
            <a data-toggle="collapse" data-parent="#accordionmaster" href="#collapsetermcondtns">
                <span class="image-icon acc_trigger"></span>Terms & Conditions
            </a>
        </h4>
    </div>

    <div id="collapsetermcondtns" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body">
              <div class="search-and-content-wrpr">
                  <div class="search-and-actions-wrpr row">
                      <div class="content-action-wrpr col-md-12 col-sm-9">
                          <a href="#" class="btn btn-primary addtermscndtn" id="addtermscndtn" title="Add Terms & conditions"><span class="icon-add"></span> Add</a>

                          <!--<a class="list-terms" id="listtermscndtn"></a>-->
                          
                      </div>
                  </div>

                  <div>
                    <div id="termscondtnsection">
                      <!-- list start here -->
                      <div class="resource-type-cntnt-wrpr terhead row">
							<div class="col-md-10 col-sm-10">
								<div class="row">
									
									<div class="col-md-2 col-sm-1">
										<div class="row">
											<div class="col-md-3">
												<label>#</label><br/>	
											</div>
											<div class="col-md-9">
											</div>
										</div>
										
										
									</div>
									<div class="col-md-8 col-sm-7">
										<label>Title</label><br/>
									</div>
									<!--<div class="col-md-3 col-sm-4 acc-sub-group">
										<label>Content</label><br/>
									</div>-->
									<div class="col-md-2 col-sm-4"></div>
								</div>
								
							</div>
							<div class="col-md-2 col-sm-2 icon-groups">
							</div>
						</div>
                      <?php 

                      	$models = TermsCondtns::find()->where(['status' => 0])->all();

                      	if(count($models)>0){

                      	foreach($models as $key => $model):

                      ?>
						<div class="resource-type-cntnt-wrpr row">
							<div class="col-md-10 col-sm-10">
								<div class="row">
									
									<div class="col-md-2 col-sm-1">
										<div class="row">
											<div class="col-md-3">
												<span><?= ($key+1) ?></span>	
											</div>
											<div class="col-md-9">
											</div>
										</div>
										
										
									</div>
									<div class="col-md-8 col-sm-7 type">
										<span><?= $model->title ?></span>
									</div>
									
									<!--<div class="col-md-3 col-sm-4 acc-sub-group">
										<?php 

											$allcontents=TermsCondtnsContent::find()->Where(['termsid'=> $model->id])->all();

											foreach($allcontents as $key1=> $allcontent):
												$slno = $key1+1;

										?>
										<span><?= $slno.') '.$allcontent->content ?></span><br>
										<?php endforeach; ?>
									</div>-->
									
									<div class="col-md-2 col-sm-4"></div>
								</div>
								
							</div>
							<div class="col-md-2 col-sm-2 icon-groups">
								<button type="button" class="edittermstypebutton" value="<?= $model->id ?>" id="edittermstypebutton<?= $model->id ?>" title="Edit terms & conditions">
                                <a href="#" class="icon-pencil"></a></button>
                                <button type="button" class="deletetermstypebutton" value="<?= $model->id ?>" id="deletetermstypebutton<?= $model->id ?>" title="Delete terms & conditions">
                                <a href="#" class="icon-trash1"></a></button>
							</div>
						</div>
						<?php endforeach; } ?>
						<!--<div class="resource-type-cntnt-wrpr row">
							<div class="col-md-10 col-sm-10">
								<div class="row">
									
									<div class="col-md-2 col-sm-1">
										<div class="row">
											<div class="col-md-3">
												<span></span>	
											</div>
											<div class="col-md-9">
											</div>
										</div>
										
										
									</div>
									<div class="col-md-5 col-sm-3 type">
										<label>Title</label><br/>
										<span></span>
									</div>
									<div class="col-md-3 col-sm-4 acc-sub-group">
										<label>Content</label><br/>
										<span></span>
									</div>
									<div class="col-md-2 col-sm-4"></div>
								</div>
								
							</div>
							<div class="col-md-2 col-sm-2 icon-groups">
							</div>
						</div>-->

                    </div>

                    <div id="termscondtnaddsection">
                      
                      
                    </div>

                    <div id="termscondtneditsection">
                      
                      
                    </div>
                    
                  </div>  
              </div>
        
        </div>
    </div>
</div>
                