$(function(){

	$(document).on('click','.navbar-nav .overNow4, .gantt-actlib-btn', function(e){
		e.preventDefault();
		if($('.overNow').hasClass('active')){
			$( ".overNow" ).removeClass("active");
			$('.menu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow1').hasClass('active')){
			$( ".overNow1" ).removeClass("active");
			$('.menu1-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow2').hasClass('active')){
			$( ".overNow2" ).removeClass("active");
			$('.finmenu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow5').hasClass('active')){
				$( ".overNow5" ).removeClass("active");
				$('.menu5-popup-cntnr').removeClass('active');
				$('body').css('overflow-y','auto');
		}else if($('.icon-dashboard').hasClass('active')){
			$( ".icon-dashboard" ).removeClass("active");
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		jQuery('body').removeClass('menu-active');
		$('.overNow4').toggleClass('active');
		if($('.overNow4').hasClass('active')){
			$('#project-title-head').html('Activity Library');
			$('#prjct_head').html('Activity Library');
			$('#procurement-title-head').html('Activity Library');
			$('.menu4-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#prjct_head').html('Operations');
			$('#project-title-head').html('Projects');
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		


		
	});
	$(document).on('click','.menu4-win-close', function(e){
		e.preventDefault();
		$('.menu4-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
	});

	/* drag + resize for Activity Library popup */
	(function(){
		var MIN_W=360, MIN_H=260;
		var _action=null,_sx=0,_sy=0,_ox=0,_oy=0,_ow=0,_oh=0;

		function getPanel(){ return document.querySelector('.menu4-popup-cntnr'); }

		function anchorAbsolute(el){
			var r = el.getBoundingClientRect();
			el.style.transform = 'none';
			el.style.left   = r.left + 'px';
			el.style.top    = r.top  + 'px';
			el.style.width  = r.width  + 'px';
			el.style.height = r.height + 'px';
			return r;
		}

		$(document).on('mousedown', '.menu4-drag-hdr', function(e){
			if($(e.target).hasClass('menu4-win-close') || $(e.target).closest('.menu4-win-close').length) return;
			var el = getPanel(); if(!el) return;
			var r = anchorAbsolute(el);
			_action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top;
			e.preventDefault();
		});

		$(document).on('mousedown', '.menu4-rs', function(e){
			var el = getPanel(); if(!el) return;
			var r = anchorAbsolute(el);
			_action=$(this).data('dir');
			_sx=e.clientX; _sy=e.clientY;
			_ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
			e.preventDefault(); e.stopPropagation();
		});

		$(document).on('mousemove', function(e){
			if(!_action) return;
			var el = getPanel(); if(!el) return;
			var dx=e.clientX-_sx, dy=e.clientY-_sy;
			if(_action==='drag'){
				var x=Math.max(0,Math.min(_ox+dx, window.innerWidth -el.offsetWidth));
				var y=Math.max(0,Math.min(_oy+dy, window.innerHeight-el.offsetHeight));
				el.style.left=x+'px'; el.style.top=y+'px';
			} else {
				var l=_ox,t=_oy,w=_ow,h=_oh;
				if(_action.indexOf('e')>-1){ w=Math.max(MIN_W,_ow+dx); }
				if(_action.indexOf('s')>-1){ h=Math.max(MIN_H,_oh+dy); }
				if(_action.indexOf('w')>-1){ var nw=Math.max(MIN_W,_ow-dx); l=_ox+(_ow-nw); w=nw; }
				el.style.left=l+'px'; el.style.top=t+'px';
				el.style.width=w+'px'; el.style.height=h+'px';
			}
		});

		$(document).on('mouseup', function(){ _action=null; });
	})();


	//$(document).on('click','.overNow.active', function(){
	//$(document).on('click','.overNow', function(){
		/*$('.menu-cntnt-wrpr .panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.menu-cntnt-wrpr .panel-group').removeClass('active');
			$('.menu-cntnt-wrpr .panel-default').removeClass('active');
			$('.menu-cntnt-wrpr .panel-default').removeClass('prev-tab');
			$('.menu-cntnt-wrpr .panel-group .tab-content').removeClass('in');
	        	$('.menu-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.menu-cntnt-wrpr .panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);*/

		/*$('.panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.panel-group').removeClass('active');
			$('.panel-default').removeClass('active');
			$('.panel-default').removeClass('prev-tab');
			$('.panel-group .tab-content').removeClass('in');
	        	$('.menu4-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);*/
	//});
	
});
