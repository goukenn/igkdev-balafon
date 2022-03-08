<?php

namespace IGK\Helper;

class MaintenanceHelper
{
    public static function LockSite(string $bdir)
    {
        if (file_exists($lock = $bdir . "/maintenance.lock")) {
            return;
        }
        igk_io_w2file($lock, '1');
        $rnlist = [$bdir . "/index.php" => $bdir . "/.lock.index.php", $bdir . "/.htaccess" => $bdir . "/.lock.htaccess",];
        foreach ($rnlist as $k => $v) {
            rename($k, $v);
        }
        igk_io_w2file($bdir . "/index.php", igk_io_read_allfile(IGK_LIB_DIR . "/Default/Views/maintenance.mode.phtml"));
        igk_io_w2file(
            $bdir . "/.htaccess",
            <<<EOF
<IfModule rewrite_module>
RewriteEngine On
#redirect all to index.php
RewriteCond "%{REQUEST_FILENAME}" !-f
RewriteCond "%{REQUEST_FILENAME}" !-d
RewriteRule ^(/)?(.)*$  "index.php" [QSA,L]
</IfModule>
EOF
        );
    }
    /**
     * 
     * @param string $bdir 
     * @return void 
     */
    public static function UnlockSite(string $bdir)
    {
        if (!file_exists($lock = $bdir . "/maintenance.lock")) {
            return;
        }
        unlink($bdir . "/index.php");
        unlink($bdir . "/.htaccess");
        $restore = [$bdir . "/.lock.index.php" => $bdir . "/index.php", $bdir . "/.lock.htaccess" => $bdir . "/.htaccess"];
        foreach ($restore as $k => $v) {
            if (file_exists($k)) {
                rename($k, $v);
            }
        }
        unlink($lock);
    }
}
