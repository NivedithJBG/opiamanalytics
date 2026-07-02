<?php
/** @var yii\web\View $this */
/** @var string $projectName */
/** @var int $projectId */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($projectName) ?> - Performance Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
/* position:fixed + inset:0 is the bulletproof no-scroll approach */
html{height:100%}
body{
    position:fixed;inset:0;overflow:hidden;
    font-family:'Segoe UI',Arial,sans-serif;
    background:#0d1b2e;
    display:flex;flex-direction:column
}

/* ── Header ── */
#dash-header{
    height:48px;background:#0d1b2e;display:flex;align-items:center;
    padding:0 10px;gap:8px;border-bottom:2px solid #1e3554;flex-shrink:0
}
#dash-header h1{
    flex:1;color:#e8f0fb;font-size:13px;font-weight:700;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis
}
#dash-back{
    background:none;border:1px solid #3a5a80;color:#8ab4d8;
    font-size:10px;padding:2px 8px;border-radius:3px;cursor:pointer;flex-shrink:0
}
#dash-back:hover{background:#1e3554;color:#fff}
#dash-hint{font-size:9px;color:#4a6a90;font-style:italic;flex-shrink:0}

/* ── Grid ── */
#dash-grid{
    flex:1;min-height:0;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    grid-template-rows:repeat(4,1fr);
    gap:4px;padding:4px;
    overflow:hidden
}

