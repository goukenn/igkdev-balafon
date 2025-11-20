<?php
// @author: C.A.D. BONDJE DOUE
// @file: Installer.php
// @date: 20251120 16:01:35
namespace IGK\Composer;

defined('IGK_VERSION') && die('already defined');

require_once __DIR__.'/../../../igk_framework.php';
/**
* 
* @package IGK\Composer
* @author C.A.D. BONDJE DOUE
*/
class Installer{
    /**
     * 
     * @return never 
     */
    public static function PostInstall(){        
         echo 'running post install', PHP_EOL;
        $chdir = getcwd();
        $argv = igk_getv($_SERVER, 'argv');
        igk_wln('VERSION: '.IGK_VERSION);
        igk_wln('cwd: '.$chdir); 
        $vendor_dir = $chdir.'/vendor';
        $args = [];
        if (is_dir($vendor_dir)){
            $args['--vendor-dir'] =$vendor_dir;
        }

        igk_wln_e($argv);

        $cli = IGK_LIB_DIR.'/bin/balafon';
        $wdir = IGK_LIB_DIR;
        `chown -R www-data:www-data {$wdir}`;
        // + | init project 
        `cd {$chdir} && $cli --init --noconfig --reset ./`;
    }
    public static function PostUpdate(){
        echo 'running post update', PHP_EOL;
        
    }
}