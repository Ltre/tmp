//刷播放量
//粘贴到http://video.duowan.com/play/xxxxxx.html页面console中即可

var vid = '8575297';
var channel = 'bzsj';
var limit = 100;
var laiyuanv3='oldweb';
var baseUrl = 'http://playstats.v.duowan.com/index.php?referrer=&laiyuanv3='+laiyuanv3+'&r=play%2Fload&vid='+vid+'&type=web&channelId='+channel+'&source_url=http%3A%2F%2Fvideo%2Eduowan%2Ecom%2Fplay%2F8575297%2Ehtml%3Ft='
setInterval(function(){
    var url = '';
    var t = new Date().getTime();
    for(i=0; i<limit; i++){
      url = baseUrl+t+i;
      fetch(url)
    }
}, 1000);