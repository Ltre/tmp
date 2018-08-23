gad2dwcn_php_pids=`ps aux|awk '/[0-9] \/usr\/local\/php\/bin\/php.+gad2dwcn\.php/{print $2}'`
for gad2dwcn_pid in $gad2dwcn_php_pids
do
    sudo kill -9 $gad2dwcn_pid
    echo "killed gad2dwcn.php, pid = $gad2dwcn_pid"
done

gad2dwcn_daemon_pids=`ps aux|awk '/gad2dwcn\.sh/{print $2}'`
for gad2dwcn_pid in $gad2dwcn_daemon_pids
do
    sudo kill -9 $gad2dwcn_pid
    echo "killed gad2dwcn.daemon, pid = $gad2dwcn_pid"
done
