<?php
// Ruta del proyecto en Windows Server
$projectDir = 'P:\\xampp\\htdocs\\dosxdosiidos';

/*
// Lee el payload de GitHub
$payload = file_get_contents('php://input');

// Verifica que el payload esté presente
if (!$payload) {
    http_response_code(400);
    die('Bad request: no payload received.');
}
*/

// Ejecuta el comando de actualización del repositorio en Windows
$output = shell_exec("cd /d {$projectDir} && git pull 2>&1");
echo $output;
?>