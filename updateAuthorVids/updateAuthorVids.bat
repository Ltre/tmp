@echo off 
@echo ������ר��ID�������������ȫ��ר��

set channel=
set /p channel=

php updateAuthorVids.php %channel%
pause