@echo off 
@echo 请输入专区ID，不输入则更新全部专区

set channel=
set /p channel=

php updateAuthorVids.php %channel%
pause