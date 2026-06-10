<?php 
use app\models\ProjuserSelection;
use app\models\Projects;

?>
		    <div class="panel panel-default acco-one projects-masters-tab tab">
            <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/rproject.js" type="text/javascript"></script>
				<!-- <input type="radio" class="rprojectlist" id="rd3" name="rd"> -->
				
					<div class="panel-heading" id="selected-projctid">
						<?php 
							$uid = Yii::$app->user->Id; 
							$projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
							if($projuser){
								$model = Projects::findOne($projuser->projectid);
						?>
						<h4 class="panel-title" id="rprojectlist">
							<a data-toggle="collapse" data-parent="#accordionoper" href="#collapseproj">
							<span class="icon-note1"></span>Projects - <?php echo $model->Name ?></a>
						</h4>
						<?php } else { ?>
							<h4 class="panel-title" id="rprojectlist">
									<a data-toggle="collapse" data-parent="#accordionoper" href="#collapseproj">
									<span class="icon-note1"></span>Projects</a>
								</h4>
							<?php } ?>
					</div>
				
				
				<div id="collapseproj" class="tab-content cOrder-body panel-collapse collapse">
                <div class="panel-body">
                    <div class="search-and-content-wrpr" id="projectlistsection"> 
                        <div id="operationprojlisting" class="content-wrpr project-fav-cards-cntnr">
                                



                        </div>
                    </div>
                    </div>
				</div>
			</div>
