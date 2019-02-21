const fs = require('fs');
const ftpc = require('ftp');
const path = require('path');

var client = new ftpc();
var connProperties = {
    host: '172.16.14.111',
    port: 21,
    user: 'qqqq',
    password: 'qqqq'
};

client.on('ready', function(){
    var lines = fs.readFileSync("Favorites.m3u.txt", 'utf8');
    var src, dest;
    lines.split("\n").forEach(function(e, i){
        src = '/sdcard/' + e.trim();
        dest = '/xlight-ftp-upload/' + path.basename(e);
        client.put(src, dest, function(err){
            console.log(err);
        });
        console.log({src:src, dest:dest});
        client.end();
        console.log('Done!');
    });
});

client.connect(connProperties);