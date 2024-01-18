<?php
// @author: C.A.D. BONDJE DOUE
// @file: FileHandler.php
// @date: 20240115 10:34:09
namespace IGK\System\IO;

use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
abstract class FileHandler{
    private static $sm_handler;
    /**
     * register file handler
     * @param string $extension 
     * @param FileHandler $handler 
     * @return void 
     */
    public static function Register(string $extension, FileHandler $handler){
        if (is_null(self::$sm_handler)){
            self::$sm_handler = [];
        }
        self::$sm_handler[$extension] = $handler;
    }
    /**
     * retrieve file handler from extension
     * @param string $extension 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetFileHandlerFromExtenstion(string $extension){
        if (self::$sm_handler){
            return igk_getv(self::$sm_handler, $extension);
        }
        return null;
    }
    /**
     * transform content an return data
     * @return mixed
     */
    abstract function transform(string $content);
}