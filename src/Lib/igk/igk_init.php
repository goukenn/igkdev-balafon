#!/usr/bin/env php
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_init.php   
// @desc: init application core system  
// @license : see licence.txt attached to the library

use IGK\Helper\StringUtility;

require __DIR__ . "/igk_framework.php";
require_once IGK_LIB_CLASSES_DIR . "/Helper/StringUtility.php";
$b = StringUtility::Dir(__DIR__ . "/bin/balafon");
if (!igk_io_file_exists($b)) {
    die("balafon not found");
}
$conf_path = '/Configs';
$v_gateway = igk_server()->GATEWAY_INTERFACE ?? '';
$is_cgi = strpos($v_gateway, "CGI/") === 0;
if ($is_cgi) {
    echo "Content-Type: text/html;\r\n\r\n";
}
if (!igk_is_function_disable("shell_exec")) {
    $install_dir = realpath(getcwd() . "/../../");
    $index = $install_dir . "/index.php";
    $code = 0;
    if (!igk_io_file_exists($index)) {
        // echo "init configuration : $b \n";
        // echo "--".shell_exec($b ." --init --noconfig --wdir:".getcwd());
        error_log("install site : \n");
        exec($b . " --install-site --wdir:'" . $install_dir . "' --force &2> /dev/null > /dev/null", $output, $code);
        error_log("install site done ", $code);
    }

    if (!igk_is_cmd()) {
        if (is_dir($install_dir . $conf_path)) {
            if ($is_cgi) {
                // + | run script as cgi              
                echo "<script>document.location = '{$conf_path}'; </script>";
                igk_exit();
            }
            igk_navto($conf_path);
        } else {
            if ($is_cgi === 0) {
                // + | run script as cgi
                echo "Content-Type: text/html;\r\n\r\n";
            }
            echo "failed to install site";
            igk_exit();
        }
    } else {
        echo "init complete.\n";
    }
} else {
    echo "shell_exec is disabled on this server.";
}
igk_exit();
