<?php 
use app\models\ProjuserSelection;
use app\models\Projects;

?>

<div class="panel panel-default acco-one tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/projects.js" type="text/javascript"></script>

    <div class="panel-heading" id="selectedprocu-projctid">

    <?php 
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $model = Projects::findOne($projuser->projectid);
    ?>
    <h4 class="panel-title" id="procuprojectlist">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseprojs">
        <span class="icon-note1"></span>Projects - <?php echo $model->Name ?></a>
    </h4>

    <?php } else { ?>
        <h4 class="panel-title" id="procuprojectlist">
                <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseprojs">
                <span class="icon-note1"></span>Projects</a>
            </h4>
        <?php } ?>

    </div>

    <div id="collapseprojs" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body">

            <div class="search-and-content-wrpr" id="projectlistsection">
                <div id="procuprojlisting" class="content-wrpr project-fav-cards-cntnr">
                    
                </div>
            </div>
        </div>

    </div>
</div>