<?php
// Page the "all notes" phone notification opens (ProjectsmainController::actionNotesview):
// every open note of the project as its own panel, each with Done / Snooze.
// $notes rows carry their own signed token ('t') for notifviewact.
use yii\helpers\Html;

$h = function ($s) { return Html::encode((string)$s); };
$url = Yii::$app->urlManager;
$actUrl = $url->createUrl(['/projectsmain/notifviewact']);
$homeUrl = $url->createUrl(['/projectsmain/index', 'notif' => 1]);
$label = $kind === 'en' ? 'Estimate notes' : 'Gantt notes';
$ist = new DateTimeZone('Asia/Kolkata');
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
  .top { max-width:600px; margin:0 auto 12px; background:var(--hdr); color:#fff; border-radius:14px; padding:12px 14px; display:flex; align-items:center; gap:10px; }
  .top img { width:24px; height:24px; border-radius:5px; }
  .top b { font-size:15px; } .top span { font-size:13px; opacity:.8; }
  .top .n { margin-left:auto; font-size:13px; font-weight:700; }
  .card { max-width:600px; margin:0 auto 12px; background:var(--card); border-radius:14px; overflow:hidden; box-shadow:0 4px 18px rgba(0,0,0,.12); border-left:6px solid #2ba899; }
  .card.snz { border-left-color:#e0a100; } .card.done { border-left-color:#9aa3b2; opacity:.65; }
  .body { padding:14px 16px 4px; }
  .meta { color:var(--muted); font-size:12px; margin-bottom:6px; display:flex; justify-content:space-between; gap:8px; }
  .text { white-space:pre-wrap; word-wrap:break-word; font-size:16px; }
  .btns { display:flex; flex-wrap:wrap; gap:8px; padding:12px 16px 14px; border-top:1px solid var(--line); margin-top:12px; }
  .sz { display:none; flex-basis:100%; gap:8px; flex-wrap:wrap; }
  button, a.btn { flex:1 1 auto; min-width:90px; border:1px solid var(--line); background:transparent; color:var(--ink); border-radius:9px;
                  padding:9px 12px; font-size:15px; text-align:center; text-decoration:none; cursor:pointer; }
  .pri { background:#3d6a8f; border-color:#3d6a8f; color:#fff; }
  .pics { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
  .pics img { width:84px; height:84px; object-fit:cover; border-radius:8px; border:1px solid var(--line); display:block; }
  .files { display:flex; flex-direction:column; gap:6px; margin-top:10px; }
  .files a { display:flex; align-items:center; gap:8px; border:1px solid var(--line); border-radius:8px; padding:8px 10px; color:var(--ink); text-decoration:none; font-size:14px; word-break:break-all; }
  .files .fx { flex:none; background:#3d6a8f; color:#fff; font-size:10px; font-weight:700; border-radius:4px; padding:2px 5px; }
  .files small { margin-left:auto; color:var(--muted); flex:none; padding-left:6px; }
  .msg { color:var(--muted); font-size:13px; padding:0 16px 12px; }
  .foot { max-width:600px; margin:0 auto; display:flex; }
  .empty { max-width:600px; margin:0 auto 12px; background:var(--card); border-radius:14px; padding:24px 16px; text-align:center; color:var(--muted); }
</style>
</head>
<body>
<div class="top"><img src="/icons/opiam-192.png" alt=""><div><b><?= $h($label) ?></b><br><span><?= $h($project) ?></span></div>
  <span class="n" id="cnt"><?= count($notes) ?> open</span></div>
<?php if (!$notes): ?>
  <div class="empty">No open notes.</div>
<?php endif; ?>
<?php foreach ($notes as $n):
    $snz = '';
    if (!empty($n['snooze_until'])) { $d = new DateTime($n['snooze_until']); $d->setTimezone($ist); $snz = 'Snoozed till ' . $d->format('j M, g:i A'); }
?>
<div class="card<?= $snz ? ' snz' : '' ?>" data-id="<?= (int)$n['id'] ?>" data-t="<?= $h($n['t']) ?>">
  <div class="body">
    <div class="meta"><span>Written <?= $h(date('j M Y', strtotime($n['created_at']))) ?></span><span class="st"><?= $h($snz ?: 'Pending') ?></span></div>
    <div class="text"><?= $h($n['text']) ?></div>
<?php if (!empty($n['imgs'])): ?>
    <div class="pics"><?php foreach ($n['imgs'] as $p): ?><a href="<?= $h($p) ?>" target="_blank"><img src="<?= $h($p) ?>" alt=""></a><?php endforeach; ?></div>
<?php endif; ?>
<?php if ($FL = ($n['flist'] ?? [])): ?>
    <div class="files"><?php foreach ($FL as $f): ?><a href="<?= $h($f['u']) ?>" target="_blank" download><span class="fx"><?= $h(strtoupper($f['ext'])) ?></span><?= $h($f['name']) ?><small><?= $h($f['s'] >= 1048576 ? round($f['s'] / 1048576, 1) . ' MB' : max(1, round($f['s'] / 1024)) . ' KB') ?></small></a><?php endforeach; ?></div>
<?php endif; ?>
  </div>
  <div class="btns">
    <button type="button" class="pri" data-do="done">&#10003; Done</button>
    <button type="button" data-do="snoozemenu">&#128164; Snooze</button>
    <div class="sz">
      <button type="button" data-do="snooze" data-for="1h">1 hour</button>
      <button type="button" data-do="snooze" data-for="tomorrow">Tomorrow 9 AM</button>
      <button type="button" data-do="snooze" data-for="3d">In 3 days</button>
    </div>
  </div>
  <div class="msg"></div>
</div>
<?php endforeach; ?>
<div class="foot"><a class="btn pri" href="<?= $h($homeUrl) ?>">Open Opiam</a></div>
<script>
(function(){
  var kind = <?= json_encode($kind) ?>, uid = <?= (int)$uid ?>, guest = <?= $guest ? 'true' : 'false' ?>;
  var open = <?= count($notes) ?>;
  document.addEventListener('click', function(e){
    var b = e.target.closest('.card button'); if (!b) return;
    var card = b.closest('.card'), act = b.getAttribute('data-do');
    if (act === 'snoozemenu') { card.querySelector('.sz').style.display = 'flex'; return; }
    b.disabled = true;
    var fd = new FormData(); fd.append('k', kind); fd.append('id', card.getAttribute('data-id')); fd.append('do', act);
    if (act === 'snooze') fd.append('for', b.getAttribute('data-for'));
    if (guest) { fd.append('u', uid); fd.append('t', card.getAttribute('data-t')); }
    fetch(<?= json_encode($actUrl) ?>, { method:'POST', body:fd, credentials:'same-origin' }).then(function(r){ return r.json(); }).then(function(d){
      b.disabled = false;
      if (d.error !== 'No') { card.querySelector('.msg').textContent = d.errortext || 'Could not save.'; return; }
      card.querySelector('.btns').remove();
      if (act === 'done') {
        card.className = 'card done'; card.querySelector('.st').textContent = 'Done';
        card.querySelector('.msg').textContent = 'Done. Removed from your Notes.';
        open--; document.getElementById('cnt').textContent = open + ' open';
      } else {
        card.className = 'card snz'; card.querySelector('.st').textContent = 'Snoozed till ' + d.until;
        card.querySelector('.msg').textContent = 'You will get it again on ' + d.until + '.';
      }
    }).catch(function(){ b.disabled = false; card.querySelector('.msg').textContent = 'No connection. Try again.'; });
  });
})();
</script>
</body>
</html>
