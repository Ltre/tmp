# Create program chunks, and execute their multiply.
tmpdir='fakeyou.id'
/usr/local/php/bin/php multi.php $tmpdir 1
chmod -R 777 ./*
./nohup_tmp.sh

# If all program chunks are finished, then calculate their summary.
/usr/local/php/bin/php summary.php $tmpdir 1
exit;

# If all program chunks are finished, then calculate their summary.
while(true)
do
    check_finish=1
    for k in $( seq 1 10 )
    do
        echo ${tmpdir}/${k}/${tmpdir}/finish.is
        if 
            [ ! -f ${tmpdir}/${k}/${tmpdir}/finish.is ]
        then
            check_finish=0
            break
        fi
    done
    echo $check_finish
    if 
        [ $check_finish == 1 ]
    then
        break;
    fi
    sleep 1s
done
