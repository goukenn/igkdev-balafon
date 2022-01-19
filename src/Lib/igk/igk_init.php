#!/usr/bin/env php
<?php
// @author: C.A.D BONDJE 
// @desc: init application system 
// @file : igk_init.php
// @license : MIT license

use IGK\Helper\StringUtility;

require __DIR__ . "/igk_framework.php";
require_once IGK_LIB_CLASSES_DIR . "/Helper/StringUtility.php";
$b = StringUtility::Dir(__DIR__ . "/bin/balafon");
if (!file_exists($b)) {
    die("balafon not found");
}
$is_cgi = strpos(igk_server()->GATEWAY_INTERFACE, "CGI/") === 0;
if ($is_cgi) {
    echo "Content-Type: text/html;\r\n\r\n";
}
if (!igk_is_function_disable("shell_exec")) {
    $install_dir = realpath(getcwd() . "/../../");
    $index = $install_dir . "/index.php";
    $code = 0;
    if (!file_exists($index)) {
        // echo "init configuration : $b \n";
        // echo "--".shell_exec($b ." --init --noconfig --wdir:".getcwd());
        error_log("install site : \n");
        exec($b . " --install-site --wdir:'" . $install_dir . "' --force &2> /dev/null > /dev/null", $output, $code);
        error_log("install site done ", $code);
    }

    if (!igk_is_cmd()) {
        if (is_dir($install_dir . "/Configs")) {
            if ($is_cgi) {
                // + | run script as cgi              
                echo "<script>document.location = '/Configs'; </script>";
                exit;
            }
            igk_navto("/Configs");
        } else {
            if ($is_cgi === 0) {
                // + | run script as cgi
                echo "Content-Type: text/html;\r\n\r\n";
            }
            echo "failed to install site";
            exit;
        }
    } else {
        echo "complete.\n";
    }
} else {
    echo "shell_exec is disabled on this server.";
}
exit;
