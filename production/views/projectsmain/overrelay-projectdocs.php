<?php
use app\models\Projects;
$allProjects = Projects::find()->orderBy(['Name' => SORT_ASC])->all();
?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_projectdocs.js?v=<?php echo time(); ?>" type="text/javascript"></script>

<div class="menu8-popup-cntnr">
    <div class="menu8-cntnt-wrpr">
        <div style="background:#ffffff;padding:12px 15px 14px;">
            <div style="text-align:center; padding:4px 0 10px;">
                <h4 style="margin:0;"><span class="icon-document"></span> Project Documents</h4>
            </div>
            <div class="container-fluid" style="padding:0;">
                <div class="row" style="margin:0;">
                    <div class="col-md-3">
                        <input type="text" id="docs_search_subject" class="form-control" placeholder="Subject / Filename">
                    </div>
                    <div class="col-md-2">
                        <select id="docs_search_ref" class="form-control">
                            <option value="">All Projects</option>
                            <?php foreach($allProjects as $proj): ?>
                            <option value="<?php echo htmlspecialchars($proj->Name); ?>"><?php echo htmlspecialchars($proj->Name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="docs_search_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="docs_search_keywords" class="form-control" placeholder="Keywords">
                    </div>
                    <div class="col-md-2">
                        <button id="docs_search_btn" class="btn btn-primary btn-block">
                            <span class="icon-search5"></span> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div style="background:#ffffff; padding:15px;">
            <div id="docs_results">
                <p class="text-muted">Enter a search term and click Search.</p>
            </div>
        </div>
    </div>
</div>

<!-- Document viewer overlay -->
<div id="docViewerOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:99999;">
    <div style="position:absolute;top:30px;left:5%;right:5%;bottom:30px;background:#fff;border-radius:6px;display:flex;flex-direction:column;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 15px;background:#4a4e5a;border-radius:6px 6px 0 0;">
            <span id="docViewerTitle" style="color:#fff;font-weight:700;font-size:15px;"></span>
            <div style="display:flex;gap:8px;align-items:center;">
                <a id="docDownloadBtn" href="#" target="_blank" style="background:#337ab7;color:#fff;border:none;border-radius:50px;padding:5px 14px;font-size:13px;text-decoration:none;"><span class="icon-download"></span> Download</a>
                <button id="docViewerClose" style="background:#d9534f;color:#fff;border:none;border-radius:50px;padding:5px 14px;font-size:13px;cursor:pointer;">&#10005; Close</button>
            </div>
        </div>
        <div style="flex:1;overflow:hidden;">
            <iframe id="docViewerFrame" src="" style="width:100%;height:100%;border:0;"></iframe>
        </div>
    </div>
</div>
