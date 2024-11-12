<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $postBody = file_get_contents("php://input");
    $filePath = 'callBackBulkCrm.json';
    file_put_contents($filePath, $postBody);
}
