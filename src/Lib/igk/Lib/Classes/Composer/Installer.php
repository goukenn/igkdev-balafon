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
        echo '* --------------------------------------------------------', PHP_EOL;
        echo '* running post install', PHP_EOL;
        echo '* --------------------------------------------------------', PHP_EOL;

        $chdir = getcwd();
        $argv = igk_getv($_SERVER, 'argv');
        igk_debug_wln('VERSION: ' . IGK_VERSION);
        igk_debug_wln('cwd: ' . $chdir);
        igk_debug_wln('argv: ' . json_encode($argv));


        $v_moveto_vendor = false;
        $vendor_dir = $chdir . '/vendor';
        $args = [];
        if (is_dir($vendor_dir)) {
            $args['--vendor-dir'] = Path::GetRelativePath($chdir, $vendor_dir);
        }
        $args['--app-dir'] = Path::GetRelativePath($chdir, $chdir . '/src/application');
        $args[] = '--no-config';
        $ct = false;
        $cli = null;
        $fc_handlers = [
            'create-project' => static::class . '::_composer_create_project',
            'install' => static::class . '::_composer_install'
        ];
        foreach ($fc_handlers as $f => $fc) {
            if (false !== ($idx = array_search($f, $argv))) {
                $fc($chdir, $argv, $args, $cli, $idx);
                break;
            }
        }
        if (!$ct || !Utility::HaveArg($ct))
            $args[] = './src';
        $cm = Utility::BuildArgs($args) . ' ';
        $cli = $cli ?? IGK_LIB_DIR . '/bin/balafon';
        igk_wln('CLI : ' . $cli);
        if ($v_moveto_vendor) {
            self::_CoreMoveToVendorDir();
        }
        $cmd = "cd {$chdir} && {$cli} --init --no-config --reset {$cm}";
        igk_wln('command: ' . $cmd);
        // + | init project 
        igk_wln(`{$cmd}`);
        // + | create a symlink to balafon cli
        $fs = $chdir . '/balafon';
        // + | create a symlink to balafon cli - reference link 
        if (!is_link($fs)) {
            $reflink = Path::GetRelativePath($chdir, $cli);
            @symlink($reflink, $fs);
        }
    }
    private static function _composer_install(string $chdir, &$argv, &$args, ?string &$cli, $idx) {}
    private static function _composer_create_project(string $chdir, &$argv, &$args, ?string &$cli, $idx)
    {
        // + | ---------------------------------------------------
        // + | create in composer.phar with create-project command
        // + | 
        $ct = array_slice($argv, $idx + 2);
        $args = array_merge($args, $ct);
        $lib = $chdir . '/src/application/Lib/igk';
        $c = is_link($lib);
        $core_lib = $chdir . '/src/Lib/igk';
        if (!file_exists($lib) || $c) {
            if ($c) {
                @unlink($lib);
            }
            $link = Path::GetRelativePath($lib, $core_lib);
            IO::CreateDir(dirname($lib));
            @symlink($link, $lib);
        }
        self::_ReorguanisePackage($chdir);
        $cli = $chdir . '/src/application/Lib/igk/bin/balafon';
        if (file_exists($git = $chdir.'/.gitignore')){
            $data = explode("\n", file_get_contents($git));
            $data[] = 'src/Lib';
            igk_io_w2file( $git, implode("\n", $data) );
        }
    }
    /**
     * 
     * @param mixed $dir 
     * @return void 
     */
    private static function _ReorguanisePackage(string $dir)
    {
        // $src = file_get_contents($file = $dir . '/composer.json');
        // $src = str_replace('src/Lib/igk/', 'src/application/Lib/igk/', $src);
        // $app_dir = $dir . '/src/application';
        // IO::CreateDir($app_dir);
        // rename($dir . '/src/Lib', $app_dir . '/Lib');
        // if (is_link($file)){
        //     @unlink($file);
        // }
        // igk_io_w2file($file, $src);
        // igk_wln_e($src);
    }
    /**
     * 
     * @param null|string $vendor_dir 
     * @return void 
     */
    private static function _CoreMoveToVendorDir(?string $vendor_dir = null) {}
    /**
     * 
     * @return void 
     */
    public static function PostUpdate()
    {
        // echo 'running post update', PHP_EOL;        
    }
}
