const ftpc = require('ftp');

var client = new ftpc();
var connProperties = {
    host: '172.16.14.111',
    port: 21,
    user: 'qqqq',
    password: 'qqqq'
};

client.on('ready', function(){
    client.put('bigsongs.zip', '/xlight-ftp-upload/bigsongs.zip', function(err){
        console.log(err);
    });
    client.end();
    console.log('Done!');
});

client.connect(connProperties);