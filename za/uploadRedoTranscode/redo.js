//查2018-01-26 10:00:00之后同步失败、转码失败、发布失败的视频，点击重新转码
//将代码粘贴到类似http://cloud.v.duowan.com/index.php?r=video/view&id=8737413的页面即可

window.LtreLib = window.LtreLib || {};//使用一个生僻的名称作为全局变量，以存储自定义的库，防止与其它变量冲突
LtreLib.timing = function(opt){
    opt.a           = opt.a || 0;//开始
    opt.z           = opt.z || 100;//结束
    opt.step        = opt.step || +1;//步长
    opt.delay       = opt.delay || 10;//延迟(毫秒)
    opt.amplTop     = opt.amplTop || +0;//延迟增幅(毫秒，用于设定变频延迟器)
    opt.amplBot     = opt.amplBot || -0;//延迟减幅(毫秒，用于设定变频延迟器)
    opt.onStart     = opt.onStart || function(i){};//启动时
    opt.onTiming    = opt.onTiming || function(i){};//进行时
    opt.onStop      = opt.onStop || function(i){};//结束时
    opt.i = opt.a;
    
    var innerThat = this;
    this.ctrl = {goPause:false, goStop:false, goFirst:false, goLast:false, goPrev:false, goNext:false, goTo:false};
    ~ function f(){
        if (opt.i <= opt.z) {
            //触发延时过程
            var randAmpl = opt.amplBot + Math.random() * (opt.amplTop - opt.amplBot);
            setTimeout(f, opt.delay + randAmpl);
            
            //外部干预{innerThat.ctrl实现}
            if (innerThat.ctrl.goPause) {
                return;//暂停，并保证下次可从所停位置继续
            }
            if (innerThat.ctrl.goStop) {
                opt.i = opt.z + opt.step;
                return;//终止，其它任何指令都无法恢复
            }
            if (innerThat.ctrl.goFirst) {
                innerThat.ctrl.goFirst = false;
                opt.i = opt.a;
                return;//跳至开始
            }
            if (innerThat.ctrl.goLast){
                innerThat.ctrl.goLast = false;
                opt.i = opt.z;
                return;//跳至末尾
            }
            if (innerThat.ctrl.goPrev) {
                innerThat.ctrl.goPrev = false;
                opt.i -= opt.step;
                return;//跳至上次
            }
            if (innerThat.ctrl.goNext) {
                innerThat.ctrl.goNext = false;
                opt.i += opt.step;
                return;//跳至下次
            }
            if ('number' == typeof innerThat.ctrl.goTo && opt.a <= innerThat.ctrl.goTo && innerThat.ctrl.goTo <= opt.z) {
                opt.i = innerThat.ctrl.goTo;
                innerThat.ctrl.goTo = false;
                return;//跳至指定位置
            }
            
            //核心执行部分
            var copiedOpt = JSON.parse(JSON.stringify(opt));//这里作拷贝，防止过短的delay导致循环体错读到别的opt
            opt.a == opt.i && opt.onStart(copiedOpt, copiedOpt);
            opt.onTiming(copiedOpt);
            opt.z == opt.i && opt.onStop(copiedOpt);
        }
        opt.i += opt.step;
    }();
};


function redo(vid, ifrId){
    (new Image).src = 'http://cloud.v.duowan.com/index.php?r=video/handle&action=upload%2Fprobe&vid=' + vid;
    setTimeout(function(){        
        var url = "http://cloud.v.duowan.com/index.php?r=video/view&id=" + vid;
        $('body').append('<iframe id="'+ifrId+'" name="'+ifrId+'"></iframe>');
        $('#'+ifrId).attr('src', url);
        //debugger;
        //打开视频新详细页
        $('#'+ifrId)[0].onload = function(){
            //debugger;
            var currIfrObj = $('#'+ifrId)[0];
            var subDoc = currIfrObj.contentDocument;
            var subWin = currIfrObj.contentWindow;
            var subJQ = subWin.$;
            var btns = subJQ('span.button.button-mini.cyan:contains("重转")');
            $.each(btns, function(i, e){
                //debugger;
                var ifrId = '_' + (+new Date) + '_' + parseInt(Math.random()*1000);
                subJQ('body').append('<iframe id="'+ifrId+'" name="'+ifrId+'"></iframe>');
                var sub2win = subJQ('#'+ifrId)[0].contentWindow;
                var subIfr = subJQ('#'+ifrId)[0];
                //依次打开视频每个方案的[重新转码操作页]
                subIfr.onload = function(){
                    //debugger;
                    var btn = sub2win.$('button.button.button-large.cyan:contains("重新转码")');
                    sub2win.alert = function(str){
                        console.log("curr vid="+vid+", alert: " + str);
                        $('#'+ifrId).remove();//确认一个视频的所有转码已全部处理，将清理现场
                    };
                    btn.click();
                };
                subIfr.setAttribute('src', e.getAttribute('href'));
            });
        };
    }, 1000);
}



var vids = [8737413, 8739259, 8739263, 8737919, 8739237, 8737891, 8737879, 8739243, 8737909, 8737929, 8737669, 8737911, 8737771, 8737843, 8737963, 8739267, 8737897, 8731121, 8737895, 8737941, 8739297, 8739299, 8739301, 8737969, 8739223, 8737959, 8737913, 8737951, 8731243, 8731135, 8737915, 8737815, 8737923, 8737967, 8737983, 8737981, 8737945, 8738015, 8737953, 8739337, 8737975, 8731707, 8731765, 8732027, 8748461, 8731887, 8731231, 8732033, 8737933, 8738047, 8737889, 8738031, 8738053, 8738059, 8738027, 8738051, 8737921, 8738069, 8737985, 8738025, 8738029, 8738055, 8738009, 8738039, 8738099, 8738105, 8738089, 8737831, 8738081, 8737989, 8737935, 8738139, 8738143, 8748521, 8748529, 8748509, 8748553, 8737949, 8748519, 8738127, 8738137, 8748523, 8738095, 8748539, 8748547, 8730509, 8737977, 8737979, 8737987, 8737583, 8737659, 8737749, 3559051, 3526677, 6097273, 6097519, 8738987, 8739079, 8739055, 8739061, 8738739, 3594726, 8748951, 8423247, 8423249, 8423251, 8423253, 8586445, 8592007, 8590737, 8748527, 8748541, 8748545, 8748549, 8748559, 8748563, 8732851, 8737325, 8748467, 8748555, 8732125, 8732351, 8732687, 8737331, 8731223, 8731237, 8731239, 8731271, 8731495, 8731629, 8731671, 8731685, 8731689, 8731701, 8739333, 8748449, 8731125, 8739209, 8739281, 8739293, 8739303, 8739307, 8739323, 8739325, 8739331, 8748445, 8731095, 8739197, 8739241, 8739251, 8739261, 8739265, 8739269, 8739273, 8739275, 8739283, 8739285, 8739289, 8739291, 8739247, 8739249, 8739257, 8739185, 8739187, 8739189, 8739193, 8739213, 8739221, 8739231, 8739129, 8739163, 8643319, 8643777, 8643321, 8640475, 8640477, 8640775, 8748457, 8739205];
var vids = [8737413, 8739259, 8739263, 8737919, 8739237];//debug
var len = vids.length;
LtreLib.timing({
    a: 0,
    z: len - 1,
    step: 1,
    delay: 2000, //限定一分钟最多30个，不要过快
    onTiming: function(opt){
        var vid = vids[opt.i];
        console.log('opt.i: '+opt.i + ', vid: ' + vid);
        redo(vid, 'abcdefghijklmn_' + vid);
    }
});
