<?php
// @author: C.A.D. BONDJE DOUE
// @file: CoreUtility.php
// @date: 20230313 19:49:53
namespace IGK\Helper;

use IGK\System\Console\App;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\Helper
*/
abstract class CoreUtility{
    /**
     * lint project file 
     * @return int|void 
     */
    public static function LintCoreLib(){
        if ($files = igk_io_getfiles(IGK_LIB_DIR, "/\.(php|pinc|phtml|pcss)$/")){
            Logger::info("checking core files....syntax");
        $count = 0;
        $T = count($files);
        foreach($files  as $f){
            $o = `php -l $f 2>&1 && echo 'ok'`;
            if (!$o || !igk_str_endwith(trim($o), 'ok')){
                Logger::danger("error in $f");
                return -3;
            }
            else {
                $count++;
                igk_is_debug() && Logger::info("file : $f OK");
                fwrite(STDERR, (App::Gets(App::BLUE, "\r".$count.'/'.$T)));
            }
        }}
        else {
            Logger::danger("missing core files:");
            return -4;
        }
        return true;
    }
}