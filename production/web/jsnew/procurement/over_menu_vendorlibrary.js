$(function(){

  if(typeof window.bindDragTouch !== 'function'){
    window.bindDragTouch = function(el, mouseType, handler, opts){
      if(el) el.addEventListener(mouseType, handler, opts);
    };
  }

  /* ═══════════════════════════════════════════════════════════════
     Drag+resize engine for #vendorlib-win — mirrors Resource
     Library's rlBindDragResize, duplicated (vl-prefixed) since that
     helper is scoped locally to overrelay-resourcemaster.php and
     not exposed on window.
  ═══════════════════════════════════════════════════════════════ */
  var _vlDrag = null;

  function vlAnchor(win){
    var r = win.getBoundingClientRect();
    win.style.left   = r.left   + 'px';
    win.style.top    = r.top    + 'px';
    win.style.width  = r.width  + 'px';
    win.style.height = r.height + 'px';
    return r;
  }

  function vlBindDragResize(win, hdrSel, minW, minH){
    var hdr = win.querySelector(hdrSel);

    bindDragTouch(hdr, 'mousedown', function(e){
      if(e.target.closest('#vendorlib-win-hdr-btns')) return;
      var r = vlAnchor(win);
      _vlDrag = { win:win, action:'drag', sx:e.clientX, sy:e.clientY, ox:r.left, oy:r.top, ow:r.width, oh:r.height, mw:minW, mh:minH };
      e.preventDefault();
    });

    win.querySelectorAll('.rl-rs').forEach(function(el){
      bindDragTouch(el, 'mousedown', function(e){
        var r = vlAnchor(win);
        _vlDrag = { win:win, action:el.dataset.dir, sx:e.clientX, sy:e.clientY, ox:r.left, oy:r.top, ow:r.width, oh:r.height, mw:minW, mh:minH };
        e.preventDefault(); e.stopPropagation();
      });
    });
  }

  bindDragTouch(document, 'mousemove', function(e){
    if(!_vlDrag) return;
    e.preventDefault();
    var d = _vlDrag, dx = e.clientX - d.sx, dy = e.clientY - d.sy;
    var l = d.ox, t = d.oy, w = d.ow, h = d.oh;
    if(d.action === 'drag'){
      l = Math.max(0, d.ox + dx);
      t = Math.max(0, d.oy + dy);
    } else {
      if(d.action.indexOf('e') > -1){ w = Math.max(d.mw, d.ow + dx); }
      if(d.action.indexOf('s') > -1){ h = Math.max(d.mh, d.oh + dy); }
      if(d.action.indexOf('w') > -1){ var nw = Math.max(d.mw, d.ow - dx); l = d.ox + (d.ow - nw); w = nw; }
      if(d.action.indexOf('n') > -1){ var nh = Math.max(d.mh, d.oh - dy); t = d.oy + (d.oh - nh); h = nh; }
    }
    d.win.style.left = l + 'px'; d.win.style.top  = t + 'px';
    d.win.style.width = w + 'px'; d.win.style.height = h + 'px';
  }, { passive: false });
  bindDragTouch(document, 'mouseup', function(){ _vlDrag = null; });

  /* ═══════════════════════════════════════════════════════════════
     Vendor Library popup
  ═══════════════════════════════════════════════════════════════ */
  var win = document.getElementById('vendorlib-win');
  if(!win) return;

  vlBindDragResize(win, '#vendorlib-win-hdr', 420, 280);

  function openVendorlib(){
    win.classList.add('rl-open');
    document.dispatchEvent(new Event('vendorlib:open'));
    $('#listvendorslib').trigger('click');   // vendors listed on open
  }
  function closeVendorlib(){
    win.classList.remove('rl-open');
    document.querySelectorAll('.overNowVendorLib').forEach(function(el){ el.classList.remove('active'); });
    $('.po-preview-cntnr').removeClass('active');
    $('#vendorliblistsection').html('');
    $('#vendor-list-body').html('<tr><td colspan="8" style="text-align:center;padding:30px;color:#aaa;font-size:13px;border:1px solid #eee;">Select a Resource Type to view vendors</td></tr>');
    $('#search-restype').val('0');
    $('#search-resid').html('<option value="0">-- Resource --</option>').prop('disabled', true);
  }

  document.getElementById('vendorlib-close').addEventListener('click', closeVendorlib);

  $(document).on('click', '.navbar-nav .overNowVendorLib', function(e){
    e.preventDefault();
    if(win.classList.contains('rl-open')){ closeVendorlib(); return; }

    var others = {
      '.overNow':        '.menu-popup-cntnr',
      '.overNow4':       '.menu4-popup-cntnr',
      '.overNow2':       '.finmenu-popup-cntnr',
      '.overNow8':       '.menu8-popup-cntnr',
      '.overNow5':       '.menu5-popup-cntnr'
    };
    $.each(others, function(cls, cntnr){
      if($(cls).hasClass('active')){ $(cls).removeClass('active'); $(cntnr).removeClass('active'); }
    });
    $('#reslib-win').removeClass('rl-open');
    $('.reslib-btn').removeClass('active');
    jQuery('body').removeClass('menu-active');

    openVendorlib();
    $(this).addClass('active');
  });

});
