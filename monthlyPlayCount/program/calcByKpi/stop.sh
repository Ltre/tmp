# find pid list from `ps aux|grep '/usr/local/php/bin/php kpi.php'`
kpis=`ps aux|awk '/[0-9] \/usr\/local\/php\/bin\/php kpi\.php/{print $2}'`
for pid in $kpis
do
    sudo kill -9 $pid
    echo "killed pid $pid"
done

kpis=`ps aux|awk '/[0-9] \/usr\/local\/php\/bin\/php summary\.php/{print $2}'`
for pid in $kpis
do
    sudo kill -9 $pid
    echo "killed pid $pid"
done
