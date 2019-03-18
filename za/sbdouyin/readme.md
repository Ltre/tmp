/data1/webapps/mcstatic.duowan.com 
14.17.108.113，14.17.108.114


管理员账号
douyin_admin@dou_yin
69w0ZxIp

写账号
douyin_media_rw@dou_yin
E54J3BZ5Qp

10.21.43.42:6304  (主写)
58.215.169.34:6305 (主读)



线上部署：
sudo svn co https://github.com/Ltre/tmp/trunk/za/sbdouyin/bin /data1/webapps/mcstatic.duowan.com/bin
//初始化资源目录： sudo mkdir /data1/webapps/mcstatic.duowan.com/audio; sudo mkdir /data1/webapps/mcstatic.duowan.com/cover; sudo mkdir /data1/webapps/mcstatic.duowan.com/typecover; sudo rm /data1/webapps/mcstatic.duowan.com/log.log -f
//清理数据表： TRUNCATE mc_info; TRUNCATE mc_type; TRUNCATE mc_relate; 
//更新程序： svn up
//执行全部： /usr/local/php/bin/php listbytype2.php
//执行单个归类： nohup /usr/local/php/bin/php listbytype2.php 865 >> 865.nohup &
//仅在linux执行
