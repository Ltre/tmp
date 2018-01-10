taskIdPre="mytaskid_"

# find pid list from `ps aux|grep '/usr/local/php/bin/php ./program.php mytaskid_'`
ftuians=`ps aux|awk '/[0-9] \/usr\/local\/php\/bin\/php \.\/program\.php mytaskid_/{print $2}'`
for ftuianPid in $ftuians
do
    sudo kill -9 $ftuianPid
    echo "killed pid $ftuianPid"
done