<?php
// @author: C.A.D. BONDJE DOUE
// @file: Installer.php
// @date: 20251120 16:01:35
namespace IGK\Composer;

use IGK\Helper\IO;
use IGK\System\Console\Commands\Utility;
use IGK\System\IO\Path;

defined('IGK_COMPOSE_DEBUG_INSTALLER') || (defined('IGK_VERSION') && die('balafon framework. already defined')); 
require_once __DIR__ . '/../../../igk_framework.php';
require_once IGK_LIB_CLASSES_DIR . '/System/Console/Commands/Utility.php';

/**
 * 
 * @package IGK\Composer
 * @author C.A.D. BONDJE DOUE
 */
class Installer
{
    /**
     * 
     * @return never 
     */
    public static function PostInstall()
    {
        echo '* --------------------------------------------------------',PHP_EOL;
        echo '* running post install', PHP_EOL;
        echo '* --------------------------------------------------------',PHP_EOL;


        $chdir = getcwd();
        $argv = igk_getv($_SERVER, 'argv');
        igk_wln('VERSION: ' . IGK_VERSION);
        igk_wln('cwd: ' . $chdir);
        igk_wln('argv: ' .json_encode( $argv));
        $vendor_dir = $chdir . '/vendor';
        $args = [];
        if (is_dir($vendor_dir)) {
            $args['--vendor-dir'] = Path::GetRelativePath($chdir, $vendor_dir);
        }
        $args['--app-dir'] = Path::GetRelativePath($chdir, $chdir . '/src/application');

        $ct = false;
        if (false !== ($idx = array_search('create-project', $argv))){
            // + | ---------------------------------------------------
            // + | create in composer.phar with create-project command
            // + |
            $ct = array_slice($argv, $idx +2);
            $args = array_merge($args, $ct);
            igk_wln('reorganize the package directory...');
            self::_ReorguanizPackage($chdir);
        }
        if (!$ct || !Utility::HaveArg($ct))
            $args[] = './';
        $cm = Utility::BuildArgs($args) . ' ';
        $cli = IGK_LIB_DIR . '/bin/balafon';
        igk_wln('CLI : '.$cli);
        self::_CoreMoveToVendorDir();  
        // + | init project 
        echo `cd {$chdir} && $cli --init --noconfig --reset {$cm}`;
        // + | create a symlink to balafon cli
        $fs = $chdir . '/balafon';
        // + | create a symlink to balafon cli - reference link 
        if (!is_link($fs)) {
            $reflink = Path::GetRelativePath($chdir, $cli);
            @symlink($reflink, $fs);
        }
    }
    private static function _ReorguanizPackage($dir){
        $src = file_get_contents($file = $dir.'/composer.json');
        $src = str_replace('src/Lib/igk/', 'src/application/Lib/igk/',$src);
        $app_dir = $dir.'/src/application';
        IO::CreateDir($app_dir);
        rename($dir.'/src/Lib', $app_dir.'/Lib');
        igk_io_a2file($file, $src);

    }
    /**
     * 
     * @param null|string $vendor_dir 
     * @return void 
     */
    private static function _CoreMoveToVendorDir(?string $vendor_dir){

    }   
    /**
     * 
     * @return void 
     */
    public static function PostUpdate()
    {
        // echo 'running post update', PHP_EOL;        
    }
}
