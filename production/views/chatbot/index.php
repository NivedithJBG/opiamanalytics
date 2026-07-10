<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Opiam Assistant</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f0f3fa;font-family:'Times New Roman',Times,serif;height:100vh;display:flex;flex-direction:column}
#cb-hdr{background:#1a2540;color:#fff;padding:14px 16px;font-size:21px;font-weight:600;display:flex;align-items:center;gap:10px;flex-shrink:0;font-family:'Times New Roman',Times,serif}
#cb-hdr .dot{width:10px;height:10px;border-radius:50%;background:#4ade80;flex-shrink:0}
#cb-msgs{flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:12px}
.cb-msg{max-width:85%;padding:10px 14px;border-radius:14px;font-size:19px;line-height:1.6;word-wrap:break-word;font-family:'Times New Roman',Times,serif}
.cb-msg.user{background:#1a2540;color:#fff;align-self:flex-end;border-bottom-right-radius:3px;font-weight:500}
.cb-msg.bot{background:#fff;color:#1a1a1a;align-self:flex-start;border-bottom-left-radius:3px;box-shadow:0 1px 4px rgba(0,0,0,.1)}
.cb-msg.typing{color:#888;font-style:italic;background:#fff}
.cb-msg-wrap{display:flex;flex-direction:column;align-self:flex-start;max-width:85%}
.cb-msg-wrap .cb-msg{max-width:100%;align-self:unset}
.cb-speak{background:none;border:none;cursor:pointer;font-size:15px;color:#aaa;padding:3px 4px;align-self:flex-start;margin-top:3px;line-height:1}
.cb-speak:hover{color:#1a2540}
.cb-speak.speaking{color:#e53935}
#cb-foot{display:flex;gap:8px;padding:12px;background:#fff;border-top:1px solid #e8ecf4;flex-shrink:0}
#cb-input{flex:1;border:1px solid #cbd5e1;border-radius:24px;padding:10px 16px;font-size:19px;outline:none;font-family:'Times New Roman',Times,serif}
#cb-input:focus{border-color:#1a2540}
#cb-send{background:#1a2540;color:#fff;border:none;border-radius:50%;width:46px;height:46px;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-family:'Times New Roman',Times,serif}
#cb-send:active{background:#2d3f6e}
#cb-mic{background:#f0f3fa;color:#1a2540;border:1px solid #cbd5e1;border-radius:50%;width:46px;height:46px;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0}
#cb-mic.listening{background:#e53935;color:#fff;border-color:#e53935;animation:cb-pulse 1s infinite}
@keyframes cb-pulse{0%,100%{box-shadow:0 0 0 0 rgba(229,57,53,.4)}50%{box-shadow:0 0 0 8px rgba(229,57,53,0)}}
</style>
</head>
<body>
<div id="cb-hdr">
    <div class="dot"></div>
    Opiam Assistant
</div>
<div id="cb-msgs">
    <div class="cb-msg bot">Hi! Ask me anything about your projects.</div>
</div>
<div id="cb-foot">
    <input id="cb-input" type="text" placeholder="Ask a question…" autocomplete="off">
    <button id="cb-mic" title="Speak" aria-label="Voice input">&#127908;</button>
    <button id="cb-send" title="Send" aria-label="Send">&#10148;</button>
</div>
<script>
(function(){
    var history = [];
    var msgs = document.getElementById('cb-msgs');
    var inp  = document.getElementById('cb-input');

    /* Voice input */
    var micBtn = document.getElementById('cb-mic');
    var listening = false;
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if(SpeechRecognition){
        var recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.lang = 'en-IN';
        recognition.onstart = function(){ listening = true; micBtn.classList.add('listening'); };
        recognition.onend   = function(){ listening = false; micBtn.classList.remove('listening'); };
        recognition.onerror = function(){ listening = false; micBtn.classList.remove('listening'); };
        recognition.onresult = function(e){
            var final = '';
            for(var i = e.resultIndex; i < e.results.length; i++){
                if(e.results[i].isFinal) final += e.results[i][0].transcript;
            }
            if(final){ inp.value = (inp.value + ' ' + final).trim(); }
        };
        micBtn.addEventListener('click', function(){
            if(listening){ recognition.stop(); }
            else { inp.focus(); try{ recognition.start(); } catch(ex){} }
        });
    } else {
        micBtn.style.opacity = '0.4';
        micBtn.style.cursor = 'not-allowed';
    }

    document.getElementById('cb-send').addEventListener('click', sendMsg);
    inp.addEventListener('keydown', function(e){ if(e.key==='Enter') sendMsg(); });

    function sendMsg(){
        var text = inp.value.trim();
        if(!text) return;
        inp.value = '';
        addMsg(text, 'user');
        history.push({role:'user', content:text});
        var typing = addMsg('Thinking...', 'bot typing');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/chatbot/chat"]); ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            msgs.removeChild(typing);
            if(xhr.status !== 200){ addMsg('Server error. Please try again.', 'bot'); return; }
            try {
                var d = JSON.parse(xhr.responseText);
                if(d.error){ addMsg('Error: ' + d.error, 'bot'); return; }
                var reply = d.reply || 'No response.';
                addMsg(reply, 'bot');
                history.push({role:'assistant', content:reply});
                if(history.length > 20) history = history.slice(-20);
            } catch(e){
                addMsg('Error: ' + xhr.responseText.substring(0,200), 'bot');
            }
        };
        xhr.onerror = function(){ msgs.removeChild(typing); addMsg('Network error.', 'bot'); };
        var priorHistory = history.slice(0, -1);
        xhr.send('message=' + encodeURIComponent(text) + '&history=' + encodeURIComponent(JSON.stringify(priorHistory)));
    }

    /* Text-to-speech */
    function speak(text, btn){
        if(!window.speechSynthesis) return;
        window.speechSynthesis.cancel();
        var utt = new SpeechSynthesisUtterance(text);
        utt.lang = 'en-IN';
        utt.rate = 1;
        if(btn){ btn.classList.add('speaking'); btn.textContent = '⏹'; }
        utt.onend = utt.onerror = function(){ if(btn){ btn.classList.remove('speaking'); btn.textContent = '🔊'; } };
        window.speechSynthesis.speak(utt);
    }

    function addMsg(text, cls){
        var isBot = cls.indexOf('bot') !== -1 && cls.indexOf('typing') === -1;
        if(isBot){
            var wrap = document.createElement('div');
            wrap.className = 'cb-msg-wrap';
            var d = document.createElement('div');
            d.className = 'cb-msg ' + cls;
            d.textContent = text;
            var spk = document.createElement('button');
            spk.className = 'cb-speak';
            spk.textContent = '🔊';
            spk.title = 'Read aloud';
            spk.addEventListener('click', function(){
                if(window.speechSynthesis && window.speechSynthesis.speaking){ window.speechSynthesis.cancel(); spk.classList.remove('speaking'); spk.textContent='🔊'; }
                else speak(text, spk);
            });
            wrap.appendChild(d);
            wrap.appendChild(spk);
            msgs.appendChild(wrap);
            msgs.scrollTop = msgs.scrollHeight;
            return wrap;
        }
        var d = document.createElement('div');
        d.className = 'cb-msg ' + cls;
        d.textContent = text;
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
        return d;
    }
})();
</script>
</body>
</html>
