@echo off
:: Configurações
set FECHA=%date:~6,4%-%date:~3,2%-%date:~0,2%
set HORA=%time:~0,2%-%time:~3,2%
set PASTA_BACKUP=C:\laragon\www\bibliotecaV2\backups
:: ATENÇÃO: Verifique se sua versão do mysql no Laragon é a 8.0.30 ou ajuste abaixo
set MYSQL_BIN=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe

if not exist "%PASTA_BACKUP%" mkdir "%PASTA_BACKUP%"

echo Iniciando backup do banco: biblioteca_v2...

"%MYSQL_BIN%" -u root biblioteca_v2 > "%PASTA_BACKUP%\backup_biblioteca_%FECHA%_%HORA%.sql"

echo Backup concluído! Arquivo salvo em %PASTA_BACKUP%