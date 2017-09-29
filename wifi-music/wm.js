window.FM = {
    audio: $('audio').get(0),
    playPolicy: 0, //顺序0 列表循环1 单曲循环2 随机3 @TODO：未开发
    randList: [], //随机播放时，曲目的播放序列，元素取自history的下标 @TODO: 未开发，可能不需要该属性，直接在next()中使用随机数切换
    playState: 0,//从未播放0 正在播放1 已暂停2 已停止3
    currentPlay : -1, //加载页面后，从未进行播放时，默认为-1
    history : [], //载入曲目时，压入的列表，即原列表
    // isImporting : false //后台是否正在采集新曲目
};




//定时监测播放状态，符合条件即切换下一曲。载入页面后立即开始，每秒检测500次
FM.autoNext = function(){
    var f = function(){
        var d = FM.audio.duration;
        var c = FM.audio.currentTime;
        if (!! d && c >= d) {
            FM.next();//随机、顺序、循环的控制，交给next()控制 @TODO：待开发
        }
    };
    setInterval(f, 2);
};


//【SOCKET版,正在使用】定时监听来自移动设备发往服务端的控制命令
FM.listenRemoteCmd = function(){
    /*
     * 被控客户端生成token，拼接用于主控客户端所需访问的链接[http://xxx/fm/setCmd/{$token}](以下简称CL, Control link)
     * 主控客户端首次访问CL接口，服务端会按token创建一个独立的命令存储文件。
     * 在CL接口中，参数依次是：token, 控制类型, 控制值。CL接口执行时，会向文件中写入当前命令及执行状态
     * 被控客户端向服务端的getCmd接口发送token，链接为[http://xxx/fm/getCmd]，以获取当前命令
     * 被控客户端获取到命令并执行后，会告知服务端已执行，服务端会对此作好标记
     */
    //准备特技后勤工作
    var enable = false;
    $('#enable-duang').on('click', function(){
        if ($(this).data('enabled')) {
            $(this).text('启动特技').data('enabled', false);
            $('#duang').css('display','none'); 
            enable = false;
            //del jinguanyu
            $('#duang-jinguanyu-js').remove();
            $('[id^=anime]').remove();
        } else {
            $(this).text('关闭特技').data('enabled', true);
            $('#duang').css('display','');
            enable = true;
            //add jinguanyu
            $(document.body).append('<script id="duang-jinguanyu-js" src="res/lib/anime.js?v=20150513"></script>');
        }
    });
    $('#duang').on('mouseover', function(){
        $('#qrcode').css('display','').qrcode({width:300,height:300,text:location.protocol+'//'+location.host+'/?fm/ctrlview/'+token});
    }).on('mouseout', function(){
        $('#qrcode').css('display','none').html('');
    });
    //特技开始实现
    var token = location.hash.replace('#','') || -(-new Date());//token也可手动用fragment生成
    var u = 'http://io.yooo.moe:3000/';
    var socket = io.connect(u);
    socket.emit('fm/regCmd', token);
    socket.on('fm/acceptCmd', function(rToken, type, value){
        if (token != rToken) return;
        switch (type) {
            case 0: break;
            case 1:
            case 2: FM.pause();break;
            case 3: FM.next();break;
            case 4: FM.prev();break;
            case 5: FM.switchSong(value);break;
            case 6: FM.favorite();break;
            case 7: FM.increaseVolume();break;
            case 8: FM.reduceVolume();break;
        }
    });
};



//监听单曲地址提交
FM.listenSubmitSong = function(){
    $('#submit-new-song').click(function(){
        $(this).data('clicked') || $(this).after('<iframe name="subSong" style="width:1px;height:1px;display:inline-block;"></iframe><form target="subSong" method="get" style="display:inline-block;"><input type="hidden" name="audio/parse5sing"><input type="text" name="addr"><input type="submit"></form>');
        $(this).data('clicked', true);
        $(this).siblings('iframe').show();
        $(this).siblings('form:first').children('input[name=addr]:first').val('').focus();
        $(this).siblings('form:first').show().submit(function(){
            var _form = $(this);
            _form.siblings('iframe').hide(); 
            _form.hide().unbind('submit');
            clearInterval(_form.data('lastInv')); //清除上次提交时产生的定时监测任务
            var inv = setInterval(function(){
                try {
                    var response = eval('('+window['subSong'].document.body.innerText+')');
                    var songObj = undefined == response.data ? {} : response.data;
                    console.log(songObj);
                    if (songObj.file) {
                        _form.data('submitted') || _form.after('<a href="' + songObj.file + '" target="_blank" class="btn btn-info btn-sm">点此下载</a>');
                        _form.data('submitted', true);
                    }
                } catch (e){}
            }, 100);
            _form.data('lastInv', inv);
        });
    });
};


