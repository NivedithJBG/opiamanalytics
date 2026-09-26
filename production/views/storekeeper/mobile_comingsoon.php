<?php
$this->title = $pageTitle;
?>
<style>
*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
body{background:#f4f5f7}
#mob-app{display:flex;flex-direction:column;height:100vh;background:#f4f5f7}
#mob-topbar{
  background:#334155;color:#f1f5f9;
  padding:calc(env(safe-area-inset-top,0px) + 14px) 16px 12px;
  display:flex;align-items:center;gap:12px;flex-shrink:0;
}
#mob-topbar .mob-back{background:none;border:none;color:#f1f5f9;font-size:22px;line-height:1;padding:4px}
#mob-topbar .mob-title{font-size:17px;font-weight:700}
#mob-empty{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:#64748b;padding:30px;text-align:center}
#mob-empty .mob-icon{font-size:44px}
</style>
<div id="mob-app">
  <div id="mob-topbar">
    <button class="mob-back" onclick="history.back()">&#8592;</button>
    <div class="mob-title"><?= htmlspecialchars($pageTitle) ?></div>
  </div>
  <div id="mob-empty">
    <div class="mob-icon">&#128736;</div>
    <div>This section is coming soon.</div>
  </div>
</div>
