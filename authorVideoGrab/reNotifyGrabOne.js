//����֪ͨ��Ҫץȡ����Ƶurl_task_id

window.LtreLib = window.LtreLib || {};//ʹ��һ����Ƨ��������Ϊȫ�ֱ������Դ洢�Զ���Ŀ⣬��ֹ������������ͻ
/**
 * �ӳ���
 * @param opt
 * @returns {LtreLib.timing}
 * @example
 * �÷�һ��
 *      //��Ƶ�ӳ�������ʱ����
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
 *      //��Ƶ�ӳ���
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
 * �÷�����
 *      //�ⲿ��Ԥ��ʱ
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
 *      timingObj.ctrl.goTo = 12;//����12
 *      timingObj.ctrl.goPause = true;//��ͣ��ʱ
 *      timingObj.ctrl.goPause = false;//�ָ���ʱ
 *      timingObj.ctrl.goStop = true;//��ֹ
 */
LtreLib.timing = function(opt){
    opt.a           = opt.a || 0;//��ʼ
    opt.z           = opt.z || 100;//����
    opt.step        = opt.step || +1;//����
    opt.delay       = opt.delay || 10;//�ӳ�(����)
    opt.amplTop     = opt.amplTop || +0;//�ӳ�����(���룬�����趨��Ƶ�ӳ���)
    opt.amplBot     = opt.amplBot || -0;//�ӳټ���(���룬�����趨��Ƶ�ӳ���)
    opt.onStart     = opt.onStart || function(i){};//����ʱ
    opt.onTiming    = opt.onTiming || function(i){};//����ʱ
    opt.onStop      = opt.onStop || function(i){};//����ʱ
    opt.i = opt.a;
    
    var innerThat = this;
    this.ctrl = {goPause:false, goStop:false, goFirst:false, goLast:false, goPrev:false, goNext:false, goTo:false};
    ~ function f(){
        if (opt.i <= opt.z) {
            //������ʱ����
            var randAmpl = opt.amplBot + Math.random() * (opt.amplTop - opt.amplBot);
            setTimeout(f, opt.delay + randAmpl);
            
            //�ⲿ��Ԥ{innerThat.ctrlʵ��}
            if (innerThat.ctrl.goPause) {
                return;//��ͣ������֤�´οɴ���ͣλ�ü���
            }
            if (innerThat.ctrl.goStop) {
                opt.i = opt.z + opt.step;
                return;//��ֹ�������κ�ָ��޷��ָ�
            }
            if (innerThat.ctrl.goFirst) {
                innerThat.ctrl.goFirst = false;
                opt.i = opt.a;
                return;//������ʼ
            }
            if (innerThat.ctrl.goLast){
                innerThat.ctrl.goLast = false;
                opt.i = opt.z;
                return;//����ĩβ
            }
            if (innerThat.ctrl.goPrev) {
                innerThat.ctrl.goPrev = false;
                opt.i -= opt.step;
                return;//�����ϴ�
            }
            if (innerThat.ctrl.goNext) {
                innerThat.ctrl.goNext = false;
                opt.i += opt.step;
                return;//�����´�
            }
            if ('number' == typeof innerThat.ctrl.goTo && opt.a <= innerThat.ctrl.goTo && innerThat.ctrl.goTo <= opt.z) {
                opt.i = innerThat.ctrl.goTo;
                innerThat.ctrl.goTo = false;
                return;//����ָ��λ��
            }
            
            //����ִ�в���
            var copiedOpt = JSON.parse(JSON.stringify(opt));//��������������ֹ���̵�delay����ѭ�����������opt
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