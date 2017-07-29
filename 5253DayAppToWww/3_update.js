//在cms.duowan.com域名下执行：
//找到contentIframe的frame，加上ID abcdefghijklmn

var list = [
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152323.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284203095&channelId=ceshi","time":"2013-12-17 00:00:00","channel":"ceshi","articleId":"365284203095"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152320.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284204570&channelId=ceshi","time":"2013-12-16 00:00:00","channel":"ceshi","articleId":"365284204570"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152317.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284207025&channelId=ceshi","time":"2013-12-13 00:00:00","channel":"ceshi","articleId":"365284207025"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152314.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284208488&channelId=ceshi","time":"2013-12-12 00:00:00","channel":"ceshi","articleId":"365284208488"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152311.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284209996&channelId=ceshi","time":"2013-12-11 00:00:00","channel":"ceshi","articleId":"365284209996"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152308.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284211698&channelId=ceshi","time":"2013-12-10 00:00:00","channel":"ceshi","articleId":"365284211698"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152305.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284213148&channelId=ceshi","time":"2013-12-09 00:00:00","channel":"ceshi","articleId":"365284213148"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152302.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284214652&channelId=ceshi","time":"2013-12-06 00:00:00","channel":"ceshi","articleId":"365284214652"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152299.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284216063&channelId=ceshi","time":"2013-12-05 00:00:00","channel":"ceshi","articleId":"365284216063"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152296.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284217509&channelId=ceshi","time":"2013-12-04 00:00:00","channel":"ceshi","articleId":"365284217509"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152293.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284219019&channelId=ceshi","time":"2013-12-03 00:00:00","channel":"ceshi","articleId":"365284219019"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152290.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284220474&channelId=ceshi","time":"2013-12-02 00:00:00","channel":"ceshi","articleId":"365284220474"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152287.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284222241&channelId=ceshi","time":"2013-11-29 00:00:00","channel":"ceshi","articleId":"365284222241"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152284.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284223718&channelId=ceshi","time":"2013-11-28 00:00:00","channel":"ceshi","articleId":"365284223718"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152281.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284225378&channelId=ceshi","time":"2013-11-27 00:00:00","channel":"ceshi","articleId":"365284225378"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152278.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284226802&channelId=ceshi","time":"2013-11-26 00:00:00","channel":"ceshi","articleId":"365284226802"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/152275.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365284228263&channelId=ceshi","time":"2013-11-25 00:00:00","channel":"ceshi","articleId":"365284228263"}
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
        }, 2000);
    }, 2000);
}

function cb(){
    if (++i < list.length) {
        doit(list[i], cb);
    }
}

i = 0;
doit(list[i], cb);
