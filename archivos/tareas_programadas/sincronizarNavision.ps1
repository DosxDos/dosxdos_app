# Script para abrir una URL en Google Chrome
$url = "https://dosxdos.app.iidos.com/apirest/sincronizador2.php"
$chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe"
Start-Process $chromePath -ArgumentList $url
