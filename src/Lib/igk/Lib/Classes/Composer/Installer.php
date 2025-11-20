<?php
// @author: C.A.D. BONDJE DOUE
// @file: Installer.php
// @date: 20251120 16:01:35
namespace IGK\Composer;

use IGK\System\Console\Commands\Utility;

defined('IGK_VERSION') && die('already defined');

require_once __DIR__.'/../../../igk_framework.php';
require_once IGK_LIB_CLASSES_DIR.'/System/Console/Commands/Utility.php';

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
        $cm = Utility::BuildArgs($args).' ';
        igk_wln($argv, $cm);

        $cli = IGK_LIB_DIR.'/bin/balafon';
        $wdir = IGK_LIB_DIR;
        `chown -R www-data:www-data {$wdir}`;
        // + | init project 
        `cd {$chdir} && $cli --init --noconfig --reset {$cm}./`;
    }
    public static function PostUpdate(){
        echo 'running post update', PHP_EOL;
        
    }
}