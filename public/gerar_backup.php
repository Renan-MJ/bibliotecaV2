<?php
// gerar_backup.php

// 1. Configurações do Banco
$host = 'localhost';
$user = 'root';
$pass = ''; 
$dbname = 'biblioteca_v2'; // Nome atualizado conforme solicitado

// 2. Configurações do Arquivo
$backup_file = 'backup_biblioteca_' . date('Y-m-d_H-i') . '.sql';

// 3. Cabeçalhos para forçar o download pelo navegador
header('Content-Type: application/sql');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . $backup_file . "\"");

// 4. Comando Mysqldump (Ajuste o caminho se a versão do seu MySQL for diferente)
$dumpPath = "C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe";

// 5. Executa o comando e joga o resultado direto no download
$command = "\"$dumpPath\" --opt -h $host -u $user $dbname";

passthru($command); 
exit;