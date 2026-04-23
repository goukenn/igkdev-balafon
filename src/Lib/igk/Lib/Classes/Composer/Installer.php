<?php
// @author: C.A.D. BONDJE DOUE
// @file: Installer.php
// @date: 20251120 16:01:35
namespace IGK\Composer;
use IGK\ApplicationFactory;
use IGK\ApplicationLoader;
use IGK\Helper\IO;
use IGK\System\Console\Commands\Utility;
use IGK\System\Installers\InstallSite;
use IGK\System\IO\Path;

defined('IGK_COMPOSE_DEBUG_INSTALLER') || (defined('IGK_VERSION') && die('balafon framework. already defined'));
require_once __DIR__ . '/../../../igk_framework.php';
require_once IGK_LIB_CLASSES_DIR . '/System/Console/Commands/Utility.php';
/**
 * 
 * @package IGK\Composer
 * @author C.A.D. BONDJE DOUE
 */
/**
* auto generate doc.
* @package IGK\Composer
*/
class Installer
{
    /**
    * auto generate doc.
    * @return never
    */
    public static function PostInstall()
    {
        require_once __DIR__.'/PostInstallApplication.php';
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
        if (!$ct)
            $args[] = './src';
        $cm = Utility::BuildArgs($args) . ' ';
        $cli = $cli ?? IGK_LIB_DIR . '/bin/balafon';
        igk_wln('CLI: ' . $cli);
        if ($v_moveto_vendor) {
            self::_CoreMoveToVendorDir();
        }
        $cmd = "cd {$chdir} && {$cli} --init --no-config --reset {$cm}";
        igk_wln('command: ' . $cmd);
        // + | init project 
        igk_wln(shell_exec("{$cmd}"));
        // + | create a symlink to balafon cli
        $fs = $chdir . '/balafon';
        // + | create a symlink to balafon cli - reference link 
        if (!is_link($fs)) {
            $reflink = Path::GetRelativePath($chdir, $cli);
            @symlink($reflink, $fs);
        }
        // + | TO use loading class as priority 
        ApplicationFactory::Register('composer-post-install', PostInstallApplication::class);
        // + | boot post installer 
        ApplicationLoader::Boot('composer-post-install'); 
        InstallSite::CreateApacheVHostFile('composer-server', $chdir, $chdir, $chdir.'/src/public');
        InstallSite::CreatePhpUnitConfig($chdir, $chdir . '/src/application',  $chdir . '/src/public');
    }
    /**
    * auto generate doc.
    * @param string $chdir
    * @param mixed & $argv
    * @param mixed & $args
    * @param null|string & $cli
    * @param mixed $idx
    * @return
    */
    private static function _composer_install(string $chdir, &$argv, &$args, ?string &$cli, $idx) {}
    /**
    * auto generate doc.
    * @param string $chdir
    * @param mixed & $argv
    * @param mixed & $args
    * @param null|string & $cli
    * @param mixed $idx
    * @return
    */
    private static function _composer_create_project(string $chdir, &$argv, &$args, ?string &$cli, $idx)
    {
        // + | ---------------------------------------------------
        // + | create in composer.phar with create-project command
        // + | 
        $ct = array_slice($argv, $idx + 2);
        if (false !== ($idx = array_search('--', $ct))){
            array_shift($ct);
            $args = array_merge($args, $ct);  
        }
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
        $cli = $chdir . '/src/application/Lib/igk/bin/balafon';
        if (file_exists($git = $chdir.'/.gitignore')){
            $data = explode("\n", file_get_contents($git));
            $data[] = 'src/Lib';
            igk_io_w2file( $git, implode("\n", $data) );
        }
    }
    /**
    * auto generate doc.
    * @param null|string $vendor_dir
    * @return void
    */
    private static function _CoreMoveToVendorDir(?string $vendor_dir = null) {}
    /**
    * auto generate doc.
    * @return void
    */
    public static function PostUpdate()
    {
    }
}