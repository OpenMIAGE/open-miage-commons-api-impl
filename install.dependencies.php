<?php

$scriptName = "Auto-Download-Script";
$url = "http://open-miage.org/download/$scriptName";
$autoDownloadScript = file_get_contents($url);
if ($autoDownloadScript === false)
    die($url . " not found");

file_put_contents("$scriptName.php", $autoDownloadScript);
require_once "$scriptName.php";
?>