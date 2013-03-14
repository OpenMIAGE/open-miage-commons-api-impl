<?php

$util_version = explode("=", file_get_contents("../lib/openm.util.dependencies"));
require_once dirname(dirname(dirname(__FILE__))) . "/lib/" . $util_version[0] . "/Import.class.php";
?>
