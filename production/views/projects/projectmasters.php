<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/over_menu_projectmaster.js?v=<?php echo time(); ?>" type="text/javascript"></script>
<?php if(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters'): ?>
<script>
$(function(){
    if (!$('.overNow4').hasClass('active')) {
        $('.menu4-popup-cntnr').addClass('active');
        $('body').css('overflow-y','hidden');
        $('#project-title-head, #prjct_head, #procurement-title-head').html('Activity Library');
        $('.overNow4').addClass('active');
        document.dispatchEvent(new Event('actlib:open'));
    }
});
</script>
<?php endif; ?>
<input type="hidden" id="selectedSectorid">
<input type="hidden" id="selectedwbsid">
<input type="hidden" id="selectedsectoriowid">

<div class="menu4-popup-cntnr">
  <div class="m4-rs m4-rs-n" data-dir="n"></div>
  <div class="m4-rs m4-rs-s" data-dir="s"></div>
  <div class="m4-rs m4-rs-e" data-dir="e"></div>
  <div class="m4-rs m4-rs-w" data-dir="w"></div>
  <div class="m4-rs m4-rs-ne" data-dir="ne"></div>
  <div class="m4-rs m4-rs-nw" data-dir="nw"></div>
  <div class="m4-rs m4-rs-se" data-dir="se"></div>
  <div class="m4-rs m4-rs-sw" data-dir="sw"></div>
  <div class="menu4-win-hdr">
    <span style="font-size:18px;font-weight:700;color:#333;">Activity Library</span>
    <div class="menu4-win-hdr-btns">
      <button class="menu4-win-expand" title="Fullscreen">&#x26F6;</button>
      <button class="menu4-win-close" title="Close">&times;</button>
    </div>
  </div>
  <div class="menu4-cntnt-wrpr" style="padding:0 15px;">
    <?php echo $this->render('_estactivity'); ?>
  </div>
  <div style="padding:10px 15px;border-top:1px solid #e5e5e5;text-align:right;flex-shrink:0;">
    <button class="menu4-win-close btn" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;padding:6px 14px;"><span class="icon-close"></span> Close</button>
  </div>
</div>

<script>
$(function(){
  var win = document.querySelector('.menu4-popup-cntnr');
  var hdr = document.querySelector('.menu4-win-hdr');
  if(!win || !hdr) return;
  var MIN_W=400, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;

  function _anchor(){
    var r=win.getBoundingClientRect();
    win.style.left=r.left+'px'; win.style.top=r.top+'px';
    win.style.width=r.width+'px'; win.style.height=r.height+'px'; return r;
  }

  // Close
  document.querySelectorAll('.menu4-win-close').forEach(function(btn){
    btn.addEventListener('click', function(){
      win.classList.remove('active');
      document.querySelector('.overNow4') && document.querySelector('.overNow4').classList.remove('active');
      document.body.style.overflowY = 'auto';
    });
  });

  // Expand
  document.querySelector('.menu4-win-expand').addEventListener('click', function(){
    if(_saved){
      win.style.left=_saved.left; win.style.top=_saved.top;
      win.style.width=_saved.width; win.style.height=_saved.height;
      _saved=null; this.innerHTML='&#x26F6;'; this.title='Fullscreen';
    } else {
      _anchor();
      _saved={left:win.style.left,top:win.style.top,width:win.style.width,height:win.style.height};
      win.style.left='0'; win.style.top='0';
      win.style.width='100vw'; win.style.height='100vh';
      this.innerHTML='&#x2716;'; this.title='Restore';
    }
  });

  // Drag
  bindDragTouch(hdr, 'mousedown', function(e){
    if(e.target.closest('.menu4-win-hdr-btns')) return;
    var r=_anchor(); _action='drag';
    _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
  });

  // Resize
  document.querySelectorAll('.m4-rs').forEach(function(el){
    bindDragTouch(el, 'mousedown', function(e){
      var r=_anchor(); _action=el.getAttribute('data-dir');
      _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
      e.preventDefault(); e.stopPropagation();
    });
  });

  bindDragTouch(document, 'mousemove', function(e){
    if(!_action) return;
    e.preventDefault();
    var dx=e.clientX-_sx, dy=e.clientY-_sy;
    if(_action==='drag'){
      win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px';
    } else {
      var l=_ox,t=_oy,w=_ow,h=_oh;
      if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);}
      if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);}
      if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;}
      if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;}
      win.style.left=l+'px'; win.style.top=t+'px';
      win.style.width=w+'px'; win.style.height=h+'px';
    }
  }, { passive: false });
  bindDragTouch(document, 'mouseup', function(){ _action=null; });

  // Move Activity Library modals to <body> so they aren't clipped by overflow:hidden on the container
  ['alAddActivityPopup','alProjTypePopup','alIowGroupPopup','alIowPopup','alActTypePopup'].forEach(function(id){
    var el = document.getElementById(id);
    if(el && el.parentNode !== document.body) document.body.appendChild(el);
  });
});
</script>
