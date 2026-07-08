<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Opiam Assistant</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f0f3fa;font-family:'Nunito',sans-serif;height:100vh;display:flex;flex-direction:column}
#cb-hdr{background:#1a2540;color:#fff;padding:14px 16px;font-size:18px;font-weight:600;display:flex;align-items:center;gap:10px;flex-shrink:0}
#cb-hdr .dot{width:10px;height:10px;border-radius:50%;background:#4ade80;flex-shrink:0}
#cb-msgs{flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:12px}
.cb-msg{max-width:85%;padding:10px 14px;border-radius:14px;font-size:16px;line-height:1.6;word-wrap:break-word;font-family:'Nunito',sans-serif}
.cb-msg.user{background:#1a2540;color:#fff;align-self:flex-end;border-bottom-right-radius:3px;font-weight:500}
.cb-msg.bot{background:#fff;color:#1a1a1a;align-self:flex-start;border-bottom-left-radius:3px;box-shadow:0 1px 4px rgba(0,0,0,.1)}
.cb-msg.typing{color:#888;font-style:italic;background:#fff}
#cb-foot{display:flex;gap:8px;padding:12px;background:#fff;border-top:1px solid #e8ecf4;flex-shrink:0}
#cb-input{flex:1;border:1px solid #cbd5e1;border-radius:24px;padding:10px 16px;font-size:16px;outline:none;font-family:'Nunito',sans-serif}
#cb-input:focus{border-color:#1a2540}
#cb-send{background:#1a2540;color:#fff;border:none;border-radius:24px;padding:10px 20px;font-size:16px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:'Nunito',sans-serif}
#cb-send:active{background:#2d3f6e}
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
    <input id="cb-input" type="text" placeholder="Ask a question..." autocomplete="off">
    <button id="cb-send">Send</button>
</div>
<script>
(function(){
    var history = [];
    var msgs = document.getElementById('cb-msgs');
    var inp  = document.getElementById('cb-input');

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
        xhr.send('message=' + encodeURIComponent(text) + '&history=' + encodeURIComponent(JSON.stringify(history.slice(0,-1))));
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
