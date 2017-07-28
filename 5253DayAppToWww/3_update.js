//在cms.duowan.com域名下执行：
//找到contentIframe的frame，加上ID abcdefghijklmn

var list = [
    {rawUrl: 'http://www.5253.com/fdfs.html', artiUrl: 'http://cms.duowan.com/article/toEditArticlePage.do?articleId=365195995429&channelId=ceshi', time: '2015-01-01 00:00:00'},
    {rawUrl: 'http://www.5253.com/fdfs.html', artiUrl: 'http://cms.duowan.com/article/toEditArticlePage.do?articleId=365195995429&channelId=ceshi', time: '2015-01-01 00:00:00'}
    /*{rawUrl: 'http://www.5253.com/fdfs.html', artiUrl: 'http://cms.duowan.com/article/toEditArticlePage.do?articleId=365195995429&channelId=ceshi', time: '2015-01-01 00:00:00'},
    {rawUrl: 'http://www.5253.com/fdfs.html', artiUrl: 'http://cms.duowan.com/article/toEditArticlePage.do?articleId=365195995429&channelId=ceshi', time: '2015-01-01 00:00:00'},
    {rawUrl: 'http://www.5253.com/fdfs.html', artiUrl: 'http://cms.duowan.com/article/toEditArticlePage.do?articleId=365195995429&channelId=ceshi', time: '2015-01-01 00:00:00'},
    {rawUrl: 'http://www.5253.com/fdfs.html', artiUrl: 'http://cms.duowan.com/article/toEditArticlePage.do?articleId=365195995429&channelId=ceshi', time: '2015-01-01 00:00:00'},
    {rawUrl: 'http://www.5253.com/fdfs.html', artiUrl: 'http://cms.duowan.com/article/toEditArticlePage.do?articleId=365195995429&channelId=ceshi', time: '2015-01-01 00:00:00'} */
];

function doit(o, cb){
    document.getElementById('abcdefghijklmn').setAttribute('src', o.artiUrl);
    setTimeout(function(){
        var win = window['abcdefghijklmn'].contentWindow;
        var doc = window['abcdefghijklmn'].contentDocument;
        doc.getElementById('newPublishTimeStr').value = o.time;
        win.updatePublishTime();
        setTimeout(function(){
            cb();
        }, 1000);
    }, 1000);
}

function cb(){
    if (++i < list.length) {
        doit(list[i], cb);
    }
}

i = 0;
doit(list[i], cb);