//播放列表添加新曲(UI)
FM.pushList = function(newSong){
    var lastPos = FM.history.length-1<0?0:FM.history.length-1;
    var newSong = newSong || FM.history[lastPos];
    if (! newSong) {
        return;
    }
    
    var tpl = $('#tpl-history').get(0).outerHTML;
    $('#play-list').append(tpl);
    //找到最新添加的行，去除id，并标注曲目id，最后呈现
    var newLine = $('[id=tpl-history]').not('[id=tpl-history]:first').last();
    newLine.removeAttr('id').attr('data-history-pos', lastPos).css({'cursor':'pointer'}).removeClass('hide');
    newLine.children(':not(.fm-share)').click(function(){ var pos = $(this).parent('div').first().attr('data-history-pos'); pos = parseInt(pos); var dontReload = FM.currentPlay == pos; FM.currentPlay = pos; FM.switchSong(pos, dontReload); }); //切换到被点击歌曲
    newLine.children('.fm-share').click(function(){ window.open('http://5sing.kugou.com/'+newSong.songtype+'/'+newSong.songid+'.html', '_blank'); });//跳转到5sing曲目详情页面
    
    var avatar = newLine.find('.fm-avatar:first>img:first');
    var singer = newLine.find('.fm-singer:first');
    var songname = newLine.find('.fm-songname:first');
    var duration = newLine.find('.fm-duration:first');
    avatar.attr('src', newSong.avatar);
    singer.text(newSong.singer);
    songname.text(newSong.songname);
    duration.text('00:00');
};


//随机加载单曲入队
FM.loadOne = function(){
    if (! $('tpl[name=loadOne]').size() > 0) return false;
    var url = './?fm/loadOne';
    $.ajax({
        type: 'GET',
        async: false,
        url: url,
        dataType: 'jsonp',
        success: function(j){
            if (0 === j.code) {
                FM.history.push(j.data);
                FM.pushList();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            console.log(['throw an error::', XMLHttpRequest, textStatus, errorThrown]);
        }
    });
    return true;
};


//切换播放状态，更改按钮和状态的显示
FM.switchState = function(state){
    FM.playState = state;
    var b = $('audio').get(0);
    var playControl = $('.song-pause');
    var toPlay;
    switch (state) {
        case 0: toPlay = true;b.pause();break;
        case 1: toPlay = false;b.play();break;
        case 2: toPlay = true;b.pause();break;
        case 3: toPlay = true;b.pause();break;
    }
    if (toPlay) {
        playControl.eq(1).addClass('hide');
        playControl.eq(0).removeClass('hide');
    } else {
        playControl.eq(0).addClass('hide');
        playControl.eq(1).removeClass('hide');
    }
};


//切换歌曲并渲染播放
FM.switchSong = function(pos, dontReload){
    var pos = pos || FM.currentPlay;
    var playData = FM.history[pos];
    console.log(['render:playData::', playData, 'pos::', pos]);
    if (undefined != playData) {
        var audio = $('audio:eq(0)');
        if (true !== dontReload || ! audio.attr('src')) {
            $('.current-song>marquee:eq(0)').text(playData.songname);
            audio.attr('src', playData.file);
            $('[data-history-pos='+pos+']:first')[0].scrollIntoViewIfNeeded();
        }
        audio.get(0).play();
        FM.switchState(1);
    }
};


//动作：上一曲
FM.prev = function(){
    if (FM.currentPlay - 1 >= 0) {
        FM.currentPlay --;
        FM.switchSong();
    }
};


//动作：下一曲
FM.next = function(){
    var next = FM.currentPlay + 1;
    var oldLen = FM.history.length;
    var newLen = oldLen + 1;
    console.log(['next, oldLen, newLen',next, oldLen, newLen, next<newLen]);
    if (next < newLen) {
        FM.currentPlay ++;
        if (next >= oldLen && ! FM.loadOne()) {
            FM.currentPlay --;
            return;//如果需要载入新曲目，但载入失败，则不会执行下面的switchSong()，并且将当前曲目索引复原
        }
    }
    FM.switchSong();
};


//动作：暂停
FM.pause = function(){
    switch (FM.playState) {
    case 1: FM.switchState(2);break;
    case 0:
    case 2:
    case 3:
        0 === FM.history.length && FM.loadOne();
        -1 === FM.currentPlay && (FM.currentPlay = 0);
        FM.switchSong(FM.currentPlay, true);//暂停后再播放不需要切换歌曲
    }
};


//动作：增加音量
FM.increaseVolume = function(){
    FM.audio.volume + 0.2 <= 1 && (FM.audio.volume += 0.2);
};


//动作：减少音量
FM.reduceVolume = function(){
    FM.audio.volume - 0.2 >= 0 && (FM.audio.volume -= 0.2);
};


//加入喜好，待开发
FM.favorite = function(){
    $.get('./?fm/favorite', FM.history[FM.currentPlay], function(j){}, 'jsonp');
};


//UI初始化
FM.initUI = function(){
    var playMain = '#play-main';
    put_in_center(playMain, $(playMain).html());
    $('.prev-song').click(FM.prev);
    $('.next-song').click(FM.next);
    $('.song-pause').click(FM.pause);
};


(FM.init = function(){
    FM.initUI();
    //FM.collect();//定时收集曲目(暂停该任务)
    FM.autoNext();//确保能自动下一曲
    FM.listenRemoteCmd();//监听远程设备控制
    FM.listenSubmitSong();//监听收录单曲
})();