<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectSettings.php
// @date: 20230128 20:23:57
namespace IGK\System\Console\Commands\Sync;

use IGK\Helper\Activator;
use IGK\System\IO\Path;

///<summary></summary>
/**
* sync project settings
* @package IGK\System\Console\Commands\Sync
*/
class SyncProjectSettings{
    const P_FILE = '.balafon-sync.project.json';
    /**
     * ignore directory list 
     * @var array
     */
    var $ignoredirs = [];
    /**
     * create project setting
     * @param mixed $jsond_data 
     * @return self 
     */
    public static function Load($jsond_data){
        return Activator::CreateNewInstance(self::class, $jsond_data);
    }
    /**
     * 
     * @param string $pdir 
     * @param mixed $excludir 
     * @return void 
     */
    public static function InitProjectExcludeDir(string $pdir, & $excludedir){
        $excludedir = \IGK\Helper\Project::IgnoreDefaultDir();
        if (file_exists($fc = Path::Combine($pdir, self::P_FILE))){
            $g = SyncProjectSettings::Load(json_decode(file_get_contents($fc)));
            if ($g->ignoredirs ){
                $v_ignores =  array_fill_keys($g->ignoredirs , 1);
                $excludedir = array_merge($excludedir,$v_ignores);
            }
        } 
    }
}