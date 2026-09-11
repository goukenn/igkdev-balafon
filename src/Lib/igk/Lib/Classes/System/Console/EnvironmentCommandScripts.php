<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvironmentCommandScripts.php
// @date: 20250907 23:42:59
namespace IGK\System\Console;
use Exception;
use IGK\Helper\Activator;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\System\IO\Path;
use IGK\System\Text\RegexMatcherContainer;
use IGKEvents;
use IGKException;

/**
 * 
 * @package IGK\System\Console
 * @author C.A.D. BONDJE DOUE
 */
/**
* auto generate doc.
* @package IGK\System\Console
*/
class EnvironmentCommandScripts
{
    /**
    * Cache: caches.
    * @var mixed
    */
    private static $sm_caches;
    /**
     * get cached definition 
     * @return mixed 
     */
    public static function GetCacheDefinition(){
        if (is_null(self::$sm_caches)){
            self::EnvLoad();
        }
        return self::$sm_caches;
    }
    /**
     * get cache file 
     * @return string 
     */
    static function GetCacheFile(): string
    {
        return Path::Combine(igk_io_cachedir(), '.env.commands.cache');
    }
    /**
    * auto generate doc.
    * @return void
    */
    static function EnvLoad()
    {
        $f = self::GetCacheFile();
        $data = file_exists($f) ?
            $data = include($f) : null;
        if ($data) {
            foreach ($data as $v) {
                self::LoadDefinition($v, $data);
            }
        }
        self::$sm_caches = $data ?? self::DetectCachingCommand();
    }
    /**
    * Store cache.
    */
    static function StoreCache()
    {
        $f = self::GetCacheFile();
        $tab = [];
        foreach (self::$sm_caches as $k => $v) {
            $tab[$k] = $v->file;
        }
        $sb = implode("\n", ['<?php', sprintf('return [%s];', StringUtility::DumpArray($tab))]);
        igk_io_w2file($f, $sb);
    }
    /**
    * auto generate doc.
    * @param ?string $dir
    * @return array
    */
    static function DetectCachingCommand(?string $dir = null)
    {
        $dir = $dir ?? self::DefaultCommandLocation();
        $files = IO::GetFiles($dir, '/\.php$/', true) ?? [];
        $definitions = [];
        foreach ($files as $f) {
            self::LoadDefinition($f, $definitions);
        }
        return $definitions;
    }
    /**
     * get default command location 
     * @return ?string
     */
    public static function DefaultCommandLocation(){
        return igk_configs()->commands_dir ??
        igk_app()->getApplication()->configs->commands_dir ?? 
        Path::Combine(Path::getInstance()->getApplicationDir(), 'Lib/igk/scripts/commands');
    }
    /**
    * auto generate doc.
    * @param string $file
    * @param ?string $dir
    */
    public static function GetCommandFile(string $file, ?string $dir = null)
    {
        if ($dir){        
            $def = self::DetectCachingCommand($dir);
            return igk_getv($def, $file);
        }
        if (is_null(self::$sm_caches)) {
            self::EnvLoad();
            if (!file_exists(self::GetCacheFile())) {
                igk_reg_hook(IGKEvents::HOOK_APP_SHUTDOWN, function () {
                    self::StoreCache();
                });
            }
        }
        if ($r = igk_getv(self::$sm_caches, $file)) {
            return $r->file;
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param string $file
    * @param mixed &$definition
    * @return void
    */
    static function LoadDefinition(string $file, &$definition)
    {
        $c_command = &$definition;
        $src = file_get_contents($file);
        $regex = new RegexMatcherContainer;
        $regex->match("^<\?php", 'start-proc');
        $regex->begin("\/\/\\s+(?:@([a-zA-Z][a-zA-Z0-9-]*))\\s*:[^\\n\\S]*", '$', 'start-command-block');
        $regex->match("[^\\s]", 'stop-header');
        $pos = 0;
        $info = igk_createobj();
        $actions = [
            'start-proc' => function () use (&$start) {
                $start = true;
            },
            'start-command-block' => function ($e) use ($info) {
                $v = trim(igk_str_rm_start($e->value, $e->beginCaptures[0][0], 1));
                $n = $e->beginCaptures[1][0];
                $c = StringUtility::FuncName($n);
                $info->{$c} = $v;
            },
            'stop-header' => function ($e) use (&$start, &$stop) {
                if ($start) {
                    $stop = true;
                }
            }
        ];
        $start = false;
        $stop = false;
        while (!$stop && ($g = $regex->detect($src, $pos))) {
            if ($e = $regex->end($g, $src, $pos)) {
                $handle = igk_getv($actions, $e->tokenID);
                if ($handle) {
                    $handle($e, $g, $pos, $src);
                }
            }
        }
        $tab = [];
        $l = array_filter((array) $info, function ($a) {
            return 0 !== strpos($a, 'balafon_');
        });
        foreach ($l as $k => $v) {
            $c = ltrim(igk_str_rm_start($k, 'balafon_command', 1), '_ ');
            if (empty($c)) $c = 'name';
            $tab[$c] = $v;
        }
        $tab['file'] = $file;
        if (!isset($tab['desc'])) {
            $tab['desc'] = igk_getv($info, 'desc');
        }
        $i = Activator::CreateNewInstance(CommandInfo::class, $tab);
        if (!empty($i->name)) {
            $c_command[$i->name] = $i;
        }
    }
}