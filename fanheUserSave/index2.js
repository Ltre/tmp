//粘贴到此处执行：http://fanhe.admin.duowan.com/mediaAccount/main?_nodeId=519


function loadScript(src, onload, confirmLoad, win){
    if ('string' != typeof src || ! src.match(/^(https?\:)?\/\/\w+\./))
    onload = 'function' == typeof onload ? onload : function(win){};
    confirmLoad = 'function' == typeof confirmLoad ? confirmLoad : function(win){return true;};
    win = win || window;
    if (! confirmLoad(win)) {
	    var doc = win.document;
		var je = doc.createElement("script"); 
		je.setAttribute("type", "text/javascript"); 
		je.setAttribute("src", src);
		var heads = doc.getElementsByTagName("head"); 
		if (heads.length) {
			heads[0].appendChild(je);
		} else {
			doc.documentElement.appendChild(je);
		}
    }
	var iv = setInterval(function(){
        confirmLoad(win) && ! function(){
            clearInterval(iv);
            if ('function' == typeof onload) {
                onload(win);
            }
        }();
    }, 10);
}

function showTip(msg, autoHide){
    autoHide = autoHide === undefined ? true : autoHide;
    var t = $('#diy-tip');
    t.width($('body').width());
    t.html(msg).show();
    if (autoHide) {
        ~ function(t){
            setTimeout(function(){
                t.hide();
            }, 500);
        }(t);
    }
}

loadScript('https://cdn.bootcss.com/jquery/1.9.1/jquery.min.js', function(w){
    loadScript("http://res.miku.us/res/js/timing.js", function(ww){
        
        $('body').append('<div id="diy-tip" style="display: none; position: fixed; top: 10px; z-index: 100; background-color:aliceblue; color: orangered; font-size: 36px; font-weight: bold; text-align: left;"></div>');
        var fids = [];
        $('#media_id>option').each(function(i, e){
            var url = 'http://fanhe.admin.duowan.com'+$(e).data('url');
            var fid = 'if' + (+ new Date);
            fids.push(fid);
            $('body').append('<iframe id="'+fid+'" src="'+url+'"></iframe>');
        });
        showTip('等待后台进行加载...', false);
        
        Ltrelib.timing({
            a: 0,
            z: fids.length - 1,
            delay: 250,
            onTiming: function(opt){
                var iv = setInterval(function(){                    
                    var fid = fids[opt.i];
                    console.log({fid: fid})
                    var win = window[fid].contentWindow;
                    var doc = win.document;
                    var saveBtn = doc.getElementById('save');
                    if (saveBtn) {
                        showTip('子页面['+fid+']加载成功，等待执行保存...', false);
                        setTimeout(function(){//加载好页面后，为确保点击可以成功保存，需等待子页面js执行完毕
                            doc.body.scrollIntoView();
                            saveBtn.click();
                            showTip('点击保存'+fid, false);
                            fids = fids.splice(opt.i, 1);
                            showTip('完成保存任务[ID=' + fid + ']', false);
                            clearInterval(iv);
                        }, 3000);
                    }
                }, 200);
            },
            onStop: function(opt){
                var fuck = setInterval(function(){
                    if (fids.length == 0) {
                        showTip('全部搞定啦！', false);
                        clearInterval(fuck);
                    }
                }, 200);
            }
        });
        
    }, function(ww){
        return ('Ltrelib' in ww) && ('timing' in ww.Ltrelib);
    });
}, function(w){
    return 'jQuery' in w;
}, window);
