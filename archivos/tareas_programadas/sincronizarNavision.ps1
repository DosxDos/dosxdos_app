# Script para abrir una URL en Google Chrome
$url = "http://localhost:8080/apirest/sincronizador2.php"
$chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe"
Start-Process $chromePath -ArgumentList $url
