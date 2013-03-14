<?php

/**
 * recover OpenM.util package
 */
$util = explode("=", file_get_contents("lib/openm.util.dependencies"));
$lib = "http://open-miage.org/download/" . $util[0] . "/" . $util[1];
$lib_dir = "../lib";
if (!is_dir($lib_dir)) {
    if (mkdir($lib_dir))
        echo " - $lib_dir <b>created</b><br>";
    else
        die("<h1>error occurs during creation of $lib_dir</h1>");
}
$lib_abs_dir = realpath($lib_dir);
$temp = "$lib_abs_dir/temp/";
if (!is_dir($temp))
    mkdir($temp);

$targetZip = $temp . $util[1];
if (copy($lib, $targetZip))
    echo " - $lib <b>copied to</b> $targetZip<br>";
else
    die("<h1>error occurs during copy of $lib</h1>");

$zip = new ZipArchive();
$res = $zip->open($targetZip);
if ($res === TRUE) {
    if ($zip->extractTo($temp))
        echo " - $targetZip <b>unZip in</b> $temp<br>";
    else
        die("<h1>error occurs during unZip of $targetZip</h1>");
    $zip->close();
    if (unlink($targetZip))
        echo " - $targetZip <b>correctly removed</b><br>";
    else
        die("<h1>error occurs during removing $targetZip</h1>");

    define("OpenM_EXT_LIB_PATH", $lib_abs_dir);
    require_once $temp . "lib/" . $util[0] . "/Import.class.php";
    Import::php("util.file.OpenM_Dir");
    OpenM_Dir::cp($temp . "lib/", $lib_abs_dir);
    echo " - $temp" . "lib/ <b>correctly copied in</b> $lib_abs_dir<br>";
    Import::addLibPath($util[0]);
    OpenM_Dir::rm($temp);
    echo " - $temp <b>correctly removed</b><br>";
    echo ' - lib OpenM.util succesfully installed !<br>';
}
else
    die('<h1>error occurs</h1>');

/**
 * install other dependencies
 */
Import::php("util.pkg.OpenM_Dependencies");
$dependencies = new OpenM_Dependencies("lib");
$dependencies->install("./temp", OpenM_Dependencies::ALL(), true);
OpenM_Dir::rm("./temp");
?>