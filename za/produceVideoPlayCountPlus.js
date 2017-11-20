function loadToLoad(cb){
    var e = document.createElement('script');
    e.setAttribute('type', 'text/javascript');
    e.setAttribute('src', '//res.miku.us/res/js/loadScript.js');
    document.body.appendChild(e);
    var iv = setInterval(function(){
        if ('Ltrelib' in window && 'loadScript' in window.Ltrelib) {
            clearInterval(iv);
            if (typeof cb == 'function') {
                cb.call(this, window.Ltrelib.loadScript);
            }
        }
    }, 200);
}


loadToLoad(function(loadScript){
    loadScript('http://assets.dwstatic.com/common/lib/jquery/1.11.3/jquery-1.11.3.min.js', function(){
        loadScript('//res.miku.us/res/js/timing.js', function(){

            //---------------------------------------------------
            var monkeyProduceBtn = '__monkey_produce_dwplaycount';
            var h = '<button id="'+monkeyProduceBtn+'" style="position:fixed; right:0; top:0; font-size:28px; z-index: 9999999;">增加播放量!<span></span></button>';
            $('body').append(h);
            $('#'+monkeyProduceBtn).click(function(){
                $(this).css('cursor', 'not-allowed').unbind('click');
                var goal = parseInt(prompt('输入目标[真实]播放量 (目前假播放量 = 真实值*3.1 + 用vid计算好的1000以内初始化值。请自行估算)', 10000));
                var vid = prompt('输入vid', (location.href.match(/\/(v|new|test)?play\/(\d+)(\-\d+)?\.html/)||[null,null,0])[2]);
                var articleId = (location.href.match(/\/(v|new|test)?play\/\d+(\-(\d+))?\.html/)||[null,null,''])[2];
                var channel = prompt('专区ID', 'lol');
                var loop = 100;
                var delay = 1000;//每loop次循环，休息delay毫秒
                var laiyuanv3='oldweb';
                var baseUrl = 'http://playstats.v.duowan.com/index.php?referrer=&laiyuanv3='+laiyuanv3+'&r=play%2Fload&vid='+vid+'&type=web&channelId='+channel+'&source_url=http%3A%2F%2Fvideo%2Eduowan%2Ecom%2Fplay%2F'+vid+'%2Ehtml%3Ft=';

                function getRealPlayCount(vid, cb){
                    var url = 'http://playstats.v.duowan.com/index.php?r=api/get&vid='+vid+'&nocache=1';
                    $.get(url, function(j){
                        if (vid in j) {
                            cb(j[vid]);
                        } else {
                            alert('can not get real play count!');
                        }
                    }, 'jsonp');
                }

                getRealPlayCount(vid, function(count){
                    if (goal > count) {
                        Ltrelib.timing({
                            a: 1,
                            z: Math.ceil((goal - count) / loop),
                            delay: delay,
                            onTiming: function(opt){
                                var i = opt.i;
                                for (i = 0; i < loop; ++i) {
                                    (new Image()).src = baseUrl + (+ new Date()) + i;
                                }
                                console.log('opt.i = ' + opt.i + ', opt.z = ' + opt.z);
                                $('#'+monkeyProduceBtn).html('刷量中..<span></span>');
                                $('#'+monkeyProduceBtn).children('span').text('(' + opt.i + '/' + opt.z + ')').css('color', 'red');
                            },
                            onStop: function(opt){ //结束后，来点小甜品
                                alert('恭喜，vid = ' + vid + '刷量结束！还需要等待几分钟才能看到最新播放量，请耐心等待。现在进入页面刷新倒计时..');
                                Ltrelib.timing({
                                    a: 1,
                                    z: 25,
                                    delay: 1000,
                                    onTiming: function(opt){
                                        $('#'+monkeyProduceBtn).html('刷新计时中..<span></span>');
                                        $('#'+monkeyProduceBtn).children('span').text('(' + (opt.z - opt.i) + ')').css('color', 'red');
                                        if (opt.i == 10) {
                                            (new Image()).src = 'http://playstats.v.duowan.com/index.php?r=api/get&vid='+vid+'&nocache=1';
                                        }
                                        if (opt.i == 20) {
                                            var aid = articleId || $('#__ARTICLEID__').val();
                                            (new Image()).src = 'http://video.duowan.com/jsapi/playPageVideoInfo/?vids='+vid+'&articleIds='+aid+'&cache=update';
                                        }
                                        if (opt.i == 25) {
                                            location.href += (location.href.match(/\d+\.html$/) ? '?' : '&') + 'cache=update';
                                        }
                                    }
                                });
                            }
                        });
                    } else {
                        alert('已达到目标，不需要刷量');
                    }
                });
            });
            //---------------------------------------------------

        }, function(win){
            return 'Ltrelib' in win && 'timing' in win.Ltrelib;
        }, window);
    }, function(win){
        return 'jQuery' in win;
    }, window);
});
