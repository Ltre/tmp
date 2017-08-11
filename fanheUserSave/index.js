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
showTip('等待后台进行5秒加载...', true);
setTimeout(function(){
    fids.forEach(function(e, i){
        var iv = setInterval(function(){
            var saveBtn = window[e].contentWindow.document.getElementById('save');
            console.log(e, saveBtn);
            if (saveBtn) {
                saveBtn.click();
                showTip('点击保存'+e);
                var idx=fids.indexOf(e); 
                //if (idx !== -1) {
                    //fids = fids.splice(idx, 1);
                    showTip('完成保存任务[ID=' + e + ']', false);
                    clearInterval(iv);
                //}
            }
        }, 200);
    });
    setInterval(function(){
        if (fids.length == 0) {
            showTip('全部搞定啦！', true);
        }
    }, 200);
}, 5000);
