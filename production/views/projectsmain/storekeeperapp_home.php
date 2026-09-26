<?php
$this->title = 'Store Office';
?>
<style>
:root{
  --bg:#0f172a;--card:#ffffff;--border:#e2e8f0;
  --text:#0f172a;--muted:#64748b;
  --accent:#2563eb;--slate:#1e293b;--slate-text:#f8fafc;
}
*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
body{background:var(--bg)}

#mob-app{display:flex;flex-direction:column;height:100vh;overflow:hidden;background:var(--bg)}

#mob-topbar{
  padding:calc(env(safe-area-inset-top,0px) + 22px) 20px 26px;
  flex-shrink:0;
}
#mob-topbar .mob-eyebrow{color:rgba(248,250,252,.55);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px}
#mob-topbar .mob-title{color:var(--slate-text);font-size:24px;font-weight:700;letter-spacing:-.3px}
#mob-topbar .mob-proj{font-size:13px;color:rgba(248,250,252,.7);margin-top:6px}

#mob-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;background:var(--card);border-radius:24px 24px 0 0;padding:22px 18px 40px}

.mob-action{
  display:flex;align-items:center;gap:14px;
  padding:18px 16px;border-radius:16px;margin-bottom:12px;
  text-decoration:none;border:1px solid var(--border);
}
.mob-action:active{opacity:.7}
.mob-action-icon{
  width:46px;height:46px;border-radius:12px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;font-size:22px;
}
.mob-action-body{flex:1;min-width:0}
.mob-action-label{font-size:15px;font-weight:700;color:var(--text)}
.mob-action-sub{font-size:12px;color:var(--muted);margin-top:2px}
.mob-action-arrow{color:var(--muted);font-size:18px}

.mob-a1 .mob-action-icon{background:#eef2ff;color:#4338ca}
.mob-a2 .mob-action-icon{background:#ecfdf5;color:#047857}

#mob-bottom-nav{
  position:fixed;bottom:0;left:0;right:0;
  background:var(--slate);
  padding:8px 0 calc(env(safe-area-inset-bottom,0px) + 6px);
  display:flex;justify-content:space-around;z-index:100;flex-shrink:0;
}
.mob-nav-item{display:flex;flex-direction:column;align-items:center;gap:2px;font-size:9px;font-weight:600;color:rgba(248,250,252,.55);text-transform:uppercase;letter-spacing:.4px;padding:0 12px;text-decoration:none}
.mob-nav-item.active{color:var(--slate-text)}
.mob-nav-icon{font-size:18px;line-height:1}
</style>

<div id="mob-app">

  <div id="mob-topbar">
    <div class="mob-eyebrow">Store Office</div>
    <div class="mob-title">Hi there</div>
    <div class="mob-proj"><?= htmlspecialchars($projectName) ?></div>
  </div>

  <div id="mob-content">

    <a class="mob-action mob-a1" href="<?= Yii::$app->urlManager->createUrl('storekeeper/mobileindent') ?>">
      <div class="mob-action-icon">&#128221;</div>
      <div class="mob-action-body">
        <div class="mob-action-label">Indents</div>
        <div class="mob-action-sub">Request materials for site</div>
      </div>
      <div class="mob-action-arrow">&#8250;</div>
    </a>

    <a class="mob-action mob-a2" href="<?= Yii::$app->urlManager->createUrl('storekeeper/mobilegrn') ?>">
      <div class="mob-action-icon">&#128230;</div>
      <div class="mob-action-body">
        <div class="mob-action-label">Goods Received Notes</div>
        <div class="mob-action-sub">Confirm materials received</div>
      </div>
      <div class="mob-action-arrow">&#8250;</div>
    </a>

  </div>

</div>

<div id="mob-bottom-nav">
  <a class="mob-nav-item active" href="<?= Yii::$app->urlManager->createUrl('projectsmain/storekeeperapp') ?>">
    <span class="mob-nav-icon">&#127968;</span>Home
  </a>
  <a class="mob-nav-item" href="<?= Yii::$app->urlManager->createUrl('site/logout') ?>" data-method="post">
    <span class="mob-nav-icon">&#128275;</span>Logout
  </a>
</div>
