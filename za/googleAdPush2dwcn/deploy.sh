sudo rm -rf /home/zhongxiaofa/gad2dwcn/
cd /home/zhongxiaofa
sudo svn checkout https://github.com/Ltre/tmp/trunk/za/googleAdPush2dwcn
sudo mv /home/zhongxiaofa/googleAdPush2dwcn /home/zhongxiaofa/gad2dwcn
cd /home/zhongxiaofa/gad2dwcn
sudo chmod -R 777 .
# sudo /usr/local/php/bin/php exe.php > log.log
sudo /home/zhongxiaofa/gad2dwcn/run.sh