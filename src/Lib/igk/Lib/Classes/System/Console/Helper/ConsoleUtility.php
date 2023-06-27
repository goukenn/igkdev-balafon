<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConsoleUtility.php
// @date: 20230616 11:12:43
namespace IGK\System\Console\Helper;

use IGK\Helper\Utility;

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
}