/* ── Cards ── */
.dcard{
    background:#fff;border-radius:4px;
    display:flex;flex-direction:column;
    overflow:hidden;min-height:0;
    box-shadow:0 1px 4px rgba(0,0,0,.3)
}
.dcard-hdr{
    background:#1a3052;color:#e8f0fb;
    font-size:10.5px;font-weight:700;
    padding:3px 8px;flex-shrink:0;
    display:flex;align-items:center;justify-content:space-between;
    line-height:1.3
}
.dcard-hdr .hist{font-size:9px;font-weight:400;color:#7aacda;cursor:pointer}

/* card body — flex column, must not grow past its cell */
.dcard-body{
    flex:1;min-height:0;overflow:hidden;
    padding:4px 6px;
    display:flex;flex-direction:column
}
#ch-project-card .dcard-body{overflow:visible;}

/* ── Bar chart cards ── */
.leg{
    display:flex;gap:6px;font-size:11px;color:#1a2540;
    flex-shrink:0;margin-bottom:3px;flex-wrap:wrap;
}
.leg i{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:3px;vertical-align:middle}
.cv-area{flex:1;min-height:0;position:relative}
.cv-area canvas{position:absolute;inset:0;width:100%!important;height:100%!important}

/* ── Gauge cards ── */
.gauge-wrap{
    flex:1;min-height:0;overflow:hidden;
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;gap:1px
}
.gauge-wrap svg{display:block;width:100%;height:auto;flex-shrink:1;max-height:76px}
.g-vals{display:flex;gap:12px;justify-content:center;flex-shrink:0}
.g-val{text-align:center;line-height:1.2}
.g-val-lbl{font-size:8px;font-weight:700;display:block}
.g-val-num{font-size:11px;font-weight:700;color:#1a2a4a}
.g-act{
    font-size:8.5px;color:#8892a4;text-align:center;
    overflow:hidden;white-space:nowrap;text-overflow:ellipsis;
    width:100%;padding:0 3px;flex-shrink:0
}

/* ── Placeholder ── */
.ph{display:flex;align-items:center;justify-content:center;flex:1;color:#bbc;font-size:10px}
</style>
</head>
<body>

<div id="dash-header">
  <h1 id="dash-title"><?= htmlspecialchars($projectName) ?> - Performance Dashboard</h1>
  <span id="dash-hint">Click an activity bar to update KPI panels</span>
  <button id="dash-back" onclick="history.back()">&#8592; Back</button>
</div>

<div id="dash-grid">

  <!-- R1C1 IOW Group -->
  <div class="dcard">
    <div class="dcard-hdr">IOW Group</div>
    <div class="dcard-body">
      <div class="leg">
        <span><i style="background:#2878c0"></i>Scheduled</span>
        <span><i style="background:#e55353"></i>Delay</span>
      </div>
      <div class="cv-area"><canvas id="ch-iow-grp"></canvas></div>
    </div>
  </div>

  <!-- R1C2 Project -->
  <div class="dcard" id="ch-project-card">
    <div class="dcard-hdr">Project</div>
    <div class="dcard-body">
      <div class="leg" style="flex-wrap:wrap;gap:6px 12px;">
        <span><i style="background:#2878c0"></i>Planned Duration: <b id="leg-planned-dur" style="font-size:13px;font-weight:700;color:#1a2540;">—</b></span>
        <span><i style="background:#e55353"></i>Actual Duration: <b id="leg-actual-dur" style="font-size:13px;font-weight:700;color:#1a2540;">—</b></span>
        <span><i style="background:#f0c419"></i>Difference: <b id="leg-diff-dur" style="font-size:13px;font-weight:700;color:#1a2540;">—</b></span>
      </div>
      <div class="cv-area"><canvas id="ch-project"></canvas></div>
    </div>
  </div>

  <!-- R1C3 Work Done -->
  <div class="dcard">
    <div class="dcard-hdr">Work Done <span class="hist">History</span></div>
    <div class="dcard-body">
      <div class="gauge-wrap" id="gw-work-done"><div class="ph">Loading&hellip;</div></div>
    </div>
  </div>

  <!-- R2C1 IOW -->
  <div class="dcard">
    <div class="dcard-hdr">IOW</div>
    <div class="dcard-body">
      <div class="cv-area"><canvas id="ch-iow"></canvas></div>
    </div>
  </div>

  <!-- R2C2 Target Production -->
  <div class="dcard">
    <div class="dcard-hdr">Target Production <span class="hist">History</span></div>
    <div class="dcard-body">
      <div class="gauge-wrap" id="gw-tgt-prod"><div class="ph">Loading&hellip;</div></div>
    </div>
  </div>

  <!-- R2C3 Productivity -->
  <div class="dcard">
    <div class="dcard-hdr">Productivity <span class="hist">History</span></div>
    <div class="dcard-body">
      <div class="gauge-wrap" id="gw-productivity"><div class="ph">Loading&hellip;</div></div>
    </div>
  </div>

  <!-- R3C1 Ongoing Activity -->
  <div class="dcard">
    <div class="dcard-hdr">Ongoing Activity</div>
    <div class="dcard-body">
      <div class="cv-area"><canvas id="ch-ongoing"></canvas></div>
    </div>
  </div>

  <!-- R3C2 Cycle Time -->
  <div class="dcard">
    <div class="dcard-hdr">Cycle Time <span class="hist">History</span></div>
    <div class="dcard-body">
      <div class="gauge-wrap" id="gw-cycle"><div class="ph">Loading&hellip;</div></div>
    </div>
  </div>

  <!-- R3C3 Capacity Utilisation -->
  <div class="dcard">
    <div class="dcard-hdr">Capacity Utilisation <span class="hist">History</span></div>
    <div class="dcard-body">
      <div class="gauge-wrap" id="gw-capacity"><div class="ph">Loading&hellip;</div></div>
    </div>
  </div>

  <!-- R4C1 Upcoming Activity -->
  <div class="dcard">
    <div class="dcard-hdr">Upcoming Activity</div>
    <div class="dcard-body">
      <div class="cv-area"><canvas id="ch-upcoming"></canvas></div>
    </div>
  </div>

  <!-- R4C2 Cause of Delay -->
  <div class="dcard">
    <div class="dcard-hdr">Cause of Delay</div>
    <div class="dcard-body">
      <div class="cv-area"><canvas id="ch-cod"></canvas></div>
    </div>
  </div>

  <!-- R4C3 Resource Capacity -->
  <div class="dcard">
    <div class="dcard-hdr">Resource Capacity</div>
    <div class="dcard-body">
      <div class="cv-area"><canvas id="ch-res"></canvas></div>
    </div>
  </div>

</div>

<script>
var CSRF = '<?= Yii::$app->request->csrfToken ?>';
var BASE = '<?= rtrim(Yii::$app->request->baseUrl, '/') ?>';
var _ch  = {};

// ── Fetch ────────────────────────────────────────────────────────────────────
function post(path, data, cb) {
    var body = '_csrf=' + encodeURIComponent(CSRF);
    if (data) for (var k in data) body += '&' + k + '=' + encodeURIComponent(data[k]);
    fetch(BASE + path, {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body:body
    }).then(function(r){return r.json();}).then(cb).catch(function(e){console.error(e);});
}

// ── Init ─────────────────────────────────────────────────────────────────────
post('/projectsmain/performancedashboard', null, function(d){
    if (!d || d.error === undefined) return;
    hbar('ch-iow-grp',  grpDs(d.iow_groups),         true);
    hbar('ch-project',  projDs(d.project_name, d.project_bar), false);
    if (d.project_bar) {
        var pb = d.project_bar;
        var planned = +pb.budgeted || 0;
        var actual  = +pb.actual  > 0 ? +pb.actual : planned;
        var diff    = actual - planned;
        document.getElementById('leg-planned-dur').textContent = planned > 0 ? planned + ' days' : '—';
        document.getElementById('leg-actual-dur').textContent  = actual  > 0 ? actual  + ' days' : '—';
        document.getElementById('leg-diff-dur').textContent    = planned > 0 ? (diff > 0 ? '+' : '') + diff + ' days' : '—';
        document.getElementById('leg-diff-dur').style.color    = diff > 0 ? '#e55353' : diff < 0 ? '#27ae60' : '#1a2540';
    }
    hbar('ch-iow',      actDs(d.iow_items),           true);
    hbar('ch-ongoing',  actDs(d.ongoing),             true);
    hbar('ch-upcoming', actDs(d.upcoming),            true);
    if (d.kpi) doKpi(d.kpi);
});

// ── Dataset builders ─────────────────────────────────────────────────────────
function s(str,n){str=str||'';return str.length>n?str.substring(0,n-1)+'…':str;}
function num(v){v=+v||0;return Number.isInteger(v)?v:v.toFixed(1);}

function grpDs(g){
    var lb=[],sc=[],dl=[];
    (g||[]).forEach(function(r){lb.push(s(r.name,20));sc.push(+r.scheduled||0);dl.push(+r.delay||0);});
    return {labels:lb,_ids:null,datasets:[
        {label:'Scheduled',data:sc,backgroundColor:'#2878c0',borderWidth:0,barThickness:12},
        {label:'Delay',    data:dl,backgroundColor:'#e55353',borderWidth:0,barThickness:12}
    ]};
}
function projDs(name,bar){
    var planned = +bar.budgeted || 0;
    var actual  = +bar.actual  || 0;
    var delay   = Math.max(0, actual - planned);
    return {labels:[s(name||'Project',26)],_ids:null,datasets:[
        {label:'Planned Duration', data:[planned], backgroundColor:'#2878c0', borderWidth:0, barThickness:18},
        {label:'Actual Duration',  data:[delay],   backgroundColor:'#e55353', borderWidth:0, barThickness:18}
    ]};
}
function actDs(items){
    var lb=[],sc=[],dl=[],ids=[];
    (items||[]).forEach(function(a){
        lb.push(s(a.name,22));sc.push(+a.duration||0);dl.push(+a.delay||0);ids.push(a.id);
    });
    return {labels:lb,_ids:ids,datasets:[
        {label:'Scheduled',data:sc,backgroundColor:'#2878c0',borderWidth:0,barThickness:13},
        {label:'Delay',    data:dl,backgroundColor:'#e55353',borderWidth:0,barThickness:13}
    ]};
}

// ── Horizontal bar ───────────────────────────────────────────────────────────
function hbar(id, ds, clickable){
    var cv=document.getElementById(id); if(!cv) return;
    if(_ch[id]){_ch[id].destroy();delete _ch[id];}
    _ch[id]=new Chart(cv,{
        type:'bar', data:ds,
        options:{
            indexAxis:'y', responsive:true, maintainAspectRatio:false, animation:{duration:300},
            onClick: clickable ? function(e,items){
                if(!items.length) return;
                var aid=ds._ids&&ds._ids[items[0].index]; if(aid) loadKpi(aid);
            } : null,
            plugins:{
                legend:{display:false},
                tooltip:{callbacks:{label:function(c){return ' '+c.dataset.label+': '+c.parsed.x.toFixed(1)+' d';}}}
            },
            scales:{
                x:{stacked:true,ticks:{font:{size:8},color:'#778'},grid:{color:'rgba(0,0,0,.06)'},border:{display:false}},
                y:{stacked:true,ticks:{font:{size:9},color:'#334'},grid:{display:false},border:{display:false}}
            }
        }
    });
}

// ── KPI ──────────────────────────────────────────────────────────────────────
function loadKpi(actid){
    post('/projectsmain/performancedashboardkpi',{actid:actid},function(d){if(d&&d.kpi)doKpi(d.kpi);});
}
function doKpi(k){
    var u=k.unit||'', an=s(k.activity_name||'',34);
    gauge('gw-work-done',   k.work_done_pct, 100,              null,  null, k.work_done_pct+'%', null, null, an);
    gauge('gw-tgt-prod',    k.actual_qty,    Math.max(k.target_qty,k.actual_qty,1), null,
          'Target', num(k.target_qty)+' '+u, 'Actual', num(k.actual_qty)+' '+u, an);
    gauge('gw-productivity',k.actual_productivity, Math.max(k.target_productivity,k.actual_productivity,.01), null,
          'Target', num(k.target_productivity)+' '+u+'/d', 'Actual', num(k.actual_productivity)+' '+u+'/d', an);
    gauge('gw-cycle',       k.actual_cycle_time, Math.max(k.target_cycle_time,k.actual_cycle_time,.01),
          'WH: '+k.wh, 'Target', num(k.target_cycle_time)+' d', 'Actual', num(k.actual_cycle_time)+' d', an);
    gauge('gw-capacity',    k.capacity_pct,  100,              null,  null, k.capacity_pct+'%', null, null, an);
    cod(k.cause_of_delay, an);
    res(k.resources,      an);
}

// ── SVG Gauge ─────────────────────────────────────────────────────────────────
// Arc travels from left (180°) through top (270°) to right (360°)
// SVG angle at fraction f:  a = 180 + f*180  (degrees)
// Sweep=0 (counter-clockwise in SVG) goes through the top  ✓
function gauge(gwId, val, maxVal, topNote, lbl1, v1, lbl2, v2, actName){
    var el=document.getElementById(gwId); if(!el) return;
    val=+val||0; maxVal=+maxVal||1;
    var f=Math.max(0,Math.min(1,val/maxVal));
    var cx=100,cy=100,R=76,SW=13;

    function rad(deg){return deg*Math.PI/180;}
    function ptF(frac){var a=rad(180+frac*180);return[cx+R*Math.cos(a),cy+R*Math.sin(a)];}
    function arc(f1,f2,sw,col){
        if(f2<=f1) return '';
        var p1=ptF(f1),p2=ptF(f2),large=(f2-f1)>=1?1:0;
        return '<path d="M'+p1[0].toFixed(1)+','+p1[1].toFixed(1)+
               ' A'+R+','+R+' 0 '+large+',0 '+p2[0].toFixed(1)+','+p2[1].toFixed(1)+
               '" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="round"/>';
    }

    var fill = f<0.4?'#e55353':f<0.7?'#e8a43d':'#27ae60';

    // Tick marks at 0,25,50,75,100%
    var ticks='';
    [0,.25,.5,.75,1].forEach(function(tf){
        var a=rad(180+tf*180),oR=R+1,iR=R-SW/2-2;
        ticks+='<line x1="'+(cx+oR*Math.cos(a)).toFixed(1)+'" y1="'+(cy+oR*Math.sin(a)).toFixed(1)+
               '" x2="'+(cx+iR*Math.cos(a)).toFixed(1)+'" y2="'+(cy+iR*Math.sin(a)).toFixed(1)+
               '" stroke="#fff" stroke-width="1.5"/>';
    });

    // Needle
    var nR=R-SW/2-3,na=rad(180+f*180);
    var nx=(cx+nR*Math.cos(na)).toFixed(1),ny=(cy+nR*Math.sin(na)).toFixed(1);

    // Min/max labels on the baseline
    var W=200,H=106;
    var svg='<svg viewBox="0 0 '+W+' '+H+'" xmlns="http://www.w3.org/2000/svg">'
        +arc(0,1,SW,'#dde4ee')
        +(f>0?arc(0,f,SW-2,fill):'')
        +ticks
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny
        +'" stroke="#1a2a4a" stroke-width="2.5" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="4.5" fill="#1a2a4a"/>'
        +'<text x="'+(cx-R-1)+'" y="'+(cy+11)+'" text-anchor="end" font-size="8" fill="#9aa" font-family="Arial">0</text>'
        +'<text x="'+(cx+R+2)+'" y="'+(cy+11)+'" text-anchor="start" font-size="8" fill="#9aa" font-family="Arial">'+num(maxVal)+'</text>'
        +'<text x="'+cx+'" y="'+(cy-3)+'" text-anchor="middle" font-size="12" font-weight="700" fill="#1a2a4a" font-family="Arial">'+(v2||v1||(val.toFixed?val.toFixed(1):val))+'</text>'
        +'</svg>';

    var topH=topNote?'<div style="font-size:9.5px;font-weight:700;color:#667;text-align:center;">'+topNote+'</div>':'';
    var vH='';
    if(lbl1||lbl2){
        vH='<div class="g-vals">';
        if(lbl1) vH+='<div class="g-val"><div class="g-val-lbl" style="color:#e55353;">'+lbl1+'</div><div class="g-val-num">'+v1+'</div></div>';
        if(lbl2) vH+='<div class="g-val"><div class="g-val-lbl" style="color:#27ae60;">'+lbl2+'</div><div class="g-val-num">'+v2+'</div></div>';
        vH+='</div>';
    }
    var aH=actName?'<div class="g-act">'+actName+'</div>':'';
    el.innerHTML=topH+svg+vH+aH;
}

// ── Cause of Delay ────────────────────────────────────────────────────────────
function cod(items, actName){
    var cv=document.getElementById('ch-cod'); if(!cv) return;
    if(_ch['ch-cod']){_ch['ch-cod'].destroy();delete _ch['ch-cod'];}
    var cols=['#3498db','#e74c3c','#2ecc71','#f39c12','#9b59b6','#1abc9c','#e67e22','#16a085'];
    var lb=[],dt=[];
    (items||[]).forEach(function(r){lb.push(r.name);dt.push(+r.count||0);});
    if(!lb.length){lb=['No data'];dt=[1];cols=['#dde'];}
    _ch['ch-cod']=new Chart(cv,{
        type:'doughnut',
        data:{labels:lb,datasets:[{data:dt,backgroundColor:cols.slice(0,lb.length),borderWidth:2,borderColor:'#fff'}]},
        options:{
            responsive:true,maintainAspectRatio:false,animation:{duration:300},
            plugins:{
                legend:{position:'right',labels:{font:{size:8.5},boxWidth:8,padding:4}},
                tooltip:{callbacks:{label:function(c){
                    var t=c.dataset.data.reduce(function(a,b){return a+b},0);
                    return ' '+c.label+': '+c.parsed+' ('+Math.round(c.parsed/t*100)+'%)';
                }}}
            }
        }
    });
}

// ── Resource Capacity ─────────────────────────────────────────────────────────
function res(items, actName){
    var cv=document.getElementById('ch-res'); if(!cv) return;
    if(_ch['ch-res']){_ch['ch-res'].destroy();delete _ch['ch-res'];}
    var cols=['#e07b54','#f0c040','#9e9e9e','#7e57c2','#ab47bc','#42a5f5','#26a69a','#ec407a'];
    var lb=[],dt=[];
    (items||[]).forEach(function(r,i){lb.push(s(r.name||'',10));dt.push(+(r.cnt||r.count)||0);});
    if(!lb.length){lb=['No data'];dt=[0];}
    _ch['ch-res']=new Chart(cv,{
        type:'bar',
        data:{labels:lb,datasets:[{data:dt,backgroundColor:cols.slice(0,lb.length),borderWidth:0,borderRadius:3}]},
        options:{
            responsive:true,maintainAspectRatio:false,animation:{duration:300},
            plugins:{legend:{display:false}},
            scales:{
                x:{ticks:{font:{size:8.5},color:'#556'},grid:{display:false},border:{display:false}},
                y:{beginAtZero:true,ticks:{font:{size:8.5},color:'#556'},grid:{color:'rgba(0,0,0,.06)'},border:{display:false}}
            }
        }
    });
}
</script>
</body>
</html>
