# Create program chunks, and execute their multiply.
tmpdir='fakeyou.id'
/usr/local/php/bin/php multi.php $tmpdir 3
chmod -R 777 ./*
./nohup_tmp.sh

# If all program chunks are finished, then calculate their summary.
nohup /usr/local/php/bin/php summary.php $tmpdir 3 &
exit;
