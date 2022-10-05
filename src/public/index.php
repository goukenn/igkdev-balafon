<?php

// @author: C.A.D. BONDJE DOUE
// @filename: index.php
// @date: 20221005 08:48:19
 
if (!version_compare(PHP_VERSION, "7.3", ">=")) {
    die("mandory version required. 7.3 >=");
}

// + | ----------------------------------------------------
// + | init definition 
$appdir = realpath("../");

// + | ----------------------------------------------------
// + | application
define("IGK_APP_DIR", $appdir . "/application");

// + | ----------------------------------------------------
// + | project install directory
define("IGK_PROJECT_DIR", IGK_APP_DIR . "/Projects");

// + | ----------------------------------------------------
// + | require core framework
require_once __DIR__."/../Lib/igk/igk_framework.php";

// + | ----------------------------------------------------
// + | bootraps
IGKApplication::Boot('web')->run(__FILE__);  