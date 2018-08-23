gad2dwcnExec(){
    gad2dwcndir=/home/zhongxiaofa/gad2dwcn
    gad2dwcnbin=${gad2dwcndir}/gad2dwcn.php
    sudo chmod +x $gad2dwcnbin
    nohup ${gad2dwcnbin} prod >> ${gad2dwcndir}/log.log &
}

while :
do
    gad2dwcn_pids=`ps aux|awk '/[0-9] \/usr\/local\/php\/bin\/php.+gad2dwcn\.php/{print $2}'`
    gad2dwcn_terminate=1
    for gad2dwcnpid in $gad2dwcn_pids
    do
        gad2dwcn_terminate=0
    done
    if [ $gad2dwcn_terminate == 1 ]; then
        echo `date --date=now '+%Y-%m-%d %H:%M:%S %Z'`
        echo 'wake teecExec..'
        gad2dwcnExec
    fi
    sleep 2s
done
