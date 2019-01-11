sudo rm -rf /home/liuyadan/gad2dwcn/
cd /home/liuyadan
sudo svn checkout https://github.com/Ltre/tmp/trunk/za/googleAdPush2dwcn
sudo mv /home/liuyadan/googleAdPush2dwcn /home/liuyadan/gad2dwcn
cd /home/liuyadan/gad2dwcn
sudo chmod -R 777 .
# sudo /usr/local/php/bin/php exe.php > log.log
sudo /home/liuyadan/gad2dwcn/run.sh