<?php
use app\models\Projects;
$allProjects = Projects::find()->orderBy(['Name' => SORT_ASC])->all();
?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_projectdocs.js" type="text/javascript"></script>

<div class="menu8-popup-cntnr">
    <div class="menu8-cntnt-wrpr">
        <div style="text-align:center; padding:15px 0 5px;">
            <h4><span class="icon-document"></span> Project Documents</h4>
        </div>
        <div class="container-fluid">
            <div class="row" style="padding:0 15px 10px;">
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
            <div id="docs_results" style="padding:0 15px;">
                <p class="text-muted">Enter a search term and click Search.</p>
            </div>
        </div>
    </div>
</div>

<!-- Document viewer modal -->
<div class="modal fade" id="docViewerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:90%; margin:40px auto;">
        <div class="modal-content" style="height:82vh;">
            <div class="modal-header">
                <h4 class="modal-title" id="docViewerTitle">Document</h4>
                <button type="button" class="close" data-dismiss="modal" style="font-size:28px;">×</button>
            </div>
            <div class="modal-body" style="padding:0; height:calc(100% - 115px); overflow:hidden;">
                <iframe id="docViewerFrame" src="" style="width:100%;height:100%;border:0;"></iframe>
            </div>
            <div class="modal-footer" style="padding:8px 15px;">
                <a id="docShareWhatsapp" href="#" target="_blank" class="btn btn-success btn-sm">
                    <span class="icon-whatsapp"></span> WhatsApp
                </a>
                <a id="docShareEmail" href="#" class="btn btn-default btn-sm">
                    <span class="icon-mail"></span> Email
                </a>
                <button id="docPrintBtn" class="btn btn-default btn-sm">
                    <span class="icon-print"></span> Print
                </button>
                <a id="docDownloadBtn" href="#" target="_blank" class="btn btn-primary btn-sm">
                    <span class="icon-download"></span> Download
                </a>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
