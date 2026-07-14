<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Project Assistant</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f0f3fa;font-family:'Times New Roman',Times,serif;height:100vh;display:flex;flex-direction:column}
#cb-hdr{background:#1a2540;color:#fff;padding:14px 16px;font-size:21px;font-weight:600;display:flex;align-items:center;gap:10px;flex-shrink:0;font-family:'Times New Roman',Times,serif}
#cb-hdr .dot{width:10px;height:10px;border-radius:50%;background:#4ade80;flex-shrink:0}
#cb-msgs{flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:12px}
.cb-msg{max-width:92%;padding:13px 18px;border-radius:14px;font-size:20px;line-height:1.7;word-wrap:break-word;font-family:'Times New Roman',Times,serif}
.cb-msg.user{background:#1a2540;color:#fff;align-self:flex-end;border-bottom-right-radius:3px;font-weight:500}
.cb-msg.bot{background:#fff;color:#1a1a1a;align-self:flex-start;border-bottom-left-radius:3px;box-shadow:0 1px 4px rgba(0,0,0,.1)}
.cb-msg.typing{color:#888;font-style:italic;background:#fff}
#cb-foot{display:flex;gap:8px;padding:12px;background:#fff;border-top:1px solid #e8ecf4;flex-shrink:0}
#cb-input{flex:1;border:1px solid #cbd5e1;border-radius:24px;padding:12px 20px;font-size:20px;outline:none;font-family:'Times New Roman',Times,serif}
#cb-input:focus{border-color:#1a2540}
#cb-send{background:#1a2540;color:#fff;border:none;border-radius:50%;width:46px;height:46px;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0}
#cb-send:active{background:#2d3f6e}
#cb-mic{background:#f0f3fa;color:#1a2540;border:1px solid #cbd5e1;border-radius:50%;width:46px;height:46px;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0}
#cb-mic.listening{background:#e53935;color:#fff;border-color:#e53935;animation:cb-pulse 1s infinite}
@keyframes cb-pulse{0%,100%{box-shadow:0 0 0 0 rgba(229,57,53,.4)}50%{box-shadow:0 0 0 8px rgba(229,57,53,0)}}
</style>
</head>
<body>
<div id="cb-hdr">
    <div class="dot"></div>
    Project Assistant
</div>
<div id="cb-msgs">
    <div class="cb-msg bot">I am your project assistant. Ask me anything about your projects — costs, schedule, resources, or progress — and I will provide the information you need for your decisions.</div>
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
    // Session id: one per browser tab open, persists for this conversation window
    var sessionId = (function(){
        var k = 'cb_session_id';
        var s = sessionStorage.getItem(k);
        if (!s) { s = Math.random().toString(36).slice(2) + Date.now().toString(36); sessionStorage.setItem(k, s); }
        return s;
    })();

    /* Voice input */
    var micBtn = document.getElementById('cb-mic');
    var listening = false;
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if(SpeechRecognition){
        var recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-IN';
        recognition.onstart = function(){ listening = true; micBtn.classList.add('listening'); micBtn.title = 'Tap to stop'; };
        recognition.onend   = function(){
            listening = false;
            micBtn.classList.remove('listening');
            micBtn.title = 'Speak';
        };
        recognition.onerror = function(e){
            listening = false;
            micBtn.classList.remove('listening');
            micBtn.title = 'Speak';
            if(e.error === 'not-allowed'){
                addMsg('Microphone access denied. Please allow mic permission in your browser settings.', 'bot');
            } else if(e.error === 'no-speech'){
                /* silent */
            } else {
                addMsg('Mic error: ' + e.error, 'bot');
            }
        };
        recognition.onresult = function(e){
            var final = '';
            for(var i = e.resultIndex; i < e.results.length; i++){
                if(e.results[i].isFinal) final += e.results[i][0].transcript;
            }
            if(final){ inp.value = final.trim(); }
        };
        micBtn.addEventListener('click', function(){
            if(listening){ recognition.stop(); }
            else { try{ recognition.start(); } catch(ex){} }
        });
    } else {
        micBtn.title = 'Voice not supported in this browser';
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
        xhr.send('message=' + encodeURIComponent(text) + '&history=' + encodeURIComponent(JSON.stringify(priorHistory)) + '&session_id=' + encodeURIComponent(sessionId));
    }

    function addMsg(text, cls){
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
