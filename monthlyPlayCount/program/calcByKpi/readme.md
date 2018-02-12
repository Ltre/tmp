说明：计算运营编辑在数个月的播放量总和及明细

复制到生产机器221.228.83.82后，执行：
chmod +x start.sh
./start.sh
即可开始统计计算


执行：./stop.sh
可终止程序，但可能会产生不完整的临时数据文件


执行：./clear.sh
可清理临时数据文件


将test-let目录中的文件覆盖到本程序根目录，可以用于测试


分别执行：
ps aux|awk '/[0-9] \/usr\/local\/php\/bin\/php kpi\.php/{print $2}'
ps aux|awk '/[0-9] \/usr\/local\/php\/bin\/php summary\.php/{print $2}'
可查看执行中进程



summary有关的进程属于等待进程，用于汇总
kpi有关的进程属于分片任务进程，用于分片统计




如需合并所有明细文件，可执行（数字根据需要设置，脚本暂时没开发好）：
cp -r 0/fakeyou.id/dw_* summary/
cp -r 1/fakeyou.id/dw_* summary/
cp -r 2/fakeyou.id/dw_* summary/
cp -r 3/fakeyou.id/dw_* summary/
cp -r 4/fakeyou.id/dw_* summary/
cp -r 5/fakeyou.id/dw_* summary/
cp -r 6/fakeyou.id/dw_* summary/
cp -r 7/fakeyou.id/dw_* summary/