<?php
function generarClaveSegura(int $longitud = 64): string {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}<>?';
    $clave = '';
    $max = strlen($caracteres) - 1;

    for ($i = 0; $i < $longitud; $i++) {
        $clave .= $caracteres[random_int(0, $max)];
    }

    return $clave;
}
$claveSegura = generarClaveSegura(64);
echo $claveSegura;
?>