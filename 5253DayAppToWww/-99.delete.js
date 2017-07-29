//用于批量删除文章
//在cms.duowan.com域名下执行：
//找到contentIframe的frame，加上ID abcdefghijklmn

var list = [
    {"rawUrl":"http:\/\/www.5253.com\/articles\/328489.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283634889&channelId=ceshi","time":"2017-06-22 00:00:00","channel":"ceshi","articleId":"365283634889"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/328456.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283636432&channelId=ceshi","time":"2017-06-21 00:00:00","channel":"ceshi","articleId":"365283636432"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/328357.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283638129&channelId=ceshi","time":"2017-06-20 00:00:00","channel":"ceshi","articleId":"365283638129"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/328312.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283639593&channelId=ceshi","time":"2017-06-19 00:00:00","channel":"ceshi","articleId":"365283639593"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/328248.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283641076&channelId=ceshi","time":"2017-06-16 00:00:00","channel":"ceshi","articleId":"365283641076"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/328180.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283642892&channelId=ceshi","time":"2017-06-15 00:00:00","channel":"ceshi","articleId":"365283642892"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/328053.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283644353&channelId=ceshi","time":"2017-06-14 00:00:00","channel":"ceshi","articleId":"365283644353"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327982.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283646942&channelId=ceshi","time":"2017-06-13 00:00:00","channel":"ceshi","articleId":"365283646942"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327935.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283648365&channelId=ceshi","time":"2017-06-12 00:00:00","channel":"ceshi","articleId":"365283648365"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327837.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283650812&channelId=ceshi","time":"2017-06-09 00:00:00","channel":"ceshi","articleId":"365283650812"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327764.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283652322&channelId=ceshi","time":"2017-06-08 00:00:00","channel":"ceshi","articleId":"365283652322"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327692.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283654016&channelId=ceshi","time":"2017-06-07 00:00:00","channel":"ceshi","articleId":"365283654016"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327619.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283655460&channelId=ceshi","time":"2017-06-06 00:00:00","channel":"ceshi","articleId":"365283655460"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327535.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283656949&channelId=ceshi","time":"2017-06-05 00:00:00","channel":"ceshi","articleId":"365283656949"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327403.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283659562&channelId=ceshi","time":"2017-06-02 00:00:00","channel":"ceshi","articleId":"365283659562"},
    {"rawUrl":"http:\/\/www.5253.com\/articles\/327336.html","artiUrl":"http:\/\/cms.duowan.com\/article\/toEditArticlePage.do?articleId=365283661009&channelId=ceshi","time":"2017-06-01 00:00:00","channel":"ceshi","articleId":"365283661009"}
];

function doit(o, cb){
    document.getElementById('abcdefghijklmn').setAttribute('src', o.artiUrl);
    setTimeout(function(){
        var win = window['abcdefghijklmn'].contentWindow;
        var __bk = win.confirm;
        win.confirm = function(msg){ return true; };
        win.deleteArticle(o.channel, o.articleId);
        win.confirm = __bk;
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

