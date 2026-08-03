<?php
// @author: C.A.D. BONDJE DOUE
// @file: CommandFlags.php
// @date: 20260730 12:35:29
namespace IGK\System\Console;

use IGK\System\Traits\EnumeratesConstants;

require_once IGK_LIB_CLASSES_DIR."/System/Traits/EnumeratesConstants.php";
/**
 * 
 * @package IGK\System\Console
 * @author C.A.D. BONDJE DOUE
 */
abstract class CommandFlags
{
    use EnumeratesConstants; 
     
    /**
     * Constant: pre-include file on command start 
     */
    const INCLUDE_COMMAND = '--include';
    /**
     * 
     */
    const DISABLE_LOG_COLOR = '--no-console-log-color';

    const DEBUG = '--debug';

    const QUERYDEBUG = '--querydebug';




    static function all(){
        return self::GetConstantKeys(); 
    }
}
