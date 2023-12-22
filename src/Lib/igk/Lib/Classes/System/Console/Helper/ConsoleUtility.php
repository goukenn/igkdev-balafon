<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConsoleUtility.php
// @date: 20230616 11:12:43
namespace IGK\System\Console\Helper;

use IGK\Helper\Utility;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Helper
*/
abstract class ConsoleUtility{    
    /**
     * show db result 
     */
    static function ShowJSonDdResult($result){
        echo ($result ? Utility::TO_JSON ($result,null, JSON_PRETTY_PRINT) : ''). PHP_EOL;
    }

    /**
     * bind and make file 
     * @param array $bind 
     * @param mixed $command 
     * @return void 
     */
    static function MakeFiles(array $bind, $command, bool $force = false){
    
        foreach($bind as $n=>$c){
            if ($force || !file_exists($n)){
                $c($n, $command);
                Logger::info("generate : ".$n);
            }
        } 
    }
}