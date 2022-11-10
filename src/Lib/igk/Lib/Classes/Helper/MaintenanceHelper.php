<?php

// @author: C.A.D. BONDJE DOUE
// @filename: MaintenanceHelper.php
// @date: 20220427 14:54:14
// @desc: maintenance helper

namespace IGK\Helper;

use IGK\System\Installers\InstallerUtils;

/**
 * 
 */
class MaintenanceHelper
{
    const lockFile = ".maintenance.lock"; 
    public static function LockSite(string $bdir)
    {
        if (file_exists($lock = $bdir . "/".self::lockFile)) {
            return;
        }
        igk_io_w2file($lock, '1');
        $rnlist = [$bdir . "/index.php" => $bdir . "/.lock.index.php", $bdir . "/.htaccess" => $bdir . "/.lock.htaccess",];
        foreach ($rnlist as $k => $v) {
            if (file_exists($k)){
                @rename($k, $v);
            }
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
        if (!file_exists($lock = $bdir ."/".self::lockFile)) {
            return;
        }
        @unlink($bdir . "/index.php");
        @unlink($bdir . "/.htaccess");
        $restore = [$bdir . "/.lock.index.php" => $bdir . "/index.php", $bdir . "/.lock.htaccess" => $bdir . "/.htaccess"];
        foreach ($restore as $k => $v) {
            if (file_exists($k)) {
                @rename($k, $v);
            }
        }

        $is_primary = dirname(igk_io_applicationdir()) == igk_io_basedir();
        if (!file_exists($index = $bdir . "/index.php")){
            // install index file ...
            igk_io_w2file(
                $index,
                InstallerUtils::GetEntryPointSource([
                    "is_primary" => $is_primary,
                    "app_dir" => $is_primary ? '$appdir' : '$appdir."/application"',
                    "project_dir" => 'IGK_APP_DIR."/Projects"'
                ])
            );
        }

        if (!file_exists($h = $bdir . "/.htaccess")){
            // install index file ...
            igk_io_w2file(
                $h,
                igk_getbase_access(igk_io_basedir())
            );
        }
        unlink($lock);
    }
}
