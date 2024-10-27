<?php
$output = shell_exec("echo test 2>&1");
echo $output ?: "Error: shell_exec no está permitido.";
?>