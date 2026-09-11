<?php
// @author: C.A.D. BONDJE DOUE
// @file: CommandFlags.php
// @date: 20260730 12:35:29
namespace IGK\System\Console;

use IGK\System\Traits\EnumeratesConstants;

require_once IGK_LIB_CLASSES_DIR."/System/Traits/EnumeratesConstants.php";
/**
* auto generate doc.
* @package IGK\System\Console
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Console
*/
abstract class CommandFlags
{
    use EnumeratesConstants;
    /**
     * Constant: pre-include file on command start 
     */
    const INCLUDE_COMMAND = '--include';
    /**
    * auto generate doc.
    */
    const DISABLE_LOG_COLOR = '--no-console-log-color';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const DEBUG = '--debug';
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    const QUERYDEBUG = '--querydebug';
    /**
    * auto generate doc.
    * @return void
    */
    static function all(){
        return self::GetConstantKeys(); 
    }
}
