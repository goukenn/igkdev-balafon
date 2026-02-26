<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpUnitHelper.php
// @date: 20230313 19:53:15
namespace IGK\Helper;
use IGK\System\Console\Logger;
/**
* 
* @package IGK\Helper
*/
abstract class PhpUnitHelper{

    /**
    * Tests Core Project.
    * @param string $phpunit
    * @param string $core_suite
    */
    public static function TestCoreProject(string $phpunit, string $core_suite){
        Logger::info("run test...");
        $r = `{$phpunit} -c phpunit.xml.dist --testsuite {$core_suite} 1>&2 2>&2 && echo 'ok-complete'`;
        if ($r && igk_str_endwith(rtrim($r), 'ok-complete') ){
            Logger::success("test success.");
        }
        else {
            Logger::danger("phpunit test failed.");
            if ($r)
                fwrite(STDERR, $r);
            return -2;
        }
    }
}