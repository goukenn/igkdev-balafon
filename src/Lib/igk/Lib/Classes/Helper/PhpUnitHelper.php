<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpUnitHelper.php
// @date: 20230313 19:53:15
namespace IGK\Helper;

use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\Helper
*/
abstract class PhpUnitHelper{
    public static function TestCoreProject(string $phpunit, string $core_suite){
        Logger::info("run test......");
        $r = `{$phpunit} -c phpunit.xml.dist --testsuite {$core_suite} 2>&1 && echo 'ok-complete';`;
        if ($r && igk_str_endwith($r, 'ok-complete') ){
            Logger::success("test success.");
        }
        else {
            Logger::danger("test unit failed.");
            fwrite(STDERR, $r);
            return -2;
        }
    }
}