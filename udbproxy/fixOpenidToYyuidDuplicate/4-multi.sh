chmod +x 4i.php

for tableN in $( seq 0 9 )
do
    nohup ./4i.php $tableN >o$tableN.log &
done
