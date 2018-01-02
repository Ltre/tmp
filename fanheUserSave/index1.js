//粘贴到此处执行：http://fanhe.admin.duowan.com/mediaAccount/main?_nodeId=519

$('body').append('<div id="diy-tip" style="display: none; position: fixed; top: 10px; z-index: 100; background-color:aliceblue; color: orangered; font-size: 36px; font-weight: bold; text-align: left;"></div>');
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
var fids = [];
$('#media_id>option').each(function(i, e){
    var url = 'http://fanhe.admin.duowan.com'+$(e).data('url');
    var fid = 'if' + (+ new Date);
    fids.push(fid);
    $('body').append('<iframe id="'+fid+'" src="'+url+'"></iframe>');
});
showTip('等待后台进行加载...', false);
setTimeout(function(){
    var finishCount = 0;
    fids.forEach(function(e, i){
        var lockSave = false;
        var iv = setInterval(function(){
            var win = window[e].contentWindow;
            var doc = win.document;
            var saveBtn = doc.getElementById('save');
            if (saveBtn && ! lockSave) {
                lockSave = true;
                showTip('子页面['+e+']加载成功，等待执行保存...', false);
                setTimeout(function(){//加载好页面后，为确保点击可以成功保存，需等待子页面js执行完毕
                    doc.body.scrollIntoView();
                    saveBtn.click();
                    showTip('点击保存'+e, false);
                    debugger
                    finishCount ++;
                    console.log({finishCount: finishCount});
                    showTip('完成保存任务[ID=' + e + '], ' + (finishCount/fids.length)*100 + '%', false);
                    clearInterval(iv);
                    lockSave = false;
                }, 1000);
            }
        }, 3000);
    });
    var fuck = setInterval(function(){
        if (finishCount == fids.length) {
            showTip('全部搞定啦！', false);
            clearInterval(fuck);
        }
    }, 200);
}, 5000);
