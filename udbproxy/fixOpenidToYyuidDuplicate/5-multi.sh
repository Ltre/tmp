chmod +x 5i.php

for tableN in $( seq 0 9 )
do
    nohup ./5i.php $tableN >o$tableN.log &
done
