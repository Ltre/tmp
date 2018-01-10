chmod -R 777 *

tmpdir="tmp"
startP=1
taskIdPre="mytaskid_"

if [ ! -d $tmpdir ]; then
    mkdir $tmpdir
fi

for tableN in $( seq 0 9 )
do
    taskId=$taskIdPre$tableN
    nohup ./program.php mytaskid_$tableN $tableN $startP >./$tmpdir/nohup_$tableN.output &
done
