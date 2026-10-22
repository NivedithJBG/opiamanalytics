<?php
// Page a phone notification opens (ProjectsmainController::actionNotifview):
// the full message / alert / action plan as one card sized to its text.
use yii\helpers\Html;

$h = function ($s) { return Html::encode((string)$s); };
$url = Yii::$app->urlManager;
$actUrl = $url->createUrl(['/projectsmain/notifviewact']);
$homeUrl = $url->createUrl(['/projectsmain/index', 'notif' => 1]);
$today = (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d');

$label = ['msg' => 'Message', 'alert' => 'Alert', 'ap' => 'Action plan'][$kind] ?? 'Notification';
$title = ''; $meta = []; $done = false; $overdue = false;
if ($item) {
    if ($kind === 'msg') { $title = $item['title'] ?: $item['sender']; if ($item['sender']) $meta[] = 'From ' . $item['sender']; $done = (int)$item['archived'] === 1; }
    if ($kind === 'alert') { $title = $item['title']; if ($item['project']) $meta[] = $item['project']; $done = (int)$item['archived'] === 1; }
    if ($kind === 'ap') {
        $title = 'Action plan';
        if ($item['project']) $meta[] = $item['project'];
        if ($item['responsible']) $meta[] = 'Responsible: ' . $item['responsible'];
        if ($item['target_date']) $meta[] = 'Due ' . date('j M Y', strtotime($item['target_date']));
        $done = (int)$item['status'] === 1;
        $overdue = !$done && $item['target_date'] && substr($item['target_date'], 0, 10) < $today;
    }
}
$dot = $done ? '#9aa3b2' : ($overdue ? '#d84a30' : '#2ba899');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#2f3b4c">
<title><?= $h($label) ?> · Opiam</title>
<style>
  :root { --bg:#eef1f5; --card:#fff; --ink:#1f2733; --muted:#6b7686; --line:#dde2e9; --hdr:#2f3b4c; }
  @media (prefers-color-scheme: dark) { :root { --bg:#141a22; --card:#1f2731; --ink:#e7ebf0; --muted:#9aa5b4; --line:#33404f; --hdr:#2f3b4c; } }
  * { box-sizing:border-box; }
  body { margin:0; background:var(--bg); color:var(--ink); font:16px/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
         padding:calc(16px + env(safe-area-inset-top)) 16px calc(16px + env(safe-area-inset-bottom)); }
  .card { max-width:600px; margin:0 auto; background:var(--card); border-radius:14px; overflow:hidden; box-shadow:0 4px 18px rgba(0,0,0,.12); border-left:6px solid <?= $dot ?>; }
  .hdr { background:var(--hdr); color:#fff; padding:10px 14px; display:flex; align-items:center; gap:10px; font-size:13px; }
  .hdr img { width:22px; height:22px; border-radius:5px; }
  .hdr .k { font-weight:700; letter-spacing:.3px; }
  .hdr .st { margin-left:auto; display:flex; align-items:center; gap:6px; }
  .hdr .st i { width:9px; height:9px; border-radius:50%; background:<?= $dot ?>; display:inline-block; }
  .body { padding:16px 16px 6px; }
  h1 { font-size:19px; margin:0 0 4px; line-height:1.3; }
  .meta { color:var(--muted); font-size:13px; margin-bottom:12px; }
  .text { white-space:pre-wrap; word-wrap:break-word; font-size:16px; }
  .btns { display:flex; flex-wrap:wrap; gap:8px; padding:14px 16px 16px; border-top:1px solid var(--line); margin-top:14px; }
  button, a.btn { flex:1 1 auto; min-width:90px; border:1px solid var(--line); background:transparent; color:var(--ink); border-radius:9px;
                  padding:10px 12px; font-size:15px; text-align:center; text-decoration:none; cursor:pointer; }
  .pri { background:#3d6a8f; border-color:#3d6a8f; color:#fff; }
  .del { color:#d84a30; }
  .msg { text-align:center; color:var(--muted); font-size:13px; min-height:18px; margin-top:10px; }
</style>
</head>
<body>
<div class="card">
  <div class="hdr"><img src="/icons/opiam-192.png" alt=""><span class="k"><?= $h($label) ?></span>
    <span class="st"><i></i><span id="st"><?= $done ? ($kind === 'ap' ? 'Done' : 'Archived') : ($overdue ? 'Overdue' : 'Pending') ?></span></span></div>
<?php if (!$item): ?>
  <div class="body"><h1>Not found</h1><div class="text">This item has been deleted or is not addressed to you.</div></div>
  <div class="btns"><a class="btn pri" href="<?= $h($homeUrl) ?>">Open Opiam</a></div>
<?php else: ?>
  <div class="body">
    <h1><?= $h($title) ?></h1>
    <?php if ($meta): ?><div class="meta"><?= $h(implode(' · ', $meta)) ?></div><?php endif; ?>
    <div class="text"><?= $h($kind === 'msg' ? strip_tags((string)$item['text']) : $item['text']) ?></div>
  </div>
  <div class="btns" id="btns">
<?php if ($kind === 'ap'): ?>
    <button type="button" data-do="<?= $done ? 'open' : 'done' ?>"><?= $done ? '&#8634; Reopen' : '&#10003; Done' ?></button>
<?php else: ?>
    <button type="button" data-do="<?= $done ? 'restore' : 'archive' ?>"><?= $done ? '&#8634; Restore' : 'Archive' ?></button>
    <button type="button" class="del" data-do="delete">Delete</button>
<?php endif; ?>
    <a class="btn pri" href="<?= $h($homeUrl) ?>">Open Opiam</a>
  </div>
<?php endif; ?>
</div>
<div class="msg" id="msg"></div>
<script>
(function(){
  var btns = document.getElementById('btns'); if (!btns) return;
  var kind = <?= json_encode($kind) ?>, id = <?= (int)($item['id'] ?? 0) ?>;
  var labels = { archive:['restore','&#8634; Restore','Archived'], restore:['archive','Archive','Pending'], done:['open','&#8634; Reopen','Done'], open:['done','&#10003; Done','Pending'] };
  btns.addEventListener('click', function(e){
    var b = e.target.closest('button'); if (!b) return;
    var act = b.getAttribute('data-do');
    if (act === 'delete' && !confirm('Delete this ' + (kind === 'msg' ? 'message' : 'alert') + '?')) return;
    b.disabled = true;
    var fd = new FormData(); fd.append('k', kind); fd.append('id', id); fd.append('do', act);
    fetch(<?= json_encode($actUrl) ?>, { method:'POST', body:fd, credentials:'same-origin' }).then(function(r){ return r.json(); }).then(function(d){
      b.disabled = false;
      if (d.error !== 'No') { document.getElementById('msg').textContent = d.errortext || 'Could not save.'; return; }
      if (act === 'delete') { document.querySelector('.body').innerHTML = '<h1>Deleted</h1>'; btns.querySelectorAll('button').forEach(function(x){ x.remove(); }); return; }
      var l = labels[act]; b.setAttribute('data-do', l[0]); b.innerHTML = l[1]; document.getElementById('st').textContent = l[2];
      var c = l[2] === 'Pending' ? '#2ba899' : '#9aa3b2';
      document.querySelector('.card').style.borderLeftColor = c; document.querySelector('.hdr .st i').style.background = c;
    }).catch(function(){ b.disabled = false; document.getElementById('msg').textContent = 'No connection. Try again.'; });
  });
})();
</script>
</body>
</html>
