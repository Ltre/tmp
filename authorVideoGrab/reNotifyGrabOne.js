//重新通知需要抓取的视频url_task_id

window.LtreLib = window.LtreLib || {};//使用一个生僻的名称作为全局变量，以存储自定义的库，防止与其它变量冲突
/**
 * 延迟器
 * @param opt
 * @returns {LtreLib.timing}
 * @example
 * 用法一：
 *      //恒频延迟器（定时器）
 *      LtreLib.timing({
 *          delay: 100,
 *          onStart: function(opt){
 *              console.log('start');
 *          },
 *          onTiming: function(opt){
 *              console.log('timing');
 *          },
 *          onStop: function(opt){
 *              console.log('stop');
 *          }
 *      });
 *      //变频延迟器
 *      LtreLib.timing({
 *          delay: 100,
 *          amplTop: +200,
 *          amplBot: -1000,
 *          onStart: function(opt){
 *              console.log('start');
 *          },
 *          onTiming: function(opt){
 *              console.log('timing');
 *          },
 *          onStop: function(opt){
 *              console.log('stop');
 *          }
 *      });
 * 用法二：
 *      //外部干预计时
 *      var timingObj = new LtreLib.timing({
 *          delay: 100,
 *          onStart: function(opt){
 *              console.log('start');
 *          },
 *          onTiming: function(opt){
 *              console.log('timing');
 *          },
 *          onStop: function(opt){
 *              console.log('stop');
 *          }
 *      });
 *      timingObj.ctrl.goTo = 12;//跳至12
 *      timingObj.ctrl.goPause = true;//暂停计时
 *      timingObj.ctrl.goPause = false;//恢复计时
 *      timingObj.ctrl.goStop = true;//终止
 */
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


var urlTaskIdList = [89406,89409,89419,89420,89421,89425,89426,89427,89428,89429,89433,89434,89436,89438,89439,89440,89443,89446,89453,89454,89456,89459,89462,89464,89466,89469,89470,89471,89472,89474,89475,89477,89479,89482,89483,89484,89486,89488,89489,89490,89491,89492,89493,89494,89495,89496,89497,89498,89499,89500,89501,89503,89504,89506,89508,89510,89511,89512,89513,89515,89516,89517,89518,89523,89525,89526,89529,89530,89531,89532,89535,89536,89537,89538,89539,89540,89542,89543,89544,89545,89546,89547,89549,89552,89553,89554,89556,89560,89562,89564,89567,89569,89571,89572,89573,89574,89575,89576,89577,89581,89582,89585,89586,89589,89590,89594,89596,89597,89600,89601,89602,89606,89607,89608,89609,89615,89616,89618,89619,89620,89621,89622,89623,89626,89627,89633,89634,89642,89643,89649,89650,89653,89654,89657,89658,89659];
LtreLib.timing({
    delay: 100,
    a: 0,
    z: urlTaskIdList.length - 1,
    onStart: function(opt){
    },
    onTiming: function(opt){
        var taskId = urlTaskIdList[opt.i];
        var url = 'http://grab-v.duowan.com/api/NotifyOneVideo?__hehe__=1&eventCode=video_grab_create&urlTaskId=' + taskId;
        (new Image).src = url;
        console.log(opt.i, url);
    },
    onStop: function(opt){
    }
});


setTimeout(function(){    
    LtreLib.timing({
        delay: 100,
        a: 0,
        z: urlTaskIdList.length - 1,
        onStart: function(opt){
        },
        onTiming: function(opt){
            var taskId = urlTaskIdList[opt.i];
            var url = 'http://grab-v.duowan.com/api/NotifyOneVideo?__hehe__=1&eventCode=video_grab_success&urlTaskId=' + taskId;
            (new Image).src = url;
            console.log(opt.i, url);
        },
        onStop: function(opt){
        }
    });
}, 10);