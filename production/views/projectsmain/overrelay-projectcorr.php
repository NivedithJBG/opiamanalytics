<?php ?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_projectcorr.js" type="text/javascript"></script>

<div class="menu9-popup-cntnr">
    <div class="menu9-cntnt-wrpr">
        <div style="text-align:center; padding:15px 0 5px;">
            <h4><span class="icon-mail"></span> Project Correspondence</h4>
        </div>
        <div class="container-fluid">
            <div class="row" style="padding:0 15px 10px;">
                <div class="col-md-3">
                    <input type="text" id="corr_search_subject" class="form-control" placeholder="Subject / Filename">
                </div>
                <div class="col-md-2">
                    <input type="text" id="corr_search_ref" class="form-control" placeholder="Project Name">
                </div>
                <div class="col-md-2">
                    <input type="date" id="corr_search_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="text" id="corr_search_keywords" class="form-control" placeholder="Keywords">
                </div>
                <div class="col-md-2">
                    <button id="corr_search_btn" class="btn btn-primary btn-block">
                        <span class="icon-search5"></span> Search
                    </button>
                </div>
            </div>
            <div id="corr_results" style="padding:0 15px;">
                <p class="text-muted">Enter a search term and click Search.</p>
            </div>
        </div>
    </div>
</div>

<!-- Correspondence viewer modal -->
<div class="modal fade" id="corrViewerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:90%; margin:40px auto;">
        <div class="modal-content" style="height:82vh;">
            <div class="modal-header">
                <h4 class="modal-title" id="corrViewerTitle">Document</h4>
                <button type="button" class="close" data-dismiss="modal" style="font-size:28px;">×</button>
            </div>
            <div class="modal-body" style="padding:0; height:calc(100% - 115px); overflow:hidden;">
                <iframe id="corrViewerFrame" src="" style="width:100%;height:100%;border:0;"></iframe>
            </div>
            <div class="modal-footer" style="padding:8px 15px;">
                <a id="corrShareWhatsapp" href="#" target="_blank" class="btn btn-success btn-sm">
                    <span class="icon-whatsapp"></span> WhatsApp
                </a>
                <a id="corrShareEmail" href="#" class="btn btn-default btn-sm">
                    <span class="icon-mail"></span> Email
                </a>
                <button id="corrPrintBtn" class="btn btn-default btn-sm">
                    <span class="icon-print"></span> Print
                </button>
                <a id="corrDownloadBtn" href="#" target="_blank" class="btn btn-primary btn-sm">
                    <span class="icon-download"></span> Download
                </a>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
